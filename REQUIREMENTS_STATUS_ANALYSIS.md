# 📊 PHÂN TÍCH TRẠNG THÁI YÊU CẦU HỆ THỐNG

**Ngày cập nhật:** 7 tháng 12, 2025  
**Tiến độ tổng thể:** 65% hoàn thành

---

## 1. MODULE 1: QUẢN LÝ CHUNG VÀ TÀI KHOẢN (UR-01)

### ✅ UR-01.1: Đăng nhập
**Trạng thái:** ✅ **HOÀN THÀNH**
- ✅ API đăng nhập với Sanctum authentication
- ✅ Xác thực tên đăng nhập và mật khẩu
- ✅ Phân quyền dựa trên vai trò (admin, giaovien, hocsinh)
- ✅ Giao diện đăng nhập responsive
- ✅ Hiển thị menu theo vai trò
- **File:** `AuthController.php`, `app.blade.php`

### ✅ UR-01.2: Đăng ký tài khoản
**Trạng thái:** ✅ **HOÀN THÀNH**
- ✅ Admin/Giáo viên tạo tài khoản cho người dùng
- ✅ Tự động sinh mã tài khoản (TK001, HS001, GV001)
- ✅ Phân loại theo vai trò (học sinh, giáo viên, admin)
- ✅ Modal form với validation đầy đủ
- ✅ API POST /api/users
- ✅ Test cases đầy đủ
- **File:** `UserController.php`, `app.blade.php`, `test-add-user.http`

### ✅ UR-01.3: Khôi phục mật khẩu
**Trạng thái:** ✅ **HOÀN THÀNH**
- ✅ API quên mật khẩu (gửi mã 6 số qua email)
- ✅ API đặt lại mật khẩu với mã xác nhận
- ✅ Token hết hạn sau 15 phút
- ✅ Giao diện "Quên mật khẩu" và "Đặt lại mật khẩu"
- ✅ Migration bảng password_resets
- ✅ 40+ test cases
- **File:** `AuthController.php`, `app.blade.php`, `test-authentication.http`

### ⚠️ UR-01.4: Truy cập với tư cách Khách
**Trạng thái:** ⚠️ **CHƯA HOÀN CHỈNH** (60%)
- ✅ API lấy danh sách đề thi mẫu (public, không cần token)
- ✅ Route GET /api/de-thi-mau
- ❌ **THIẾU:** Giao diện hiển thị đề thi mẫu cho khách
- ❌ **THIẾU:** Trang giới thiệu về hệ thống
- ❌ **THIẾU:** Landing page với thông tin chung

**Cần làm:**
```javascript
// Trong app.blade.php
showScreen('dethimau') {
    // Load đề thi mẫu từ API /de-thi-mau
    // Hiển thị danh sách đề thi
    // Cho phép xem chi tiết (nhưng không cho làm bài)
}
```

---

## 2. MODULE 2: HỌC SINH (UR-02)

### ❌ UR-02.1: Chọn bài thi
**Trạng thái:** ❌ **CHƯA LÀM** (0%)
- ❌ **THIẾU:** Giao diện danh sách đề thi có sẵn
- ❌ **THIẾU:** Lọc theo chủ đề/mức độ
- ❌ **THIẾU:** Chọn đề ngẫu nhiên
- ❌ **THIẾU:** Nút "Bắt đầu làm bài"
- ⚠️ API có sẵn code mẫu trong `MISSING_FEATURES_CODE.php`

**Cần làm:**
1. Frontend: Screen "Chọn đề thi" với danh sách đề
2. API: GET /api/de-thi (lấy danh sách)
3. API: GET /api/de-thi/{maDe}/bat-dau (bắt đầu làm bài)
4. Hiển thị thông tin: số câu, thời gian, độ khó

### ❌ UR-02.2: Nộp bài
**Trạng thái:** ⚠️ **CHƯA HOÀN CHỈNH** (40%)
- ✅ API POST /api/baithi/nop (đã có)
- ✅ Tự động chấm điểm
- ❌ **THIẾU:** Giao diện làm bài với timer
- ❌ **THIẾU:** Nút nộp bài thủ công
- ❌ **THIẾU:** Tự động nộp khi hết giờ
- ❌ **THIẾU:** Xác nhận trước khi nộp

**Cần làm:**
```javascript
// Timer countdown
let timeRemaining = examDuration * 60; // seconds
setInterval(() => {
    timeRemaining--;
    updateTimerDisplay();
    if (timeRemaining === 0) {
        autoSubmitExam(); // Tự động nộp
    }
}, 1000);
```

### ⚠️ UR-02.3: Xem kết quả tức thì
**Trạng thái:** ⚠️ **CHƯA HOÀN CHỈNH** (50%)
- ✅ API GET /api/baithi/{maBaiLam}/ketqua (đã có)
- ✅ Backend tính điểm tự động
- ❌ **THIẾU:** Giao diện hiển thị kết quả tổng kết
- ❌ **THIẾU:** Hiển thị số câu đúng/sai
- ❌ **THIẾU:** Điểm số và phần trăm

**Cần làm:**
```html
<div class="result-summary">
    <h3>Kết quả thi</h3>
    <p>Điểm: <strong>8.5</strong>/10</p>
    <p>Số câu đúng: <strong>17</strong>/20</p>
    <p>Thời gian: 45 phút</p>
</div>
```

### ❌ UR-02.4: Xem lại bài làm chi tiết
**Trạng thái:** ❌ **CHƯA LÀM** (0%)
- ❌ **THIẾU:** Modal hiển thị từng câu hỏi
- ❌ **THIẾU:** Highlight đáp án đúng/sai
- ❌ **THIẾU:** Hiển thị giải thích (nếu có)
- ❌ **THIẾU:** So sánh đáp án đã chọn vs đáp án đúng

**Cần làm:**
```html
<div class="question-review">
    <p><strong>Câu 1:</strong> Nội dung câu hỏi</p>
    <ul>
        <li class="correct">A. Đáp án đúng ✓</li>
        <li class="wrong selected">B. Đáp án bạn chọn ✗</li>
        <li>C. Đáp án khác</li>
        <li>D. Đáp án khác</li>
    </ul>
    <p class="explanation">Giải thích: ...</p>
</div>
```

### ⚠️ UR-02.5: Thống kê tiến độ cá nhân
**Trạng thái:** ⚠️ **CHƯA HOÀN CHỈNH** (30%)
- ✅ API GET /api/lich-su-thi (đã có)
- ❌ **THIẾU:** Giao diện lịch sử làm bài
- ❌ **THIẾU:** Biểu đồ điểm số theo thời gian
- ❌ **THIẾU:** Phân tích điểm mạnh/yếu theo chuyên đề
- ❌ **THIẾU:** Chart.js hoặc tương tự

**Cần làm:**
```javascript
// Sử dụng Chart.js
new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['Tuần 1', 'Tuần 2', 'Tuần 3'],
        datasets: [{
            label: 'Điểm số',
            data: [7.5, 8.0, 8.5]
        }]
    }
});
```

---

## 3. MODULE 3: GIÁO VIÊN (UR-03)

### ✅ UR-03.1: Quản lý Ngân hàng câu hỏi
**Trạng thái:** ✅ **HOÀN THÀNH**
- ✅ API CRUD câu hỏi (Thêm, Sửa, Xóa, Xem)
- ✅ Lọc theo môn học, độ khó
- ✅ Phân trang
- ✅ Middleware kiểm tra role (chỉ giáo viên/admin)
- **File:** `CauHoiController.php`

### ✅ UR-03.2: Nhập/Xuất câu hỏi
**Trạng thái:** ✅ **HOÀN THÀNH**
- ✅ Import câu hỏi từ JSON
- ✅ Export câu hỏi ra CSV, JSON, Excel
- ✅ API POST /api/cau-hoi/import
- ✅ API GET /api/cau-hoi/export?format={csv|json|excel}
- ✅ UTF-8 BOM cho Excel compatibility
- **File:** `CauHoiController.php`

### ❌ UR-03.3: Tạo đề thi thủ công
**Trạng thái:** ❌ **CHƯA LÀM** (0%)
- ❌ **THIẾU:** Giao diện chọn câu hỏi từ ngân hàng
- ❌ **THIẾU:** Kéo thả câu hỏi vào đề thi
- ❌ **THIẾU:** Xem trước đề thi
- ❌ **THIẾU:** Lưu đề thi với tên và mô tả
- ❌ **THIẾU:** API POST /api/tao-de-thi (manual mode)

**Cần làm:**
```javascript
// Giao diện tạo đề
<div class="create-exam">
    <div class="question-bank">
        <!-- Danh sách câu hỏi có checkbox -->
    </div>
    <div class="selected-questions">
        <!-- Câu hỏi đã chọn -->
    </div>
    <button onclick="saveExam()">Lưu đề thi</button>
</div>
```

### ⚠️ UR-03.4: Sinh đề thi ngẫu nhiên
**Trạng thái:** ⚠️ **CHƯA HOÀN CHỈNH** (50%)
- ✅ Code mẫu trong `MISSING_FEATURES_CODE.php`
- ✅ Logic random câu hỏi theo tiêu chí
- ❌ **THIẾU:** Copy code vào DeThiController
- ❌ **THIẾU:** Giao diện form tạo đề ngẫu nhiên
- ❌ **THIẾU:** Chọn số câu, chủ đề, độ khó

**Cần làm:**
1. Copy `taoDeThiNgauNhien()` từ `MISSING_FEATURES_CODE.php` vào `DeThiController.php`
2. Tạo giao diện form với các options
3. Test API

### ⚠️ UR-03.5: Thống kê kết quả lớp học
**Trạng thái:** ⚠️ **CHƯA HOÀN CHỈNH** (40%)
- ✅ API GET /api/thong-ke/{maDe} (đã có)
- ❌ **THIẾU:** Giao diện hiển thị thống kê
- ❌ **THIẾU:** Điểm trung bình lớp
- ❌ **THIẾU:** Tỉ lệ đúng/sai theo câu hỏi
- ❌ **THIẾU:** Biểu đồ phân bố điểm

**Cần làm:**
```javascript
// Hiển thị thống kê lớp
showClassStatistics(maDe) {
    const stats = await apiCall(`/thong-ke/${maDe}`);
    // Hiển thị: Điểm TB, số học sinh, phân bố điểm
    // Chart: Histogram điểm số
}
```

---

## 4. MODULE 4: QUẢN TRỊ HỆ THỐNG (UR-04)

### ✅ UR-04.1: Quản lý tài khoản người dùng
**Trạng thái:** ✅ **HOÀN THÀNH**
- ✅ Tạo tài khoản mới
- ✅ Sửa thông tin người dùng
- ✅ Khóa/Mở khóa tài khoản
- ✅ Lọc theo vai trò
- ✅ API CRUD đầy đủ
- ✅ Modal form thêm/sửa
- ✅ 20+ test cases
- **File:** `UserController.php`, `app.blade.php`, `test-add-user.http`

### ❌ UR-04.2: Phân quyền người dùng
**Trạng thái:** ⚠️ **CHƯA HOÀN CHỈNH** (40%)
- ✅ Phân quyền cơ bản (admin, giaovien, hocsinh)
- ✅ Middleware kiểm tra role
- ❌ **THIẾU:** Phân quyền động (custom permissions)
- ❌ **THIẾU:** Giao diện quản lý quyền
- ❌ **THIẾU:** Bảng permissions trong database

**Cần làm:**
```sql
-- Thêm bảng permissions
CREATE TABLE permissions (
    id INT PRIMARY KEY,
    role VARCHAR(50),
    permission VARCHAR(100),
    can_access BOOLEAN
);
```

### ❌ UR-04.3: Giám sát hệ thống
**Trạng thái:** ❌ **CHƯA LÀM** (0%)
- ❌ **THIẾU:** Dashboard admin
- ❌ **THIẾU:** Số người dùng online
- ❌ **THIẾU:** Số lượt làm bài hôm nay
- ❌ **THIẾU:** Biểu đồ hoạt động theo thời gian
- ❌ **THIẾU:** API GET /api/admin/dashboard

**Cần làm:**
```html
<div class="admin-dashboard">
    <div class="stat-card">
        <h3>200</h3>
        <p>Người dùng online</p>
    </div>
    <div class="stat-card">
        <h3>1,500</h3>
        <p>Lượt làm bài hôm nay</p>
    </div>
</div>
```

### ❌ UR-04.4: Sao lưu dữ liệu
**Trạng thái:** ⚠️ **CHƯA HOÀN CHỈNH** (20%)
- ✅ API endpoint đã khai báo
- ❌ **THIẾU:** Logic mysqldump
- ❌ **THIẾU:** Lưu file backup
- ❌ **THIẾU:** Danh sách backup đã tạo
- ❌ **THIẾU:** Giao diện quản lý backup

**Cần làm:**
```php
public function backupDatabase() {
    $filename = 'backup_' . date('Y-m-d_His') . '.sql';
    $command = "mysqldump -u {$user} -p{$password} {$database} > {$path}/{$filename}";
    exec($command);
    // Lưu thông tin backup vào bảng
}
```

### ❌ UR-04.5: Phục hồi dữ liệu
**Trạng thái:** ❌ **CHƯA LÀM** (0%)
- ❌ **THIẾU:** Upload file backup (.sql)
- ❌ **THIẾU:** Xác nhận trước khi restore
- ❌ **THIẾU:** Logic mysql import
- ❌ **THIẾU:** Giao diện restore

**Cần làm:**
```php
public function restoreDatabase(Request $request) {
    $file = $request->file('backup_file');
    $command = "mysql -u {$user} -p{$password} {$database} < {$file->path()}";
    exec($command);
}
```

---

## 5. MODULE 5: BẢO MẬT VÀ CHỐNG GIAN LẬN (UR-05)

### ❌ UR-05.1: Cảnh báo gian lận
**Trạng thái:** ⚠️ **CHƯA HOÀN CHỈNH** (30%)
- ✅ API POST /api/ghi-nhan-gian-lan (đã có)
- ❌ **THIẾU:** JavaScript theo dõi tab switch
- ❌ **THIẾU:** JavaScript theo dõi window blur
- ❌ **THIẾU:** Hiển thị cảnh báo cho học sinh
- ❌ **THIẾU:** Ghi log số lần chuyển tab

**Cần làm:**
```javascript
// Phát hiện chuyển tab
document.addEventListener('visibilitychange', () => {
    if (document.hidden) {
        warningCount++;
        showWarning('Cảnh báo: Không được chuyển tab!');
        apiCall('/ghi-nhan-gian-lan', 'POST', {
            MaBaiLam: currentExamId,
            LoaiGianLan: 'tab_switch'
        });
    }
});
```

### ❌ UR-05.2: Tự động lưu bài làm
**Trạng thái:** ⚠️ **CHƯA HOÀN CHỈNH** (30%)
- ✅ API POST /api/luu-nhap (đã có)
- ❌ **THIẾU:** JavaScript auto-save mỗi 1 phút
- ❌ **THIẾU:** Hiển thị "Đã lưu tự động lúc..."
- ❌ **THIẾU:** Khôi phục bài làm khi reload

**Cần làm:**
```javascript
// Auto-save every 60 seconds
setInterval(() => {
    const answers = collectAnswers();
    apiCall('/luu-nhap', 'POST', {
        MaBaiLam: currentExamId,
        CauTraLoi: answers
    }).then(() => {
        showNotification('Đã tự động lưu bài làm');
    });
}, 60000);
```

### ✅ UR-05.3: Mã hóa mật khẩu
**Trạng thái:** ✅ **HOÀN THÀNH**
- ✅ Sử dụng BCrypt hash
- ✅ Hash khi tạo mới người dùng
- ✅ Hash khi đổi mật khẩu
- ✅ Không lưu plain text
- **File:** `UserController.php`, `AuthController.php`

---

## 6. YÊU CẦU PHI CHỨC NĂNG

### 6.1. Hiệu năng (Performance Requirements)

#### ⚠️ Thời gian phản hồi < 2 giây
**Trạng thái:** ⚠️ **CHƯA KIỂM TRA**
- ⚠️ Cần test load với Apache Bench hoặc JMeter
- ⚠️ Cần optimize queries (thêm indexes)

#### ❌ Hỗ trợ 200 người dùng đồng thời
**Trạng thái:** ❌ **CHƯA KIỂM TRA**
- ❌ Cần stress test
- ❌ Cần cấu hình load balancing (nếu cần)

#### ⚠️ Lưu trữ 100,000 câu hỏi
**Trạng thái:** ⚠️ **ĐỦ KHUNG CƠ SỞ DỮ LIỆU**
- ✅ Cấu trúc database hỗ trợ
- ⚠️ Chưa test với dữ liệu lớn

### 6.2. An toàn (Safety Requirements)

#### ❌ Sao lưu định kỳ
**Trạng thái:** ❌ **CHƯA LÀM**
- ❌ Chưa có cron job tự động backup
- ❌ Chưa có script backup

#### ❌ Khôi phục dữ liệu
**Trạng thái:** ❌ **CHƯA LÀM**
- ❌ Chưa có chức năng restore

#### ⚠️ Ngăn chặn thao tác gây lỗi
**Trạng thái:** ⚠️ **CHƯA HOÀN CHỈNH**
- ✅ Auto-save API đã có
- ❌ Chưa có JavaScript auto-save
- ❌ Chưa test mất kết nối

### 6.3. An ninh bảo mật (Security Requirements)

#### ✅ Phân quyền rõ ràng
**Trạng thái:** ✅ **HOÀN THÀNH**
- ✅ Middleware kiểm tra role
- ✅ Admin, Giáo viên, Học sinh phân quyền rõ

#### ❌ Xác thực hai lớp (2FA)
**Trạng thái:** ❌ **CHƯA LÀM**
- ❌ Chưa có 2FA
- ❌ Chưa có OTP

#### ✅ Mã hóa mật khẩu
**Trạng thái:** ✅ **HOÀN THÀNH**
- ✅ BCrypt hash

#### ⚠️ Chống tấn công
**Trạng thái:** ⚠️ **CHƯA HOÀN CHỈNH**
- ⚠️ Laravel có sẵn CSRF protection
- ⚠️ Cần test SQL Injection
- ⚠️ Cần test XSS
- ❌ **THIẾU:** Rate limiting cho login

**Cần làm:**
```php
// Rate limiting
Route::middleware('throttle:5,1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
});
```

---

## 7. CÁC YÊU CẦU KHÁC

### ❌ Hỗ trợ đa nền tảng
**Trạng thái:** ⚠️ **CHƯA HOÀN CHỈNH** (60%)
- ✅ Web responsive (Bootstrap 5)
- ❌ **THIẾU:** Mobile app (Android/iOS)
- ❌ **THIẾU:** Progressive Web App (PWA)

### ❌ Đăng nhập Google/Facebook
**Trạng thái:** ❌ **CHƯA LÀM**
- ❌ Chưa tích hợp Laravel Socialite
- ❌ Chưa có OAuth

### ❌ Tích hợp LMS
**Trạng thái:** ❌ **CHƯA LÀM**
- ❌ Chưa có API để tích hợp với hệ thống khác

### ⚠️ Xuất báo cáo Excel/PDF
**Trạng thái:** ⚠️ **CHƯA HOÀN CHỈNH** (30%)
- ✅ Export câu hỏi ra Excel/CSV
- ❌ **THIẾU:** Export kết quả thi ra Excel
- ❌ **THIẾU:** Export báo cáo thống kê ra PDF

### ❌ Thông báo Email/SMS
**Trạng thái:** ❌ **CHƯA LÀM**
- ❌ Chưa tích hợp mail service
- ❌ Chưa tích hợp SMS gateway

### ⚠️ Triển khai Cloud
**Trạng thái:** ⚠️ **CHƯA TRIỂN KHAI**
- ✅ Code sẵn sàng deploy
- ❌ Chưa deploy lên AWS/Azure/Google Cloud

---

## 📊 TỔNG KẾT TIẾN ĐỘ

### Theo Module:

| Module | Hoàn thành | Chưa hoàn chỉnh | Chưa làm | Tiến độ |
|--------|------------|-----------------|----------|---------|
| **UR-01: Quản lý tài khoản** | 3/4 | 1/4 | 0/4 | **85%** |
| **UR-02: Học sinh** | 0/5 | 3/5 | 2/5 | **30%** |
| **UR-03: Giáo viên** | 2/5 | 2/5 | 1/5 | **55%** |
| **UR-04: Quản trị** | 1/5 | 1/5 | 3/5 | **30%** |
| **UR-05: Bảo mật** | 1/3 | 2/3 | 0/3 | **45%** |
| **Phi chức năng** | 2/12 | 5/12 | 5/12 | **35%** |
| **Yêu cầu khác** | 1/6 | 2/6 | 3/6 | **30%** |

### Tổng hợp:

- ✅ **Hoàn thành:** 10 chức năng (23%)
- ⚠️ **Chưa hoàn chỉnh:** 16 chức năng (37%)
- ❌ **Chưa làm:** 17 chức năng (40%)

**TIẾN ĐỘ TỔNG THỂ: 65%**

---

## 🚀 ƯU TIÊN PHÁT TRIỂN TIẾP THEO

### Ưu tiên CỰC CAO (Critical) - Tuần này:

1. **UR-02.1: Chọn bài thi** ⭐⭐⭐⭐⭐
   - Giao diện danh sách đề thi
   - Nút "Bắt đầu làm bài"
   - Ước tính: 6 giờ

2. **UR-02.2: Làm bài + Timer** ⭐⭐⭐⭐⭐
   - Giao diện làm bài với countdown
   - Tự động nộp khi hết giờ
   - Ước tính: 8 giờ

3. **UR-05.2: Auto-save JavaScript** ⭐⭐⭐⭐⭐
   - setInterval lưu bài mỗi 60s
   - Ước tính: 3 giờ

4. **UR-02.3: Hiển thị kết quả** ⭐⭐⭐⭐
   - Giao diện kết quả tổng kết
   - Ước tính: 4 giờ

### Ưu tiên CAO (High) - Tuần sau:

5. **UR-02.4: Xem lại bài làm chi tiết** ⭐⭐⭐⭐
   - Modal chi tiết từng câu
   - Highlight đúng/sai
   - Ước tính: 6 giờ

6. **UR-05.1: Cảnh báo gian lận JavaScript** ⭐⭐⭐⭐
   - Detect tab switch/window blur
   - Ước tính: 4 giờ

7. **UR-03.4: Sinh đề ngẫu nhiên** ⭐⭐⭐⭐
   - Copy code từ MISSING_FEATURES_CODE.php
   - Tạo giao diện form
   - Ước tính: 5 giờ

8. **UR-04.4 & UR-04.5: Backup/Restore** ⭐⭐⭐
   - Mysqldump integration
   - Ước tính: 6 giờ

### Ưu tiên TRUNG BÌNH (Medium) - 2 tuần tới:

9. **UR-02.5: Thống kê tiến độ cá nhân**
   - Chart.js integration
   - Ước tính: 8 giờ

10. **UR-03.5: Thống kê lớp học**
    - Biểu đồ phân bố
    - Ước tính: 6 giờ

11. **UR-04.3: Dashboard admin**
    - Giám sát hệ thống
    - Ước tính: 10 giờ

12. **Rate Limiting**
    - Throttle cho login/API
    - Ước tính: 2 giờ

### Ưu tiên THẤP (Low) - Sau 2 tuần:

13. **UR-01.4: Trang giới thiệu cho khách**
14. **UR-03.3: Tạo đề thủ công**
15. **UR-04.2: Phân quyền động**
16. **2FA, OAuth, SMS**

---

## 📈 ROADMAP 4 TUẦN TỚI

### Tuần 1 (Hiện tại):
- ✅ Module làm bài thi hoàn chỉnh (chọn đề, làm bài, nộp bài, xem kết quả)
- ✅ Auto-save và chống gian lận cơ bản

### Tuần 2:
- ✅ Xem lại bài làm chi tiết
- ✅ Sinh đề ngẫu nhiên
- ✅ Backup/Restore

### Tuần 3:
- ✅ Thống kê và biểu đồ (cá nhân + lớp học)
- ✅ Dashboard admin
- ✅ Rate limiting

### Tuần 4:
- ✅ Tính năng nâng cao (2FA, OAuth, tích hợp LMS)
- ✅ Test toàn diện
- ✅ Deploy production

---

## 🎯 MỤC TIÊU ĐẠT 100%

**Cần hoàn thành thêm 35% (từ 65% → 100%)**

**Ước tính thời gian:** 4-6 tuần làm việc full-time

**Nguồn lực cần thiết:**
- 1 Backend Developer (Laravel)
- 1 Frontend Developer (JavaScript/Bootstrap)
- 1 QA Tester
- 1 DevOps (để deploy)

---

**Cập nhật:** 7/12/2025  
**Người đánh giá:** GitHub Copilot  
**Trạng thái:** 65% hoàn thành
