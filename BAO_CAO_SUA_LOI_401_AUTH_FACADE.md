# 🔧 BÁO CÁO: Sửa lỗi 401 Unauthorized khi Download Backup

**Ngày:** 14/12/2025  
**Trạng thái:** ✅ HOÀN THÀNH

---

## ❌ VẤN ĐỀ

### Console Error:
```
GET http://127.0.0.1:8000/api/backups/12/download?token=5|s5tphfG9nFGKxqiuyTC2...
401 (Unauthorized)
```

### Response:
```json
{
    "success": false,
    "message": "Unauthenticated.",
    "error": "You must be logged in to access this resource."
}
```

### Phân tích:

**Token được gửi đúng** qua query string NHƯNG backend vẫn trả về 401!

Nguyên nhân:
1. ✅ Frontend gửi token trong URL: `?token=5|xxx`
2. ✅ `AuthenticateWithQueryToken` middleware parse token
3. ✅ Middleware tìm thấy token hợp lệ
4. ❌ **Middleware chỉ set `$request->setUserResolver()`**
5. ❌ **`auth:sanctum` middleware không nhận ra user đã được set**
6. ❌ Request bị reject với 401 Unauthorized

---

## 🔍 ROOT CAUSE

### Code Cũ (SAI):

```php
// app/Http/Middleware/AuthenticateWithQueryToken.php

if ($accessToken) {
    // CHỈ set user resolver
    $request->setUserResolver(function () use ($accessToken) {
        return $accessToken->tokenable;
    });
}
```

**Vấn đề:** 
- `setUserResolver()` chỉ set callback function
- `auth:sanctum` middleware chạy SAU và không gọi resolver này
- Laravel Auth facade vẫn chưa có user → 401 Unauthorized

---

## ✅ GIẢI PHÁP

### Set User vào Auth Facade

**Code Mới (ĐÚNG):**

```php
// app/Http/Middleware/AuthenticateWithQueryToken.php

use Illuminate\Support\Facades\Auth;

if ($accessToken) {
    // QUAN TRỌNG: Set user vào Auth facade TRƯỚC
    Auth::setUser($accessToken->tokenable);
    
    // Cũng set vào request resolver để đảm bảo
    $request->setUserResolver(function () use ($accessToken) {
        return $accessToken->tokenable;
    });
}
```

**Tại sao cần `Auth::setUser()`?**

1. **Auth facade** là singleton được share toàn bộ request lifecycle
2. Middleware `auth:sanctum` check `Auth::check()` → cần user trong Auth facade
3. `setUserResolver()` chỉ hoạt động khi gọi `$request->user()`
4. Phải set CẢ HAI để đảm bảo compatibility

---

## 📝 FULL CODE

**File:** `app/Http/Middleware/AuthenticateWithQueryToken.php`

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class AuthenticateWithQueryToken
{
    /**
     * Handle an incoming request.
     * 
     * Accept token from:
     * 1. Authorization header (default)
     * 2. Query parameter ?token=xxx (for file downloads)
     */
    public function handle(Request $request, Closure $next)
    {
        // Nếu có token trong query string
        if ($request->has('token') && !$request->bearerToken()) {
            $token = $request->query('token');
            
            // Validate token với Sanctum
            $accessToken = PersonalAccessToken::findToken($token);
            
            if ($accessToken) {
                // QUAN TRỌNG: Set user vào Auth facade
                Auth::setUser($accessToken->tokenable);
                
                // Cũng set vào request resolver để đảm bảo
                $request->setUserResolver(function () use ($accessToken) {
                    return $accessToken->tokenable;
                });
            }
        }
        
        // Tiếp tục với Sanctum middleware
        return $next($request);
    }
}
```

---

## 🔄 MIDDLEWARE FLOW

### Trước khi sửa:

```
Request: /api/backups/12/download?token=5|xxx
    ↓
AuthenticateWithQueryToken:
    - Parse token từ query
    - Find PersonalAccessToken ✅
    - setUserResolver() only ❌
    ↓
auth:sanctum:
    - Check Auth::check() → FALSE ❌
    - Throw AuthenticationException
    ↓
Exception Handler:
    - Return 401 JSON
```

---

### Sau khi sửa:

```
Request: /api/backups/12/download?token=5|xxx
    ↓
AuthenticateWithQueryToken:
    - Parse token từ query
    - Find PersonalAccessToken ✅
    - Auth::setUser() ✅
    - setUserResolver() ✅
    ↓
auth:sanctum:
    - Check Auth::check() → TRUE ✅
    - Continue
    ↓
BackupController:
    - Check user is admin
    - Download file ✅
```

---

## 🧪 TEST CASE

### Test 1: Download với token hợp lệ

**Request:**
```
GET /api/backups/12/download?token=5|s5tphfG9nFGKxqiuyTC2...
```

**Expected:**
```
Status: 200 OK
Content-Type: application/x-sql
Content-Disposition: attachment; filename="backup_20251214_084425.sql"

[SQL file content...]
```

✅ **File downloaded successfully**

---

### Test 2: Download không có token

**Request:**
```
GET /api/backups/12/download
```

**Expected:**
```json
{
    "success": false,
    "message": "Unauthenticated.",
    "error": "You must be logged in to access this resource."
}
```

Status: 401 Unauthorized ✅

---

### Test 3: Download với token sai

**Request:**
```
GET /api/backups/12/download?token=invalid_token_123
```

**Expected:**
```json
{
    "success": false,
    "message": "Unauthenticated.",
    "error": "You must be logged in to access this resource."
}
```

Status: 401 Unauthorized ✅

---

## 📊 SO SÁNH API METHODS

### Method 1: setUserResolver (KHÔNG ĐỦ)

```php
$request->setUserResolver(function () use ($user) {
    return $user;
});
```

**Hoạt động:**
- ✅ `$request->user()` returns user
- ❌ `Auth::check()` returns FALSE
- ❌ `Auth::user()` returns NULL
- ❌ Middleware `auth:sanctum` fails

---

### Method 2: Auth::setUser (ĐÚNG)

```php
Auth::setUser($user);
```

**Hoạt động:**
- ✅ `$request->user()` returns user
- ✅ `Auth::check()` returns TRUE
- ✅ `Auth::user()` returns user
- ✅ Middleware `auth:sanctum` passes

---

### Method 3: CẢ HAI (TỐT NHẤT)

```php
Auth::setUser($user);
$request->setUserResolver(function () use ($user) {
    return $user;
});
```

**Hoạt động:**
- ✅ Compatible với mọi cách check authentication
- ✅ Works với Auth facade
- ✅ Works với $request->user()
- ✅ Works với middleware

---

## 🎓 LESSONS LEARNED

### 1. Laravel Authentication Có Nhiều Layer

```
1. Auth Facade (global singleton)
2. Request User Resolver (request-specific)
3. Sanctum Guard (token validation)
```

**Phải set đúng layer để middleware hoạt động!**

---

### 2. Middleware Order Matters

```php
Route::middleware(['auth.token', 'auth:sanctum'])->group(function () {
    // auth.token phải chạy TRƯỚC auth:sanctum
});
```

`auth.token` phải set user TRƯỚC KHI `auth:sanctum` check!

---

### 3. Debug Authentication Issues

**Checklist:**
1. ✅ Token có được gửi không? (Console Network tab)
2. ✅ Middleware có parse token không? (Add debug log)
3. ✅ Token có hợp lệ không? (Check database)
4. ✅ User có được set không? (Check Auth::check())
5. ✅ Middleware order đúng không? (Check routes)

---

## 🎉 KẾT QUẢ

### Trước khi sửa:
```
❌ 401 Unauthorized
❌ File không tải được
❌ Token bị ignore
```

### Sau khi sửa:
```
✅ 200 OK
✅ File download thành công
✅ Token được authenticate đúng
✅ Auth::check() returns TRUE
```

---

## 📝 COMMIT MESSAGE

```
fix: Set user in Auth facade for query token authentication

- Add Auth::setUser() in AuthenticateWithQueryToken middleware
- Fix 401 Unauthorized error when downloading backup with token
- Ensure auth:sanctum middleware recognizes authenticated user
- Keep setUserResolver() for compatibility

Fixes: Download backup returns 401 despite valid token
```

---

**Tóm tắt:** Middleware chỉ set `setUserResolver()` KHÔNG ĐỦ vì `auth:sanctum` middleware check `Auth::check()` chứ không check request resolver. Phải dùng `Auth::setUser()` để set user vào Auth facade.

**Status:** ✅ HOÀN THÀNH

