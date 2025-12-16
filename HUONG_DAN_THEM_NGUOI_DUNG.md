# 🎉 HƯỚNG DẪN QUẢN LÝ NGƯỜI DÙNG

## ✅ Đã hoàn thành

Chức năng **quản lý người dùng** đã được triển khai đầy đủ:
- ✅ Thêm người dùng mới
- ✅ Sửa thông tin người dùng
- ✅ Khóa/Mở khóa tài khoản
- ✅ Lọc theo vai trò
- ✅ Xem danh sách người dùng

## 🚀 Cách sử dụng trên giao diện Web

### Bước 1: Đăng nhập với tài khoản Admin
1. Mở trình duyệt: `http://localhost:8000`
2. Đăng nhập với tài khoản admin:
   - **Tên đăng nhập**: `admin`
   - **Mật khẩu**: `admin123`

### Bước 2: Vào màn hình Quản lý người dùng
1. Sau khi đăng nhập, trên thanh menu phía trên, click vào **"Quản lý người dùng"**
2. Bạn sẽ thấy danh sách tất cả người dùng hiện có

---

## 📝 THÊM NGƯỜI DÙNG MỚI

### Bước 3: Thêm người dùng mới
1. Click nút **"+ Thêm người dùng"** (màu xanh dương)
2. Một modal (cửa sổ popup) sẽ hiện ra
3. Điền thông tin:

#### Thông tin chung (bắt buộc):
- **Tên đăng nhập**: Tên để đăng nhập (duy nhất)
- **Email**: Địa chỉ email (duy nhất)
- **Mật khẩu**: Tối thiểu 6 ký tự
- **Vai trò**: Chọn một trong ba:
  - **Học sinh**: Người làm bài thi
  - **Giáo viên**: Người tạo đề thi và quản lý câu hỏi
  - **Quản trị viên**: Quản lý toàn bộ hệ thống

#### Nếu chọn vai trò "Học sinh":
- **Họ tên** (bắt buộc): VD: Nguyễn Văn A
- **Lớp** (tùy chọn): VD: 12A1
- **Trường** (tùy chọn): VD: THPT Nguyễn Huệ

#### Nếu chọn vai trò "Giáo viên":
- **Họ tên** (bắt buộc): VD: Trần Thị B
- **Số điện thoại** (tùy chọn): VD: 0912345678
- **Chuyên môn** (tùy chọn): VD: Tin học

#### Nếu chọn vai trò "Quản trị viên":
- Chỉ cần thông tin đăng nhập (không cần thông tin bổ sung)

4. Click nút **"✓ Tạo tài khoản"**

### Bước 4: Kiểm tra kết quả
- Nếu thành công: Hiện thông báo màu xanh "Tạo người dùng thành công!"
- Modal tự động đóng
- Danh sách người dùng tự động cập nhật
- Bạn sẽ thấy người dùng mới trong bảng

---

## ✏️ SỬA THÔNG TIN NGƯỜI DÙNG

### Bước 1: Mở modal sửa
1. Trong danh sách người dùng, tìm người dùng cần sửa
2. Click nút **✏️** (bút chì màu vàng) ở cột "Thao tác"
3. Modal "Sửa thông tin người dùng" sẽ hiện ra với dữ liệu hiện tại

### Bước 2: Chỉnh sửa thông tin
Bạn có thể sửa các thông tin sau:

#### Thông tin chung:
- **Email**: Có thể thay đổi (phải duy nhất)
- **Mật khẩu mới**: Để trống nếu không muốn đổi mật khẩu
- ⚠️ **Không thể thay đổi**: Tên đăng nhập và Vai trò

#### Nếu là học sinh:
- **Họ tên**: Cập nhật họ tên mới
- **Lớp**: VD: 12A1 → 12A2
- **Trường**: Cập nhật tên trường

#### Nếu là giáo viên:
- **Họ tên**: Cập nhật họ tên mới
- **Số điện thoại**: Cập nhật số điện thoại
- **Chuyên môn**: VD: Tin học → Tin học ứng dụng

#### Nếu là admin:
- Chỉ có thể sửa Email và Mật khẩu

### Bước 3: Lưu thay đổi
1. Click nút **"✓ Cập nhật"** (màu vàng)
2. Nếu thành công: Hiện thông báo "Cập nhật người dùng thành công!"
3. Modal tự động đóng và danh sách cập nhật

### Lưu ý quan trọng:
- ⚠️ **Không thể đổi vai trò**: Nếu muốn chuyển học sinh thành giáo viên, bạn phải tạo tài khoản mới
- 🔒 **Mật khẩu**: Để trống ô "Mật khẩu mới" nếu không muốn thay đổi mật khẩu
- ✉️ **Email phải duy nhất**: Không được trùng với người dùng khác

---

## 🎨 Các tính năng khác

### Lọc người dùng theo vai trò
- Dùng dropdown **"Lọc theo vai trò"** để xem:
  - Tất cả
  - Chỉ học sinh
  - Chỉ giáo viên
  - Chỉ quản trị viên

### Khóa/Mở khóa tài khoản
- Click icon **🔒** (ổ khóa màu xám) để khóa tài khoản
- Click icon **🔓** (mở khóa màu xanh) để kích hoạt lại tài khoản đã khóa
- Tài khoản bị khóa không thể đăng nhập vào hệ thống

### Sửa thông tin
- Click nút **✏️** (bút chì màu vàng) để mở modal sửa thông tin
- Có thể cập nhật email, mật khẩu, và thông tin cá nhân
- Không thể thay đổi tên đăng nhập và vai trò

---

## 📝 Ví dụ cụ thể

### Ví dụ 1: Sửa email và lớp của học sinh
```
Người dùng: Nguyễn Văn A (hocsinh)
Click ✏️ → Sửa:
- Email: nguyenvana@gmail.com → nguyenvana.new@gmail.com
- Lớp: 12A1 → 12A2
→ Click "Cập nhật"
```

### Ví dụ 2: Đổi mật khẩu giáo viên
```
Người dùng: Trần Thị B (giaovien)
Click ✏️ → Sửa:
- Mật khẩu mới: newpassword123
- Các trường khác: giữ nguyên
→ Click "Cập nhật"
```

### Ví dụ 3: Cập nhật thông tin giáo viên
```
Người dùng: Nguyễn Thị C (giaovien)
Click ✏️ → Sửa:
- Họ tên: Nguyễn Thị C (Cập nhật)
- Số điện thoại: 0987654321
- Chuyên môn: Tin học ứng dụng
→ Click "Cập nhật"
```

---

## 🔧 Test API bằng REST Client

Nếu bạn muốn test trực tiếp API (không qua giao diện):

1. Cài đặt extension **REST Client** trong VS Code
2. Mở file: `test-add-user.http`
3. Làm theo hướng dẫn trong file:
   - Bước 1: Login để lấy token
   - Bước 2: Copy token vào các request sau
   - Bước 3: Chạy các test case thêm/sửa người dùng

Có 20+ test cases bao gồm:
- Thêm người dùng (học sinh, giáo viên, admin)
- Sửa thông tin (email, mật khẩu, thông tin cá nhân)
- Test lỗi (email trùng, mật khẩu ngắn, đổi role...)

---

## 📝 Ví dụ cụ thể - THÊM NGƯỜI DÙNG

### Ví dụ 1: Thêm học sinh
```
Tên đăng nhập: hocsinh_nguyen_van_a
Email: nguyenvana@gmail.com
Mật khẩu: 123456
Vai trò: Học sinh
Họ tên: Nguyễn Văn A
Lớp: 12A1
Trường: THPT Lê Quý Đôn
```

### Ví dụ 2: Thêm giáo viên
```
Tên đăng nhập: gv_tran_thi_b
Email: tranthib@school.edu.vn
Mật khẩu: giaovien123
Vai trò: Giáo viên
Họ tên: Trần Thị B
Số điện thoại: 0987654321
Chuyên môn: Tin học
```

### Ví dụ 3: Thêm admin
```
Tên đăng nhập: admin_system
Email: admin@system.com
Mật khẩu: admin@2025
Vai trò: Quản trị viên
```

## ❌ Các lỗi thường gặp

### Lỗi: "Tên đăng nhập đã tồn tại"
- **Nguyên nhân**: Tên đăng nhập đã được sử dụng
- **Giải pháp**: Chọn tên đăng nhập khác

### Lỗi: "Email đã được sử dụng"
- **Nguyên nhân**: Email đã tồn tại trong hệ thống
- **Giải pháp**: Sử dụng email khác

### Lỗi: "Vui lòng nhập họ tên"
- **Nguyên nhân**: Bạn chọn vai trò "Học sinh" hoặc "Giáo viên" nhưng không điền họ tên
- **Giải pháp**: Điền đầy đủ họ tên vào ô "Họ tên"

### Lỗi: "Không thể thay đổi Role của người dùng"
- **Nguyên nhân**: Bạn đang cố thay đổi vai trò (VD: từ học sinh sang giáo viên)
- **Giải pháp**: Không thể thay đổi role. Tạo tài khoản mới với role mong muốn

### Lỗi khi sửa: "Email đã được sử dụng"
- **Nguyên nhân**: Email mới trùng với người dùng khác
- **Giải pháp**: Chọn email khác hoặc giữ nguyên email hiện tại

---

## 🔐 Bảo mật

- **Mật khẩu tự động mã hóa**: Sử dụng BCrypt (không lưu dạng plain text)
- **Chỉ Admin mới được quản lý người dùng**: API kiểm tra quyền trước khi xử lý
- **Validation đầy đủ**: Kiểm tra dữ liệu ở cả frontend và backend
- **Không đổi được Role**: Ngăn chặn việc thay đổi vai trò tùy tiện
- **Transaction safety**: Đảm bảo tính toàn vẹn dữ liệu khi cập nhật

---

## 📊 Cấu trúc dữ liệu

Khi tạo người dùng mới, hệ thống sẽ:
1. Tạo record trong bảng `TaiKhoan` (thông tin đăng nhập)
2. Tạo record trong bảng tương ứng:
   - `HocSinh` nếu role = hocsinh
   - `GiaoVien` nếu role = giaovien
   - `QuanTriVien` nếu role = admin
3. Tự động sinh ID duy nhất (MaTK, MaHS, MaGV, MaQTV)
4. Đặt trạng thái mặc định: "hoatdong" (tài khoản đang hoạt động)

## 🎯 Các API liên quan

| Method | Endpoint | Mô tả |
|--------|----------|-------|
| POST | `/api/users` | Tạo người dùng mới |
| GET | `/api/users` | Lấy danh sách người dùng |
| GET | `/api/users?Role=hocsinh` | Lọc theo role |
| PUT | `/api/users/{id}` | Cập nhật thông tin người dùng |
| PATCH | `/api/users/{id}/toggle` | Khóa/Mở khóa |

---

## ✨ Tính năng đã hoàn thành

✅ Modal form thêm người dùng với validation  
✅ Modal form sửa người dùng  
✅ Ẩn/hiện fields theo role  
✅ Gọi API tạo user  
✅ Gọi API cập nhật user  
✅ Hiển thị danh sách người dùng  
✅ Lọc theo vai trò  
✅ Khóa/Mở khóa tài khoản  
✅ Tự động reload sau khi thêm/sửa thành công  
✅ Thông báo lỗi chi tiết  
✅ Responsive design (Bootstrap 5)  
✅ Bảo vệ không cho đổi Role  
✅ Hỗ trợ cập nhật từng phần (partial update)  

---

## 🎬 Workflow hoàn chỉnh

### Thêm người dùng mới:
1. Click "Thêm người dùng" → Điền form → Click "Tạo tài khoản"
2. Hệ thống tạo record trong bảng `TaiKhoan` và bảng tương ứng (`HocSinh`, `GiaoVien`, hoặc `QuanTriVien`)
3. Tự động sinh ID duy nhất
4. Mã hóa mật khẩu BCrypt
5. Đặt trạng thái: "hoatdong"
6. Hiển thị thông báo thành công

### Sửa thông tin người dùng:
1. Click nút ✏️ bên cạnh người dùng → Modal hiện ra với dữ liệu hiện tại
2. Chỉnh sửa thông tin cần thiết (email, mật khẩu, thông tin cá nhân)
3. Click "Cập nhật"
4. Hệ thống cập nhật bảng `TaiKhoan` và bảng liên quan
5. Nếu có mật khẩu mới → Mã hóa BCrypt
6. Hiển thị thông báo thành công

### Khóa/Mở khóa:
1. Click nút 🔒 hoặc 🔓
2. Xác nhận thao tác
3. Hệ thống toggle trạng thái `TrangThai`
4. Cập nhật hiển thị

---

## 📸 Screenshot

**Nút "Thêm người dùng":**
![Nút thêm](https://via.placeholder.com/800x100?text=N%C3%BAt+Th%C3%AAm+Ng%C6%B0%E1%BB%9Di+D%C3%B9ng)

**Modal form thêm học sinh:**
![Form học sinh](https://via.placeholder.com/600x500?text=Form+Th%C3%AAm+H%E1%BB%8Dc+Sinh)

**Danh sách người dùng:**
![Danh sách](https://via.placeholder.com/800x300?text=Danh+S%C3%A1ch+Ng%C6%B0%E1%BB%9Di+D%C3%B9ng)

**Modal sửa người dùng:**
![Modal sửa](https://via.placeholder.com/600x500?text=Modal+S%E1%BB%ADa+Ng%C6%B0%E1%BB%9Di+D%C3%B9ng)

---

## 🎓 Tóm tắt

Bạn đã có thể:
1. ✅ **Thêm** người dùng mới (học sinh, giáo viên, admin)
2. ✅ **Sửa** thông tin người dùng (email, mật khẩu, thông tin cá nhân)
3. ✅ **Xem** danh sách người dùng
4. ✅ **Lọc** theo vai trò
5. ✅ **Khóa/Mở khóa** tài khoản

### Các trường hợp sử dụng phổ biến:
- 📧 **Đổi email**: Khi học sinh/giáo viên thay đổi email
- 🔑 **Reset mật khẩu**: Khi người dùng quên mật khẩu
- 📝 **Cập nhật thông tin**: Học sinh chuyển lớp, giáo viên đổi SĐT
- 🔒 **Khóa tài khoản**: Khi học sinh nghỉ học hoặc giáo viên nghỉ việc
- 🔓 **Mở khóa tài khoản**: Khi muốn kích hoạt lại tài khoản

Chúc bạn sử dụng hiệu quả! 🚀
