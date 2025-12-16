# 🔧 BÁO CÁO: Đổi Download Method từ Query Token → Bearer Token

**Ngày:** 14/12/2025  
**Trạng thái:** ✅ HOÀN THÀNH

---

## ❌ VẤN ĐỀ

### Vấn đề với Query Token:

```
GET /api/backups/13/download?token=5|s5tphfG9nFGKxqiuyTC2...
401 Unauthorized
```

**Nguyên nhân:**
1. Token Sanctum có ký tự `|` (pipe)
2. Khi gửi qua URL query string, `|` bị encode thành `%7C`
3. Browser/server có thể không parse đúng token
4. `PersonalAccessToken::findToken()` không tìm thấy token
5. Auth failed → 401

---

## ✅ GIẢI PHÁP MỚI

### Dùng fetch() với Authorization Bearer Header

**Thay vì:** `window.location.href` với token trong URL  
**Dùng:** `fetch()` với Bearer token trong header + blob download

---

## 📝 CODE CHANGES

### 1. Frontend: Đổi từ window.location → fetch

**File:** `resources/views/app.blade.php`

#### Before (SAI):
```javascript
async downloadBackup(maSaoLuu) {
    // Gửi token qua query string
    const url = `${this.apiUrl}/backups/${maSaoLuu}/download?token=${this.token}`;
    window.location.href = url; // ❌ Không gửi headers được
}
```

#### After (ĐÚNG):
```javascript
async downloadBackup(maSaoLuu) {
    // Gửi token qua Authorization header
    const url = `${this.apiUrl}/backups/${maSaoLuu}/download`;
    
    const response = await fetch(url, {
        method: 'GET',
        headers: {
            'Authorization': `Bearer ${this.token}`, // ✅ Bearer token
            'Accept': 'application/octet-stream'
        }
    });
    
    if (!response.ok) {
        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
    }
    
    // Lấy filename từ Content-Disposition header
    const contentDisposition = response.headers.get('Content-Disposition');
    let filename = `backup_${maSaoLuu}.sql`;
    if (contentDisposition) {
        const matches = /filename="(.+)"/.exec(contentDisposition);
        if (matches) filename = matches[1];
    }
    
    // Download file as blob
    const blob = await response.blob();
    const downloadUrl = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = downloadUrl;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(downloadUrl);
    
    this.showAlert('Đã tải backup thành công!', 'success');
}
```

---

### 2. Backend: Đổi route về auth:sanctum chuẩn

**File:** `routes/api.php`

#### Before (PHỨC TẠP):
```php
// Cần custom middleware auth.token
Route::middleware(['auth.token', 'auth:sanctum'])->group(function () {
    Route::get('/backups/{maSaoLuu}/download', [BackupController::class, 'downloadBackup']);
});
```

#### After (ĐƠN GIẢN):
```php
// Dùng auth:sanctum bình thường
Route::middleware('auth:sanctum')->get('/backups/{maSaoLuu}/download', [BackupController::class, 'downloadBackup']);
```

---

## 🔄 SO SÁNH 2 PHƯƠNG PHÁP

### Method 1: Query Token (window.location)

**Request:**
```
GET /download?token=5|abc123
(No headers)
```

**Nhược điểm:**
- ❌ Token có ký tự đặc biệt `|` bị encode
- ❌ URL có thể bị log/cache với token
- ❌ Không gửi được custom headers
- ❌ Cần custom middleware phức tạp
- ❌ Security risk (token trong URL history)

---

### Method 2: Bearer Token (fetch + blob)

**Request:**
```
GET /download
Authorization: Bearer 5|abc123
Accept: application/octet-stream
```

**Ưu điểm:**
- ✅ Token trong header (an toàn hơn)
- ✅ Không bị URL encode
- ✅ Dùng auth:sanctum chuẩn
- ✅ Không cần custom middleware
- ✅ Token không lưu trong browser history
- ✅ Có thể xử lý errors tốt hơn
- ✅ Hiển thị progress/alerts

---

## 🧪 TEST FLOW

### Download Backup Flow:

```
User clicks "Tải về"
    ↓
Frontend: downloadBackup(maSaoLuu)
    ↓
fetch('/api/backups/13/download', {
    headers: {
        'Authorization': 'Bearer 5|token...',
        'Accept': 'application/octet-stream'
    }
})
    ↓
Backend: auth:sanctum middleware
    - Parse Bearer token ✅
    - Validate with Sanctum ✅
    - Set user ✅
    ↓
BackupController: downloadBackup()
    - Check user is admin ✅
    - Find backup file ✅
    - return response()->download() ✅
    ↓
Frontend: response
    - Convert to blob ✅
    - Create download link ✅
    - Trigger download ✅
    - Show success alert ✅
```

---

## 📊 FILE DOWNLOAD FLOW

### Blob Download Implementation:

```javascript
// 1. Fetch file as blob
const response = await fetch(url, {
    headers: { 'Authorization': `Bearer ${token}` }
});

// 2. Convert response to blob
const blob = await response.blob();

// 3. Create temporary download URL
const downloadUrl = window.URL.createObjectURL(blob);

// 4. Create hidden <a> element
const a = document.createElement('a');
a.href = downloadUrl;
a.download = filename;

// 5. Trigger download
document.body.appendChild(a);
a.click();

// 6. Cleanup
document.body.removeChild(a);
window.URL.revokeObjectURL(downloadUrl);
```

**Advantages:**
- ✅ Works with Authorization headers
- ✅ Can show download progress
- ✅ Can handle errors gracefully
- ✅ User-friendly alerts
- ✅ No page navigation

---

## 🎓 LESSONS LEARNED

### 1. window.location.href Limitations

**Không thể:**
- ❌ Gửi custom headers
- ❌ Xử lý errors
- ❌ Hiển thị progress
- ❌ Prevent navigation

**Chỉ dùng khi:**
- Public files (không cần auth)
- Simple downloads
- No error handling needed

---

### 2. fetch() + Blob Download Pattern

**Sử dụng khi:**
- ✅ Cần authentication headers
- ✅ Cần error handling
- ✅ Cần custom filename
- ✅ Cần user feedback

**Pattern:**
```javascript
fetch(url, { headers }) 
  → response.blob() 
  → URL.createObjectURL() 
  → <a>.click() 
  → cleanup
```

---

### 3. Bearer Token vs Query Token

**Bearer Token (KHUYẾN NGHỊ):**
- ✅ Standard HTTP authentication
- ✅ Sanctum native support
- ✅ More secure
- ✅ Not logged in URL history
- ✅ Not cached by proxies

**Query Token (AVOID):**
- ❌ Token in URL (security risk)
- ❌ Saved in browser history
- ❌ Can be logged by servers
- ❌ Encoding issues with special chars

---

## 🔒 SECURITY IMPROVEMENTS

### Before:
```
URL: /download?token=5|s5tphfG9nFGKxqiuyTC2blONEK8aaiDrtqbLiFbA498f3cc0

Risks:
- Token in browser history ⚠️
- Token in server logs ⚠️
- Token in proxy caches ⚠️
```

### After:
```
URL: /download
Header: Authorization: Bearer 5|s5t...

Security:
- Token not in URL ✅
- Token in memory only ✅
- Token in encrypted header ✅
```

---

## ✅ FILES ĐÁNG LƯU Ý

### Có thể XÓA (không cần nữa):

1. **app/Http/Middleware/AuthenticateWithQueryToken.php**
   - Custom middleware không cần thiết
   - auth:sanctum đã đủ

2. **app/Http/Kernel.php** - Remove line:
   ```php
   'auth.token' => \App\Http\Middleware\AuthenticateWithQueryToken::class,
   ```

---

## 🎉 KẾT QUẢ

### Trước khi đổi:
```
❌ 401 Unauthorized
❌ Token bị encode sai
❌ Cần custom middleware
❌ Security risks
```

### Sau khi đổi:
```
✅ Download thành công
✅ Bearer token chuẩn
✅ Dùng auth:sanctum bình thường
✅ An toàn hơn
✅ Code đơn giản hơn
✅ Có error handling
✅ Có success alerts
```

---

## 📝 COMMIT MESSAGE

```
refactor: Use fetch() with Bearer token for backup download

- Replace window.location with fetch() + blob download
- Send token via Authorization header instead of query string
- Remove auth.token middleware dependency
- Simplify route to use standard auth:sanctum
- Add proper error handling and user feedback
- Improve security by not exposing token in URL

Fixes: 401 Unauthorized when downloading backup
Security: Token no longer visible in browser history
```

---

**Tóm tắt:** Đổi từ `window.location` với query token sang `fetch()` với Bearer token. Đơn giản hơn, an toàn hơn, và hoạt động đúng với Sanctum authentication!

**Status:** ✅ 100% HOÀN THÀNH

