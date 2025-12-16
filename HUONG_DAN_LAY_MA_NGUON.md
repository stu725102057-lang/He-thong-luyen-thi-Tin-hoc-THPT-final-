# 📦 HƯỚNG DẪN LẤY MÃ NGUỒN

## 🎯 HỆ THỐNG LUYỆN THI THPT QUỐC GIA MÔN TIN HỌC

---

## 📁 CẤU TRÚC THƯ MỤC DỰ ÁN

```
Hệ thống luyện thi THPT môn Tin học/
│
├── app/                          # Mã nguồn ứng dụng chính
│   ├── Http/
│   │   ├── Controllers/          # Các Controller xử lý logic
│   │   │   ├── AuthController.php
│   │   │   ├── CauHoiController.php
│   │   │   ├── BaiThiController.php
│   │   │   ├── DeThiController.php
│   │   │   ├── UserController.php
│   │   │   └── BackupController.php
│   │   └── Middleware/           # Middleware xử lý request
│   └── Models/                   # Eloquent Models
│       ├── TaiKhoan.php
│       ├── HocSinh.php
│       ├── GiaoVien.php
│       ├── CauHoi.php
│       ├── DeThi.php
│       └── BaiLam.php
│
├── database/                     # Database migrations & seeds
│   ├── migrations/               # Migration files
│   └── seeders/                  # Seeder files
│
├── public/                       # Thư mục public (Document Root)
│   ├── index.php                 # Entry point
│   ├── css/                      # CSS files
│   ├── js/                       # JavaScript files
│   └── images/                   # Hình ảnh
│
├── resources/                    # Tài nguyên frontend
│   └── views/
│       └── app.blade.php         # File giao diện chính (SPA)
│
├── routes/                       # Định nghĩa routes
│   ├── web.php                   # Web routes
│   └── api.php                   # API routes
│
├── storage/                      # Thư mục lưu trữ
│   ├── app/                      # File uploads
│   ├── logs/                     # Log files
│   └── framework/                # Cache, sessions
│
├── config/                       # File cấu hình
│   ├── database.php
│   ├── sanctum.php
│   └── cors.php
│
├── .env                          # Biến môi trường (QUAN TRỌNG)
├── .env.example                  # Mẫu file .env
├── composer.json                 # Quản lý thư viện PHP
├── composer.lock
├── artisan                       # Laravel CLI
├── DATABASE_EXPORT.sql           # File SQL export database
├── HUONG_DAN_CAI_DAT.md         # Hướng dẫn cài đặt
└── README.md                     # Thông tin dự án
```

---

## 💾 CÁCH LẤY MÃ NGUỒN

### Phương án 1: Tải trực tiếp (Khuyến nghị cho người mới)

#### Bước 1: Nén toàn bộ thư mục dự án
```bash
# Windows: Click phải vào thư mục -> Send to -> Compressed (zipped) folder
# Hoặc dùng 7-Zip, WinRAR

# Linux/Mac:
cd "d:\Hệ thống luyện thi THPT môn Tin học (mới)"
zip -r he-thong-thi-thpt.zip "Hệ thống luyện thi THPT môn Tin học"
```

#### Bước 2: Upload lên Google Drive hoặc Cloud Storage
1. Tạo file nén: `he-thong-thi-thpt.zip`
2. Upload lên Google Drive
3. Chia sẻ link với quyền "Anyone with the link can view"

#### Bước 3: Người nhận tải về và giải nén
```bash
# Tải file zip về
# Giải nén vào thư mục mong muốn
# Windows: Click phải -> Extract All
# Linux: unzip he-thong-thi-thpt.zip
```

---

### Phương án 2: Sử dụng Git (Khuyến nghị cho dev)

#### Bước 1: Khởi tạo Git Repository

```bash
# Di chuyển vào thư mục dự án
cd "d:\Hệ thống luyện thi THPT môn Tin học (mới)\Hệ thống luyện thi THPT môn Tin học"

# Khởi tạo Git
git init

# Tạo file .gitignore (nếu chưa có)
# Copy nội dung từ Laravel .gitignore template
```

#### Bước 2: Tạo file .gitignore
```bash
# Tạo file .gitignore với nội dung sau:
cat > .gitignore << 'EOF'
/node_modules
/public/hot
/public/storage
/storage/*.key
/vendor
.env
.env.backup
.phpunit.result.cache
Homestead.json
Homestead.yaml
npm-debug.log
yarn-error.log
/.idea
/.vscode
EOF
```

#### Bước 3: Commit code
```bash
# Add tất cả file
git add .

# Commit
git commit -m "Initial commit - Hệ thống luyện thi THPT Quốc gia môn Tin học"
```

#### Bước 4: Push lên GitHub

**Tạo repository trên GitHub:**
1. Đăng nhập GitHub: https://github.com
2. Click "New repository"
3. Tên: `he-thong-thi-thpt-tin-hoc`
4. Description: "Hệ thống luyện thi THPT Quốc gia môn Tin học"
5. Chọn "Private" hoặc "Public"
6. Click "Create repository"

**Push code lên GitHub:**
```bash
# Thêm remote
git remote add origin https://github.com/YOUR_USERNAME/he-thong-thi-thpt-tin-hoc.git

# Push code
git branch -M main
git push -u origin main
```

#### Bước 5: Clone về máy khác
```bash
# Clone repository
git clone https://github.com/YOUR_USERNAME/he-thong-thi-thpt-tin-hoc.git

# Di chuyển vào thư mục
cd he-thong-thi-thpt-tin-hoc

# Cài đặt dependencies
composer install

# Copy .env
cp .env.example .env

# Generate key
php artisan key:generate
```

---

### Phương án 3: Export từ VS Code

#### Bước 1: Sử dụng Extension
1. Cài extension "Export to Zip" trong VS Code
2. Click phải vào thư mục dự án
3. Chọn "Export to Zip"
4. Chọn vị trí lưu file

#### Bước 2: Loại trừ các thư mục không cần thiết
Đảm bảo không export:
- `node_modules/`
- `vendor/`
- `.git/`
- `storage/logs/*.log`
- `.env` (file này chứa thông tin nhạy cảm)

---

## 📤 ĐÓNG GÓI SẢN PHẨM HOÀN CHỈNH

### Chuẩn bị file cần thiết:

```
he-thong-thi-thpt-tin-hoc/
│
├── Source Code/
│   └── (Toàn bộ mã nguồn)
│
├── Database/
│   ├── DATABASE_EXPORT.sql          # Database đầy đủ
│   └── DATABASE_STRUCTURE_ONLY.sql  # Chỉ cấu trúc (không có data)
│
├── Documents/
│   ├── HUONG_DAN_CAI_DAT.md        # Hướng dẫn cài đặt
│   ├── HUONG_DAN_SU_DUNG.md        # Hướng dẫn sử dụng
│   ├── BAO_CAO_HE_THONG.md         # Báo cáo hệ thống
│   └── API_DOCUMENTATION.md        # Tài liệu API
│
├── .env.example                     # File cấu hình mẫu
└── README.md                        # Thông tin tổng quan
```

### Script tự động đóng gói (Windows PowerShell):

```powershell
# Tạo file package.ps1
$sourcePath = "d:\Hệ thống luyện thi THPT môn Tin học (mới)\Hệ thống luyện thi THPT môn Tin học"
$outputPath = "d:\he-thong-thi-thpt-package"
$zipFile = "d:\he-thong-thi-thpt-complete.zip"

# Tạo thư mục output
New-Item -ItemType Directory -Force -Path $outputPath

# Copy source code (loại trừ các thư mục không cần)
Copy-Item -Path "$sourcePath\*" -Destination "$outputPath\Source Code" -Recurse -Exclude node_modules,vendor,.git,storage\logs

# Copy database
Copy-Item -Path "$sourcePath\DATABASE_EXPORT.sql" -Destination "$outputPath\Database\"

# Copy documents
New-Item -ItemType Directory -Force -Path "$outputPath\Documents"
Copy-Item -Path "$sourcePath\*.md" -Destination "$outputPath\Documents\"

# Tạo file zip
Compress-Archive -Path $outputPath -DestinationPath $zipFile -Force

Write-Host "✅ Đóng gói hoàn tất: $zipFile"
```

### Script tự động đóng gói (Linux/Mac):

```bash
#!/bin/bash
# Tạo file package.sh

SOURCE_PATH="./Hệ thống luyện thi THPT môn Tin học"
OUTPUT_PATH="./he-thong-thi-thpt-package"
ZIP_FILE="he-thong-thi-thpt-complete.zip"

# Tạo thư mục output
mkdir -p "$OUTPUT_PATH/Source Code"
mkdir -p "$OUTPUT_PATH/Database"
mkdir -p "$OUTPUT_PATH/Documents"

# Copy source code (loại trừ các thư mục không cần)
rsync -av --exclude='node_modules' --exclude='vendor' --exclude='.git' \
     --exclude='storage/logs/*' "$SOURCE_PATH/" "$OUTPUT_PATH/Source Code/"

# Copy database
cp "$SOURCE_PATH/DATABASE_EXPORT.sql" "$OUTPUT_PATH/Database/"

# Copy documents
cp "$SOURCE_PATH"/*.md "$OUTPUT_PATH/Documents/"

# Tạo file zip
cd "$OUTPUT_PATH/.."
zip -r "$ZIP_FILE" "he-thong-thi-thpt-package"

echo "✅ Đóng gói hoàn tất: $ZIP_FILE"
```

---

## 📋 CHECKLIST TRƯỚC KHI GIAO SẢN PHẨM

### ✅ Mã nguồn:
- [ ] Đã loại bỏ file `.env` (chỉ giữ `.env.example`)
- [ ] Đã loại bỏ thư mục `vendor/` (sẽ cài lại bằng composer)
- [ ] Đã loại bỏ thư mục `node_modules/` (nếu có)
- [ ] Đã loại bỏ file log: `storage/logs/*.log`
- [ ] Đã xóa các file backup: `*.bak`, `*.tmp`
- [ ] Code đã được comment đầy đủ
- [ ] Không có thông tin nhạy cảm (password, API key)

### ✅ Database:
- [ ] File `DATABASE_EXPORT.sql` đã được export mới nhất
- [ ] Database có dữ liệu mẫu đầy đủ
- [ ] Tài khoản test đã được tạo (admin, giaovien, hocsinh)

### ✅ Tài liệu:
- [ ] `HUONG_DAN_CAI_DAT.md` - Hướng dẫn cài đặt chi tiết
- [ ] `HUONG_DAN_SU_DUNG.md` - Hướng dẫn sử dụng cho từng vai trò
- [ ] `README.md` - Thông tin tổng quan về dự án
- [ ] `API_DOCUMENTATION.md` - Tài liệu API (nếu có)

### ✅ File cấu hình:
- [ ] `.env.example` - Mẫu cấu hình đầy đủ với comment
- [ ] `composer.json` - Danh sách dependencies
- [ ] `config/` - Các file config đã được thiết lập đúng

---

## 🔑 THÔNG TIN TÀI KHOẢN MẪU

Để người nhận dễ dàng test, tạo file `ACCOUNTS.txt`:

```
========================================
TÀI KHOẢN TEST HỆ THỐNG
========================================

1. ADMIN:
   Username: admin
   Password: admin123
   Quyền: Quản trị toàn hệ thống

2. GIÁO VIÊN:
   Username: giaovien
   Password: 123456
   Quyền: Quản lý câu hỏi, đề thi, thống kê

3. HỌC SINH:
   Username: hocsinh
   Password: 123456
   Quyền: Làm bài thi, xem lịch sử

⚠️ LƯU Ý: Đổi mật khẩu ngay sau khi đăng nhập!
========================================
```

---

## 📨 GIAO SẢN PHẨM

### Cách 1: Gửi qua Google Drive
1. Upload file zip lên Google Drive
2. Chia sẻ với email người nhận
3. Gửi link download + mật khẩu (nếu có)

### Cách 2: Gửi qua WeTransfer
1. Truy cập: https://wetransfer.com
2. Upload file (miễn phí đến 2GB)
3. Nhập email người nhận
4. Họ sẽ nhận link download

### Cách 3: Git Repository (GitHub/GitLab)
1. Push code lên GitHub
2. Mời người nhận vào repository (Collaborator)
3. Họ clone về và cài đặt

### Cách 4: USB/Ổ cứng
1. Copy toàn bộ package vào USB
2. Giao trực tiếp

---

## 📞 HỖ TRỢ

Khi giao sản phẩm, cung cấp thông tin hỗ trợ:

```
========================================
THÔNG TIN HỖ TRỢ
========================================

📧 Email: your-email@example.com
📱 Hotline: 0123-456-789
💬 Zalo/Telegram: @your_username

🐛 Báo lỗi: GitHub Issues hoặc Email
📖 Tài liệu: Xem trong thư mục Documents/

⏰ Thời gian hỗ trợ:
   - Thứ 2 - Thứ 6: 8:00 - 17:00
   - Thứ 7 - CN: Theo lịch hẹn

========================================
```

---

## ✅ TỔNG KẾT

File cần giao:
1. ✅ **he-thong-thi-thpt-complete.zip** (Toàn bộ source + DB + docs)
2. ✅ **DATABASE_EXPORT.sql** (Database riêng biệt)
3. ✅ **ACCOUNTS.txt** (Tài khoản test)
4. ✅ **CONTACT.txt** (Thông tin liên hệ hỗ trợ)

**Kích thước ước tính:** 50-100 MB (không bao gồm vendor/)

---

*Ngày cập nhật: 14/12/2025*
*Phiên bản: 1.0.0*
