# 📘 HƯỚNG DẪN TEST API NỘP BÀI THI

## 🎯 Tính năng: Nộp bài thi và chấm điểm tự động

### ✅ Đã hoàn thành:

1. **BaiThiController** với hàm `nopBai()`
2. **Logic chấm điểm tự động**
3. **Lưu kết quả vào database**
4. **API Routes**

---

## 📋 BƯỚC 1: Đăng nhập lấy token

### Học sinh:
```http
POST http://localhost:8000/api/login
Content-Type: application/json

{
  "TenDangNhap": "hocsinh1",
  "MatKhau": "123456"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "token": "1|abcxyz...",
    "user": {
      "Role": "hocsinh"
    },
    "detail": {
      "MaHS": "HS001",
      "HoTen": "Trần Thị Bình"
    }
  }
}
```

**👉 Copy token để sử dụng cho các request tiếp theo!**

---

## 📋 BƯỚC 2: Nộp bài thi

### Request:
```http
POST http://localhost:8000/api/baithi/nop
Authorization: Bearer YOUR_TOKEN_HERE
Content-Type: application/json

{
  "MaDe": "DT001",
  "CauTraLoi": [
    {
      "MaCH": "CH001",
      "DapAnChon": "A"
    },
    {
      "MaCH": "CH002",
      "DapAnChon": "B"
    },
    {
      "MaCH": "CH003",
      "DapAnChon": "B"
    },
    {
      "MaCH": "CH004",
      "DapAnChon": "C"
    },
    {
      "MaCH": "CH005",
      "DapAnChon": "C"
    }
  ]
}
```

### Response - Điểm tối đa (10/10):
```json
{
  "success": true,
  "message": "Nộp bài thành công",
  "data": {
    "MaBaiLam": "BL00000001",
    "MaKQ": "KQ00000001",
    "Diem": 10.0,
    "SoCauDung": 5,
    "SoCauSai": 0,
    "SoCauKhongLam": 0,
    "TongSoCau": 5,
    "ThoiGianNop": "2025-12-06 20:45:00",
    "TenDe": "Kiểm tra Tin học đại cương",
    "HocSinh": {
      "MaHS": "HS001",
      "HoTen": "Trần Thị Bình"
    },
    "ChiTiet": [
      {
        "MaCH": "CH001",
        "DapAnChon": "A",
        "DapAnDung": "A",
        "KetQua": "Dung"
      },
      ...
    ]
  }
}
```

---

## 📊 LOGIC CHẤM ĐIỂM:

### 1. **Thu thập dữ liệu:**
- Nhận `MaDe` và mảng `CauTraLoi` từ client
- Validate dữ liệu đầu vào

### 2. **Lấy đáp án đúng:**
- Query từ bảng `CauHoi` dựa trên các câu trong `DeThi`
- Tạo mảng đáp án chuẩn

### 3. **So sánh và chấm điểm:**
```php
// Mỗi câu đúng
if ($dapAnDung[$maCH] === $dapAnChon) {
    $soCauDung++;
}

// Tính điểm (thang 10)
$diem = ($soCauDung / $tongSoCau) * 10;
```

### 4. **Lưu kết quả:**
- **Bảng `BaiLam`**: Lưu chi tiết câu trả lời (JSON)
- **Bảng `KetQua`**: Lưu điểm số, số câu đúng/sai

### 5. **Trả về kết quả ngay lập tức**

---

## 🧪 CÁC TRƯỜNG HỢP TEST:

### ✅ Case 1: Làm đúng hết (10 điểm)
- 5/5 câu đúng → Điểm: 10.0

### ✅ Case 2: Làm sai 1 câu (8 điểm)
```json
{
  "MaDe": "DT001",
  "CauTraLoi": [
    {"MaCH": "CH001", "DapAnChon": "A"},
    {"MaCH": "CH002", "DapAnChon": "B"},
    {"MaCH": "CH003", "DapAnChon": "A"},  // SAI
    {"MaCH": "CH004", "DapAnChon": "C"},
    {"MaCH": "CH005", "DapAnChon": "C"}
  ]
}
```
→ 4/5 câu đúng → Điểm: 8.0

### ✅ Case 3: Bỏ câu (không làm hết)
```json
{
  "MaDe": "DT001",
  "CauTraLoi": [
    {"MaCH": "CH001", "DapAnChon": "A"},
    {"MaCH": "CH002", "DapAnChon": "B"},
    {"MaCH": "CH003", "DapAnChon": "B"}
  ]
}
```
→ 3/5 câu đúng, 2 câu không làm → Điểm: 6.0

---

## 📌 ĐÁP ÁN ĐÚNG (Đề DT001):

| Câu | Nội dung | Đáp án |
|-----|----------|--------|
| CH001 | CPU là viết tắt của từ gì? | **A** |
| CH002 | RAM là loại bộ nhớ nào? | **B** |
| CH003 | Đơn vị nhỏ nhất của thông tin? | **B** |
| CH004 | Hệ điều hành của Microsoft? | **C** |
| CH005 | Hàm tính tổng trong Excel? | **C** |

---

## 🔍 XEM KẾT QUẢ BÀI LÀM:

```http
GET http://localhost:8000/api/baithi/{MaBaiLam}/ketqua
Authorization: Bearer YOUR_TOKEN_HERE
```

### Response:
```json
{
  "success": true,
  "data": {
    "BaiLam": {
      "MaBaiLam": "BL00000001",
      "Diem": 10.0,
      "ThoiGianNop": "2025-12-06 20:45:00",
      "TrangThai": "ChamDiem"
    },
    "ChiTietCauTraLoi": [...]
  }
}
```

---

## 🔒 PHÂN QUYỀN:

✅ **Học sinh**: Được nộp bài và xem kết quả của mình
✅ **Giáo viên/Admin**: Xem tất cả kết quả
❌ **Giáo viên/Admin**: Không được nộp bài (chỉ học sinh)

---

## 📊 DỮ LIỆU TRONG DATABASE:

### Bảng `BaiLam`:
```
MaBaiLam | MaHS  | MaDe  | Diem | DSCauTraLoi (JSON) | TrangThai
BL000001 | HS001 | DT001 | 10.0 | [{...}]            | ChamDiem
```

### Bảng `KetQua`:
```
MaKQ     | MaHS  | MaDe  | Diem | SoCauDung | SoCauSai | MaBaiLam
KQ000001 | HS001 | DT001 | 10.0 | 5         | 0        | BL000001
```

---

## ✨ TÍNH NĂNG NỔI BẬT:

✅ **Chấm điểm tự động** ngay lập tức
✅ **Thang điểm 10** chuẩn
✅ **Lưu chi tiết** từng câu trả lời
✅ **Transaction** đảm bảo dữ liệu nhất quán
✅ **Validation** đầy đủ
✅ **Phân quyền** chặt chẽ
✅ **Response** chi tiết, dễ hiểu

---

## 🎓 KẾT QUẢ MẪU:

```
Học sinh: Trần Thị Bình (HS001)
Đề thi: Kiểm tra Tin học đại cương (DT001)
Thời gian: 30 phút
Kết quả: 10/10 điểm ⭐⭐⭐⭐⭐
```
