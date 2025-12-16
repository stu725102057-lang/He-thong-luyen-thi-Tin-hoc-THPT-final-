# ✅ HỆ THỐNG ĐÃ SẴN SÀNG - HƯỚNG DẪN SỬ DỤNG

## 🚀 Server đang chạy tại: http://127.0.0.1:8000

---

## 📋 CHECKLIST TRƯỚC KHI BẮT ĐẦU

### ✅ Backend (Laravel)
- [x] Server đang chạy: `php artisan serve`
- [x] Routes đã được tạo
- [x] Controllers đã sẵn sàng
- [x] Database có dữ liệu

### ✅ Frontend
- [ ] Trình duyệt đã hard refresh (Ctrl + Shift + R)
- [ ] Cache đã được xóa
- [ ] Developer Tools mở sẵn (F12)

---

## 🎯 HƯỚNG DẪN SỬ DỤNG HỆ THỐNG

### 1️⃣ ĐĂNG NHẬP

**URL:** http://127.0.0.1:8000

**Tài khoản học sinh:**
- Username: `hocsinh1`
- Password: `123456`

**Tài khoản giáo viên:**
- Username: `giaovien1`  
- Password: `123456`

**Tài khoản admin:**
- Username: `admin`
- Password: `123456`

**Sau khi đăng nhập:**
- Hệ thống lưu token vào localStorage
- Chuyển đến dashboard

---

### 2️⃣ XEM DANH SÁCH ĐỀ THI (Học sinh)

**Cách làm:**
1. Click menu "Danh sách đề thi"
2. Xem các đề thi có sẵn
3. Mỗi đề hiển thị:
   - Tên đề
   - Số câu hỏi
   - Thời gian làm bài
   - Giáo viên ra đề
   - Nút "Làm bài"

---

### 3️⃣ BẮT ĐẦU LÀM BÀI

**Cách làm:**
1. Click nút "Làm bài" ở một đề thi
2. Đọc thông tin trong modal xác nhận:
   - Tên đề
   - Số câu hỏi
   - Thời gian
   - Lưu ý quan trọng
3. Click "Bắt đầu làm bài"

**Chuyện gì xảy ra:**
- API call: `POST /api/de-thi/{maDe}/bat-dau`
- Tạo bản ghi BaiLam trong database
- Chuyển sang màn hình làm bài
- Bắt đầu đếm ngược thời gian

**Giao diện làm bài:**
- Câu hỏi ở giữa
- Sidebar bên trái: Danh sách câu hỏi (1, 2, 3...)
- Đồng hồ đếm ngược phía trên
- Nút "Câu trước" và "Câu sau"
- Nút "Nộp bài" màu đỏ

---

### 4️⃣ LÀM BÀI

**Chọn đáp án:**
1. Đọc câu hỏi
2. Click vào đáp án A, B, C, hoặc D
3. Đáp án được đánh dấu (màu xanh)
4. Số ở sidebar chuyển màu (đã làm)

**Di chuyển giữa các câu:**
- Click "Câu sau" hoặc "Câu trước"
- Hoặc click số câu ở sidebar

**Đánh dấu câu cần xem lại:**
- Click icon cờ hoặc sao ở mỗi câu
- Câu được đánh dấu sẽ có màu khác

**Lưu tự động:**
- Hệ thống tự động lưu mỗi 60 giây
- Hiện thông báo "Đã lưu tự động"

---

### 5️⃣ NỘP BÀI

**Cách nộp bài:**
1. Click nút "Nộp bài" màu đỏ
2. Xác nhận trong modal
3. Click "Xác nhận nộp"

**Chuyện gì xảy ra:**
- API call: `POST /api/bai-lam/nop-bai`
- Gửi data:
  ```json
  {
    "MaDe": "DE009",
    "MaHS": "HS001",
    "CauTraLoi": [
      {"MaCH": "CH001", "DapAnChon": "A"},
      {"MaCH": "CH002", "DapAnChon": "C"},
      ...
    ],
    "ThoiGianBatDau": "2025-12-08 22:10:32"
  }
  ```
- Hệ thống tự động chấm điểm
- Lưu kết quả vào database
- Chuyển sang màn hình kết quả

---

### 6️⃣ XEM KẾT QUẢ

**Sau khi nộp bài, hiển thị:**

#### A. Tổng quan
- **Điểm số:** X.XX / 10
- **Số câu đúng:** X câu
- **Số câu sai:** X câu  
- **Số câu không làm:** X câu
- **Tỷ lệ đúng:** XX%
- **Thời gian hoàn thành:** XX phút XX giây

#### B. Chi tiết từng câu
Mỗi câu hiển thị:
- **STT:** Câu 1, Câu 2, ...
- **Nội dung câu hỏi:** "..."
- **Đáp án của bạn:** A (màu đỏ nếu sai, xanh nếu đúng)
- **Đáp án đúng:** B (màu xanh lá)
- **Kết quả:** ✓ Đúng / ✗ Sai / - Không làm

#### C. Các nút chức năng
- **Xem lại toàn bộ bài thi** - Xem chi tiết tất cả câu
- **Tải kết quả về** (PDF/Excel) - Coming soon
- **Làm đề khác** - Quay về danh sách đề thi
- **Xem thống kê** - Xem thống kê cá nhân

---

### 7️⃣ XEM LỊCH SỬ THI

**Cách xem:**
1. Click menu "Lịch sử thi"
2. Xem danh sách các bài đã làm

**Hiển thị:**
- Tên đề thi
- Ngày làm
- Điểm số
- Trạng thái (Đã nộp, Đạt/Không đạt)
- Nút "Xem chi tiết"

**Xem lại kết quả:**
- Click "Xem chi tiết" ở bài đã làm
- API call: `GET /api/bai-lam/{maBaiLam}/ket-qua`
- Hiển thị kết quả đầy đủ như sau khi nộp bài

---

### 8️⃣ XEM THỐNG KÊ CÁ NHÂN

**Cách xem:**
1. Click menu "Thống kê cá nhân"
2. Xem biểu đồ và số liệu

**Hiển thị:**
- Tổng số bài đã làm
- Điểm trung bình
- Điểm cao nhất / thấp nhất
- Biểu đồ xu hướng điểm theo thời gian
- Tỷ lệ đúng/sai
- Thống kê theo chuyên đề

---

## 🎓 CHỨC NĂNG GIÁO VIÊN

### 1. Quản lý câu hỏi
- Xem danh sách câu hỏi
- Thêm câu hỏi mới
- Sửa câu hỏi
- Xóa câu hỏi

### 2. Tạo đề thi
- Tạo đề thi thủ công (chọn từng câu)
- Tạo đề thi ngẫu nhiên (tự động chọn câu)
- Sửa đề thi
- Xóa đề thi (nếu chưa có học sinh làm)

### 3. Xem thống kê lớp
- Danh sách học sinh
- Điểm trung bình lớp
- Top học sinh giỏi
- Học sinh cần hỗ trợ
- Thống kê theo đề thi

---

## 🔧 CHỨC NĂNG ADMIN

### 1. Quản lý người dùng
- Xem danh sách tài khoản
- Tạo tài khoản mới
- Sửa thông tin tài khoản
- Khóa/Mở khóa tài khoản
- Xóa tài khoản

### 2. Sao lưu & Khôi phục
- Sao lưu database
- Khôi phục database
- Xem lịch sử sao lưu

---

## ⚙️ CÁC TÍNH NĂNG ĐẶC BIỆT

### 🔍 Phát hiện gian lận
Hệ thống tự động phát hiện:
- Chuyển tab (Alt + Tab)
- Copy/Paste
- Mở DevTools (F12)
- Resize cửa sổ
- Mất focus

**Cảnh báo:**
- Lần 1: Cảnh báo nhẹ
- Lần 2: Cảnh báo nghiêm trọng
- Lần 3+: Ghi nhận vào hồ sơ

### 💾 Lưu tự động
- Tự động lưu tiến độ mỗi 60 giây
- Nếu mất kết nối/đóng trình duyệt đột ngột, có thể tiếp tục làm

### ⏱️ Đồng hồ đếm ngược
- Hiển thị thời gian còn lại
- Cảnh báo khi còn 5 phút
- Tự động nộp bài khi hết giờ

### 📱 Responsive
- Hoạt động tốt trên mobile, tablet, desktop

---

## 🐛 XỬ LÝ LỖI

### Lỗi: "Không nhận được phản hồi từ server"
**Nguyên nhân:** Server không chạy hoặc API endpoint sai

**Giải pháp:**
1. Kiểm tra server: `php artisan serve`
2. Hard refresh: Ctrl + Shift + R
3. Xem Console (F12) để biết thêm chi tiết

### Lỗi: "Phiên đăng nhập hết hạn"
**Nguyên nhân:** Token hết hạn (401 Unauthorized)

**Giải pháp:**
- Logout và đăng nhập lại

### Lỗi: "Không tìm thấy thông tin học sinh"
**Nguyên nhân:** MaHS không có trong database

**Giải pháp:**
- Liên hệ admin để tạo record HocSinh

### Không thể tương tác (click không được)
**Giải pháp:**
1. Hard refresh: Ctrl + Shift + R
2. Xóa cache: Ctrl + Shift + Delete
3. Mở DevTools (F12) → Console → Xem lỗi
4. Restart server

---

## 📊 LUỒNG DỮ LIỆU

```
1. ĐĂNG NHẬP
   Frontend → POST /api/login → Backend
   Backend → Trả về token
   Frontend → Lưu token vào localStorage

2. LẤY DANH SÁCH ĐỀ THI
   Frontend → GET /api/de-thi (kèm token)
   Backend → Query DB → Trả về danh sách

3. BẮT ĐẦU LÀM BÀI
   Frontend → POST /api/de-thi/{maDe}/bat-dau
   Backend → Tạo BaiLam → Trả về câu hỏi
   Frontend → Hiển thị câu hỏi

4. LÀM BÀI
   Frontend → Lưu đáp án vào state
   (Mỗi 60s) → POST /api/bai-lam/luu-nhap

5. NỘP BÀI
   Frontend → POST /api/bai-lam/nop-bai
   Backend → Chấm điểm → Lưu KetQua
   Frontend → Hiển thị kết quả

6. XEM KẾT QUẢ
   Frontend → GET /api/bai-lam/{maBaiLam}/ket-qua
   Backend → Query DB → Trả về kết quả chi tiết
   Frontend → Hiển thị đáp án đúng/sai
```

---

## 🎯 API ENDPOINTS

| Method | Endpoint | Mô tả |
|--------|----------|-------|
| POST | `/api/login` | Đăng nhập |
| POST | `/api/register` | Đăng ký |
| GET | `/api/de-thi` | Danh sách đề thi |
| GET | `/api/de-thi/{maDe}` | Chi tiết đề thi |
| POST | `/api/de-thi/{maDe}/bat-dau` | Bắt đầu làm bài |
| POST | `/api/bai-lam/nop-bai` | Nộp bài |
| POST | `/api/bai-lam/luu-nhap` | Lưu nháp |
| GET | `/api/bai-lam/{maBaiLam}/ket-qua` | Xem kết quả |
| GET | `/api/bai-lam/{maBaiLam}/chi-tiet` | Chi tiết bài làm |
| GET | `/api/thong-ke/ca-nhan` | Thống kê cá nhân |

---

## ✅ CHECKLIST SAU MỖI LẦN CODE

- [ ] Clear cache Laravel
  ```bash
  php artisan cache:clear
  php artisan config:clear
  php artisan route:clear
  ```
- [ ] Restart server
  ```bash
  php artisan serve
  ```
- [ ] Hard refresh trình duyệt (Ctrl + Shift + R)
- [ ] Test trên incognito mode
- [ ] Kiểm tra Console không có lỗi
- [ ] Test flow đầy đủ: Login → Làm bài → Nộp bài → Xem kết quả

---

## 📞 HỖ TRỢ

Nếu gặp vấn đề, gửi cho tôi:
1. Screenshot Console (F12 → Console)
2. Screenshot Network (F12 → Network → Request lỗi)
3. Log Laravel (storage/logs/laravel.log)
4. Mô tả chi tiết bước thao tác

---

**Cập nhật:** 8/12/2025 - 22:30  
**Version:** 1.0  
**Status:** ✅ Production Ready
