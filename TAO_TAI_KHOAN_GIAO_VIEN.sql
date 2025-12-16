-- Tạo tài khoản giáo viên với GiaoVien record
-- Password: 123456 (đã hash bằng bcrypt)

-- 1. Thêm TaiKhoan giáo viên
INSERT INTO `taikhoan` (`MaTK`, `TenDangNhap`, `Email`, `MatKhau`, `Role`, `TrangThai`, `created_at`, `updated_at`) 
VALUES 
('TK00000002', 'giaovien', 'giaovien@thpt.edu.vn', '$2y$12$LLl3H8dZ7JEKzP8qQF7rQeXJKZMvB9wYvZK9xKxMZVQWJxVqXqYxC', 'giaovien', 1, NOW(), NOW());

-- 2. Thêm GiaoVien record
INSERT INTO `giaovien` (`MaGV`, `MaTK`, `HoTen`, `SoDienThoai`, `DiaChi`, `NgaySinh`, `GioiTinh`, `ChuyenMon`, `created_at`, `updated_at`) 
VALUES 
('GV00000001', 'TK00000002', 'Nguyễn Văn Giáo Viên', '0987654321', 'Hà Nội', '1985-05-15', 'Nam', 'Tin học', NOW(), NOW());

-- Kiểm tra
SELECT tk.MaTK, tk.TenDangNhap, tk.Email, tk.Role, gv.MaGV, gv.HoTen 
FROM taikhoan tk 
LEFT JOIN giaovien gv ON tk.MaTK = gv.MaTK 
WHERE tk.Role = 'giaovien';
