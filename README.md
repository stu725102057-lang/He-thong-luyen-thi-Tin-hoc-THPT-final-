# 🎓 Hệ Thống Luyện Thi THPT Môn Tin Học

> **Laravel-based Online Exam System for High School Computer Science**

[![Laravel](https://img.shields.io/badge/Laravel-10.x-red)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2-blue)](https://php.net)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-purple)](https://getbootstrap.com)
[![Chart.js](https://img.shields.io/badge/Chart.js-4.4-orange)](https://chartjs.org)
[![Status](https://img.shields.io/badge/Status-95%25%20Complete-success)]()

## 📋 Mục Lục

- [Giới thiệu](#giới-thiệu)
- [Tính năng](#tính-năng)
- [Công nghệ](#công-nghệ)
- [Cài đặt](#cài-đặt)
- [Sử dụng](#sử-dụng)
- [Tài liệu](#tài-liệu)
- [Tiến độ](#tiến-độ)
- [License](#license)

---

## 🎯 Giới Thiệu

**Hệ thống luyện thi THPT môn Tin học** là một ứng dụng web toàn diện giúp học sinh luyện tập và ôn thi môn Tin học THPT Quốc gia. Hệ thống được xây dựng với Laravel backend và SPA frontend hiện đại.

### Mục Tiêu
- ✅ Cung cấp môi trường luyện thi trực tuyến chất lượng cao
- ✅ Tự động chấm điểm và phân tích kết quả chi tiết
- ✅ Quản lý ngân hàng câu hỏi phong phú
- ✅ Hỗ trợ giáo viên và quản trị viên hiệu quả

### Vai Trò Người Dùng
- 👨‍🎓 **Học sinh**: Làm bài thi, xem kết quả, theo dõi tiến độ
- 👨‍🏫 **Giáo viên**: Quản lý câu hỏi, tạo đề thi, xem báo cáo
- 👨‍💼 **Admin**: Quản lý toàn hệ thống, backup/restore dữ liệu

---

## ✨ Tính Năng

### 👨‍🎓 Dành Cho Học Sinh

#### 1. Quản Lý Tài Khoản
- ✅ Đăng ký tài khoản mới
- ✅ Đăng nhập/Đăng xuất
- ✅ Quên mật khẩu (email recovery)
- ✅ Cập nhật thông tin cá nhân

#### 2. Làm Bài Thi
- ✅ **Chọn đề thi** từ danh sách
- ✅ **Tìm kiếm và lọc** theo độ khó, chủ đề
- ✅ **Preview đề thi** trước khi bắt đầu
- ✅ **Làm bài trực tuyến** với giao diện thân thiện
- ✅ **Tự động lưu** bài làm mỗi 60 giây
- ✅ **Countdown timer** theo thời gian quy định
- ✅ **Nộp bài** thủ công hoặc tự động khi hết giờ

#### 3. Xem Kết Quả
- ✅ Điểm số và xếp loại ngay lập tức
- ✅ Phân tích chi tiết từng câu hỏi
- ✅ Hiển thị đáp án đúng/sai
- ✅ Thời gian làm bài
- ✅ Tỷ lệ chính xác

#### 4. Thống Kê & Báo Cáo ⭐ NEW
- ✅ **Biểu đồ điểm theo thời gian** (Line chart)
- ✅ **Phân bố kết quả** (Pie chart: Xuất sắc/Giỏi/Khá/Yếu)
- ✅ **Điểm trung bình theo môn** (Bar chart)
- ✅ **Bảng lịch sử thi** (10 bài gần nhất)
- ✅ **4 chỉ số tổng quan**: Tổng bài thi, Điểm TB, Điểm cao nhất, Độ chính xác

#### 5. Chống Gian Lận
- ✅ Phát hiện chuyển tab (warning)
- ✅ Phát hiện fullscreen exit
- ✅ Ghi log các hành vi đáng ngờ
- ✅ Tự động nộp bài nếu vi phạm nhiều lần

---

### 👨‍🏫 Dành Cho Giáo Viên

#### 1. Quản Lý Câu Hỏi
- ✅ **Thêm câu hỏi** thủ công (form WYSIWYG)
- ✅ **Import câu hỏi** từ Excel/JSON
- ✅ **Sửa/Xóa** câu hỏi
- ✅ **Tìm kiếm** theo nội dung, độ khó, chủ đề
- ✅ **Phân loại** câu hỏi theo:
  - Độ khó: Dễ, Trung bình, Khó
  - Chủ đề: Tin học đại cương, Pascal, C++, Giải thuật, v.v.

#### 2. Quản Lý Đề Thi
- ✅ Tạo đề thi thủ công
- ✅ **Tạo đề ngẫu nhiên** ⭐ NEW
  - Chọn chủ đề
  - Chọn độ khó
  - Số lượng câu hỏi
  - Thời gian làm bài
  - Hệ thống tự động random câu hỏi phù hợp
- ✅ Chỉnh sửa đề thi
- ✅ Xóa đề thi

#### 3. Xem Báo Cáo
- ✅ Thống kê học sinh làm bài
- ✅ Điểm trung bình theo đề
- ✅ Phân tích độ khó câu hỏi

---

### 👨‍💼 Dành Cho Admin

#### 1. Dashboard ⭐ NEW
- ✅ **4 Stat Cards** với gradient đẹp mắt:
  - 👥 Tổng số người dùng (purple gradient)
  - 📝 Tổng số đề thi (pink gradient)
  - 📤 Tổng bài nộp (blue gradient)
  - ❓ Tổng câu hỏi (green gradient)
- ✅ **Biểu đồ hoạt động** (Line chart - 6 tháng gần nhất)
- ✅ **Biểu đồ phân bố người dùng** (Pie chart theo vai trò)
- ✅ **Bảng bài thi gần đây** (Recent submissions)
- ✅ **Cảnh báo hệ thống** (System health alerts)

#### 2. Quản Lý Người Dùng
- ✅ Xem danh sách tất cả người dùng
- ✅ Thêm người dùng mới (theo vai trò)
- ✅ Sửa thông tin người dùng
- ✅ Xóa người dùng
- ✅ Tìm kiếm và lọc
- ✅ Phân quyền: Admin, Giáo viên, Học sinh

#### 3. Backup & Restore ⭐ NEW
- ✅ **Backup Database**
  - Tạo backup với 1 click
  - Progress bar animated
  - Tự động đặt tên file theo timestamp
  - Lưu vào storage/app/backups/
- ✅ **Restore Database**
  - Upload file .sql
  - Xác nhận 2 lần (confirmation dialog)
  - Progress indicator
  - Tự động logout sau restore
- ✅ **Lịch sử Backup**
  - Bảng hiển thị tất cả backup
  - Thông tin: Thời gian, Dung lượng, Trạng thái
  - Download backup file
- ✅ **UI hiện đại** với action cards và table

---

## 🛠 Công Nghệ

### Backend
- **Framework**: Laravel 10.x
- **PHP**: 8.2+
- **Database**: MySQL/MariaDB
- **Authentication**: Laravel Sanctum (Token-based)
- **ORM**: Eloquent
- **Migration**: Database migrations & seeders

### Frontend
- **Architecture**: Single Page Application (SPA)
- **Framework**: Vanilla JavaScript (ES6+)
- **CSS Framework**: Bootstrap 5.3
- **Icons**: Bootstrap Icons 1.11
- **Charts**: Chart.js 4.4.0 ⭐ NEW
- **HTTP Client**: Fetch API (async/await)

### Tools & Libraries
- **Composer**: PHP dependency manager
- **NPM**: JavaScript package manager
- **Vite**: Frontend build tool
- **PhpUnit**: Testing framework (backend)
- **Laravel Tinker**: REPL for debugging

---

## 📦 Cài Đặt

### Yêu Cầu Hệ Thống
- PHP >= 8.2
- Composer >= 2.6
- MySQL/MariaDB >= 8.0
- Node.js >= 18.x (for Vite)
- Web Server: Apache/Nginx

### Bước 1: Clone Repository
```powershell
git clone https://github.com/your-repo/exam-system.git
cd exam-system
```

### Bước 2: Install Dependencies
```powershell
# Backend dependencies
composer install

# Frontend dependencies (if using Vite)
npm install
```

### Bước 3: Configure Environment
```powershell
# Copy .env file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Bước 4: Configure Database
Edit `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=exam_system
DB_USERNAME=root
DB_PASSWORD=your_password
```

### Bước 5: Run Migrations
```powershell
# Create database tables
php artisan migrate

# Seed sample data (optional)
php artisan db:seed
```

### Bước 6: Create Storage Directories
```powershell
# Create backup directory
mkdir storage\app\backups
```

### Bước 7: Start Development Server
```powershell
# Start Laravel server
php artisan serve

# (Optional) Start Vite dev server
npm run dev
```

### Bước 8: Access Application
Open browser: `http://localhost:8000`

---

## 🚀 Sử Dụng

### Tài Khoản Demo

#### Học sinh
- **Username**: `hocsinh1`
- **Password**: `password`

#### Giáo viên
- **Username**: `giaovien1`
- **Password**: `password`

#### Admin
- **Username**: `admin`
- **Password**: `password`

### Quick Start

#### 1. Học Sinh Làm Bài
```
1. Login → 2. Chọn đề thi → 3. Xác nhận → 4. Làm bài → 5. Nộp bài → 6. Xem kết quả → 7. Xem thống kê
```

#### 2. Giáo Viên Tạo Đề
```
1. Login → 2. Quản lý câu hỏi → 3. Click "Tạo đề ngẫu nhiên" → 4. Điền form → 5. Submit
```

#### 3. Admin Backup
```
1. Login → 2. Dashboard → 3. Click "Backup" → 4. Tạo backup → 5. Download file
```

---

## 📚 Tài Liệu

### Documentation Files

| File | Mô Tả |
|------|-------|
| `PROJECT_OVERVIEW.md` | Tổng quan dự án, kiến trúc hệ thống |
| `REQUIREMENTS_ANALYSIS.md` | Phân tích yêu cầu chi tiết |
| `API_SUMMARY.md` | Danh sách tất cả API endpoints |
| `API_ENDPOINTS_TODO.md` | ⭐ API cần implement (mới) |
| `AUTHENTICATION_COMPLETE.md` | Hệ thống xác thực hoàn chỉnh |
| `CHEATING_DETECTION_SUMMARY.md` | Chức năng chống gian lận |
| `FRONTEND_DOCUMENTATION.md` | Tài liệu frontend SPA |
| `FRONTEND_VISUAL_GUIDE.md` | Hướng dẫn giao diện |
| `SESSION_COMPLETE_FINAL.md` | ⭐ Báo cáo session (mới nhất) |
| `HUONG_DAN_TEST_FRONTEND.md` | ⭐ Hướng dẫn test (mới) |

### Quick References

| File | Mô Tả |
|------|-------|
| `QUICK_START_FRONTEND.md` | Quick start cho frontend |
| `QUICK_START_QUESTION_BANK.md` | Ngân hàng câu hỏi |
| `QUICK_START_USER_MANAGEMENT.md` | Quản lý người dùng |
| `QUICK_START_CHEATING_DETECTION.md` | Chống gian lận |

### Implementation Guides

| File | Mô Tả |
|------|-------|
| `IMPLEMENTATION_COMPLETE.md` | Tổng hợp triển khai |
| `HUONG_DAN_THEM_CAU_HOI.md` | Hướng dẫn thêm câu hỏi |
| `HUONG_DAN_THEM_NGUOI_DUNG.md` | Hướng dẫn thêm user |
| `HUONG_DAN_TICH_HOP_API.md` | Tích hợp API |

---

## 📊 Tiến Độ

### Overall Progress: **95%** ✅

```
█████████████████████░ 95%
```

### Module Status

| Module | Status | Completion |
|--------|--------|------------|
| 🔐 Authentication | ✅ Complete | 100% |
| 👨‍🎓 Student Features | ✅ Complete | 100% |
| 👨‍🏫 Teacher Features | ✅ Complete | 95% |
| 👨‍💼 Admin Features | ✅ Complete | 95% |
| 📊 Statistics & Charts | ✅ Complete | 100% |
| 🔒 Security | ⚠️ In Progress | 80% |
| 🎨 UI/UX | ✅ Complete | 95% |
| 📝 Documentation | ✅ Complete | 90% |

### Recent Updates (December 7, 2025) ⭐

#### ✅ Completed This Session
1. **Exam Selection UI** (Student)
   - Search and filter functionality
   - Preview before starting
   - Confirmation modal

2. **Statistics with Chart.js** (Student)
   - 3 chart types: Line, Pie, Bar
   - 4 summary stat cards
   - Recent exams table

3. **Admin Dashboard**
   - Gradient stat cards
   - Activity & user distribution charts
   - Recent submissions & alerts

4. **Random Exam Generation** (Teacher)
   - Full UI modal
   - Configurable parameters
   - Auto-select questions

5. **Backup & Restore System** (Admin)
   - Create backup with progress
   - Restore from file upload
   - Backup history table

### Next Steps (Remaining 5%)

- [ ] **Security Enhancements**
  - CSRF protection
  - Rate limiting
  - SQL injection prevention
  - XSS protection

- [ ] **Performance Optimization**
  - Database query optimization
  - Caching implementation
  - Lazy loading
  - Image optimization

- [ ] **Advanced Features**
  - Export reports (Excel/PDF)
  - Email notifications
  - Real-time notifications
  - Advanced analytics

- [ ] **Testing**
  - Unit tests (PHPUnit)
  - Integration tests
  - End-to-end tests
  - Load testing

---

## 🧪 Testing

### Manual Testing
```powershell
# Follow the test guide
# See: HUONG_DAN_TEST_FRONTEND.md

# Quick smoke test (7 minutes)
1. Login as student → Choose exam → Submit → Check stats
2. Login as teacher → Add question → Create random exam
3. Login as admin → View dashboard → Create backup
```

### Automated Testing (Coming Soon)
```powershell
# Run PHPUnit tests
php artisan test

# Run specific test
php artisan test --filter=ExamTest
```

---

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

---

## 📄 License

This project is licensed under the MIT License.

---

## 👥 Team

- **Backend Developer**: Laravel API, Database, Security
- **Frontend Developer**: SPA, UI/UX, Chart.js Integration
- **Project Manager**: Requirements, Documentation, Testing

---

## 📞 Contact

- **Email**: support@examapp.com
- **Website**: https://examapp.com
- **Issues**: https://github.com/your-repo/exam-system/issues

---

## 🎉 Acknowledgments

- Laravel Framework
- Bootstrap Team
- Chart.js Community
- Bootstrap Icons
- Stack Overflow Community

---

<p align="center">
Made with ❤️ for Vietnamese High School Students
</p>

<p align="center">
<b>Hệ thống luyện thi THPT môn Tin học - Version 1.0 (95% Complete)</b>
</p>

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
