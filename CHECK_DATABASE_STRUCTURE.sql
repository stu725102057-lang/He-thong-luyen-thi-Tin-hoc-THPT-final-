-- ============================================
-- SCRIPT KIỂM TRA CẤU TRÚC DATABASE
-- Mục đích: So sánh database hiện tại với yêu cầu báo cáo
-- Ngày: 14/12/2025
-- ============================================

-- Lưu ý: Copy và chạy từng câu lệnh trong phpMyAdmin (tab SQL)
-- Hoặc chạy toàn bộ script

USE trac_nghiem_thpt; -- Thay bằng tên database của bạn

-- ============================================
-- 1. KIỂM TRA CẤU TRÚC CÁC BẢNG CHÍNH
-- ============================================

-- 1.1. Bảng TaiKhoan
DESCRIBE TaiKhoan;
-- Kỳ vọng: MaTK CHAR(10), TenDangNhap VARCHAR, MatKhau VARCHAR(255), Role ENUM

-- 1.2. Bảng HocSinh
DESCRIBE HocSinh;
-- Kỳ vọng: MaHS CHAR(10), MaTK CHAR(10), HoTen VARCHAR(100)

-- 1.3. Bảng GiaoVien
DESCRIBE GiaoVien;
-- Kỳ vọng: MaGV CHAR(10), MaTK CHAR(10)

-- 1.4. Bảng DeThi
DESCRIBE DeThi;
-- Kỳ vọng: MaDe CHAR(10), ThoiGianLamBai INT, MaGV CHAR(10)

-- 1.5. Bảng CauHoi
DESCRIBE CauHoi;
-- Kỳ vọng: MaCH CHAR(10), NoiDung TEXT, DapAn CHAR(1), MaNH CHAR(10)

-- 1.6. Bảng BaiLam (QUAN TRỌNG)
DESCRIBE BaiLam;
-- Kỳ vọng: 
--   MaBaiLam CHAR(10)
--   DSCauTraLoi JSON (hoặc TEXT)
--   TrangThai ENUM('DangLam', 'DaNop', 'ChamDiem')
--   MaHS CHAR(10)
--   MaDe CHAR(10)

-- 1.7. Bảng KetQua (QUAN TRỌNG)
DESCRIBE KetQua;
-- Kỳ vọng: 
--   MaKQ CHAR(10)
--   Diem FLOAT (hoặc DECIMAL)
--   MaHS CHAR(10)
--   MaDe CHAR(10)
--   MaBaiLam CHAR(10)

-- 1.8. Bảng DETHI_CAUHOI (Bảng trung gian)
DESCRIBE DETHI_CAUHOI;
-- Kỳ vọng: MaDe CHAR(10), MaCH CHAR(10), PRIMARY KEY(MaDe, MaCH)

-- ============================================
-- 2. KIỂM TRA FOREIGN KEY CONSTRAINTS
-- ============================================

-- 2.1. Foreign keys của bảng HocSinh
SELECT 
    CONSTRAINT_NAME,
    TABLE_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'HocSinh'
  AND REFERENCED_TABLE_NAME IS NOT NULL;
-- Kỳ vọng: MaTK trỏ về TaiKhoan(MaTK)

-- 2.2. Foreign keys của bảng BaiLam
SELECT 
    CONSTRAINT_NAME,
    TABLE_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'BaiLam'
  AND REFERENCED_TABLE_NAME IS NOT NULL;
-- Kỳ vọng: MaHS trỏ về HocSinh(MaHS), MaDe trỏ về DeThi(MaDe)

-- 2.3. Foreign keys của bảng KetQua
SELECT 
    CONSTRAINT_NAME,
    TABLE_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'KetQua'
  AND REFERENCED_TABLE_NAME IS NOT NULL;
-- Kỳ vọng: MaHS trỏ về HocSinh, MaDe trỏ về DeThi, MaBaiLam trỏ về BaiLam

-- ============================================
-- 3. KIỂM TRA KIỂU DỮ LIỆU CỤ THỂ
-- ============================================

-- 3.1. Kiểm tra TẤT CẢ các khóa chính có phải CHAR(10) không
SELECT 
    TABLE_NAME,
    COLUMN_NAME,
    COLUMN_TYPE,
    COLUMN_KEY
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND COLUMN_KEY = 'PRI'
  AND TABLE_NAME IN ('TaiKhoan', 'HocSinh', 'GiaoVien', 'DeThi', 'CauHoi', 'BaiLam', 'KetQua')
ORDER BY TABLE_NAME;
-- Kỳ vọng: Tất cả phải là CHAR(10), trừ các bảng phụ (Loi, SaoLuu, ThoiGian)

-- 3.2. Kiểm tra cột DSCauTraLoi trong BaiLam
SELECT 
    COLUMN_NAME,
    COLUMN_TYPE,
    IS_NULLABLE
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'BaiLam'
  AND COLUMN_NAME = 'DSCauTraLoi';
-- Kỳ vọng: Kiểu JSON hoặc TEXT, IS_NULLABLE = YES

-- 3.3. Kiểm tra cột Diem trong KetQua
SELECT 
    COLUMN_NAME,
    COLUMN_TYPE,
    IS_NULLABLE
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'KetQua'
  AND COLUMN_NAME = 'Diem';
-- Kỳ vọng: Kiểu FLOAT hoặc DECIMAL, IS_NULLABLE = NO hoặc YES

-- 3.4. Kiểm tra cột TrangThai trong BaiLam
SELECT 
    COLUMN_NAME,
    COLUMN_TYPE,
    IS_NULLABLE
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'BaiLam'
  AND COLUMN_NAME = 'TrangThai';
-- Kỳ vọng: ENUM('DangLam', 'DaNop', 'ChamDiem')

-- ============================================
-- 4. KIỂM TRA DỮ LIỆU MẪU (Nếu có)
-- ============================================

-- 4.1. Đếm số bản ghi trong các bảng chính
SELECT 'TaiKhoan' AS TableName, COUNT(*) AS RowCount FROM TaiKhoan
UNION ALL
SELECT 'HocSinh', COUNT(*) FROM HocSinh
UNION ALL
SELECT 'GiaoVien', COUNT(*) FROM GiaoVien
UNION ALL
SELECT 'DeThi', COUNT(*) FROM DeThi
UNION ALL
SELECT 'CauHoi', COUNT(*) FROM CauHoi
UNION ALL
SELECT 'BaiLam', COUNT(*) FROM BaiLam
UNION ALL
SELECT 'KetQua', COUNT(*) FROM KetQua;

-- 4.2. Kiểm tra BaiLam có dữ liệu mẫu
SELECT 
    MaBaiLam,
    MaHS,
    MaDe,
    TrangThai,
    CASE 
        WHEN DSCauTraLoi IS NULL THEN 'NULL'
        WHEN DSCauTraLoi = '' THEN 'EMPTY'
        WHEN JSON_VALID(DSCauTraLoi) THEN 'VALID JSON'
        ELSE 'INVALID'
    END AS DSCauTraLoi_Status,
    Diem,
    ThoiGianBatDau,
    ThoiGianNop
FROM BaiLam
LIMIT 5;

-- 4.3. Kiểm tra KetQua có dữ liệu mẫu
SELECT 
    MaKQ,
    MaHS,
    MaDe,
    MaBaiLam,
    Diem,
    SoCauDung,
    SoCauSai,
    SoCauKhongLam,
    ThoiGianHoanThanh
FROM KetQua
LIMIT 5;

-- ============================================
-- 5. KIỂM TRA TÍNH TOÀN VẸN DỮ LIỆU
-- ============================================

-- 5.1. Kiểm tra BaiLam có KetQua tương ứng không (với TrangThai = 'DaNop')
SELECT 
    bl.MaBaiLam,
    bl.TrangThai,
    CASE 
        WHEN kq.MaKQ IS NOT NULL THEN 'Có KetQua'
        ELSE 'Thiếu KetQua'
    END AS KetQua_Status
FROM BaiLam bl
LEFT JOIN KetQua kq ON bl.MaBaiLam = kq.MaBaiLam
WHERE bl.TrangThai = 'DaNop';
-- Kỳ vọng: Tất cả bài làm đã nộp phải có KetQua

-- 5.2. Kiểm tra HocSinh có TaiKhoan tương ứng không
SELECT 
    hs.MaHS,
    hs.HoTen,
    CASE 
        WHEN tk.MaTK IS NOT NULL THEN 'Có TaiKhoan'
        ELSE 'Thiếu TaiKhoan'
    END AS TaiKhoan_Status
FROM HocSinh hs
LEFT JOIN TaiKhoan tk ON hs.MaTK = tk.MaTK;
-- Kỳ vọng: Tất cả học sinh phải có tài khoản

-- 5.3. Kiểm tra DeThi có câu hỏi không
SELECT 
    dt.MaDe,
    dt.TenDe,
    dt.SoLuongCauHoi AS SoLuongCauHoi_DeThi,
    COUNT(dc.MaCH) AS SoLuongCauHoi_ThucTe,
    CASE 
        WHEN dt.SoLuongCauHoi = COUNT(dc.MaCH) THEN 'Khớp'
        ELSE 'Không khớp'
    END AS Status
FROM DeThi dt
LEFT JOIN DETHI_CAUHOI dc ON dt.MaDe = dc.MaDe
GROUP BY dt.MaDe, dt.TenDe, dt.SoLuongCauHoi;
-- Kỳ vọng: SoLuongCauHoi phải khớp với số câu hỏi thực tế

-- ============================================
-- 6. EXPORT STRUCTURE (Chỉ cấu trúc, không có dữ liệu)
-- ============================================

-- Lưu ý: Không thể chạy lệnh này trong phpMyAdmin
-- Bạn cần dùng giao diện Export hoặc mysqldump

-- Lệnh mysqldump (chạy trong terminal/cmd):
/*
mysqldump -u root -p --no-data --skip-add-drop-table trac_nghiem_thpt \
  TaiKhoan HocSinh GiaoVien QuanTriVien DeThi CauHoi NganHangCauHoi \
  BaiLam KetQua DETHI_CAUHOI > CURRENT_DB_STRUCTURE.sql
*/

-- ============================================
-- 7. KẾT QUẢ ĐÁNH GIÁ
-- ============================================

-- Sau khi chạy các câu lệnh trên, hãy kiểm tra:

-- ✅ Tất cả khóa chính là CHAR(10)
-- ✅ BaiLam.DSCauTraLoi là JSON hoặc TEXT
-- ✅ KetQua.Diem là FLOAT
-- ✅ Foreign key constraints đầy đủ
-- ✅ BaiLam đã nộp đều có KetQua tương ứng
-- ✅ HocSinh đều có TaiKhoan

-- Nếu tất cả ✅ → Database khớp 100% với báo cáo
-- Nếu có ❌ → Xem phần 8 để sửa

-- ============================================
-- 8. CÂU LỆNH SỬA LỖI (Nếu cần)
-- ============================================

-- 8.1. Nếu DSCauTraLoi không phải JSON:
-- ALTER TABLE BaiLam MODIFY COLUMN DSCauTraLoi JSON;

-- 8.2. Nếu Diem không phải FLOAT:
-- ALTER TABLE KetQua MODIFY COLUMN Diem FLOAT(8,2);
-- ALTER TABLE BaiLam MODIFY COLUMN Diem FLOAT(8,2) NULL;

-- 8.3. Nếu TrangThai sai định dạng:
-- ALTER TABLE BaiLam MODIFY COLUMN TrangThai ENUM('DangLam', 'DaNop', 'ChamDiem') DEFAULT 'DangLam';

-- 8.4. Nếu thiếu Foreign Key:
-- ALTER TABLE BaiLam ADD CONSTRAINT fk_bailam_hocsinh 
--   FOREIGN KEY (MaHS) REFERENCES HocSinh(MaHS) ON DELETE CASCADE;
-- ALTER TABLE BaiLam ADD CONSTRAINT fk_bailam_dethi 
--   FOREIGN KEY (MaDe) REFERENCES DeThi(MaDe) ON DELETE CASCADE;
-- ALTER TABLE KetQua ADD CONSTRAINT fk_ketqua_bailam 
--   FOREIGN KEY (MaBaiLam) REFERENCES BaiLam(MaBaiLam) ON DELETE SET NULL;

-- ============================================
-- 9. TEST CUỐI CÙNG: INSERT DỮ LIỆU THỬ
-- ============================================

-- 9.1. Test insert BaiLam với DSCauTraLoi JSON
-- INSERT INTO BaiLam (MaBaiLam, DSCauTraLoi, TrangThai, ThoiGianBatDau, MaHS, MaDe)
-- VALUES (
--     'BL99999999',
--     JSON_ARRAY(
--         JSON_OBJECT('MaCH', 'CH00000001', 'TraLoi', 'A'),
--         JSON_OBJECT('MaCH', 'CH00000002', 'TraLoi', 'B')
--     ),
--     'DangLam',
--     NOW(),
--     'HS00000001', -- Thay bằng MaHS thực tế
--     'DE00000001'  -- Thay bằng MaDe thực tế
-- );

-- 9.2. Kiểm tra JSON vừa insert
-- SELECT MaBaiLam, DSCauTraLoi FROM BaiLam WHERE MaBaiLam = 'BL99999999';

-- 9.3. Xóa dữ liệu test
-- DELETE FROM BaiLam WHERE MaBaiLam = 'BL99999999';

-- ============================================
-- HOÀN TẤT!
-- ============================================

-- Nếu tất cả các câu lệnh trên chạy thành công và không có lỗi:
-- ✅ Database của bạn đã khớp 100% với báo cáo
-- ✅ Hệ thống sẵn sàng cho production

-- Hãy lưu kết quả các câu lệnh trên vào file VALIDATION_RESULTS.txt
-- và so sánh với file REQUIREMENTS.md
