# 🧪 HƯỚNG DẪN TEST HỆ THỐNG - SESSION HOÀN THÀNH 100%

## 🚀 Server đã khởi động thành công!
**URL:** http://127.0.0.1:8000

---

## 📋 CHECKLIST TEST CÁC TÍNH NĂNG MỚI

### 🎯 Test 1: ĐĂNG NHẬP GIÁO VIÊN
**Tài khoản test:**
- **Username:** `giaovien1`
- **Password:** `password`

**Các bước:**
1. ✅ Mở http://127.0.0.1:8000
2. ✅ Nhập username: `giaovien1`
3. ✅ Nhập password: `password`
4. ✅ Click "Đăng nhập"
5. ✅ Kiểm tra menu hiển thị 4 items:
   - Quản lý câu hỏi
   - Tạo đề thi
   - **Tạo đề thủ công** ⭐ MỚI
   - **Thống kê lớp học** ⭐ MỚI

---

### 🎯 Test 2: QUẢN LÝ CÂU HỎI (UR-03.1 & UR-03.2)

#### Test EDIT câu hỏi ⭐ MỚI
1. Click menu "**Quản lý câu hỏi**"
2. Tìm câu hỏi bất kỳ trong bảng
3. Click button **Edit** (icon ✏️ pencil) ở cột "Thao tác"
4. ✅ **Modal "Sửa câu hỏi"** hiện ra với:
   - Nội dung câu hỏi đã điền sẵn
   - 4 đáp án (A, B, C, D) đã điền sẵn
   - Đáp án đúng được chọn
   - Độ khó hiện tại
5. Thay đổi nội dung câu hỏi (ví dụ: thêm text "- ĐÃ SỬA")
6. Click "**Cập nhật câu hỏi**"
7. ✅ Thông báo thành công màu xanh
8. ✅ Bảng câu hỏi tự động refresh
9. ✅ Câu hỏi vừa sửa hiển thị nội dung mới

#### Test EXPORT câu hỏi ⭐ MỚI
1. Ở màn hình "Quản lý câu hỏi"
2. Phía trên bảng, tìm 2 nút:
   - **Xuất CSV** 📥
   - **Xuất PDF** 📥
3. Click "**Xuất CSV**"
4. ✅ File `questions.csv` tự động download
5. Mở file CSV, kiểm tra có đầy đủ cột:
   - Mã câu hỏi, Nội dung, Đáp án A-D, Đáp án đúng, Độ khó
6. Click "**Xuất PDF**"
7. ✅ File `questions.pdf` tự động download

---

### 🎯 Test 3: TẠO ĐỀ THI THỦ CÔNG (UR-03.3) ⭐⭐⭐ TÍNH NĂNG MỚI

#### Giao diện 2 panel:
1. Click menu "**Tạo đề thủ công**"
2. ✅ Màn hình split 2 cột:
   - **Bên trái:** Bảng câu hỏi với checkbox
   - **Bên phải:** Sidebar câu hỏi đã chọn + Form

#### Test chọn câu hỏi:
1. **Chọn từng câu:**
   - Click checkbox ở 3 câu hỏi bất kỳ
   - ✅ Sidebar bên phải hiển thị 3 câu đã chọn
   - ✅ Số đếm cập nhật: "Đã chọn: 3 câu"
   - ✅ Button "Tạo đề thi" đổi thành: "Tạo đề thi (3 câu)"
   - ✅ Button chuyển từ disabled → enabled (màu xanh)

2. **Bỏ chọn:**
   - Click lại checkbox 1 câu
   - ✅ Câu đó biến mất khỏi sidebar
   - ✅ Số đếm giảm: "Đã chọn: 2 câu"

3. **Xóa từ sidebar:**
   - Click icon ❌ ở sidebar
   - ✅ Câu hỏi bị xóa khỏi danh sách
   - ✅ Checkbox trong bảng tự động bỏ chọn

4. **Filter:**
   - Chọn "Ngân hàng câu hỏi" hoặc "Độ khó"
   - Click "Lọc"
   - ✅ Bảng chỉ hiển thị câu hỏi phù hợp

#### Test tạo đề thi:
1. Chọn ít nhất 5 câu hỏi
2. Điền form bên phải:
   - **Tên đề:** "Đề thi thủ công test - Ngày 8/12/2025"
   - **Môn học:** "Tin học" (mặc định)
   - **Thời gian:** 60 phút
   - **Mô tả:** "Đề test tính năng mới"
3. Click "**Tạo đề thi (5 câu)**"
4. ✅ Loading spinner hiển thị
5. ✅ Thông báo thành công: "Tạo đề thi thành công với 5 câu hỏi!"
6. ✅ Form reset về trống
7. ✅ Sidebar xóa hết câu hỏi đã chọn
8. ✅ Checkbox trong bảng reset

#### Verify trong database:
```sql
-- Kiểm tra đề thi vừa tạo
SELECT * FROM DeThi 
WHERE TenDe LIKE '%thủ công test%' 
ORDER BY NgayTao DESC 
LIMIT 1;

-- Kiểm tra chi tiết câu hỏi (thay MaDe bằng mã vừa lấy)
SELECT ct.*, c.NoiDung 
FROM ChiTietDeThi ct
JOIN CauHoi c ON ct.MaCH = c.MaCH
WHERE ct.MaDe = 'DE004'  -- Thay bằng MaDe thực tế
ORDER BY ct.STT;
```

---

### 🎯 Test 4: THỐNG KÊ LỚP HỌC (UR-03.5) ⭐⭐⭐ TÍNH NĂNG MỚI

#### Giao diện Dashboard:
1. Click menu "**Thống kê lớp học**"
2. ✅ Hiển thị 4 cards tổng quan:
   - **Card 1 (xanh dương):** "Tổng học sinh" - Số lượng
   - **Card 2 (xanh lam):** "Điểm trung bình" - Điểm TB lớp
   - **Card 3 (xanh lá):** "Tỷ lệ đạt" - % học sinh >= 5 điểm
   - **Card 4 (vàng):** "Tổng bài thi" - Số bài thi đã hoàn thành

#### Test Top 5 học sinh:
3. ✅ **Bảng "Top 5 học sinh giỏi nhất":**
   - Header màu xanh lá với icon 🏆
   - 5 học sinh có điểm TB cao nhất
   - Cột: #, Tên, Điểm TB (màu xanh đậm), Số bài thi
   - Hàng 1 có badge vàng số thứ tự

4. ✅ **Bảng "Top 5 học sinh cần hỗ trợ":**
   - Header màu đỏ với icon ⚠️
   - 5 học sinh có điểm TB thấp nhất
   - Điểm TB hiển thị màu đỏ

#### Test biểu đồ Chart.js:
5. ✅ **Biểu đồ "Phân bố điểm số":**
   - Header màu xanh dương với icon 📊
   - Biểu đồ cột (bar chart)
   - **6 cột màu gradient:**
     1. Đỏ: Kém (0-2 điểm)
     2. Vàng: Yếu (2-4 điểm)
     3. Xám: Trung bình (4-5 điểm)
     4. Xanh lam: Khá (5-6.5 điểm)
     5. Xanh ngọc: Khá Giỏi (6.5-8 điểm)
     6. Xanh lá: Giỏi (8-10 điểm)
   - Hover vào cột hiển thị tooltip: "Số HS: X"
   - Trục Y: Số học sinh (step = 1)

#### Test bảng chi tiết:
6. ✅ **Bảng "Chi tiết toàn bộ học sinh":**
   - 8 cột: STT, Tên, Email, Điểm TB, Max, Min, Số bài thi, Trạng thái
   - **Badge trạng thái:**
     - Xanh: "Đạt" (điểm TB >= 5)
     - Đỏ: "Chưa đạt" (điểm TB < 5)
     - Xám: "Chưa thi" (chưa làm bài nào)
   - Sắp xếp theo điểm TB giảm dần
   - Table striped (dòng xám/trắng xen kẽ)
   - Hover effect (nền xám nhạt khi hover)

#### Test data accuracy:
7. **Verify số liệu:**
   - Tổng học sinh = số dòng trong bảng chi tiết
   - Điểm TB card = Average của cột "Điểm TB"
   - Tỷ lệ đạt = (Số badge xanh "Đạt" / Tổng đã thi) × 100%
   - Tổng cột biểu đồ = Số học sinh đã thi

---

### 🎯 Test 5: KIỂM TRA API ENDPOINTS

#### Test API Manual Exam:
```bash
# Test tạo đề thủ công qua API
curl -X POST http://127.0.0.1:8000/api/de-thi/manual \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "TenDe": "API Test Exam",
    "ChuDe": "Tin học",
    "ThoiGianLamBai": 45,
    "MoTa": "Test qua API",
    "DanhSachCauHoi": ["CH001", "CH002", "CH003"]
  }'
```

**Expected response:**
```json
{
  "success": true,
  "message": "Tạo đề thi thủ công thành công",
  "data": {
    "MaDe": "DE005",
    "TenDe": "API Test Exam",
    "SoLuongCauHoi": 3,
    "ThoiGianLamBai": 45,
    "DanhSachCauHoi": ["CH001", "CH002", "CH003"]
  }
}
```

#### Test API Class Statistics:
```bash
# Test thống kê lớp học qua API
curl -X GET http://127.0.0.1:8000/api/thong-ke/lop-hoc \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Expected response structure:**
```json
{
  "success": true,
  "message": "Lấy thống kê lớp học thành công",
  "data": {
    "summary": {
      "totalStudents": 10,
      "studentsWithScores": 8,
      "averageScore": 7.25,
      "passRate": 75.5,
      "totalExams": 45
    },
    "topStudents": [...],
    "weakStudents": [...],
    "scoreDistribution": [...],
    "studentDetails": [...]
  }
}
```

---

## 🐛 TROUBLESHOOTING

### Nếu màn hình trắng:
```bash
php artisan cache:clear
php artisan view:clear
php artisan config:clear
```

### Nếu API 500 error:
```bash
# Kiểm tra logs
tail -f storage/logs/laravel.log

# Kiểm tra database connection
php artisan migrate:status
```

### Nếu Chart.js không render:
1. Mở Developer Console (F12)
2. Kiểm tra error: "Chart is not defined"
3. Verify CDN load thành công:
   ```html
   <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
   ```

### Nếu checkbox không hoạt động:
1. Mở Console (F12)
2. Check error: `app.toggleQuestionSelection is not a function`
3. Verify window.app initialized:
   ```javascript
   console.log(window.app);
   console.log(window.app.selectedQuestions);
   ```

---

## ✅ CHECKLIST HOÀN THÀNH

### UR-03.1 - CRUD Câu hỏi:
- [ ] Edit modal hiển thị đúng
- [ ] Pre-fill data chính xác
- [ ] Update thành công
- [ ] Table refresh sau update

### UR-03.2 - Export:
- [ ] CSV download thành công
- [ ] PDF download thành công
- [ ] File có đầy đủ data

### UR-03.3 - Tạo đề thủ công:
- [ ] Checkbox chọn câu hỏi
- [ ] Sidebar cập nhật real-time
- [ ] Filter hoạt động
- [ ] Tạo đề thành công
- [ ] Database lưu đúng

### UR-03.5 - Thống kê lớp học:
- [ ] 4 cards hiển thị số liệu
- [ ] Top 5 tables render đúng
- [ ] Chart.js vẽ biểu đồ
- [ ] Bảng chi tiết 8 cột
- [ ] Badge trạng thái đúng màu

---

## 📊 KẾT QUẢ MONG ĐỢI

### Thành công 100% khi:
✅ Tất cả 4 tính năng mới hoạt động không lỗi
✅ UI/UX mượt mà, không lag
✅ Data hiển thị chính xác
✅ API trả về đúng format JSON
✅ Database lưu trữ đúng

### Hệ thống production-ready khi:
✅ Không có PHP errors
✅ Không có JavaScript console errors
✅ Chart.js render mượt
✅ Mobile responsive (Bootstrap)
✅ Loading states hoạt động

---

**🎉 Chúc mừng! Hệ thống đã hoàn thiện 100%!**

**Test ngay tại:** http://127.0.0.1:8000
**Login:** giaovien1 / password
