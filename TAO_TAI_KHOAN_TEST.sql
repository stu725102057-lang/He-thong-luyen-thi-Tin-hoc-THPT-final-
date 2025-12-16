-- =============================================
-- TẠO TÀI KHOẢN TEST ĐỂ ĐĂNG NHẬP
-- =============================================
-- Copy và chạy trong phpMyAdmin → Tab SQL

-- 1. Tạo tài khoản (Mật khẩu: password)
INSERT INTO TaiKhoan (MaTK, TenDangNhap, MatKhau, Email, Role, TrangThai)
VALUES ('TK99999999', 'hocsinh_test', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'hstest@test.com', 'hocsinh', 1);

-- 2. Tạo học sinh
INSERT INTO HocSinh (MaHS, MaTK, HoTen, Lop, Truong)
VALUES ('HS99999999', 'TK99999999', 'Học Sinh Test', '12A1', 'THPT Test');

-- =============================================
-- ĐĂNG NHẬP VỚI:
-- Username: hocsinh_test
-- Password: password
-- =============================================
