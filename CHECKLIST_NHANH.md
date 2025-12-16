# 📋 CHECKLIST NHANH - KIỂM TRA HỆ THỐNG

**In ra và tick ✓ khi hoàn thành**

---

## 🎯 BƯỚC 1: ĐỌC TÀI LIỆU (5 phút)

- [ ] Đọc file `TONG_KET_HOAN_THANH.md` (file tóm tắt)
- [ ] Đọc file `HUONG_DAN_DONG_BO_HE_THONG.md` (hướng dẫn chi tiết)
- [ ] Đọc file `REQUIREMENTS.md` (chuẩn từ báo cáo)

---

## 🧪 BƯỚC 2: TEST HỆ THỐNG (15 phút)

### Test 1: Auto-save (QUAN TRỌNG NHẤT)

- [ ] Mở terminal, chạy: `php artisan serve`
- [ ] Mở trình duyệt: `http://127.0.0.1:8000`
- [ ] Đăng nhập tài khoản học sinh
- [ ] Chọn đề thi, bắt đầu làm bài
- [ ] Trả lời 1-2 câu hỏi
- [ ] Mở DevTools (F12) → Tab Network
- [ ] **ĐỢI 60 GIÂY** (quan trọng!)
- [ ] Kiểm tra có request: `POST /api/luu-nhap` → Status 200
- [ ] Kiểm tra màn hình hiện: "✓ Đã lưu tự động"
- [ ] Vào phpMyAdmin → Bảng `BaiLam` → Xem cột `DSCauTraLoi` có dữ liệu JSON
- [ ] Nhấn F5 (refresh), vào lại đề thi → Câu đã chọn vẫn còn

**Kết quả:** ✅ PASS / ❌ FAIL

---

### Test 2: Nộp bài

- [ ] Tiếp tục làm bài (hoặc làm bài mới)
- [ ] Trả lời đủ các câu hỏi
- [ ] Nhấn "Nộp bài"
- [ ] Kiểm tra response có: `Diem`, `SoCauDung`, `SoCauSai`
- [ ] Màn hình chuyển sang trang kết quả
- [ ] Vào phpMyAdmin → Bảng `KetQua` → Có bản ghi mới với điểm số

**Kết quả:** ✅ PASS / ❌ FAIL

---

### Test 3: Cheating Detection

- [ ] Bắt đầu làm bài thi
- [ ] Chuyển sang tab khác (Ctrl+Tab)
- [ ] Kiểm tra màn hình hiện cảnh báo
- [ ] Network tab có request: `POST /api/ghi-nhan-gian-lan`
- [ ] Vào phpMyAdmin → Bảng `BaiLam` → `SoLanViPham` tăng lên

**Kết quả:** ✅ PASS / ❌ FAIL

---

## 🗄️ BƯỚC 3: KIỂM TRA DATABASE (10 phút)

### Export Structure

- [ ] Vào `http://localhost/phpmyadmin`
- [ ] Chọn database của bạn
- [ ] Tab Export → Custom
- [ ] Bỏ tích "Data", chỉ để "Structure"
- [ ] Chọn các bảng: TaiKhoan, HocSinh, GiaoVien, DeThi, CauHoi, BaiLam, KetQua, DETHI_CAUHOI
- [ ] Export → Lưu file `CURRENT_DB_STRUCTURE.sql`
- [ ] Kéo file vào VS Code

### Chạy Script Kiểm tra

- [ ] Mở file `CHECK_DATABASE_STRUCTURE.sql`
- [ ] Copy toàn bộ nội dung
- [ ] Vào phpMyAdmin → Tab SQL
- [ ] Paste và chạy
- [ ] Xem kết quả:
  - [ ] Tất cả khóa chính là CHAR(10) ✅
  - [ ] BaiLam.DSCauTraLoi là JSON ✅
  - [ ] KetQua.Diem là FLOAT ✅
  - [ ] Foreign keys đầy đủ ✅

**Kết quả:** ✅ PASS / ❌ FAIL

---

## 🐛 BƯỚC 4: XỬ LÝ LỖI (Nếu có)

### Nếu Test 1 FAIL (Auto-save không hoạt động):

- [ ] Kiểm tra log: `storage/logs/laravel.log` (50 dòng cuối)
- [ ] Tìm dòng "LƯU NHÁP ERROR"
- [ ] Copy error message
- [ ] Hỏi AI:
  ```
  @BaiThiController.php @app.blade.php
  
  Auto-save không hoạt động. Log lỗi:
  [PASTE LOG Ở ĐÂY]
  
  Hãy phân tích và sửa.
  ```

### Nếu Test 2 FAIL (Nộp bài lỗi):

- [ ] Kiểm tra log: `storage/logs/laravel.log`
- [ ] Tìm dòng "NỘP BÀI THI ERROR"
- [ ] Hỏi AI với log đó

### Nếu Database sai cấu trúc:

- [ ] Chạy các lệnh ALTER TABLE trong file `CHECK_DATABASE_STRUCTURE.sql` (phần 8)
- [ ] Hoặc hỏi AI:
  ```
  @CHECK_DATABASE_STRUCTURE.sql @CURRENT_DB_STRUCTURE.sql
  
  Database hiện tại có vấn đề: [MÔ TẢ]
  
  Hãy viết lệnh SQL để sửa.
  ```

---

## ✅ BƯỚC 5: XÁC NHẬN HOÀN THÀNH

Tất cả các điều sau phải đúng:

- [x] Test 1: Auto-save → ✅ PASS
- [x] Test 2: Nộp bài → ✅ PASS
- [x] Test 3: Cheating → ✅ PASS
- [x] Database structure → ✅ Khớp với báo cáo
- [x] Không có lỗi trong log

**➡️ Nếu tất cả ✅ → HỆ THỐNG ĐẠT 100% YÊU CẦU BÁO CÁO**

---

## 📊 KẾT QUẢ ĐÁNH GIÁ

| Chức năng | Yêu cầu báo cáo | Trạng thái |
|-----------|-----------------|------------|
| Database CHAR(10) | Bắt buộc | ☐ ✅ / ☐ ❌ |
| Auto-save 60s | Bắt buộc | ☐ ✅ / ☐ ❌ |
| Auto-grading | Bắt buộc | ☐ ✅ / ☐ ❌ |
| Save KetQua ngay | Bắt buộc | ☐ ✅ / ☐ ❌ |
| Cheating Detection | Bắt buộc | ☐ ✅ / ☐ ❌ |

**Tổng điểm:** ___ / 5 ✅

**Kết luận:**
- 5/5 ✅ → 🎉 HOÀN THÀNH 100%
- 4/5 ✅ → ⚠️ Cần sửa 1 chức năng
- 3/5 ✅ → ❌ Cần review lại

---

## 🆘 LIÊN HỆ HỖ TRỢ

### Prompt hỏi AI khi gặp vấn đề:

```
@TONG_KET_HOAN_THANH.md @HUONG_DAN_DONG_BO_HE_THONG.md

Tôi đang ở bước [SỐ BƯỚC] và gặp vấn đề:
[MÔ TẢ CHI TIẾT]

Logs (nếu có):
[COPY LOG]

Hãy giúp tôi khắc phục.
```

---

## 📚 TÀI LIỆU THAM KHẢO

**Đọc theo thứ tự:**

1. `TONG_KET_HOAN_THANH.md` ← Bắt đầu từ đây
2. `HUONG_DAN_DONG_BO_HE_THONG.md` ← Hướng dẫn chi tiết
3. `REQUIREMENTS.md` ← Chuẩn từ báo cáo
4. `DATABASE_COMPARISON_REPORT.md` ← So sánh chi tiết
5. `FIX_LUU_NHAP_AUTO_SAVE.md` ← Chi tiết vấn đề đã sửa

---

**IN RA VÀ TICK ✓ KHI HOÀN THÀNH!**

**Chúc bạn thành công! 🚀**
