-- =============================================
-- KIỂM TRA KẾT QUẢ TEST TRONG DATABASE
-- =============================================
-- Copy và chạy trong phpMyAdmin → Tab SQL

-- 1. Kiểm tra bài làm vừa lưu (auto-save)
SELECT 
    MaBaiLam,
    MaHS,
    MaDe,
    TrangThai,
    CASE 
        WHEN DSCauTraLoi IS NULL THEN '❌ NULL'
        WHEN DSCauTraLoi = '' THEN '❌ EMPTY'
        WHEN JSON_VALID(DSCauTraLoi) THEN '✅ VALID JSON'
        ELSE '❌ INVALID'
    END AS Status_DSCauTraLoi,
    JSON_LENGTH(DSCauTraLoi) AS SoCauDaLam,
    Diem,
    ThoiGianBatDau,
    ThoiGianNop,
    SoLanViPham
FROM BaiLam
ORDER BY updated_at DESC
LIMIT 3;

-- KỲ VỌNG:
-- - TrangThai = 'DangLam' (đang làm) hoặc 'DaNop' (đã nộp)
-- - Status_DSCauTraLoi = '✅ VALID JSON'
-- - SoCauDaLam > 0
-- - SoLanViPham = số lần chuyển tab

-- =============================================

-- 2. Kiểm tra kết quả (sau khi nộp bài)
SELECT 
    kq.MaKQ,
    kq.MaBaiLam,
    kq.MaHS,
    hs.HoTen,
    kq.MaDe,
    dt.TenDe,
    kq.Diem,
    kq.SoCauDung,
    kq.SoCauSai,
    kq.SoCauKhongLam,
    kq.ThoiGianHoanThanh
FROM KetQua kq
JOIN HocSinh hs ON kq.MaHS = hs.MaHS
JOIN DeThi dt ON kq.MaDe = dt.MaDe
ORDER BY kq.created_at DESC
LIMIT 3;

-- KỲ VỌNG:
-- - Có bản ghi mới với Diem > 0
-- - SoCauDung + SoCauSai + SoCauKhongLam = TongSoCauHoi

-- =============================================

-- 3. Xem chi tiết câu trả lời (JSON)
SELECT 
    MaBaiLam,
    JSON_PRETTY(DSCauTraLoi) AS ChiTietCauTraLoi
FROM BaiLam
WHERE TrangThai IN ('DangLam', 'DaNop')
ORDER BY updated_at DESC
LIMIT 1;

-- KỲ VỌNG FORMAT:
-- [
--   {
--     "MaCH": "CH00000001",
--     "TraLoi": "A"
--   },
--   {
--     "MaCH": "CH00000002",
--     "TraLoi": "B"
--   }
-- ]
