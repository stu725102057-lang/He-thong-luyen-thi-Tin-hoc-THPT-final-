# 🧪 TEST HỆ THỐNG - HƯỚNG DẪN NHANH

## ❗ VẤN ĐỀ: "Không thể tương tác với hệ thống"

### 🔍 Các bước kiểm tra:

## 1️⃣ Kiểm tra Laravel Server
```bash
# Xem process PHP
Get-Process -Name php

# Nếu không có, khởi động server
cd "d:\Hệ thống luyện thi THPT môn Tin học"
php artisan serve
```

Server đang chạy tại: **http://127.0.0.1:8000**

---

## 2️⃣ Kiểm tra trình duyệt

### A. Mở Developer Tools (F12)

#### Console Tab - Kiểm tra lỗi JavaScript
Tìm các lỗi màu đỏ:
- `ReferenceError` - Biến không tồn tại
- `TypeError` - Lỗi kiểu dữ liệu
- `SyntaxError` - Lỗi cú pháp

#### Network Tab - Kiểm tra API requests
1. Bật "Preserve log"
2. Thử thao tác (click vào nút)
3. Xem các request:
   - Màu đỏ = Lỗi
   - Xem Status Code (200 = OK, 500 = Server error, 404 = Not found)
   - Xem Response

### B. Hard Refresh (Xóa cache)
```
Windows: Ctrl + Shift + R
hoặc: Ctrl + F5
```

### C. Xóa hoàn toàn cache
1. Nhấn `Ctrl + Shift + Delete`
2. Chọn "Cached images and files"
3. Chọn "All time"
4. Click "Clear data"

---

## 3️⃣ Test từng chức năng

### ✅ Test 1: Đăng nhập
```
URL: http://127.0.0.1:8000
Username: hocsinh1
Password: 123456
```

**Mong đợi:**
- Đăng nhập thành công
- Chuyển sang màn hình chính
- Thấy menu: Danh sách đề thi, Lịch sử thi, Thống kê

**Nếu lỗi:**
- F12 → Console → Chụp lỗi
- F12 → Network → Tìm request `/api/login` → Xem Response

---

### ✅ Test 2: Xem danh sách đề thi
**Sau khi đăng nhập**, click "Danh sách đề thi"

**Mong đợi:**
- Hiển thị danh sách đề thi
- Mỗi đề có: Tên, Số câu hỏi, Thời gian, Nút "Làm bài"

**Nếu lỗi:**
- F12 → Network → Tìm request `/api/de-thi` → Xem Response

---

### ✅ Test 3: Bắt đầu làm bài
Click nút **"Làm bài"** ở một đề thi

**Mong đợi:**
- Hiện modal xác nhận
- Click "Bắt đầu làm bài"
- Chuyển sang màn hình làm bài
- Hiện câu hỏi đầu tiên
- Đồng hồ đếm ngược

**Nếu lỗi:**
- F12 → Console → Xem log "=== START EXAM ==="
- F12 → Network → Tìm request `/api/de-thi/{maDe}/bat-dau` → Xem Response

---

### ✅ Test 4: Làm bài
- Chọn đáp án cho câu hỏi
- Click "Câu sau" để chuyển câu

**Mong đợi:**
- Đáp án được lưu
- Chuyển câu thành công
- Số câu đã làm tăng lên

---

### ✅ Test 5: Nộp bài
Click nút **"Nộp bài"** màu đỏ

**Mong đợi:**
- Hiện modal xác nhận
- Click "Nộp bài" → Chuyển sang màn hình kết quả
- Hiển thị: Điểm số, Số câu đúng/sai, Chi tiết từng câu

**Nếu lỗi:**
- F12 → Console → Xem log "=== SUBMIT EXAM ==="
- F12 → Network → Tìm request `/api/bai-lam/nop-bai` → Xem Response

---

## 4️⃣ Các lỗi thường gặp

### ❌ Lỗi: "Không nhận được phản hồi từ server"
**Nguyên nhân:** Server trả về HTML thay vì JSON

**Giải pháp:**
1. Kiểm tra route có đúng không
2. Kiểm tra Controller có return JSON không
3. Xem log: `storage/logs/laravel.log`

```bash
Get-Content "d:\Hệ thống luyện thi THPT môn Tin học\storage\logs\laravel.log" -Tail 50
```

---

### ❌ Lỗi: "Cannot read properties of null"
**Nguyên nhân:** Biến JavaScript là null/undefined

**Giải pháp:**
1. F12 → Console → Xem dòng lỗi
2. Kiểm tra dữ liệu từ API
3. Kiểm tra sessionStorage/localStorage

Xem trong Console:
```javascript
localStorage.getItem('token')
sessionStorage.getItem('currentExam')
sessionStorage.getItem('hocSinhInfo')
```

---

### ❌ Lỗi: HTTP 401 Unauthorized
**Nguyên nhân:** Token hết hạn hoặc không hợp lệ

**Giải pháp:**
1. Logout
2. Login lại
3. Hoặc clear localStorage:
```javascript
localStorage.clear()
location.reload()
```

---

### ❌ Lỗi: HTTP 404 Not Found
**Nguyên nhân:** Route không tồn tại

**Giải pháp:**
1. Kiểm tra route:
```bash
php artisan route:list | Select-String "api"
```

2. Clear route cache:
```bash
php artisan route:clear
```

---

### ❌ Lỗi: HTTP 500 Internal Server Error
**Nguyên nhân:** Lỗi trong Controller (SQL, logic, etc.)

**Giải pháp:**
Xem log chi tiết:
```bash
Get-Content storage\logs\laravel.log -Tail 100
```

Hoặc bật debug mode trong `.env`:
```
APP_DEBUG=true
```

---

## 5️⃣ Test API bằng Command Line

### Test đăng nhập:
```powershell
$body = @{
    TenDangNhap = "hocsinh1"
    MatKhau = "123456"
} | ConvertTo-Json

Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/login" `
    -Method POST `
    -Body $body `
    -ContentType "application/json"
```

### Test lấy danh sách đề thi:
```powershell
# Thay YOUR_TOKEN bằng token từ login
$token = "YOUR_TOKEN_HERE"

Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/de-thi" `
    -Headers @{Authorization = "Bearer $token"} `
    -Method GET
```

---

## 6️⃣ Checklist đầy đủ

- [ ] Laravel server đang chạy (`php artisan serve`)
- [ ] Truy cập được `http://127.0.0.1:8000`
- [ ] Hard refresh trình duyệt (Ctrl + Shift + R)
- [ ] Xóa cache trình duyệt
- [ ] F12 → Console không có lỗi đỏ
- [ ] Đăng nhập thành công
- [ ] Token được lưu vào localStorage
- [ ] Xem được danh sách đề thi
- [ ] Bắt đầu làm bài thành công
- [ ] Làm bài và chọn đáp án OK
- [ ] Nộp bài thành công
- [ ] Xem được kết quả

---

## 7️⃣ Nếu vẫn không được

### Gửi cho tôi:

1. **Screenshot Console (F12 → Console)**
2. **Screenshot Network tab** (F12 → Network → Request màu đỏ)
3. **Log Laravel:**
```bash
Get-Content storage\logs\laravel.log -Tail 100 > debug.txt
```

4. **Mô tả chi tiết:**
   - Bạn đang ở màn hình nào?
   - Bạn click vào nút gì?
   - Có thông báo lỗi gì không?
   - Có pop-up/modal nào hiện không?

---

## 8️⃣ Quick Fix - Restart toàn bộ

Nếu tất cả đều không được, restart lại:

```bash
# 1. Stop server (Ctrl + C trong terminal đang chạy server)

# 2. Clear tất cả cache
cd "d:\Hệ thống luyện thi THPT môn Tin học"
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 3. Restart server
php artisan serve

# 4. Trong trình duyệt:
# - Ctrl + Shift + Delete → Clear cache
# - Ctrl + Shift + R (Hard refresh)
# - F12 → Application → Clear storage → Clear site data
```

---

## 🎯 Routes đã tạo

```
POST   /api/login
POST   /api/register
GET    /api/de-thi                       # Danh sách đề thi
GET    /api/de-thi/{maDe}                # Chi tiết đề thi
POST   /api/de-thi/{maDe}/bat-dau        # Bắt đầu làm bài
POST   /api/bai-lam/nop-bai              # Nộp bài
POST   /api/bai-lam/luu-nhap             # Lưu nháp (auto-save)
GET    /api/bai-lam/{maBaiLam}/chi-tiet  # Xem chi tiết bài làm
GET    /api/bai-lam/{maBaiLam}/ket-qua   # Xem kết quả
```

---

**Cập nhật:** 8/12/2025 - 22:30  
**Status:** Hệ thống đã sẵn sàng, cần test trên trình duyệt
