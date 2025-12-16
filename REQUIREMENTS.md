# DATABASE SCHEMA STANDARD (Trích xuất từ Báo cáo Nhóm 8)

## Cấu trúc các bảng

### 1. TaiKhoan (Bảng cha - Quản lý đăng nhập)
- **MaTK**: CHAR(10), PK, Not Null
- **TenDangNhap**: VARCHAR(100), Unique
- **MatKhau**: VARCHAR(255) (Đã mã hóa)
- **Role**: Phân quyền (HocSinh, GiaoVien, QuanTriVien)

### 2. HocSinh (Kế thừa từ TaiKhoan)
- **MaHS**: CHAR(10), PK
- **MaTK**: CHAR(10), FK trỏ về TaiKhoan(MaTK)
- **HoTen**: VARCHAR(255)

### 3. GiaoVien (Kế thừa từ TaiKhoan)
- **MaGV**: CHAR(10), PK
- **MaTK**: CHAR(10), FK trỏ về TaiKhoan(MaTK)

### 4. DeThi
- **MaDe**: CHAR(10), PK
- **ThoiGianLamBai**: INT (phút)
- **MaGV**: CHAR(10), FK trỏ về GiaoVien

### 5. CauHoi
- **MaCH**: CHAR(10), PK
- **NoiDung**: TEXT
- **MaNH**: CHAR(10), FK trỏ về NganHangCauHoi

### 6. BaiLam (Quan trọng)
- **MaBaiLam**: CHAR(10), PK
- **DSCauTraLoi**: TEXT hoặc JSON (Lưu danh sách câu trả lời)
- **TrangThai**: VARCHAR(50) (đang làm, đã nộp)
- **MaHS**: CHAR(10), FK
- **MaDe**: CHAR(10), FK

### 7. KetQua
- **MaKQ**: CHAR(10), PK
- **Diem**: FLOAT
- **MaHS**: FK
- **MaDe**: FK

### 8. DETHI_CAUHOI (Bảng trung gian)
- **MaDe**: FK
- **MaCH**: FK

---

## BUSINESS RULES (Quy tắc nghiệp vụ)

1. **Auto-save:** Hệ thống phải tự động lưu bài làm tạm thời mỗi 1 phút
2. **Auto-grading:** Ngay khi nộp bài, hệ thống phải tự động chấm điểm và lưu vào bảng KetQua
3. **Data Types:** Khóa chính bắt buộc là CHAR(10), không dùng ID tự tăng (INT)

---

## YÊU CẦU CHỨC NĂNG CHI TIẾT

### Chức năng Học sinh làm bài thi:
- Học sinh chọn đề thi từ danh sách
- Hệ thống tạo bài làm mới với trạng thái "đang làm"
- Hiển thị đề thi với các câu hỏi trắc nghiệm
- Tự động lưu câu trả lời mỗi 1 phút vào cột DSCauTraLoi
- Hiển thị đồng hồ đếm ngược thời gian làm bài

### Chức năng Nộp bài:
- Khi học sinh nhấn "Nộp bài" hoặc hết thời gian:
  1. Cập nhật trạng thái BaiLam thành "đã nộp"
  2. Tính điểm tự động dựa trên đáp án đúng
  3. Lưu điểm vào bảng KetQua
  4. Hiển thị kết quả ngay lập tức cho học sinh

### Chức năng Xem kết quả:
- Học sinh xem danh sách các bài thi đã làm
- Xem điểm số chi tiết
- Xem lại đáp án đã chọn và đáp án đúng

---

## QUY TẮC VALIDATION

1. **MaTK, MaHS, MaGV, MaDe, MaCH**: Phải đúng định dạng CHAR(10)
2. **TenDangNhap**: Phải unique, không được trùng
3. **MatKhau**: Phải mã hóa trước khi lưu (bcrypt/hash)
4. **Role**: Chỉ chấp nhận giá trị: HocSinh, GiaoVien, QuanTriVien
5. **TrangThai**: Chỉ chấp nhận: "đang làm", "đã nộp"
6. **Diem**: Phải là số thực (FLOAT), từ 0 đến 10

---

## API ENDPOINTS YÊU CẦU

### Authentication:
- POST `/api/login` - Đăng nhập
- POST `/api/logout` - Đăng xuất

### Học sinh:
- GET `/api/dethi` - Lấy danh sách đề thi
- POST `/api/bailam/batdau` - Bắt đầu làm bài
- PUT `/api/bailam/luu` - Lưu bài làm tạm thời (auto-save)
- POST `/api/bailam/nop` - Nộp bài (phải chấm điểm ngay)
- GET `/api/ketqua` - Xem kết quả các bài đã làm
- GET `/api/ketqua/{id}` - Xem chi tiết một kết quả

### Giáo viên:
- GET `/api/dethi` - Quản lý đề thi
- POST `/api/dethi` - Tạo đề thi mới
- PUT `/api/dethi/{id}` - Sửa đề thi
- DELETE `/api/dethi/{id}` - Xóa đề thi
- GET `/api/thongke` - Xem thống kê kết quả học sinh

---

## FRONTEND REQUIREMENTS

### Trang làm bài thi phải có:
1. Hiển thị thông tin đề thi (tên đề, số câu, thời gian)
2. Đồng hồ đếm ngược thời gian
3. Danh sách câu hỏi với radio button cho các đáp án
4. Nút "Lưu bài" (manual) và "Nộp bài"
5. Auto-save mỗi 60 giây bằng setInterval
6. Cảnh báo khi sắp hết thời gian (còn 5 phút)
7. Tự động nộp bài khi hết thời gian

### Trang kết quả phải có:
1. Danh sách các bài thi đã làm
2. Hiển thị điểm số, thời gian làm bài
3. Nút xem chi tiết (đáp án đã chọn vs đáp án đúng)
4. Phân loại: Giỏi (8-10), Khá (6.5-8), Trung bình (5-6.5), Yếu (<5)

---

## STORED PROCEDURES (Nếu sử dụng)

```sql
-- Procedure nộp bài (phải chấm điểm tự động)
DELIMITER $$
CREATE PROCEDURE sp_HocSinh_NopBai(
    IN p_MaBaiLam CHAR(10)
)
BEGIN
    DECLARE v_Diem FLOAT;
    DECLARE v_MaHS CHAR(10);
    DECLARE v_MaDe CHAR(10);
    
    -- Lấy thông tin bài làm
    SELECT MaHS, MaDe INTO v_MaHS, v_MaDe
    FROM BaiLam
    WHERE MaBaiLam = p_MaBaiLam;
    
    -- Tính điểm (logic chấm điểm)
    -- ... (so sánh DSCauTraLoi với đáp án đúng)
    
    -- Cập nhật trạng thái
    UPDATE BaiLam 
    SET TrangThai = 'đã nộp'
    WHERE MaBaiLam = p_MaBaiLam;
    
    -- Lưu kết quả
    INSERT INTO KetQua (MaKQ, Diem, MaHS, MaDe)
    VALUES (UUID(), v_Diem, v_MaHS, v_MaDe);
END$$
DELIMITER ;
```

---

## CHECKLIST KIỂM TRA

### Database:
- [ ] Tất cả khóa chính là CHAR(10), không phải INT/BIGINT
- [ ] Bảng BaiLam có cột DSCauTraLoi kiểu TEXT/JSON
- [ ] Bảng KetQua có cột Diem kiểu FLOAT
- [ ] Foreign key constraints đã được thiết lập đúng

### Backend:
- [ ] API nộp bài có chấm điểm tự động và lưu vào KetQua
- [ ] API lưu bài tạm thời hoạt động đúng
- [ ] Middleware xác thực hoạt động
- [ ] Validation đầu vào đầy đủ

### Frontend:
- [ ] Auto-save mỗi 60 giây đã được implement
- [ ] Đồng hồ đếm ngược hoạt động
- [ ] Tự động nộp bài khi hết giờ
- [ ] Hiển thị kết quả ngay sau khi nộp

### Business Logic:
- [ ] Học sinh không thể làm lại bài đã nộp
- [ ] Điểm được tính đúng theo số câu đúng
- [ ] Trạng thái bài làm được cập nhật chính xác
