-- =============================================
-- KIỂM TRA VÀ SỬA ĐÁP ÁN TRONG DATABASE
-- =============================================
-- Chạy trong phpMyAdmin → Tab SQL

-- 1. KIỂM TRA đáp án hiện tại
SELECT 
    MaCH,
    LEFT(NoiDung, 80) AS NoiDung_Short,
    DapAn,
    LENGTH(DapAn) AS Len,
    ASCII(DapAn) AS ASCII_Code,
    CASE 
        WHEN DapAn IN ('A', 'B', 'C', 'D') THEN '✅ OK'
        WHEN DapAn IN ('a', 'b', 'c', 'd') THEN '⚠️ Chữ thường'
        WHEN LENGTH(DapAn) > 1 THEN '❌ Có khoảng trắng'
        ELSE '❌ SAI'
    END AS Status
FROM CauHoi
LIMIT 20;

-- KỲ VỌNG: Status = '✅ OK'
-- Nếu có '⚠️ Chữ thường' hoặc '❌' → Chạy tiếp bước 2

-- =============================================

-- 2. SỬA đáp án (nếu cần)
-- Chuẩn hóa: TRIM + UPPERCASE
UPDATE CauHoi 
SET DapAn = TRIM(UPPER(DapAn))
WHERE DapAn IS NOT NULL;

-- =============================================

-- 3. KIỂM TRA lại sau khi sửa
SELECT 
    COUNT(*) AS TongSoCauHoi,
    SUM(CASE WHEN DapAn IN ('A', 'B', 'C', 'D') THEN 1 ELSE 0 END) AS CauHoi_OK,
    SUM(CASE WHEN DapAn NOT IN ('A', 'B', 'C', 'D') THEN 1 ELSE 0 END) AS CauHoi_SAI
FROM CauHoi;

-- KỲ VỌNG: CauHoi_SAI = 0

-- =============================================

-- 4. XEM bài làm gần nhất
SELECT 
    bl.MaBaiLam,
    bl.MaHS,
    hs.HoTen,
    bl.MaDe,
    dt.TenDe,
    bl.Diem,
    bl.TrangThai,
    bl.ThoiGianNop,
    JSON_LENGTH(bl.DSCauTraLoi) AS SoCauDaLam
FROM BaiLam bl
JOIN HocSinh hs ON bl.MaHS = hs.MaHS
JOIN DeThi dt ON bl.MaDe = dt.MaDe
WHERE bl.TrangThai = 'DaNop'
ORDER BY bl.ThoiGianNop DESC
LIMIT 5;

-- =============================================

-- 5. XEM chi tiết câu trả lời của bài làm gần nhất
SELECT 
    bl.MaBaiLam,
    JSON_PRETTY(bl.DSCauTraLoi) AS ChiTiet_CauTraLoi
FROM BaiLam bl
WHERE bl.TrangThai = 'DaNop'
ORDER BY bl.ThoiGianNop DESC
LIMIT 1;

-- KỲ VỌNG FORMAT:
-- [
--   {
--     "MaCH": "CH00000001",
--     "DapAnChon": "A",      ← Đáp án học sinh chọn
--     "DapAnDung": "A",      ← Đáp án đúng
--     "KetQua": "Dung"       ← Kết quả
--   },
--   {
--     "MaCH": "CH00000002",
--     "DapAnChon": "B",
--     "DapAnDung": "A",
--     "KetQua": "Sai"
--   }
-- ]

-- =============================================

-- 6. SO SÁNH đáp án học sinh chọn với đáp án đúng
-- (Để debug tại sao hiển thị sai)

-- Lấy MaBaiLam gần nhất
SET @maBaiLam = (
    SELECT MaBaiLam 
    FROM BaiLam 
    WHERE TrangThai = 'DaNop' 
    ORDER BY ThoiGianNop DESC 
    LIMIT 1
);

-- Phân tích từng câu
SELECT 
    JSON_EXTRACT(json_data, '$.MaCH') AS MaCH,
    JSON_UNQUOTE(JSON_EXTRACT(json_data, '$.DapAnChon')) AS DapAnChon,
    JSON_UNQUOTE(JSON_EXTRACT(json_data, '$.DapAnDung')) AS DapAnDung_JSON,
    ch.DapAn AS DapAnDung_DB,
    CASE 
        WHEN JSON_UNQUOTE(JSON_EXTRACT(json_data, '$.DapAnChon')) = ch.DapAn THEN '✅ ĐÚNG'
        ELSE '❌ SAI'
    END AS KetQua
FROM (
    SELECT 
        MaBaiLam,
        JSON_EXTRACT(DSCauTraLoi, CONCAT('$[', idx, ']')) AS json_data
    FROM BaiLam,
    JSON_TABLE(
        JSON_ARRAY(0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19),
        '$[*]' COLUMNS (idx INT PATH '$')
    ) AS numbers
    WHERE MaBaiLam = @maBaiLam
      AND JSON_EXTRACT(DSCauTraLoi, CONCAT('$[', idx, ']')) IS NOT NULL
) AS parsed
JOIN CauHoi ch ON JSON_UNQUOTE(JSON_EXTRACT(json_data, '$.MaCH')) = ch.MaCH;

-- Nếu MySQL < 8.0, dùng query đơn giản hơn:
-- SELECT 
--     MaBaiLam,
--     DSCauTraLoi
-- FROM BaiLam
-- WHERE MaBaiLam = @maBaiLam;
-- (Rồi phân tích thủ công JSON)

-- =============================================

-- 7. KIỂM TRA kết quả đã lưu
SELECT 
    kq.MaKQ,
    kq.MaBaiLam,
    kq.MaHS,
    hs.HoTen,
    kq.Diem,
    kq.SoCauDung,
    kq.SoCauSai,
    kq.SoCauKhongLam,
    (kq.SoCauDung + kq.SoCauSai + kq.SoCauKhongLam) AS TongCauHoi,
    kq.ThoiGianHoanThanh
FROM KetQua kq
JOIN HocSinh hs ON kq.MaHS = hs.MaHS
ORDER BY kq.ThoiGianHoanThanh DESC
LIMIT 5;

-- KỲ VỌNG: 
-- - Diem = (SoCauDung / TongCauHoi) * 10
-- - SoCauDung + SoCauSai + SoCauKhongLam = TongCauHoi của đề thi

-- =============================================

-- 8. NẾU VẪN SAI: Xóa bài làm cũ và làm lại
-- (Vì có thể bài cũ dùng format JSON cũ)

-- XÓA bài làm test (CẢNH BÁO: Chỉ xóa bài test!)
-- DELETE FROM BaiLam 
-- WHERE MaHS = 'HS99999999'  -- Thay bằng MaHS test của bạn
--   AND ThoiGianNop > NOW() - INTERVAL 1 HOUR;  -- Chỉ xóa bài trong 1 giờ gần nhất

-- Sau đó làm bài thi mới để test lại

-- =============================================
-- KẾT THÚC
-- =============================================

-- CHECKLIST:
-- [ ] Chạy bước 1: Kiểm tra đáp án → Có '✅ OK' không?
-- [ ] Nếu không: Chạy bước 2 để sửa
-- [ ] Chạy bước 3: Xác nhận CauHoi_SAI = 0
-- [ ] Chạy bước 4-5: Xem bài làm gần nhất
-- [ ] Chạy bước 7: Kiểm tra kết quả
-- [ ] Nếu OK → Test lại trên web
-- [ ] Nếu vẫn sai → Xóa bài cũ (bước 8) và làm bài mới
