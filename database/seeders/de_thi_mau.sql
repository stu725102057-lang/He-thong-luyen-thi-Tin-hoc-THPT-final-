-- Đề thi mẫu cho hệ thống
-- Thêm 3 đề thi mẫu về Tin học THPT

INSERT INTO `DeThi` (`MaDe`, `TenDe`, `MoTa`, `ThoiGianLamBai`, `SoCau`, `TrangThai`, `MaGV`, `ChuDe`, `created_at`, `updated_at`) VALUES
('DE_MAU_01', 'Đề thi mẫu - Tin học cơ bản', 'Đề thi mẫu về các kiến thức cơ bản trong tin học THPT', 45, 10, 1, 'GV001', 'Tin học cơ bản', NOW(), NOW()),
('DE_MAU_02', 'Đề thi mẫu - Lập trình Pascal', 'Đề thi mẫu về ngôn ngữ lập trình Pascal', 60, 15, 1, 'GV001', 'Lập trình Pascal', NOW(), NOW()),
('DE_MAU_03', 'Đề thi mẫu - Cấu trúc dữ liệu', 'Đề thi mẫu về cấu trúc dữ liệu và giải thuật', 60, 12, 1, 'GV001', 'Cấu trúc dữ liệu', NOW(), NOW());

-- Link câu hỏi cho đề mẫu 1 (sử dụng câu hỏi hiện có)
INSERT INTO `DETHI_CAUHOI` (`MaDe`, `MaCH`) 
SELECT 'DE_MAU_01', `MaCH` FROM `CauHoi` LIMIT 10;

-- Link câu hỏi cho đề mẫu 2
INSERT INTO `DETHI_CAUHOI` (`MaDe`, `MaCH`) 
SELECT 'DE_MAU_02', `MaCH` FROM `CauHoi` LIMIT 10, 15;

-- Link câu hỏi cho đề mẫu 3
INSERT INTO `DETHI_CAUHOI` (`MaDe`, `MaCH`) 
SELECT 'DE_MAU_03', `MaCH` FROM `CauHoi` LIMIT 25, 12;
