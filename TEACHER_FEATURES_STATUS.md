# TÌNH TRẠNG CÁC CHỨC NĂNG GIÁO VIÊN (UR-03)
*Ngày kiểm tra: 08/12/2025*

## 📊 TỔNG QUAN

| STT | Yêu cầu | Trạng thái | Hoàn thành | Còn thiếu |
|-----|---------|------------|------------|-----------|
| 1 | UR-03.1: CRUD Câu hỏi | ⚠️ Thiếu | 80% | Chức năng SỬA |
| 2 | UR-03.2: Import/Export | ⚠️ Thiếu | 50% | Export CSV/PDF |
| 3 | UR-03.3: Tạo đề thủ công | ❌ Chưa có | 0% | Toàn bộ |
| 4 | UR-03.4: Sinh đề ngẫu nhiên | ✅ Hoàn thiện | 100% | - |
| 5 | UR-03.5: Thống kê lớp học | ❌ Chưa có | 0% | Toàn bộ |

**Tổng tiến độ: 46%** (2.3/5 yêu cầu hoàn thành)

---

## ✅ UR-03.4: SINH ĐỀ THI NGẪU NHIÊN (100%)

### Đã có:
- ✅ Modal tạo đề ngẫu nhiên với form
- ✅ Chọn số câu hỏi, thời gian, chủ đề, độ khó
- ✅ API POST `/de-thi/random` trong DeThiController
- ✅ JavaScript `generateRandomExam()` hoàn chỉnh
- ✅ Hiển thị thông báo thành công/thất bại

### Vị trí code:
- **Frontend:** Line 2622-2695 (Modal), Line 5770-5803 (JS)
- **Backend:** `app/Http/Controllers/DeThiController.php` - method `generateRandom()`
- **Route:** `routes/api.php` - Line 62

---

## ⚠️ UR-03.1: QUẢN LÝ NGÂN HÀNG CÂU HỎI (80%)

### Đã có:
- ✅ **THÊM câu hỏi:** Form đầy đủ với 4 đáp án, độ khó
  - Frontend: Line 1860-1930 (Form)
  - API: POST `/cau-hoi` 
  - Method: `CauHoiController@store`

- ✅ **XÓA câu hỏi:** Nút xóa màu đỏ, confirm trước khi xóa
  - Frontend: Line 3497 (Button), Line 3522-3540 (JS)
  - API: DELETE `/cau-hoi/{id}`
  - Method: `CauHoiController@destroy`

- ✅ **XEM chi tiết:** Nút xem info màu xanh
  - Frontend: Line 3494 (Button), Line 3512-3520 (JS)
  - API: GET `/cau-hoi/{id}`
  - Method: `CauHoiController@show`

### ❌ Còn thiếu:
- **SỬA câu hỏi:**
  - ❌ Nút "Sửa" trong bảng (đã thêm line 3495 nhưng chưa có logic)
  - ❌ Modal edit với pre-fill dữ liệu
  - ❌ JavaScript `editQuestion(maCH)` và `updateQuestion(event)`
  - ⚠️ API PUT `/cau-hoi/{id}` ĐÃ CÓ ở backend (Line 56)
  - ⚠️ Method `CauHoiController@update()` ĐÃ CÓ (Line 233-290)

### Cần làm:
1. Tạo modal `editQuestionModal`
2. JavaScript load data câu hỏi vào form
3. JavaScript submit update API

---

## ⚠️ UR-03.2: NHẬP/XUẤT CÂU HỎI (50%)

### Đã có:
- ✅ **IMPORT JSON:** 
  - Frontend: Line 1938-1955 (Form upload file)
  - API: POST `/cau-hoi/import`
  - Method: `CauHoiController@importJson()` (Line 96-166)
  - Hỗ trợ: Upload file JSON với array câu hỏi

### ❌ Còn thiếu:
- **EXPORT:**
  - ⚠️ API route `/cau-hoi/export` ĐÃ CÓ (Line 51)
  - ⚠️ Method `CauHoiController@export()` ĐÃ CÓ (Line 388-446)
  - ❌ Nút "Xuất file" trong giao diện
  - ❌ JavaScript gọi API export
  - ⚠️ Backend hỗ trợ CSV format (đã có)
  - ⚠️ Backend hỗ trợ PDF format (đã có, dùng DomPDF)

### Cần làm:
1. Thêm nút "Xuất CSV" và "Xuất PDF" trong màn hình câu hỏi
2. JavaScript `exportQuestions(format)` gọi API `/cau-hoi/export?format=csv`
3. Download file về máy

---

## ❌ UR-03.3: TẠO ĐỀ THI THỦ CÔNG (0%)

### Yêu cầu:
Giáo viên tự chọn từng câu hỏi cụ thể để tạo đề thi

### Cần làm:
1. **Màn hình mới:** "Tạo đề thi thủ công"
2. **Danh sách câu hỏi** với checkbox
3. **Sidebar** hiển thị câu đã chọn
4. **Form thông tin đề thi:** Tên đề, thời gian, môn học
5. **API mới:** POST `/de-thi/manual` với array `[MaCH1, MaCH2, ...]`
6. **Backend Controller:** Method `createManualExam()` trong DeThiController

### Thiết kế UI:
```
┌─────────────────────────────────────────┐
│ Tạo đề thi thủ công                      │
├─────────────────────────────────────────┤
│ ┌─────────────┬─────────────────────┐   │
│ │ Danh sách   │ Câu đã chọn (3/50) │   │
│ │ câu hỏi     │                     │   │
│ │             │ [CH001] CPU là...   │   │
│ │ ☑ CH001     │ [CH005] RAM là...   │   │
│ │ ☐ CH002     │ [CH010] HDD vs SSD  │   │
│ │ ☐ CH003     │                     │   │
│ │ ☑ CH005     │ [Tạo đề thi]       │   │
│ └─────────────┴─────────────────────┘   │
└─────────────────────────────────────────┘
```

---

## ❌ UR-03.5: THỐNG KÊ KẾT QUẢ LỚP HỌC (0%)

### Yêu cầu:
Báo cáo thống kê cho giáo viên về kết quả học sinh:
- Điểm trung bình lớp
- Tỉ lệ đúng/sai theo từng chuyên đề
- Danh sách học sinh yếu/khá/giỏi
- Biểu đồ phân bố điểm

### Hiện tại:
- ⚠️ Có màn hình "Thống kê" nhưng chỉ dành cho HỌC SINH (line 1729)
- ❌ Chưa có thống kê dạng LỚP HỌC cho GIÁO VIÊN

### Cần làm:
1. **Màn hình mới:** "Thống kê lớp học" (cho giáo viên)
2. **API mới:** GET `/thong-ke/lop-hoc`
3. **Backend Controller:** Method `getClassStatistics()` 
4. **Metrics:**
   - Tổng học sinh
   - Điểm TB lớp
   - Số học sinh đạt/không đạt
   - Top 5 học sinh cao điểm
   - Top 5 học sinh cần hỗ trợ
5. **Biểu đồ:**
   - Phân bố điểm (0-10)
   - Tỉ lệ đúng theo chủ đề
   - Xu hướng điểm theo thời gian
6. **Bảng chi tiết:** Danh sách học sinh với điểm TB

### Thiết kế UI:
```
┌─────────────────────────────────────────┐
│ Thống kê lớp học 12A1                    │
├─────────────────────────────────────────┤
│ [50 HS] [Điểm TB: 7.2] [Đạt: 85%]      │
│                                          │
│ ┌──────────────┬──────────────┐         │
│ │ Phân bố điểm │ Top 5 HS     │         │
│ │ [Biểu đồ]    │ 1. Nguyễn A  │         │
│ └──────────────┴──────────────┘         │
│                                          │
│ Bảng điểm chi tiết:                      │
│ ┌────┬─────────┬─────┬────────┐         │
│ │ STT│ Họ tên  │ Điểm│ Đánh giá│        │
│ ├────┼─────────┼─────┼────────┤         │
│ │ 1  │ Nguyễn A│ 9.5 │ Giỏi   │         │
│ └────┴─────────┴─────┴────────┘         │
└─────────────────────────────────────────┘
```

---

## 🎯 KẾ HOẠCH HOÀN THIỆN

### Giai đoạn 1: Hoàn thiện CRUD (30 phút)
1. ✅ Thêm nút Sửa (đã xong)
2. ⏳ Tạo modal Edit
3. ⏳ JavaScript editQuestion() + updateQuestion()
4. ⏳ Test sửa câu hỏi

### Giai đoạn 2: Export (15 phút)
1. ⏳ Thêm nút Export CSV/PDF
2. ⏳ JavaScript exportQuestions()
3. ⏳ Test download file

### Giai đoạn 3: Tạo đề thủ công (1 giờ)
1. ⏳ Tạo screen mới
2. ⏳ Danh sách câu hỏi có checkbox
3. ⏳ Sidebar câu đã chọn
4. ⏳ API POST /de-thi/manual
5. ⏳ Backend method createManualExam()
6. ⏳ Test tạo đề

### Giai đoạn 4: Thống kê lớp (45 phút)
1. ⏳ Tạo screen thống kê
2. ⏳ API GET /thong-ke/lop-hoc
3. ⏳ Backend method getClassStatistics()
4. ⏳ Render biểu đồ Chart.js
5. ⏳ Bảng danh sách học sinh
6. ⏳ Test thống kê

**Tổng thời gian ước tính: 2.5 giờ**

---

## 📝 GHI CHÚ KỸ THUẬT

### Backend có sẵn:
- ✅ PUT `/cau-hoi/{id}` - CauHoiController@update
- ✅ GET `/cau-hoi/export` - CauHoiController@export
- ✅ POST `/de-thi/random` - DeThiController@generateRandom

### Backend cần thêm:
- ❌ POST `/de-thi/manual` - DeThiController@createManualExam
- ❌ GET `/thong-ke/lop-hoc` - ThongKeController@getClassStatistics

### Frontend cần thêm:
- ❌ Modal editQuestionModal
- ❌ Screen "Tạo đề thủ công"
- ❌ Screen "Thống kê lớp học"
- ❌ Nút Export
- ❌ ~15 JavaScript methods

---

*Báo cáo được tạo tự động bởi AI Assistant*
