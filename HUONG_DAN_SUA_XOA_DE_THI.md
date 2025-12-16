# 📝 HƯỚNG DẪN SỬA VÀ XÓA ĐỀ THI

## ✨ Tính năng mới đã thêm

Hệ thống đã được bổ sung 2 chức năng quan trọng cho Giáo viên:

1. **✏️ SỬA ĐỀ THI** - Cập nhật thông tin đề thi
2. **🗑️ XÓA ĐỀ THI** - Xóa đề thi không còn sử dụng

---

## 🔧 API Endpoints Mới

### 1. Cập nhật đề thi (PUT)
```
PUT /api/de-thi/{maDe}
```

**Headers:**
```json
{
  "Authorization": "Bearer {token}",
  "Content-Type": "application/json"
}
```

**Request Body:**
```json
{
  "TenDe": "Tên đề thi mới",
  "ChuDe": "Chủ đề",
  "ThoiGianLamBai": 60,
  "MoTa": "Mô tả đề thi",
  "TrangThai": 1
}
```

**Response Success:**
```json
{
  "success": true,
  "message": "Cập nhật đề thi thành công",
  "data": {
    "MaDe": "DE001",
    "TenDe": "Tên đề thi mới",
    "ChuDe": "Chủ đề",
    "ThoiGianLamBai": 60,
    "SoLuongCauHoi": 20,
    "MoTa": "Mô tả đề thi",
    "NgayTao": "2025-12-08",
    "TrangThai": 1
  }
}
```

---

### 2. Xóa đề thi (DELETE)
```
DELETE /api/de-thi/{maDe}
```

**Headers:**
```json
{
  "Authorization": "Bearer {token}"
}
```

**Response Success:**
```json
{
  "success": true,
  "message": "Xóa đề thi thành công"
}
```

**Response Error (Đã có học sinh làm):**
```json
{
  "success": false,
  "message": "Không thể xóa đề thi đã có học sinh làm bài",
  "data": {
    "submissionCount": 5
  }
}
```

---

## 🖥️ Hướng dẫn sử dụng trên giao diện

### ✏️ Sửa đề thi

1. **Đăng nhập** với tài khoản Giáo viên
2. Vào menu **"Danh sách đề thi"**
3. Tìm đề thi muốn sửa trong bảng
4. Click vào nút **"Sửa"** (biểu tượng bút chì màu vàng)
5. Modal sửa đề thi sẽ hiển thị với thông tin hiện tại
6. Chỉnh sửa các thông tin:
   - ✅ **Tên đề thi** (bắt buộc)
   - ✅ **Chủ đề** (tùy chọn)
   - ✅ **Thời gian làm bài** (bắt buộc, 10-180 phút)
   - ✅ **Mô tả** (tùy chọn)
   - ✅ **Trạng thái** (bật/tắt)
7. Click **"Cập nhật đề thi"**
8. Danh sách sẽ tự động refresh

**⚠️ Lưu ý:**
- Không thể thay đổi số lượng câu hỏi sau khi tạo
- Chỉ Giáo viên tạo đề hoặc Admin mới có quyền sửa
- Học sinh không thể sửa đề thi

---

### 🗑️ Xóa đề thi

1. **Đăng nhập** với tài khoản Giáo viên
2. Vào menu **"Danh sách đề thi"**
3. Tìm đề thi muốn xóa trong bảng
4. Click vào nút **"Xóa"** (biểu tượng thùng rác màu đỏ)
5. Xác nhận xóa trong hộp thoại cảnh báo
6. Hệ thống sẽ xóa đề thi và các câu hỏi liên quan

**⚠️ Điều kiện xóa:**
- ✅ Chỉ Giáo viên tạo đề hoặc Admin mới có quyền xóa
- ❌ **KHÔNG THỂ XÓA** nếu đã có học sinh làm bài
- ⚠️ Hành động xóa **KHÔNG THỂ HOÀN TÁC**

---

## 🛡️ Bảo mật và Phân quyền

### Quyền truy cập:

| Chức năng | Học sinh | Giáo viên | Admin |
|-----------|----------|-----------|-------|
| Xem đề thi | ✅ | ✅ | ✅ |
| Sửa đề thi | ❌ | ✅ (của mình) | ✅ (tất cả) |
| Xóa đề thi | ❌ | ✅ (của mình) | ✅ (tất cả) |

### Kiểm tra phân quyền trong code:

**Controller (`DeThiController.php`):**
```php
// Kiểm tra quyền sửa/xóa
if ($user->Role !== 'admin' && $exam->MaGV != $user->MaTK) {
    return response()->json([
        'success' => false, 
        'message' => 'Bạn không có quyền sửa/xóa đề thi này'
    ], 403);
}
```

---

## 🧪 Test chức năng

### Test 1: Sửa đề thi thành công

**Bước thực hiện:**
1. Login với `giaovien1`
2. Vào "Danh sách đề thi"
3. Click "Sửa" một đề thi
4. Đổi tên thành "Đề thi đã sửa"
5. Đổi thời gian thành 90 phút
6. Click "Cập nhật đề thi"

**Kết quả mong đợi:**
- ✅ Hiển thị "Cập nhật đề thi thành công!"
- ✅ Đề thi trong danh sách được cập nhật
- ✅ Modal tự động đóng

---

### Test 2: Xóa đề thi chưa có học sinh làm

**Bước thực hiện:**
1. Login với `giaovien1`
2. Vào "Danh sách đề thi"
3. Click "Xóa" một đề thi chưa có lượt làm
4. Xác nhận trong hộp thoại

**Kết quả mong đợi:**
- ✅ Hiển thị "Xóa đề thi thành công!"
- ✅ Đề thi biến mất khỏi danh sách
- ✅ Các câu hỏi liên quan cũng bị xóa

---

### Test 3: Không thể xóa đề thi đã có học sinh làm

**Bước thực hiện:**
1. Tạo đề thi mới
2. Cho học sinh làm bài (hoặc nộp bài)
3. Login lại với tài khoản giáo viên
4. Cố gắng xóa đề thi vừa tạo

**Kết quả mong đợi:**
- ❌ Hiển thị lỗi: "Không thể xóa đề thi đã có học sinh làm bài"
- ❌ Đề thi không bị xóa

---

### Test 4: Không có quyền sửa/xóa đề thi của giáo viên khác

**Bước thực hiện:**
1. Đề thi được tạo bởi `giaovien1`
2. Login với `giaovien2`
3. Cố gắng sửa/xóa đề thi của `giaovien1`

**Kết quả mong đợi:**
- ❌ Hiển thị lỗi: "Bạn không có quyền sửa/xóa đề thi này"
- ❌ Hành động bị từ chối

---

## 📊 Cấu trúc Database

### Bảng `dethi`
```sql
CREATE TABLE dethi (
    MaDe VARCHAR(10) PRIMARY KEY,
    TenDe VARCHAR(255) NOT NULL,
    ChuDe VARCHAR(255),
    ThoiGianLamBai INT NOT NULL,
    SoLuongCauHoi INT NOT NULL,
    MoTa TEXT,
    MaGV VARCHAR(10) NOT NULL,
    NgayTao DATE,
    TrangThai TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (MaGV) REFERENCES taikhoan(MaTK)
);
```

### Bảng `dethi_cauhoi` (Liên kết)
```sql
CREATE TABLE dethi_cauhoi (
    MaDe VARCHAR(10),
    MaCH VARCHAR(10),
    ThuTu INT,
    PRIMARY KEY (MaDe, MaCH),
    FOREIGN KEY (MaDe) REFERENCES dethi(MaDe) ON DELETE CASCADE,
    FOREIGN KEY (MaCH) REFERENCES cauhoi(MaCH)
);
```

**⚠️ Cascade Delete:**
- Khi xóa đề thi, các bản ghi trong `dethi_cauhoi` sẽ tự động bị xóa

---

## 🐛 Troubleshooting

### Lỗi 403: Forbidden
**Nguyên nhân:** Không có quyền sửa/xóa đề thi
**Giải pháp:** 
- Kiểm tra token đăng nhập
- Đảm bảo bạn là Giáo viên tạo đề hoặc Admin

---

### Lỗi 404: Not Found
**Nguyên nhân:** Đề thi không tồn tại
**Giải pháp:**
- Kiểm tra lại `MaDe`
- Có thể đề thi đã bị xóa

---

### Lỗi 400: Bad Request (Không thể xóa)
**Nguyên nhân:** Đề thi đã có học sinh làm bài
**Giải pháp:**
- Không thể xóa đề thi này
- Có thể tắt trạng thái thay vì xóa
- Hoặc xóa các bài làm trước (không khuyến khích)

---

### Modal không hiển thị
**Nguyên nhân:** Lỗi JavaScript hoặc Bootstrap chưa load
**Giải pháp:**
1. Kiểm tra Console (F12)
2. Đảm bảo Bootstrap 5 đã load
3. Clear cache trình duyệt (Ctrl + F5)

---

## 📝 Code Reference

### Backend: `DeThiController.php`

**Method sửa đề thi:**
```php
public function updateExam(Request $request, $maDe)
{
    // Validate quyền
    // Validate dữ liệu
    // Update database
    // Return response
}
```

**Method xóa đề thi:**
```php
public function destroyExam(Request $request, $maDe)
{
    // Validate quyền
    // Kiểm tra bài làm
    // Delete cascade
    // Return response
}
```

---

### Frontend: `app.blade.php`

**Modal HTML:**
```html
<div class="modal fade" id="editExamModal" tabindex="-1">
    <!-- Form sửa đề thi -->
</div>
```

**JavaScript Functions:**
```javascript
// Mở modal sửa
async editExam(maDe) { ... }

// Cập nhật đề thi
async updateExam() { ... }

// Xóa đề thi
async deleteExam(maDe, tenDe) { ... }
```

---

### Routes: `routes/api.php`

```php
// Sửa đề thi
Route::put('/de-thi/{maDe}', [DeThiController::class, 'updateExam']);

// Xóa đề thi
Route::delete('/de-thi/{maDe}', [DeThiController::class, 'destroyExam']);
```

---

## ✅ Checklist hoàn thành

- [x] ✅ API sửa đề thi (`updateExam`)
- [x] ✅ API xóa đề thi (`destroyExam`)
- [x] ✅ Routes cho PUT và DELETE
- [x] ✅ Modal sửa đề thi trên UI
- [x] ✅ JavaScript `editExam()` function
- [x] ✅ JavaScript `updateExam()` function
- [x] ✅ JavaScript `deleteExam()` function
- [x] ✅ Validation phân quyền
- [x] ✅ Kiểm tra điều kiện xóa
- [x] ✅ Cascade delete câu hỏi liên quan
- [x] ✅ Tài liệu hướng dẫn

---

## 🎉 Kết luận

Chức năng **Sửa và Xóa đề thi** đã được hoàn thành với đầy đủ:

1. ✅ **Backend API** - Validation, phân quyền, logic xử lý
2. ✅ **Frontend UI** - Modal, form, JavaScript
3. ✅ **Bảo mật** - Kiểm tra quyền, điều kiện xóa
4. ✅ **UX tốt** - Xác nhận trước khi xóa, thông báo rõ ràng

**Hệ thống quản lý đề thi giờ đã hoàn chỉnh với CRUD đầy đủ! 🚀**

---

📅 **Ngày hoàn thành:** 8/12/2025  
👨‍💻 **Phiên bản:** 1.0.0  
📧 **Hỗ trợ:** Contact Admin nếu có vấn đề
