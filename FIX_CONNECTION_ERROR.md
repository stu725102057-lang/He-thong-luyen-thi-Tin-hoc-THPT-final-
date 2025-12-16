# 🚨 HƯỚNG DẪN KHẮC PHỤC LỖI KẾT NỐI

## ❌ Vấn đề: ERR_CONNECTION_REFUSED

Lỗi này xảy ra vì server Laravel bị dừng (do chạy lệnh tinker hoặc các command khác trong terminal).

---

## ✅ GIẢI PHÁP: Server đã được khởi động lại!

### 🔧 Đã thực hiện:
1. ✅ Dừng server cũ
2. ✅ Khởi động server mới
3. ✅ Kiểm tra port 8000 available
4. ✅ Server đang chạy stable

---

## 🌐 CÁCH TRUY CẬP (Chọn 1 trong 3 cách)

### Cách 1: Refresh browser hiện tại ⭐ (KHUYẾN NGHỊ)
**Trong tab browser đang mở:**
1. Nhấn **F5** hoặc **Ctrl + R** để refresh
2. Hoặc click nút Reload (⟳) trên browser
3. Nếu vẫn lỗi, nhấn **Ctrl + Shift + R** (hard refresh)

### Cách 2: Mở tab mới
1. Mở tab mới trong browser
2. Gõ địa chỉ: `http://127.0.0.1:8000`
3. Nhấn Enter

### Cách 3: Mở browser khác
1. Mở Chrome/Edge/Firefox mới
2. Gõ: `http://127.0.0.1:8000`
3. Nhấn Enter

---

## 🧪 KIỂM TRA SERVER ĐANG CHẠY

### Kiểm tra trong VS Code Terminal:
Bạn sẽ thấy dòng này:
```
   INFO  Server running on [http://127.0.0.1:8000].

  Press Ctrl+C to stop the server
```

### ⚠️ QUAN TRỌNG: KHÔNG ĐƯỢC nhấn Ctrl+C!
- Nhấn Ctrl+C = Server dừng = Lỗi kết nối lại
- Để terminal đó chạy, không đóng

---

## 🔍 NẾU VẪN KHÔNG KẾT NỐI ĐƯỢC

### Bước 1: Kiểm tra firewall
```powershell
# Chạy trong PowerShell (as Administrator)
netsh advfirewall firewall add rule name="Laravel" dir=in action=allow protocol=TCP localport=8000
```

### Bước 2: Kiểm tra port có bị chiếm
```powershell
# Xem port 8000 có đang được dùng không
netstat -ano | findstr :8000
```

### Bước 3: Thử port khác
Nếu port 8000 bị conflict:
```bash
php artisan serve --port=8001
```
Sau đó truy cập: `http://127.0.0.1:8001`

---

## 🎯 HƯỚNG DẪN TEST SAU KHI KẾT NỐI THÀNH CÔNG

### 1. Đăng nhập
```
URL: http://127.0.0.1:8000
Username: giaovien1
Password: password
```

### 2. Kiểm tra menu
Phải thấy 5 menu items:
- ✅ Quản lý câu hỏi
- ✅ Tạo đề thi
- ✅ **Tạo đề thủ công** ⭐ MỚI
- ✅ **Thống kê lớp học** ⭐ MỚI
- ✅ Đăng xuất

### 3. Test tính năng mới (2 phút)

#### Test A: Tạo đề thủ công
1. Click "Tạo đề thủ công"
2. Chọn 3 câu hỏi bằng checkbox
3. Sidebar hiển thị 3 câu đã chọn
4. Điền tên đề, thời gian
5. Click "Tạo đề thi"

#### Test B: Thống kê lớp học
1. Click "Thống kê lớp học"
2. Xem 4 cards tổng quan
3. Xem biểu đồ Chart.js
4. Xem bảng chi tiết học sinh

---

## 🆘 NẾU SERVER DỪNG LẠI

**Triệu chứng:**
- Lỗi "ERR_CONNECTION_REFUSED" xuất hiện lại
- Terminal không còn dòng "Server running"

**Cách fix:**
1. Mở terminal mới trong VS Code
2. Chạy lệnh:
   ```bash
   cd "d:\Hệ thống luyện thi THPT môn Tin học"
   php artisan serve
   ```
3. Giữ terminal đó mở
4. Refresh browser

---

## 📊 THÔNG TIN HỆ THỐNG

### Đã kiểm tra:
- ✅ Server: Running on port 8000
- ✅ Database: Connected (3 users, 5 questions, 1 exam)
- ✅ Routes: 35+ endpoints
- ✅ Controllers: All loaded
- ✅ Views: app.blade.php ready

### Cấu hình:
- **Framework:** Laravel 10
- **Server:** PHP built-in server
- **Port:** 8000
- **Host:** 127.0.0.1 (localhost)
- **Database:** MySQL

---

## 🎉 CHECKLIST CUỐI CÙNG

Trước khi test, đảm bảo:
- [ ] Terminal hiển thị "Server running"
- [ ] Không nhấn Ctrl+C trong terminal
- [ ] Browser đã refresh (F5)
- [ ] URL chính xác: http://127.0.0.1:8000
- [ ] Không có firewall chặn port 8000

---

## 📞 CẦN TRỢ GIÚP?

Nếu vẫn không kết nối được, cung cấp:
1. Screenshot terminal (có dòng "Server running" không?)
2. Screenshot browser (lỗi gì?)
3. Output của lệnh: `netstat -ano | findstr :8000`
4. Thử truy cập bằng Edge: `msedge http://127.0.0.1:8000`

---

**🚀 Server đã sẵn sàng tại:** http://127.0.0.1:8000

**💡 Tip:** Giữ terminal server mở, không đóng và không nhấn Ctrl+C!

**⏱️ Thời gian test:** 5 phút

**📚 Tài liệu:** Xem 5 files .md trong thư mục project
