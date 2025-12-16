# ✨ Tính năng mới: Thêm câu hỏi thủ công

## 🎉 Đã hoàn thành

Đã thêm chức năng **Thêm câu hỏi thủ công** vào màn hình "Quản lý câu hỏi" cho giáo viên!

---

## 🆕 Tính năng mới

### 1. **Thêm câu hỏi thủ công** (NEW ✨)
Giáo viên có thể nhập từng câu hỏi trực tiếp vào form, không cần file Excel/CSV.

### 2. **Danh sách câu hỏi** (NEW ✨)
Hiển thị tất cả câu hỏi đã thêm với các thông tin:
- Mã câu hỏi
- Nội dung (rút gọn)
- Đáp án đúng
- Môn học
- Độ khó
- Thao tác (Xem chi tiết, Xóa)

### 3. **Import từ file** (Đã có, cải thiện UI)
Giữ nguyên chức năng import, nhưng UI đẹp hơn với card ẩn/hiện.

---

## 🎨 Giao diện mới

### Khi vào màn hình "Quản lý câu hỏi":

```
┌─────────────────────────────────────────────────────────┐
│  ❓ Quản lý câu hỏi                                     │
│                                                          │
│  [➕ Thêm câu hỏi mới]  [⬆ Import từ file]              │
│                                                          │
│  ┌─────────────────────────────────────────────────┐   │
│  │  ✅ Thêm câu hỏi mới                             │   │
│  │                                                  │   │
│  │  Nội dung câu hỏi: *                            │   │
│  │  [_____________________________________]        │   │
│  │                                                  │   │
│  │  Đáp án A: *         Đáp án B: *                │   │
│  │  [___________]       [___________]              │   │
│  │                                                  │   │
│  │  Đáp án C: *         Đáp án D: *                │   │
│  │  [___________]       [___________]              │   │
│  │                                                  │   │
│  │  Đáp án đúng: *  Môn học: *    Độ khó: *       │   │
│  │  [A ▼]          [TIN____]      [Trung bình ▼]  │   │
│  │                                                  │   │
│  │  [✅ Lưu câu hỏi]  [❌ Hủy]                      │   │
│  └─────────────────────────────────────────────────┘   │
│                                                          │
│  ┌─────────────────────────────────────────────────┐   │
│  │  Danh sách câu hỏi                               │   │
│  │                                                  │   │
│  │  ┌──────┬──────────┬────┬────┬──────┬────────┐ │   │
│  │  │ Mã   │ Nội dung │ ĐA │ Môn│ Độ khó│ Thao tác││   │
│  │  ├──────┼──────────┼────┼────┼──────┼────────┤ │   │
│  │  │CH001 │ Python..│ B  │TIN │[Dễ]  │[👁][🗑]│ │   │
│  │  │CH002 │ Java...│ A  │TIN │[TB]  │[👁][🗑]│ │   │
│  │  └──────┴──────────┴────┴────┴──────┴────────┘ │   │
│  └─────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────┘
```

---

## 📋 Form "Thêm câu hỏi mới"

### Các trường bắt buộc (*)

| Trường | Loại | Mô tả |
|--------|------|-------|
| **Nội dung câu hỏi** | Textarea | Câu hỏi chính (3 dòng) |
| **Đáp án A** | Text | Lựa chọn A |
| **Đáp án B** | Text | Lựa chọn B |
| **Đáp án C** | Text | Lựa chọn C |
| **Đáp án D** | Text | Lựa chọn D |
| **Đáp án đúng** | Select | A, B, C, hoặc D |
| **Môn học** | Text | Mã môn (mặc định: TIN) |
| **Độ khó** | Select | Dễ, Trung bình, Khó |

### Buttons
- **✅ Lưu câu hỏi**: Submit form → gọi API `POST /api/cau-hoi`
- **❌ Hủy**: Đóng form, clear dữ liệu

---

## 🔄 Luồng hoạt động

### Luồng 1: Thêm câu hỏi thủ công

```
1. Login với tài khoản giáo viên
   ↓
2. Click "Quản lý câu hỏi" trong menu
   ↓
3. Danh sách câu hỏi tự động load
   ↓
4. Click button "➕ Thêm câu hỏi mới"
   ↓
5. Form hiển thị (scroll smooth)
   ↓
6. Điền thông tin câu hỏi
   ↓
7. Click "Lưu câu hỏi"
   ↓
8. API: POST /api/cau-hoi
   ↓
9. Success: Toast "Thêm câu hỏi thành công!"
   ↓
10. Form đóng + reset
    ↓
11. Danh sách câu hỏi reload (hiển thị câu mới)
```

### Luồng 2: Import từ file

```
1. Click button "⬆ Import từ file"
   ↓
2. Form import hiển thị
   ↓
3. Chọn file Excel/CSV
   ↓
4. Click "Import câu hỏi"
   ↓
5. API: POST /api/cau-hoi/import
   ↓
6. Success: Toast "Import thành công!"
   ↓
7. Form đóng
   ↓
8. Danh sách reload
```

### Luồng 3: Xem chi tiết câu hỏi

```
1. Trong danh sách, click icon [👁]
   ↓
2. API: GET /api/cau-hoi/{maCH}
   ↓
3. Alert hiển thị chi tiết:
   - Mã câu hỏi
   - Nội dung đầy đủ
   - 4 đáp án
   - Đáp án đúng
   - Độ khó, Môn học
```

### Luồng 4: Xóa câu hỏi

```
1. Click icon [🗑]
   ↓
2. Confirm dialog: "Bạn có chắc muốn xóa?"
   ↓
3. Nếu Yes:
   → API: DELETE /api/cau-hoi/{maCH}
   → Toast "Đã xóa câu hỏi"
   → Danh sách reload
   ↓
4. Nếu No: Không làm gì
```

---

## 🎯 API Endpoints sử dụng

### 1. Thêm câu hỏi
```http
POST /api/cau-hoi
Authorization: Bearer {token}
Content-Type: application/json

Body:
{
  "NoiDung": "Python là ngôn ngữ lập trình gì?",
  "DapAn1": "Compiled",
  "DapAn2": "Interpreted",
  "DapAn3": "Assembly",
  "DapAn4": "Machine code",
  "DapAnDung": "B",
  "MaMon": "TIN",
  "MucDo": "de"
}

Response 201:
{
  "success": true,
  "message": "Thêm câu hỏi thành công",
  "data": {
    "MaCH": "CH001",
    ...
  }
}
```

### 2. Lấy danh sách câu hỏi
```http
GET /api/cau-hoi
Authorization: Bearer {token}

Response 200:
{
  "success": true,
  "data": [
    {
      "MaCH": "CH001",
      "NoiDung": "...",
      "DapAn1": "...",
      "DapAn2": "...",
      "DapAn3": "...",
      "DapAn4": "...",
      "DapAn": "B",
      "DoKho": "de",
      "MaMon": "TIN"
    },
    ...
  ]
}
```

### 3. Xem chi tiết câu hỏi
```http
GET /api/cau-hoi/{maCH}
Authorization: Bearer {token}

Response 200:
{
  "success": true,
  "data": {
    "MaCH": "CH001",
    "NoiDung": "Python là ngôn ngữ lập trình gì?",
    ...
  }
}
```

### 4. Xóa câu hỏi
```http
DELETE /api/cau-hoi/{maCH}
Authorization: Bearer {token}

Response 200:
{
  "success": true,
  "message": "Xóa câu hỏi thành công"
}
```

### 5. Import câu hỏi
```http
POST /api/cau-hoi/import
Authorization: Bearer {token}
Content-Type: multipart/form-data

Body: FormData with 'file'

Response 200:
{
  "success": true,
  "message": "Import thành công"
}
```

---

## 🧪 Test Cases

### TEST 1: Thêm câu hỏi thủ công

**Bước thực hiện:**
1. Login: `teacher001` / `teachpass123`
2. Click "Quản lý câu hỏi"
3. Click "➕ Thêm câu hỏi mới"
4. Điền form:
   - Nội dung: "Python là gì?"
   - Đáp án A: "Compiled language"
   - Đáp án B: "Interpreted language"
   - Đáp án C: "Assembly language"
   - Đáp án D: "Machine language"
   - Đáp án đúng: B
   - Môn học: TIN
   - Độ khó: Dễ
5. Click "Lưu câu hỏi"

**Kết quả mong đợi:**
- ✅ Toast "Thêm câu hỏi thành công!"
- ✅ Form đóng và reset
- ✅ Câu hỏi mới xuất hiện trong danh sách
- ✅ Mã CH tự động (CH001, CH002...)

---

### TEST 2: Validation

**Test 2.1: Bỏ trống trường bắt buộc**
- Bước: Bỏ trống "Nội dung câu hỏi", click "Lưu"
- Mong đợi: ✅ Browser validation "Please fill out this field"

**Test 2.2: Không chọn đáp án đúng**
- Bước: Điền đầy đủ nhưng không chọn "Đáp án đúng"
- Mong đợi: ✅ Validation error

---

### TEST 3: Hiển thị danh sách

**Bước:**
1. Vào "Quản lý câu hỏi"
2. Kiểm tra danh sách

**Kết quả mong đợi:**
- ✅ Loading spinner hiển thị trong khi load
- ✅ Danh sách câu hỏi hiển thị dạng table
- ✅ Nếu chưa có câu hỏi: "Chưa có câu hỏi nào. Hãy thêm câu hỏi mới!"
- ✅ Mỗi câu có 2 button: 👁 (xem) và 🗑 (xóa)
- ✅ Badges màu sắc theo độ khó:
  - Dễ: Xanh dương (info)
  - Trung bình: Vàng (warning)
  - Khó: Đỏ (danger)

---

### TEST 4: Xem chi tiết câu hỏi

**Bước:**
1. Click icon 👁 trên một câu hỏi

**Kết quả mong đợi:**
- ✅ Alert popup hiển thị đầy đủ thông tin:
  - Mã câu hỏi
  - Nội dung đầy đủ
  - 4 đáp án (A, B, C, D)
  - Đáp án đúng
  - Độ khó
  - Môn học

---

### TEST 5: Xóa câu hỏi

**Bước:**
1. Click icon 🗑 trên một câu hỏi
2. Confirm "Bạn có chắc?"
3. Click OK

**Kết quả mong đợi:**
- ✅ Confirm dialog hiển thị
- ✅ Sau khi OK: Toast "Đã xóa câu hỏi"
- ✅ Câu hỏi biến mất khỏi danh sách
- ✅ API DELETE được gọi đúng

---

### TEST 6: Toggle forms

**Test 6.1: Show/Hide "Thêm câu hỏi"**
- Click "Thêm câu hỏi mới" → Form hiển thị
- Click "Hủy" → Form ẩn

**Test 6.2: Show/Hide "Import"**
- Click "Import từ file" → Form import hiển thị
- Click "Đóng" → Form ẩn

**Test 6.3: Chỉ 1 form hiển thị tại 1 thời điểm**
- Nếu form "Thêm câu hỏi" đang mở
- Click "Import từ file"
- → Form "Thêm câu hỏi" tự động đóng
- → Form "Import" mở

---

## 🎨 UI Features

### Smooth Scrolling
- Khi click "Thêm câu hỏi mới" → Trang tự động scroll đến form
- Smooth animation (không nhảy cóc)

### Color Coding
- **Độ khó Dễ**: Badge xanh dương (bg-info)
- **Độ khó Trung bình**: Badge vàng (bg-warning)
- **Độ khó Khó**: Badge đỏ (bg-danger)
- **Đáp án đúng**: Badge xanh lá (bg-success)

### Responsive
- Form responsive (col-md-6, col-md-4)
- Table responsive (scroll ngang trên mobile)
- Buttons có icon rõ ràng

### Loading States
- Spinner hiển thị khi load danh sách
- Disable buttons khi đang submit (TODO)

---

## 🔧 JavaScript Functions Added

### Form Management
```javascript
app.showAddQuestionForm()       // Hiển thị form thêm câu hỏi
app.hideAddQuestionForm()       // Ẩn form thêm câu hỏi
app.toggleImportForm()          // Toggle form import
```

### CRUD Operations
```javascript
app.addQuestion(event)          // POST /api/cau-hoi
app.loadQuestionList()          // GET /api/cau-hoi
app.viewQuestion(maCH)          // GET /api/cau-hoi/{maCH}
app.deleteQuestion(maCH)        // DELETE /api/cau-hoi/{maCH}
```

### Auto-load
- Khi vào màn hình "Quản lý câu hỏi" → `loadQuestionList()` tự động chạy

---

## 📊 Tổng kết

| Tính năng | Status | Notes |
|-----------|--------|-------|
| Thêm câu hỏi thủ công | ✅ | Form đầy đủ validation |
| Import từ file | ✅ | UI cải thiện |
| Danh sách câu hỏi | ✅ | Table responsive |
| Xem chi tiết | ✅ | Alert popup (TODO: Modal) |
| Xóa câu hỏi | ✅ | Có confirm |
| Auto-load | ✅ | Load khi vào screen |
| Smooth scroll | ✅ | Form scroll vào view |
| Color coding | ✅ | Badges theo độ khó |

---

## 🚀 Cách test ngay

1. **Refresh trang** (F5)
2. **Login** với `teacher001` / `teachpass123`
3. **Click "Quản lý câu hỏi"**
4. **Click "➕ Thêm câu hỏi mới"**
5. **Điền form và submit**
6. **Xem danh sách** tự động reload!

---

## 🎯 TODO (Optional enhancements)

- [ ] Replace alert() bằng Bootstrap Modal cho "Xem chi tiết"
- [ ] Thêm button "Edit" (sửa câu hỏi)
- [ ] Pagination cho danh sách câu hỏi (nếu >100 câu)
- [ ] Search/Filter trong danh sách
- [ ] Export danh sách ra Excel
- [ ] Preview câu hỏi trước khi lưu
- [ ] Rich text editor cho nội dung câu hỏi
- [ ] Upload hình ảnh trong câu hỏi

---

**Last Updated**: December 7, 2025  
**Status**: ✅ COMPLETE & READY TO TEST  
**Version**: 2.0.0
