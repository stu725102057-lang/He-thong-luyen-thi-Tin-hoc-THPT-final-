# 🚀 QUICK START - BẮT ĐẦU NGAY

**Thời gian:** 10 phút  
**Mục tiêu:** Test nhanh hệ thống có hoạt động không

---

## ⚡ BƯỚC 1: CHẠY SERVER (30 giây)

```powershell
# Trong VS Code, mở Terminal (Ctrl+`)
cd "d:\Hệ thống luyện thi THPT môn Tin học (mới)\Hệ thống luyện thi THPT môn Tin học"
php artisan serve
```

**Kết quả mong đợi:**
```
Server started on http://127.0.0.1:8000
```

Giữ nguyên terminal này, **KHÔNG TẮT**.

---

## ⚡ BƯỚC 2: ĐĂNG NHẬP (1 phút)

1. Mở trình duyệt: `http://127.0.0.1:8000`

2. Đăng nhập với tài khoản học sinh:
   - Tên đăng nhập: `[Tài khoản của bạn]`
   - Mật khẩu: `[Mật khẩu của bạn]`

3. Nếu chưa có tài khoản:
   - Vào phpMyAdmin: `http://localhost/phpmyadmin`
   - Chạy SQL:
     ```sql
     -- Tạo tài khoản
     INSERT INTO TaiKhoan (MaTK, TenDangNhap, MatKhau, Email, Role, TrangThai)
     VALUES ('TK00000099', 'hocsinh_test', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'hs@test.com', 'hocsinh', 1);
     
     -- Tạo học sinh
     INSERT INTO HocSinh (MaHS, MaTK, HoTen, Lop, Truong)
     VALUES ('HS00000099', 'TK00000099', 'Học Sinh Test', '12A1', 'THPT Test');
     ```
   - Mật khẩu: `password` (đã mã hóa)

---

## ⚡ BƯỚC 3: LÀM BÀI THI (3 phút)

1. Chọn một đề thi bất kỳ

2. Nhấn **"Bắt đầu làm bài"**

3. Trả lời **1-2 câu hỏi** (chọn A, B, C, hoặc D)

4. **MỞ DEVTOOLS (F12)** → Tab **Network**

5. **ĐỢI 60 GIÂY** (quan trọng!)
   - Đừng làm gì, chỉ đợi
   - Xem đồng hồ đếm ngược

---

## ⚡ BƯỚC 4: KIỂM TRA AUTO-SAVE (1 phút)

Sau 60 giây, kiểm tra:

### ✅ Kiểm tra 1: Network Tab (F12)

Phải xuất hiện request:
```
POST http://127.0.0.1:8000/api/luu-nhap
Status: 200
```

Click vào request → Tab **Response**:
```json
{
  "success": true,
  "message": "Đã lưu nháp thành công",
  "data": {
    "MaBaiLam": "BL12345678",
    "SoCauDaLam": 2
  }
}
```

### ✅ Kiểm tra 2: Màn hình

Phải hiện thông báo:
```
✓ Đã lưu tự động
```

### ✅ Kiểm tra 3: Database

1. Mở phpMyAdmin: `http://localhost/phpmyadmin`

2. Chọn database của bạn → Bảng `BaiLam`

3. Tìm bản ghi mới nhất (sắp xếp theo `updated_at` DESC)

4. Xem cột `DSCauTraLoi`:
   ```json
   [
     {"MaCH": "CH00000001", "TraLoi": "A"},
     {"MaCH": "CH00000002", "TraLoi": "B"}
   ]
   ```

**Nếu cả 3 ✅ → AUTO-SAVE HOẠT ĐỘNG! 🎉**

---

## ⚡ BƯỚC 5: NỘP BÀI (2 phút)

1. Quay lại trang làm bài

2. Nhấn **"Nộp bài"**

3. Kiểm tra:
   - ✅ Chuyển sang trang kết quả
   - ✅ Hiển thị điểm số (ví dụ: 5.0)
   - ✅ Hiển thị số câu đúng/sai

4. Vào phpMyAdmin → Bảng `KetQua`:
   ```sql
   SELECT * FROM KetQua ORDER BY created_at DESC LIMIT 1;
   ```
   
   Phải có bản ghi mới với:
   - `Diem`: [số điểm]
   - `SoCauDung`: [số câu đúng]
   - `SoCauSai`: [số câu sai]

**Nếu có kết quả → NỘP BÀI HOẠT ĐỘNG! 🎉**

---

## ⚡ BƯỚC 6: TEST CHEATING (1 phút)

1. Làm bài thi mới

2. **Nhấn Ctrl+Tab** (chuyển sang tab khác)

3. Kiểm tra:
   - ✅ Màn hình hiện cảnh báo: "⚠️ Cảnh báo: Bạn đã chuyển tab"
   - ✅ Network tab có request: `POST /api/ghi-nhan-gian-lan`

4. Vào phpMyAdmin → Bảng `BaiLam`:
   ```sql
   SELECT MaBaiLam, SoLanViPham FROM BaiLam ORDER BY updated_at DESC LIMIT 1;
   ```
   
   `SoLanViPham` phải > 0

**Nếu có cảnh báo → CHEATING DETECTION HOẠT ĐỘNG! 🎉**

---

## 📊 KẾT QUẢ

### Nếu TẤT CẢ 3 test PASS:

```
✅ Auto-save       → HOẠT ĐỘNG
✅ Nộp bài         → HOẠT ĐỘNG
✅ Cheating detect → HOẠT ĐỘNG

🎉 HỆ THỐNG ĐẠT 100% YÊU CẦU!
```

**➡️ Đọc tiếp:** `TONG_KET_HOAN_THANH.md` để hiểu chi tiết

---

### Nếu có test FAIL:

#### ❌ Auto-save FAIL:

**Lỗi:** Không có request `POST /api/luu-nhap`

**Nguyên nhân thường gặp:**
1. Frontend không gọi API (kiểm tra Console có lỗi JS không)
2. `MaBaiLam` bị null (kiểm tra `this.examData.MaBaiLam`)

**Cách fix:**
```javascript
// Mở file: resources/views/app.blade.php
// Tìm dòng 6795, kiểm tra:
const data = {
    MaBaiLam: this.examData.MaBaiLam,  // ← Phải có giá trị
    CauTraLoi: this.answers
};
```

**Hoặc xem log:**
```powershell
# Terminal mới
cd "d:\Hệ thống luyện thi THPT môn Tin học (mới)\Hệ thống luyện thi THPT môn Tin học"
Get-Content storage/logs/laravel.log -Tail 50
```

Tìm dòng có "LƯU NHÁP ERROR".

---

#### ❌ Nộp bài FAIL:

**Lỗi:** Không có dữ liệu trong bảng `KetQua`

**Kiểm tra log:**
```powershell
Get-Content storage/logs/laravel.log -Tail 50
```

Tìm dòng có "NỘP BÀI THI ERROR".

**Hỏi AI:**
```
@BaiThiController.php

Nộp bài lỗi. Log:
[PASTE LOG]

Hãy phân tích và sửa.
```

---

#### ❌ Cheating FAIL:

**Lỗi:** Không có cảnh báo khi chuyển tab

**Kiểm tra Console (F12):**
- Có lỗi JavaScript không?

**Kiểm tra file:** `resources/views/app.blade.php`
- Tìm function `enableCheatingDetection()`
- Đảm bảo được gọi khi bắt đầu làm bài

---

## 🆘 HỖ TRỢ NHANH

### Lệnh hữu ích:

```powershell
# Xem log
Get-Content storage/logs/laravel.log -Tail 50

# Xóa cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Xem routes
php artisan route:list --path=luu-nhap

# Restart server
Ctrl+C (dừng server)
php artisan serve (khởi động lại)
```

### Prompt hỏi AI:

```
@QUICK_START.md @BaiThiController.php

Tôi đang ở BƯỚC [SỐ], test [TÊN TEST] bị FAIL.

Lỗi: [MÔ TẢ]

Log (nếu có):
[PASTE LOG]

Hãy giúp tôi khắc phục.
```

---

## 📚 ĐỌC THÊM

**Sau khi test xong, đọc:**

1. `TONG_KET_HOAN_THANH.md` - Tổng quan toàn bộ
2. `SO_DO_TONG_QUAN.md` - Sơ đồ trực quan
3. `CHECKLIST_NHANH.md` - Checklist đầy đủ

---

**Thời gian hoàn thành:** ~10 phút  
**Chúc bạn thành công! 🚀**
