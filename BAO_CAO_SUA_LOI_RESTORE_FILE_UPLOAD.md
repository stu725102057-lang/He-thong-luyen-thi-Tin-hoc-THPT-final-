# 🔧 BÁO CÁO: Sửa lỗi Restore Backup - File field is required

**Ngày:** 14/12/2025  
**Trạng thái:** ✅ HOÀN THÀNH

---

## ❌ VẤN ĐỀ

### Lỗi khi Restore:

```
Error: Không thể khôi phục database: The file field is required.
```

**Console error:**
```
API Call Error: Error: Không thể khôi phục database: The file field is required.
```

### Phân tích:

1. ✅ User tải file backup về máy thành công
2. ✅ User chọn file trong Restore modal
3. ❌ Khi bấm "Bắt đầu Restore" → Backend trả về lỗi "file field is required"
4. ❌ Backend validation failed vì không nhận được file

**Nguyên nhân:**
- Frontend gọi `this.apiCall()` để upload file
- `apiCall()` method luôn set `'Content-Type': 'application/json'`
- Khi upload file, phải dùng `multipart/form-data`
- Browser KHÔNG thể parse FormData khi có Content-Type: application/json
- Backend không nhận được file → validation fail

---

## 🔍 ROOT CAUSE

### Code Cũ (SAI):

```javascript
async startRestore() {
    const formData = new FormData();
    formData.append('file', fileInput.files[0]);
    
    // ❌ SAI: Dùng apiCall() cho FormData
    const result = await this.apiCall('/restore', {
        method: 'POST',
        headers: {
            'Authorization': `Bearer ${this.token}`
        },
        body: formData // FormData object
    });
}
```

### apiCall() method:

```javascript
async apiCall(endpoint, options = {}) {
    const headers = {
        'Content-Type': 'application/json', // ❌ LUÔN set JSON!
        'Accept': 'application/json',
        ...options.headers
    };
    
    const response = await fetch(this.apiUrl + endpoint, {
        ...options,
        headers,
        body: options.body
    });
}
```

**Vấn đề:**
1. `apiCall()` set `Content-Type: application/json`
2. Browser gửi FormData với wrong Content-Type
3. Server không parse được file từ JSON request
4. Validation fails: "file field is required"

---

## ✅ GIẢI PHÁP

### Dùng fetch() trực tiếp cho file upload

**Code Mới (ĐÚNG):**

```javascript
async startRestore() {
    const fileInput = document.getElementById('restoreFile');
    const progressDiv = document.getElementById('restoreProgress');
    const successDiv = document.getElementById('restoreSuccess');
    const btnStart = document.getElementById('btnStartRestore');
    
    if (!fileInput.files || fileInput.files.length === 0) {
        this.showAlert('Vui lòng chọn file backup', 'warning');
        return;
    }
    
    if (!confirm('BẠN CHẮC CHẮN MUỐN RESTORE? Dữ liệu hiện tại sẽ BỊ GHI ĐÈ!')) {
        return;
    }
    
    try {
        btnStart.disabled = true;
        progressDiv.style.display = 'block';
        successDiv.style.display = 'none';
        
        const formData = new FormData();
        formData.append('file', fileInput.files[0]);
        
        // QUAN TRỌNG: Dùng fetch trực tiếp cho FormData
        // KHÔNG dùng apiCall vì nó set Content-Type: application/json
        const response = await fetch(`${this.apiUrl}/restore`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${this.token}`
                // KHÔNG set Content-Type! Browser tự động set multipart/form-data
            },
            body: formData
        });
        
        const result = await response.json();
        
        if (!response.ok) {
            throw new Error(result.message || `HTTP ${response.status}`);
        }
        
        if (result && result.success) {
            progressDiv.style.display = 'none';
            successDiv.style.display = 'block';
            
            setTimeout(() => {
                this.showAlert('Restore thành công! Vui lòng đăng nhập lại.', 'success');
                bootstrap.Modal.getInstance(document.getElementById('restoreModal')).hide();
                this.logout();
            }, 2000);
        }
    } catch (error) {
        console.error('Restore error:', error);
        progressDiv.style.display = 'none';
        this.showAlert('Restore thất bại: ' + error.message, 'danger');
    } finally {
        btnStart.disabled = false;
    }
}
```

**Tại sao phải dùng fetch() trực tiếp?**

1. **FormData requires multipart/form-data**
   - Browser tự động set Content-Type với boundary
   - Format: `multipart/form-data; boundary=----WebKitFormBoundary...`

2. **KHÔNG được set Content-Type manually**
   - Nếu set Content-Type, browser KHÔNG thêm boundary
   - Server không parse được multipart data

3. **apiCall() không phù hợp cho file upload**
   - Designed cho JSON requests
   - Luôn set `Content-Type: application/json`

---

## 📊 SO SÁNH 2 PHƯƠNG PHÁP

### Method 1: apiCall() (SAI cho file upload)

**Request Headers:**
```
POST /api/restore
Content-Type: application/json  ← SAI!
Authorization: Bearer xxx

[FormData object - KHÔNG parse được]
```

**Result:** ❌ Backend không nhận được file

---

### Method 2: fetch() trực tiếp (ĐÚNG)

**Request Headers:**
```
POST /api/restore
Content-Type: multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW  ← ĐÚNG!
Authorization: Bearer xxx

------WebKitFormBoundary7MA4YWxkTrZu0gW
Content-Disposition: form-data; name="file"; filename="backup_13.sql"
Content-Type: application/x-sql

[File content...]
------WebKitFormBoundary7MA4YWxkTrZu0gW--
```

**Result:** ✅ Backend nhận được file thành công

---

## 🧪 TEST FLOW

### Restore Backup Flow:

```
User: Chọn file backup_13.sql
    ↓
User: Bấm "Bắt đầu Restore"
    ↓
Frontend: startRestore()
    - Create FormData
    - Append file
    - fetch() với multipart/form-data
    ↓
Backend: POST /api/restore
    - auth:sanctum middleware ✅
    - Validate file field ✅
    - Store temp file ✅
    - Create safety backup ✅
    - Execute mysql restore ✅
    - Return success ✅
    ↓
Frontend:
    - Hide progress
    - Show success
    - Show alert
    - Logout (require re-login)
```

---

## 🎓 LESSONS LEARNED

### 1. FormData Upload Requirements

**PHẢI:**
- ✅ Dùng FormData object
- ✅ KHÔNG set Content-Type header
- ✅ Let browser auto-set multipart/form-data
- ✅ Browser sẽ add boundary automatically

**KHÔNG ĐƯỢC:**
- ❌ Set Content-Type: application/json
- ❌ Set Content-Type: multipart/form-data (missing boundary)
- ❌ Dùng JSON.stringify() cho FormData

---

### 2. apiCall() vs fetch() cho file upload

**apiCall() - CHỈ dùng cho:**
- ✅ JSON requests (POST/PUT/PATCH)
- ✅ GET requests
- ✅ DELETE requests
- ❌ KHÔNG dùng cho file uploads!

**fetch() trực tiếp - Dùng cho:**
- ✅ File uploads
- ✅ FormData submissions
- ✅ Binary data uploads
- ✅ Custom Content-Type requirements

---

### 3. Content-Type cho File Upload

**Browser tự động set:**
```
Content-Type: multipart/form-data; boundary=----WebKitFormBoundary...
```

**Boundary format:**
```
------WebKitFormBoundary7MA4YWxkTrZu0gW
Content-Disposition: form-data; name="file"; filename="backup.sql"
Content-Type: application/x-sql

[binary data]
------WebKitFormBoundary7MA4YWxkTrZu0gW--
```

**NẾU set Content-Type manually:**
```
Content-Type: multipart/form-data  ← Missing boundary!
```
→ Server KHÔNG parse được!

---

## 🔒 VALIDATION FLOW

### Backend Validation:

```php
$request->validate([
    'file' => 'required|file|mimes:sql,txt|max:102400' // 100MB
]);
```

**Checks:**
1. ✅ Field 'file' exists?
2. ✅ Is uploaded file?
3. ✅ MIME type is .sql or .txt?
4. ✅ Size < 100MB?

**Error cases:**
- ❌ No file → "file field is required"
- ❌ Not uploaded file → "file must be a file"
- ❌ Wrong extension → "file must be sql or txt"
- ❌ Too large → "file may not be greater than 102400 KB"

---

## 🎉 KẾT QUẢ

### Trước khi sửa:
```
❌ "The file field is required"
❌ Backend không nhận được file
❌ apiCall() set wrong Content-Type
```

### Sau khi sửa:
```
✅ File upload thành công
✅ Backend nhận được file
✅ Validation pass
✅ Restore hoạt động
✅ Auto logout sau restore
```

---

## 📝 COMMIT MESSAGE

```
fix: Use fetch() instead of apiCall() for file upload in restore

- Replace apiCall() with fetch() in startRestore()
- Remove Content-Type header to let browser set multipart/form-data
- Add proper error handling for restore failures
- Preserve Authorization Bearer token

Fixes: "The file field is required" error when restoring backup
Reason: apiCall() sets Content-Type: application/json, breaking FormData upload
```

---

**Tóm tắt:** `apiCall()` method luôn set `Content-Type: application/json` nên không dùng được cho file upload. Phải dùng `fetch()` trực tiếp và KHÔNG set Content-Type để browser tự động set `multipart/form-data` với boundary.

**Status:** ✅ HOÀN THÀNH

