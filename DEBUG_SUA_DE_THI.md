# 🔧 HƯỚNG DẪN DEBUG CHỨC NĂNG SỬA ĐỀ THI

## ❓ Vấn đề hiện tại
Chức năng **XÓA** đang hoạt động ✅  
Chức năng **SỬA** chưa hoạt động ❌

---

## 🛠️ Cách debug từng bước

### Bước 1: Kiểm tra trong trình duyệt

1. **Mở giao diện chính:**
   ```
   http://127.0.0.1:8000
   ```

2. **Đăng nhập với tài khoản giáo viên:**
   - Username: `giaovien1`
   - Password: `123456`

3. **Vào menu "Danh sách đề thi"**

4. **Mở Console (F12)**

5. **Click nút "Sửa" (màu vàng) của một đề thi**

6. **Quan sát Console để xem:**
   - Có lỗi JavaScript không?
   - API có được gọi không?
   - Modal có hiện lên không?

---

### Bước 2: Sử dụng Debug Tool

1. **Mở file debug:**
   ```
   http://127.0.0.1:8000/debug-edit-exam.html
   ```

2. **Thực hiện các bước:**
   - ✅ **Bước 1:** Click "🔐 Đăng nhập"
   - ✅ **Bước 2:** Click "📋 Lấy đề thi" để xem danh sách
   - ✅ **Bước 3:** Nhập thông tin:
     - Mã đề (VD: `DE009`)
     - Tên đề mới (VD: `Đề thi đã sửa`)
     - Thời gian (VD: `90`)
     - Chủ đề (tùy chọn)
   - ✅ **Bước 4:** Click "✏️ Test Sửa Đề Thi"
   - ✅ **Bước 5:** Xem log chi tiết bên dưới

3. **Phân tích kết quả:**
   - Nếu thấy `✅ SỬA ĐỀ THI THÀNH CÔNG!` → API hoạt động tốt, vấn đề ở frontend
   - Nếu thấy `❌ SỬA ĐỀ THI THẤT BẠI!` → Xem lỗi chi tiết trong log

---

## 🔍 Các vấn đề có thể xảy ra

### 1. Modal không hiển thị
**Triệu chứng:** Click nút "Sửa" nhưng modal không xuất hiện

**Nguyên nhân:**
- Bootstrap chưa load đầy đủ
- Lỗi JavaScript trước đó
- ID modal bị sai

**Cách fix:**
```javascript
// Kiểm tra trong Console (F12)
console.log(document.getElementById('editExamModal'));
// Phải trả về element, không phải null
```

---

### 2. API không được gọi
**Triệu chứng:** Modal hiển thị nhưng không có dữ liệu

**Nguyên nhân:**
- Token hết hạn
- API route chưa đúng
- Middleware chặn request

**Cách fix:**
```javascript
// Kiểm tra token trong Console
console.log(localStorage.getItem('token'));

// Test API trực tiếp
fetch('http://127.0.0.1:8000/api/de-thi/DE009/detail', {
  headers: {
    'Authorization': 'Bearer ' + localStorage.getItem('token'),
    'Accept': 'application/json'
  }
})
.then(r => r.json())
.then(d => console.log(d));
```

---

### 3. API trả về lỗi 403 (Forbidden)
**Triệu chứng:** Báo lỗi "Không có quyền"

**Nguyên nhân:**
- Đang đăng nhập với tài khoản không phải giáo viên
- Đề thi không thuộc về giáo viên hiện tại
- Token không hợp lệ

**Cách fix:**
- Đảm bảo đăng nhập với tài khoản giáo viên
- Chỉ sửa đề thi do mình tạo

---

### 4. API trả về lỗi 404 (Not Found)
**Triệu chứng:** Báo lỗi "Không tìm thấy đề thi"

**Nguyên nhân:**
- Mã đề sai
- Đề thi đã bị xóa

**Cách fix:**
- Kiểm tra lại mã đề trong database
- Làm mới danh sách đề thi

---

### 5. Form validation lỗi
**Triệu chứng:** Click "Cập nhật" nhưng không thực hiện

**Nguyên nhân:**
- Thiếu thông tin bắt buộc
- Thời gian không hợp lệ (<10 hoặc >180)

**Cách fix:**
- Điền đầy đủ các trường có dấu `*`
- Thời gian phải từ 10-180 phút

---

## 🧪 Test thủ công bằng Postman

### Test API GET Detail
```
GET http://127.0.0.1:8000/api/de-thi/DE009/detail

Headers:
- Authorization: Bearer {your_token}
- Accept: application/json
```

**Expected Response:**
```json
{
  "success": true,
  "data": {
    "exam": {
      "MaDe": "DE009",
      "TenDe": "Đề 2",
      "ChuDe": "Tin học",
      "ThoiGianLamBai": 45,
      "SoLuongCauHoi": 5,
      "MoTa": null,
      "NgayTao": "2025-12-08",
      "TrangThai": 1
    },
    "questions": [...]
  }
}
```

---

### Test API PUT Update
```
PUT http://127.0.0.1:8000/api/de-thi/DE009

Headers:
- Authorization: Bearer {your_token}
- Content-Type: application/json
- Accept: application/json

Body (JSON):
{
  "TenDe": "Đề 2 đã sửa",
  "ChuDe": "Tin học đại cương",
  "ThoiGianLamBai": 90,
  "MoTa": "Đề thi đã được cập nhật",
  "TrangThai": 1
}
```

**Expected Response:**
```json
{
  "success": true,
  "message": "Cập nhật đề thi thành công",
  "data": {
    "MaDe": "DE009",
    "TenDe": "Đề 2 đã sửa",
    "ChuDe": "Tin học đại cương",
    "ThoiGianLamBai": 90,
    "MoTa": "Đề thi đã được cập nhật",
    "NgayTao": "2025-12-08",
    "TrangThai": 1,
    "updated_at": "2025-12-08 ..."
  }
}
```

---

## 📝 Checklist Debug

Thực hiện các bước sau theo thứ tự:

### ✅ Backend (API)
- [ ] Routes đã được đăng ký (`php artisan route:list --path=de-thi`)
- [ ] Controller method `updateExam` tồn tại
- [ ] Database có bảng `dethi` với đủ cột
- [ ] Test bằng Postman/cURL thành công

### ✅ Frontend (UI)
- [ ] Modal `editExamModal` tồn tại trong HTML
- [ ] Nút "Sửa" có `onclick="app.editExam('...')"`
- [ ] JavaScript function `editExam()` tồn tại
- [ ] JavaScript function `updateExam()` tồn tại
- [ ] Bootstrap 5 đã được load đầy đủ

### ✅ Integration (Tích hợp)
- [ ] Token được lưu trong localStorage
- [ ] API URL đúng (`http://127.0.0.1:8000/api`)
- [ ] CORS không bị chặn
- [ ] Console không có lỗi JavaScript

---

## 🚨 Lỗi thường gặp và cách fix nhanh

### Lỗi 1: "app.editExam is not a function"
**Fix:**
```javascript
// Kiểm tra trong Console
console.log(typeof app.editExam);
// Phải trả về "function"

// Nếu undefined, làm mới trang (Ctrl + F5)
```

---

### Lỗi 2: "Cannot read property 'value' of null"
**Fix:**
```javascript
// Kiểm tra element tồn tại
console.log(document.getElementById('editExamMaDe'));
console.log(document.getElementById('editExamTenDe'));

// Nếu null, kiểm tra lại ID trong HTML
```

---

### Lỗi 3: Modal hiện nhưng không có dữ liệu
**Fix:**
```javascript
// Thêm log debug trong hàm editExam
async editExam(maDe) {
    console.log('🔍 editExam called with:', maDe);
    try {
        this.showLoader();
        console.log('📡 Calling API...');
        
        const data = await this.apiCall(`/de-thi/${maDe}/detail`);
        console.log('📊 API Response:', data);
        
        if (data && data.success) {
            const exam = data.data.exam;
            console.log('✅ Exam data:', exam);
            
            // Fill form...
        }
    } catch (error) {
        console.error('❌ Error:', error);
    }
}
```

---

## 📞 Cần trợ giúp thêm?

1. **Chạy debug tool:**
   ```
   http://127.0.0.1:8000/debug-edit-exam.html
   ```

2. **Xem log chi tiết:**
   - Mở Console (F12)
   - Tab "Network" để xem API requests
   - Tab "Console" để xem JavaScript errors

3. **Kiểm tra backend:**
   ```bash
   # Xem Laravel logs
   tail -f storage/logs/laravel.log
   ```

4. **Gửi thông tin debug:**
   - Screenshot màn hình
   - Console log
   - Network tab (API responses)

---

## ✅ Sau khi fix thành công

1. Test lại toàn bộ flow:
   - Đăng nhập → Vào "Danh sách đề thi"
   - Click "Sửa" → Modal hiển thị
   - Sửa thông tin → Click "Cập nhật"
   - Kiểm tra danh sách đã được cập nhật

2. Clear cache trình duyệt (Ctrl + F5)

3. Test trên trình duyệt khác (Chrome, Firefox, Edge)

4. Test với nhiều đề thi khác nhau

---

**Good luck! 🚀**
