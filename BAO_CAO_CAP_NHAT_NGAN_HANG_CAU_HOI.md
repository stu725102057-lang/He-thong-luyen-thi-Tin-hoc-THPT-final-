# BÁO CÁO CẬP NHẬT: QUẢN LÝ NGÂN HÀNG CÂU HỎI (UR-03.1)

## 📋 TỔNG QUAN

**Ngày:** 14/12/2025
**Người thực hiện:** GitHub Copilot
**Mục đích:** Cập nhật tên và mô tả chức năng "Quản lý câu hỏi" thành "Quản lý Ngân hàng câu hỏi" theo đặc tả UR-03.1

---

## 📝 ĐẶC TẢ YÊU CẦU (UR-03.1)

### Tên Chức Năng
**UR-03.1: Quản lý Ngân hàng câu hỏi**

### Mô Tả
Cho phép giáo viên thực hiện các thao tác Thêm, Sửa, Xóa câu hỏi trắc nghiệm.

### Quy Trình
1. **Đăng nhập:** Giáo viên đăng nhập với quyền quản lý
2. **Thêm câu hỏi mới:**
   - Nhập nội dung câu hỏi
   - Nhập 4 đáp án (A, B, C, D)
   - Chọn đáp án đúng
   - Chọn chủ đề
   - Chọn mức độ khó
3. **Sửa câu hỏi:** Giáo viên có thể sửa câu hỏi cũ
4. **Xóa câu hỏi:** Giáo viên có thể xóa câu hỏi cũ
5. **Lưu trữ:** Hệ thống lưu trữ câu hỏi vào ngân hàng câu hỏi và phân loại theo chủ đề

---

## 🔄 THAY ĐỔI ĐÃ THỰC HIỆN

### 1. Cập Nhật Menu (Teacher Menu)
**File:** `resources/views/app.blade.php`
**Dòng:** ~1056

**TRƯỚC:**
```html
<a class="nav-link" href="#" onclick="app.showScreen('quanlycauhoi')">
    <i class="bi bi-question-circle"></i> Quản lý câu hỏi
</a>
```

**SAU:**
```html
<a class="nav-link" href="#" onclick="app.showScreen('quanlycauhoi')">
    <i class="bi bi-bank"></i> Quản lý Ngân hàng câu hỏi
</a>
```

**Thay đổi:**
- ✅ Icon: `bi-question-circle` → `bi-bank` (biểu tượng ngân hàng)
- ✅ Text: "Quản lý câu hỏi" → "Quản lý Ngân hàng câu hỏi"

---

### 2. Cập Nhật Screen Header
**File:** `resources/views/app.blade.php`
**Dòng:** ~2066-2077

**TRƯỚC:**
```html
<!-- Quản lý câu hỏi Screen (Teacher) -->
<div id="quanlycauhoiScreen" class="screen">
    <div class="container">
        <h2 class="text-white mb-4">
            <i class="bi bi-question-circle"></i> Quản lý câu hỏi
        </h2>
        
        <!-- Action Buttons -->
```

**SAU:**
```html
<!-- Quản lý Ngân hàng câu hỏi Screen (Teacher) - UR-03.1 -->
<div id="quanlycauhoiScreen" class="screen">
    <div class="container">
        <h2 class="text-white mb-4">
            <i class="bi bi-bank"></i> Quản lý Ngân hàng câu hỏi
        </h2>
        <p class="text-white-50 mb-4">
            <i class="bi bi-info-circle"></i> 
            <strong>UR-03.1:</strong> Thêm, sửa, xóa câu hỏi trắc nghiệm. 
            Nhập nội dung, 4 đáp án, đáp án đúng, chủ đề, mức độ khó. 
            Hệ thống lưu trữ và phân loại theo chủ đề.
        </p>
        
        <!-- Action Buttons -->
```

**Thay đổi:**
- ✅ Comment: Thêm " - UR-03.1"
- ✅ Icon: `bi-question-circle` → `bi-bank`
- ✅ Title: "Quản lý câu hỏi" → "Quản lý Ngân hàng câu hỏi"
- ✅ **MỚI:** Thêm mô tả chi tiết UR-03.1 dưới tiêu đề

---

### 3. Cập Nhật Dashboard Buttons
**File:** `resources/views/app.blade.php`
**Dòng:** ~1401

**TRƯỚC:**
```html
<button class="btn btn-success me-2" onclick="app.showScreen('quanlycauhoi')">
    <i class="bi bi-question-circle"></i> Quản lý câu hỏi
</button>
```

**SAU:**
```html
<button class="btn btn-success me-2" onclick="app.showScreen('quanlycauhoi')">
    <i class="bi bi-bank"></i> Quản lý Ngân hàng câu hỏi
</button>
```

---

### 4. Cập Nhật Permission Label
**File:** `resources/views/app.blade.php`
**Dòng:** ~3119

**TRƯỚC:**
```html
<label class="form-check-label" for="permManageQuestions">
    Quản lý câu hỏi
</label>
```

**SAU:**
```html
<label class="form-check-label" for="permManageQuestions">
    Quản lý Ngân hàng câu hỏi
</label>
```

---

## 📊 TỔNG HỢP THAY ĐỔI

| Vị trí | Loại | Thay đổi | Trạng thái |
|--------|------|----------|------------|
| Menu Teacher | Text + Icon | Quản lý câu hỏi → Quản lý Ngân hàng câu hỏi | ✅ |
| Screen Header | Text + Icon + Description | Thêm mô tả UR-03.1 | ✅ |
| Dashboard Button | Text + Icon | Cập nhật tên và icon | ✅ |
| Permission Label | Text | Cập nhật label | ✅ |

**Tổng số vị trí cập nhật:** 4 vị trí  
**Icon mới:** `bi-bank` (Bootstrap Icons - Bank/Vault)

---

## 🎨 ICON MỚI

**Bootstrap Icon:** `bi-bank`
- **Ý nghĩa:** Ngân hàng, kho lưu trữ, vault
- **Phù hợp với:** "Ngân hàng câu hỏi" - Kho lưu trữ câu hỏi trắc nghiệm
- **Thay thế:** `bi-question-circle` (icon câu hỏi đơn lẻ)

**Lý do thay đổi:**
- Icon `bi-question-circle` chỉ biểu thị một câu hỏi
- Icon `bi-bank` biểu thị kho lưu trữ, ngân hàng dữ liệu
- Phù hợp hơn với khái niệm "Ngân hàng câu hỏi"

---

## ✅ CHỨC NĂNG HIỆN CÓ (Không Thay Đổi)

Chức năng đã hoàn chỉnh theo UR-03.1:

### 1. Thêm Câu Hỏi
- ✅ Form nhập nội dung câu hỏi
- ✅ Form nhập 4 đáp án (A, B, C, D)
- ✅ Select đáp án đúng
- ✅ Select chủ đề
- ✅ Select mức độ khó (Dễ, Trung bình, Khó)
- ✅ Button "Thêm câu hỏi"
- ✅ API: `POST /api/cau-hoi`

### 2. Sửa Câu Hỏi
- ✅ Button "Sửa" trên mỗi câu hỏi
- ✅ Form edit hiển thị dữ liệu hiện tại
- ✅ Cập nhật tất cả fields
- ✅ API: `PUT /api/cau-hoi/{id}`

### 3. Xóa Câu Hỏi
- ✅ Button "Xóa" trên mỗi câu hỏi
- ✅ Confirm dialog xác nhận
- ✅ API: `DELETE /api/cau-hoi/{id}`

### 4. Danh Sách Câu Hỏi
- ✅ Hiển thị table với pagination
- ✅ Cột: STT, Nội dung, Đáp án, Đáp án đúng, Chủ đề, Mức độ, Thao tác
- ✅ Filter theo chủ đề
- ✅ Filter theo mức độ khó
- ✅ Search theo nội dung
- ✅ API: `GET /api/cau-hoi`

### 5. Phân Loại
- ✅ Câu hỏi được lưu với `ChuyenDe`
- ✅ Câu hỏi được lưu với `MucDoKho`
- ✅ Database: Bảng `cauhoi` với các cột phân loại

---

## 🧪 HƯỚNG DẪN TEST

### 1. Test Giao Diện

**Bước 1:** Reload trang (Ctrl+F5)

**Bước 2:** Đăng nhập giáo viên
```
URL: http://127.0.0.1:8000
Username: giaovien
Password: 123456
```

**Bước 3:** Kiểm tra menu
- ✅ Menu hiển thị: "🏦 Quản lý Ngân hàng câu hỏi"
- ✅ Icon là `bi-bank` (biểu tượng ngân hàng)

**Bước 4:** Click vào menu
- ✅ Screen hiển thị title: "🏦 Quản lý Ngân hàng câu hỏi"
- ✅ Có mô tả UR-03.1 dưới title
- ✅ Mô tả: "Thêm, sửa, xóa câu hỏi trắc nghiệm..."

**Bước 5:** Kiểm tra dashboard (nếu có)
- ✅ Button "Quản lý Ngân hàng câu hỏi" với icon bank

### 2. Test Chức Năng (Không Thay Đổi)

**Thêm câu hỏi:**
1. Click "Thêm câu hỏi mới"
2. Nhập nội dung: "Câu hỏi test UR-03.1"
3. Nhập 4 đáp án
4. Chọn đáp án đúng
5. Chọn chủ đề: "Tin học"
6. Chọn mức độ: "Trung bình"
7. Click "Thêm"
8. ✅ Câu hỏi xuất hiện trong danh sách

**Sửa câu hỏi:**
1. Click "Sửa" trên câu hỏi vừa tạo
2. Đổi nội dung
3. Click "Cập nhật"
4. ✅ Câu hỏi được cập nhật

**Xóa câu hỏi:**
1. Click "Xóa" trên câu hỏi test
2. Confirm xóa
3. ✅ Câu hỏi bị xóa khỏi danh sách

---

## 📝 GHI CHÚ

### Về Tên Gọi
- **Cũ:** "Quản lý câu hỏi"
- **Mới:** "Quản lý Ngân hàng câu hỏi"
- **Lý do:** Phù hợp với đặc tả UR-03.1, nhấn mạnh khái niệm "Ngân hàng câu hỏi" - kho lưu trữ câu hỏi trắc nghiệm

### Về Mô Tả
Thêm mô tả chi tiết UR-03.1 giúp:
- Giáo viên hiểu rõ chức năng
- Nhắc nhở các bước thực hiện
- Tuân thủ đặc tả hệ thống

### Về Icon
- `bi-bank`: Biểu tượng ngân hàng/kho
- Phù hợp với "Ngân hàng câu hỏi"
- Dễ nhận biết hơn `bi-question-circle`

---

## 🔗 FILE LIÊN QUAN

**File đã sửa:**
- ✅ `resources/views/app.blade.php` (4 vị trí)

**File không thay đổi:**
- `app/Http/Controllers/CauHoiController.php` (Backend logic không đổi)
- `routes/api.php` (Routes không đổi)
- Database schema (Không đổi)

**Báo cáo:**
- ✅ `BAO_CAO_CAP_NHAT_NGAN_HANG_CAU_HOI.md` (Báo cáo này)

---

## 📊 TRƯỚC VÀ SAU

### TRƯỚC
```
Menu: 📋 Quản lý câu hỏi
Screen: 📋 Quản lý câu hỏi
(Không có mô tả)
```

### SAU
```
Menu: 🏦 Quản lý Ngân hàng câu hỏi
Screen: 🏦 Quản lý Ngân hàng câu hỏi
Mô tả: UR-03.1: Thêm, sửa, xóa câu hỏi trắc nghiệm. 
       Nhập nội dung, 4 đáp án, đáp án đúng, chủ đề, mức độ khó. 
       Hệ thống lưu trữ và phân loại theo chủ đề.
```

---

## ✅ KẾT LUẬN

**Trạng thái:** ✅ HOÀN THÀNH

**Thay đổi:**
- ✅ Cập nhật tên từ "Quản lý câu hỏi" → "Quản lý Ngân hàng câu hỏi"
- ✅ Thay icon từ `bi-question-circle` → `bi-bank`
- ✅ Thêm mô tả chi tiết UR-03.1
- ✅ Cập nhật tất cả 4 vị trí trong UI

**Chức năng:** Không thay đổi, hoạt động bình thường

**Yêu cầu tiếp theo:** Reload trang (Ctrl+F5) để xem thay đổi

---

**✨ CẬP NHẬT THÀNH CÔNG!**
