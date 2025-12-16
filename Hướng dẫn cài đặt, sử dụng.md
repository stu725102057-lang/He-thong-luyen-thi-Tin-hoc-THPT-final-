# HƯỚNG DẪN CÀI ĐẶT VÀ SỬ DỤNG HỆ THỐNG

## HỆ THỐNG LUYỆN THI THPT QUỐC GIA MÔN TIN HỌC

---

## I. HƯỚNG DẪN CÀI ĐẶT

### 1. Yêu cầu hệ thống

**Phần mềm cần thiết:**
- PHP >= 8.0
- MySQL >= 5.7
- Composer
- XAMPP (Windows) hoặc LAMP (Linux)

### 2. Các bước cài đặt

#### Bước 1: Cài đặt XAMPP
1. Tải XAMPP từ: https://www.apachefriends.org/
2. Cài đặt với các component: Apache, MySQL, PHP
3. Khởi động Apache và MySQL từ XAMPP Control Panel

#### Bước 2: Import Database
1. Mở phpMyAdmin: http://localhost/phpmyadmin
2. Tạo database mới tên: `thi_thpt_tin_hoc`
3. Click tab "Import"
4. Chọn file `Cơ sở dữ liệu.sql`
5. Click "Go" để import

#### Bước 3: Cấu hình hệ thống
1. Giải nén mã nguồn vào thư mục: `C:\xampp\htdocs\thi-thpt`
2. Mở file `.env` và cấu hình:
```
DB_DATABASE=thi_thpt_tin_hoc
DB_USERNAME=root
DB_PASSWORD=
```

#### Bước 4: Cài đặt thư viện
Mở Command Prompt tại thư mục dự án:
```
cd C:\xampp\htdocs\thi-thpt
composer install
php artisan key:generate
```

#### Bước 5: Khởi động hệ thống
```
php artisan serve
```
Truy cập: http://localhost:8000

---

## II. HƯỚNG DẪN SỬ DỤNG

### A. DÀNH CHO HỌC SINH

#### 1. Đăng nhập
- Tài khoản mẫu: `hocsinh` / `123456`
- Click "Đăng nhập" trên menu

#### 2. Làm bài thi
**Các bước:**
1. Chọn đề thi từ danh sách
2. Click "Bắt đầu làm bài"
3. Chọn đáp án A, B, C hoặc D
4. Chuyển câu bằng nút "Câu sau" / "Câu trước"
5. Click "Nộp bài" khi hoàn thành

**Lưu ý:**
- Hệ thống tự động lưu đáp án mỗi 30 giây
- Không thoát trình duyệt khi đang làm bài
- Theo dõi thời gian còn lại ở góc trên

#### 3. Xem kết quả
- Sau khi nộp bài → Hiển thị điểm số và số câu đúng
- Click "Xem chi tiết" để xem đáp án đúng/sai
- Vào menu "Lịch sử thi" để xem các bài đã làm

#### 4. Thống kê cá nhân
- Click menu "Thống kê cá nhân"
- Xem điểm trung bình, cao nhất, thấp nhất
- Xem biểu đồ tiến độ học tập

---

### B. DÀNH CHO GIÁO VIÊN

#### 1. Đăng nhập
- Tài khoản mẫu: `giaovien` / `123456`

#### 2. Quản lý câu hỏi

**Thêm câu hỏi mới:**
1. Vào "Ngân hàng câu hỏi"
2. Click "Thêm câu hỏi"
3. Điền:
   - Nội dung câu hỏi
   - 4 đáp án A, B, C, D
   - Chọn đáp án đúng
   - Chủ đề và độ khó
4. Click "Lưu"

**Import nhiều câu hỏi:**
1. Chuẩn bị file JSON theo mẫu:
```json
[
  {
    "NoiDung": "Câu hỏi?",
    "DapAnA": "Đáp án A",
    "DapAnB": "Đáp án B",
    "DapAnC": "Đáp án C",
    "DapAnD": "Đáp án D",
    "DapAn": "A",
    "DoKho": "Dễ",
    "ChuDe": "Tin học"
  }
]
```
2. Click "Import" → Chọn file → Upload

#### 3. Tạo đề thi

**Tạo đề ngẫu nhiên:**
1. Vào "Danh sách đề thi"
2. Click "Tạo đề thi" → "Tạo đề ngẫu nhiên"
3. Nhập:
   - Tên đề thi
   - Chủ đề
   - Số câu hỏi (VD: 15 câu)
   - Thời gian (VD: 30 phút)
   - Phân bổ độ khó (Dễ: 5, TB: 8, Khó: 2)
4. Click "Tạo đề"

**Tạo đề thủ công:**
1. Click "Tạo đề thi" → "Tạo đề thủ công"
2. Điền thông tin đề thi
3. Chọn từng câu hỏi muốn thêm
4. Click "Lưu đề thi"

#### 4. Sửa/Xóa đề thi
- **Sửa:** Click nút ✏️ → Chỉnh sửa → "Cập nhật"
- **Xem:** Click nút 👁️ → Xem chi tiết câu hỏi
- **Xóa:** Click nút 🗑️ → Xác nhận (chỉ xóa được đề chưa có HS làm)

#### 5. Thống kê lớp học
1. Vào menu "Thống kê lớp"
2. Xem:
   - Tổng số học sinh
   - Điểm trung bình lớp
   - Top 5 học sinh giỏi/yếu
   - Biểu đồ phân bố điểm
3. Xuất Excel nếu cần

---

### C. DÀNH CHO ADMIN

#### 1. Đăng nhập
- Tài khoản: `admin` / `admin123`

#### 2. Quản lý người dùng

**Thêm người dùng:**
1. Vào "Quản lý người dùng"
2. Click "Thêm người dùng"
3. Điền thông tin:
   - Username, Email, Password
   - Chọn Role (Học sinh/Giáo viên/Admin)
   - Thông tin cá nhân
4. Click "Tạo tài khoản"

**Khóa tài khoản:**
- Click nút 🔒 → Xác nhận

**Xóa tài khoản:**
- Click nút 🗑️ → Xác nhận

#### 3. Backup & Restore

**Tạo backup:**
1. Vào menu "Backup"
2. Click "Tạo bản sao lưu"
3. Đợi hoàn tất (30s - 2 phút)

**Khôi phục:**
1. Chọn file backup
2. Click "Khôi phục"
3. Xác nhận (⚠️ sẽ ghi đè dữ liệu hiện tại)

**Tải xuống:**
- Click nút ⬇️ bên cạnh file backup

#### 4. Dashboard
- Xem tổng quan hệ thống
- Số lượng user, đề thi, câu hỏi
- Biểu đồ thống kê

---

## III. CÂU HỎI THƯỜNG GẶP

### 1. Quên mật khẩu?
→ Liên hệ Admin để reset

### 2. Không kết nối được database?
→ Kiểm tra MySQL đã chạy chưa (XAMPP Control Panel)
→ Kiểm tra thông tin trong file `.env`

### 3. Lỗi "Class not found"?
→ Chạy lại: `composer install`

### 4. Không xóa được câu hỏi?
→ Câu hỏi đã dùng trong đề thi không xóa được
→ Xóa đề thi trước hoặc tạo câu hỏi mới thay thế

### 5. Điểm số tính như thế nào?
→ Điểm = (Số câu đúng / Tổng số câu) × 10
→ VD: 13/15 = 8.67 điểm

### 6. Học sinh có thể làm lại đề thi?
→ Có, mỗi lần làm tạo bài làm mới

### 7. Hệ thống có phát hiện gian lận?
→ Có: phát hiện thoát tab, copy/paste, thoát fullscreen

### 8. Export kết quả ra Excel?
→ Giáo viên: Vào "Thống kê lớp" → "Xuất Excel"

---

## IV. TÀI KHOẢN MẶC ĐỊNH

```
┌─────────────────────────────────────┐
│  ADMIN                              │
│  Username: admin                    │
│  Password: admin123                 │
├─────────────────────────────────────┤
│  GIÁO VIÊN                         │
│  Username: giaovien                 │
│  Password: 123456                   │
├─────────────────────────────────────┤
│  HỌC SINH                          │
│  Username: hocsinh                  │
│  Password: 123456                   │
└─────────────────────────────────────┘
```

**⚠️ LƯU Ý:** Đổi mật khẩu ngay sau khi đăng nhập lần đầu!

---

## V. XỬ LÝ LỖI NHANH

### Lỗi 1: Không vào được trang
```
✓ Kiểm tra MySQL đang chạy
✓ Kiểm tra file .env
✓ Chạy: php artisan serve
```

### Lỗi 2: "SQLSTATE[HY000] [2002]"
```
→ Sửa .env: DB_HOST=127.0.0.1
```

### Lỗi 3: "419 Page Expired"
```
→ Chạy: php artisan cache:clear
→ Chạy: php artisan config:clear
```

### Lỗi 4: Menu không hiển thị
```
→ Xóa cache trình duyệt (Ctrl + Shift + Del)
→ Refresh trang (Ctrl + F5)
```

### Lỗi 5: Thoát đăng nhập khi chuyển trang
```
→ Kiểm tra SESSION_DOMAIN trong .env
→ Chạy: php artisan config:cache
```

---

## VI. LIÊN HỆ HỖ TRỢ

**📧 Email:** support@example.com  
**📱 Hotline:** 0123-456-789  
**💬 Zalo:** 0123-456-789

**🕐 Thời gian hỗ trợ:**  
Thứ 2 - Thứ 6: 8:00 - 17:00  
Thứ 7 - CN: Theo lịch hẹn

---

**Chúc bạn sử dụng hệ thống hiệu quả!** 🎉

*Ngày cập nhật: 14/12/2025*  
*Phiên bản: 1.0.0*
