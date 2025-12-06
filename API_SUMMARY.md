# 🎓 HỆ THỐNG THI TRẮC NGHIỆM - API DOCUMENTATION

## 📦 ĐÃ HOÀN THÀNH

### ✅ 1. DATABASE & MIGRATIONS
- ✅ 13 bảng đầy đủ theo thiết kế
- ✅ Khóa chính CHAR(10)
- ✅ Quan hệ kế thừa (TaiKhoan → HocSinh/GiaoVien/QuanTriVien)
- ✅ Bảng trung gian n-n (DETHI_CAUHOI)

### ✅ 2. ELOQUENT MODELS
- ✅ TaiKhoan (Authentication với Sanctum)
- ✅ QuanTriVien, GiaoVien, HocSinh
- ✅ NganHangCauHoi, CauHoi
- ✅ DeThi, BaiLam, KetQua
- ✅ Đầy đủ relationships

### ✅ 3. AUTHENTICATION (AuthController)
- ✅ `login()` - Đăng nhập với validation
- ✅ `logout()` - Đăng xuất
- ✅ `me()` - Lấy thông tin user
- ✅ Laravel Sanctum token authentication

### ✅ 4. CÂU HỎI CRUD (CauHoiController)
- ✅ `index()` - Danh sách (phân trang, filter)
- ✅ `store()` - Thêm mới (chỉ admin/giaovien)
- ✅ `show()` - Chi tiết
- ✅ `update()` - Cập nhật (chỉ admin/giaovien)
- ✅ `destroy()` - Xóa (chỉ admin/giaovien)

### ✅ 5. NỘP BÀI & CHẤM ĐIỂM (BaiThiController)
- ✅ `nopBai()` - Nộp bài và chấm điểm tự động
- ✅ Logic chấm điểm thang 10
- ✅ Lưu chi tiết vào BaiLam & KetQua
- ✅ `getKetQua()` - Xem kết quả bài làm

### ✅ 6. SEEDER DATA
- ✅ 3 tài khoản: Admin, Giáo viên, Học sinh
- ✅ 1 Ngân hàng câu hỏi
- ✅ 5 Câu hỏi mẫu tiếng Việt
- ✅ 1 Đề thi mẫu (5 câu, 30 phút)

---

## 🚀 API ENDPOINTS

### 🔓 PUBLIC ROUTES
```
POST   /api/login              Đăng nhập
```

### 🔒 PROTECTED ROUTES (Cần token)

#### Authentication
```
GET    /api/me                 Thông tin user hiện tại
POST   /api/logout             Đăng xuất
```

#### Câu hỏi (CRUD)
```
GET    /api/cauhoi             Danh sách câu hỏi
POST   /api/cauhoi             Thêm câu hỏi (admin/giaovien)
GET    /api/cauhoi/{id}        Chi tiết câu hỏi
PUT    /api/cauhoi/{id}        Cập nhật (admin/giaovien)
DELETE /api/cauhoi/{id}        Xóa (admin/giaovien)
```

#### Bài thi
```
POST   /api/baithi/nop         Nộp bài thi (học sinh)
GET    /api/baithi/{id}/ketqua Xem kết quả
```

---

## 👥 TÀI KHOẢN TEST

| Role | Username | Password | Quyền |
|------|----------|----------|-------|
| Admin | admin | 123456 | Full quyền |
| Giáo viên | giaovien1 | 123456 | Quản lý câu hỏi, đề thi |
| Học sinh | hocsinh1 | 123456 | Làm bài, xem kết quả |

---

## 📊 DỮ LIỆU MẪU

### Đề thi: DT001
- **Tên**: Kiểm tra Tin học đại cương
- **Thời gian**: 30 phút
- **Số câu**: 5 câu
- **Đáp án**: A, B, B, C, C

### Câu hỏi (CH001-CH005)
1. CPU là viết tắt của từ gì? → **A**
2. RAM là loại bộ nhớ nào? → **B**
3. Đơn vị nhỏ nhất của thông tin? → **B**
4. Hệ điều hành của Microsoft? → **C**
5. Hàm tính tổng trong Excel? → **C**

---

## 🧪 TEST WORKFLOW

### 1️⃣ Đăng nhập
```json
POST /api/login
{
  "TenDangNhap": "hocsinh1",
  "MatKhau": "123456"
}
```

### 2️⃣ Lấy token từ response
```json
{
  "data": {
    "token": "1|abcxyz..."
  }
}
```

### 3️⃣ Nộp bài thi
```json
POST /api/baithi/nop
Authorization: Bearer {token}

{
  "MaDe": "DT001",
  "CauTraLoi": [
    {"MaCH": "CH001", "DapAnChon": "A"},
    {"MaCH": "CH002", "DapAnChon": "B"},
    {"MaCH": "CH003", "DapAnChon": "B"},
    {"MaCH": "CH004", "DapAnChon": "C"},
    {"MaCH": "CH005", "DapAnChon": "C"}
  ]
}
```

### 4️⃣ Nhận kết quả ngay lập tức
```json
{
  "success": true,
  "data": {
    "Diem": 10.0,
    "SoCauDung": 5,
    "SoCauSai": 0,
    "TongSoCau": 5
  }
}
```

---

## 🔐 PHÂN QUYỀN

| Chức năng | Admin | Giáo viên | Học sinh |
|-----------|-------|-----------|----------|
| Đăng nhập | ✅ | ✅ | ✅ |
| Xem câu hỏi | ✅ | ✅ | ✅ |
| Thêm câu hỏi | ✅ | ✅ | ❌ |
| Sửa câu hỏi | ✅ | ✅ | ❌ |
| Xóa câu hỏi | ✅ | ✅ | ❌ |
| Nộp bài thi | ❌ | ❌ | ✅ |
| Xem kết quả | ✅ (all) | ✅ (all) | ✅ (own) |

---

## 📁 FILE STRUCTURE

```
app/
├── Http/Controllers/
│   ├── AuthController.php      ✅ Authentication
│   ├── CauHoiController.php    ✅ CRUD câu hỏi
│   └── BaiThiController.php    ✅ Nộp bài & chấm điểm
├── Models/
│   ├── TaiKhoan.php            ✅ Authentication model
│   ├── QuanTriVien.php         ✅
│   ├── GiaoVien.php            ✅
│   ├── HocSinh.php             ✅
│   ├── NganHangCauHoi.php      ✅
│   ├── CauHoi.php              ✅
│   ├── DeThi.php               ✅
│   ├── BaiLam.php              ✅
│   └── KetQua.php              ✅

database/
├── migrations/
│   └── 2025_12_06_..._create_all_tables.php  ✅ 13 bảng
└── seeders/
    └── DatabaseSeeder.php       ✅ Dữ liệu mẫu

routes/
└── api.php                      ✅ API routes

tests/
├── test-api.http                ✅ 18 test cases
└── HUONG_DAN_TEST_NOP_BAI.md   ✅ Documentation
```

---

## ⚙️ CHẠY PROJECT

### 1. Migration & Seed
```bash
php artisan migrate:refresh --seed
```

### 2. Start Server
```bash
php artisan serve
```

### 3. Test API
- Sử dụng Postman/Insomnia
- Hoặc REST Client với file `test-api.http`

---

## 🎯 TÍNH NĂNG NỔI BẬT

✨ **Chấm điểm tự động** - Kết quả ngay lập tức
✨ **Thang điểm 10** - Chuẩn Việt Nam
✨ **Chi tiết từng câu** - Xem đáp án đúng/sai
✨ **Phân quyền chặt chẽ** - Role-based access
✨ **Transaction safety** - Đảm bảo dữ liệu
✨ **API RESTful** - Chuẩn convention
✨ **Token authentication** - Laravel Sanctum
✨ **Validation đầy đủ** - Input validation

---

## 📖 DOCUMENTS

- `test-api.http` - 18 API test cases
- `HUONG_DAN_TEST_NOP_BAI.md` - Chi tiết chấm điểm
- `README.md` - Tổng quan project

---

## 🏆 KẾT QUẢ

✅ **Backend API hoàn chỉnh**
✅ **Database thiết kế chuẩn**
✅ **Authentication & Authorization**
✅ **CRUD đầy đủ**
✅ **Logic chấm điểm thông minh**
✅ **Test cases đầy đủ**

---

**🎓 Hệ thống sẵn sàng cho giai đoạn phát triển Frontend!**
