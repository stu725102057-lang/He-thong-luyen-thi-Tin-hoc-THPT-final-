# 📊 HƯỚNG DẪN SỬ DỤNG THỐNG KÊ CÁ NHÂN

## ✅ Tính năng đã có sẵn

Màn hình "**Thống kê cá nhân**" đã được tích hợp đầy đủ cho học sinh.

---

## 🎯 Cách sử dụng

### Bước 1: Đăng nhập bằng tài khoản học sinh

```
Tài khoản: hocsinh1
Mật khẩu: 123456
```

### Bước 2: Nhấn vào menu "Thống kê cá nhân"

Ở thanh menu phía trên, có 3 menu dành cho học sinh:
- 📋 **Danh sách đề thi**
- 🕒 **Lịch sử thi**
- 📊 **Thống kê cá nhân** ← Nhấn vào đây

---

## 📈 Các thông tin hiển thị

### 1. Tổng quan (4 thẻ)

```
┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐
│  📝 Tổng bài làm │  │  🏆 Điểm TB     │  │  ✅ Tỷ lệ đúng  │  │  ⭐ Điểm cao nhất│
│       15         │  │      7.8        │  │      85%        │  │       9.5       │
└─────────────────┘  └─────────────────┘  └─────────────────┘  └─────────────────┘
```

### 2. Biểu đồ điểm số theo thời gian (Line Chart)

- **Trục X:** Ngày làm bài (dd/mm)
- **Trục Y:** Điểm số (0-10)
- **Tooltip:** Hiển thị tên đề thi và điểm khi hover

**Ý nghĩa:** Xem xu hướng tiến bộ theo thời gian

### 3. Biểu đồ tỷ lệ đúng/sai (Doughnut Chart)

- **Xanh:** Số câu trả lời đúng
- **Đỏ:** Số câu trả lời sai
- **Tooltip:** Hiển thị số lượng và phần trăm

**Ý nghĩa:** Đánh giá tổng thể độ chính xác

### 4. Phân tích theo chuyên đề (Bar Chart)

```
Python Cơ Bản      ████████████ 85% (Xanh - Mạnh)
Hàm và Thủ tục     ██████████   70% (Vàng - TB)
Mảng và Chuỗi      ████         40% (Đỏ - Yếu)
```

- **Sắp xếp:** Từ yếu nhất đến mạnh nhất (thấp → cao)
- **Màu sắc:**
  - 🔴 Đỏ: < 60% (Cần cải thiện)
  - 🟡 Vàng: 60-79% (Trung bình)
  - 🟢 Xanh: ≥ 80% (Tốt)

**Ý nghĩa:** Biết được chuyên đề nào cần ôn tập thêm

---

## 🔧 API Backend

### Endpoint
```
GET /api/thong-ke/ca-nhan
```

### Headers
```
Authorization: Bearer {token}
Accept: application/json
```

### Response
```json
{
  "success": true,
  "message": "Lấy thống kê cá nhân thành công",
  "data": {
    "tongSoBaiLam": 15,
    "diemTrungBinh": 7.85,
    "tiLeDung": 85.5,
    "diemCaoNhat": 9.5,
    "diemThapNhat": 5.0,
    "lichSuDiem": [
      {
        "ngay": "01/12",
        "diem": 7.5,
        "tenDe": "Đề thi Python Cơ Bản"
      }
    ],
    "tyLeDungSai": {
      "dung": 170,
      "sai": 30
    },
    "chuyenDe": [
      {
        "tenChuyenDe": "Mảng & Chuỗi",
        "tyLeDung": 40.5,
        "soCauDung": 15,
        "tongSoCau": 37
      },
      {
        "tenChuyenDe": "Python Cơ Bản",
        "tyLeDung": 85.7,
        "soCauDung": 48,
        "tongSoCau": 56
      }
    ]
  }
}
```

---

## 💡 Lưu ý

### Điều kiện hiển thị:
- ✅ Học sinh phải làm **ít nhất 1 bài thi** mới có dữ liệu
- ✅ Nếu chưa làm bài nào → Hiển thị thông báo "Chưa có dữ liệu"

### Cập nhật dữ liệu:
- 🔄 Tự động cập nhật mỗi lần vào màn hình
- 🔄 Không cần làm mới trang

### Tương thích:
- 📱 Responsive trên mobile
- 💻 Hoạt động tốt trên desktop
- 🎨 Sử dụng Chart.js version 4.4.0

---

## 🐛 Troubleshooting

### Vấn đề: Không có dữ liệu hiển thị

**Nguyên nhân:** Học sinh chưa làm bài thi nào

**Giải pháp:**
1. Vào "Danh sách đề thi"
2. Chọn 1 đề thi
3. Làm bài và nộp bài
4. Quay lại "Thống kê cá nhân"

### Vấn đề: Biểu đồ không hiển thị

**Nguyên nhân:** Chart.js chưa load

**Giải pháp:**
1. Mở Console (F12)
2. Kiểm tra có lỗi Chart.js không
3. Làm mới trang (Ctrl+F5)

### Vấn đề: API trả về lỗi 401

**Nguyên nhân:** Token hết hạn

**Giải pháp:**
1. Đăng xuất
2. Đăng nhập lại
3. Thử lại

---

## 📝 Các file liên quan

### Backend
```
app/Http/Controllers/BaiThiController.php
- Method: thongKeCanhan()
- Line: 540-664
```

### Routes
```
routes/api.php
- Route: GET /api/thong-ke/ca-nhan
- Line: 99
```

### Frontend
```
resources/views/app.blade.php
- Screen HTML: Line 1578-1656
- Function: loadThongKeCanhan()
- Function: Line 4237-4396
```

---

## ✨ Demo

Sau khi làm một vài bài thi, màn hình thống kê sẽ trông như thế này:

```
┌──────────────────────────────────────────────────────────┐
│ 📊 Thống kê tiến độ cá nhân                              │
├──────────────────────────────────────────────────────────┤
│                                                           │
│  [15 bài]  [7.8 điểm]  [85%]  [9.5 max]                 │
│                                                           │
│  ┌─────────────────────┐  ┌─────────────────┐           │
│  │ Điểm theo thời gian │  │ Tỷ lệ đúng/sai │           │
│  │                     │  │                 │           │
│  │      /‾\            │  │   ●●●● Đúng    │           │
│  │     /   \    /\     │  │   ●    Sai     │           │
│  │    /     \__/  \    │  │                 │           │
│  │___/____________     │  │   85% - 15%    │           │
│  │ 1/12  5/12  8/12   │  │                 │           │
│  └─────────────────────┘  └─────────────────┘           │
│                                                           │
│  ┌───────────────────────────────────────────┐           │
│  │ Phân tích chuyên đề (Yếu → Mạnh)         │           │
│  │                                           │           │
│  │ Mảng & Chuỗi      ████░░░░░░ 40% 🔴      │           │
│  │ Hàm               ██████░░░░ 60% 🟡      │           │
│  │ Điều kiện         ████████░░ 80% 🟢      │           │
│  │ Python Cơ Bản     █████████░ 90% 🟢      │           │
│  └───────────────────────────────────────────┘           │
└──────────────────────────────────────────────────────────┘
```

---

**Trạng thái:** ✅ ĐÃ HOÀN THÀNH 100%  
**Ngày tạo:** 08/12/2025  
**Developer:** GitHub Copilot
