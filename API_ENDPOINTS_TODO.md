# 🔌 API ENDPOINTS CẦN IMPLEMENT/KIỂM TRA

## 📊 TỔNG QUAN

Đây là danh sách các API endpoints mà Frontend đang gọi.  
Backend cần đảm bảo tất cả endpoints này hoạt động đúng.

---

## ✅ ĐÃ CÓ (Working)

### Authentication
```
POST   /api/dang-nhap              ✅ Login
POST   /api/dang-ky                ✅ Register  
POST   /api/quen-mat-khau          ✅ Forgot password
```

### Exam Management (Student)
```
GET    /api/de-thi                 ✅ Get all exams
GET    /api/de-thi/mau             ✅ Get sample exams
GET    /api/de-thi/{id}            ✅ Get exam details
POST   /api/bat-dau-thi            ✅ Start exam (create bai-lam)
POST   /api/nop-bai                ✅ Submit exam
POST   /api/luu-bai-lam            ✅ Auto-save answers
```

### History & Results (Student)
```
GET    /api/lich-su-thi            ✅ Get exam history
GET    /api/ket-qua/{id}           ✅ Get detailed result
```

### Question Bank (Teacher)
```
GET    /api/cau-hoi                ✅ Get all questions
POST   /api/cau-hoi                ✅ Add question
POST   /api/cau-hoi/import         ✅ Import questions (Excel/JSON)
PUT    /api/cau-hoi/{id}           ✅ Update question
DELETE /api/cau-hoi/{id}           ✅ Delete question
```

### User Management (Admin)
```
GET    /api/nguoi-dung             ✅ Get all users
POST   /api/nguoi-dung             ✅ Create user
PUT    /api/nguoi-dung/{id}        ✅ Update user
DELETE /api/nguoi-dung/{id}        ✅ Delete user
```

---

## ⚠️ CẦN KIỂM TRA/BỔ SUNG

### Random Exam Generation (Teacher) - NEW
```
POST   /api/de-thi/random          ⚠️ CẦN IMPLEMENT
```

**Request Body:**
```json
{
  "TenDe": "Đề thi thử lần 1",
  "ThoiGian": 60,
  "ChuDe": "Tin học đại cương",
  "SoCauHoi": 20,
  "DoKho": "Trung Binh"
}
```

**Expected Response:**
```json
{
  "success": true,
  "message": "Tạo đề thi thành công",
  "data": {
    "MaDe": "DT001",
    "TenDe": "Đề thi thử lần 1",
    "SoCauHoi": 20,
    "ThoiGian": 60
  }
}
```

**Logic:**
1. Validate input (TenDe, SoCauHoi, ThoiGian)
2. Query câu hỏi theo ChuDe và DoKho
3. Random select SoCauHoi questions
4. Create new DeThi with selected questions
5. Return created exam info

---

### Backup & Restore (Admin) - NEW

#### 1. Create Backup
```
POST   /api/backup                 ⚠️ CẦN IMPLEMENT
```

**Expected Response:**
```json
{
  "success": true,
  "message": "Backup thành công",
  "file": "backup_2025-12-07_14-30-00.sql",
  "size": 2048576
}
```

**Logic:**
1. Generate filename: `backup_YYYY-MM-DD_HH-mm-ss.sql`
2. Export database to SQL file
3. Save to `storage/app/backups/`
4. Log to backup_history table
5. Return file info

**Implementation Suggestion (Laravel):**
```php
public function createBackup() {
    $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
    $path = storage_path('app/backups/' . $filename);
    
    // Dump database using mysqldump
    $command = sprintf(
        'mysqldump -u%s -p%s %s > %s',
        config('database.connections.mysql.username'),
        config('database.connections.mysql.password'),
        config('database.connections.mysql.database'),
        $path
    );
    
    exec($command);
    
    // Log to history
    BackupHistory::create([
        'file' => $filename,
        'size' => filesize($path),
        'created_at' => now()
    ]);
    
    return response()->json([
        'success' => true,
        'file' => $filename,
        'size' => filesize($path)
    ]);
}
```

#### 2. Restore from Backup
```
POST   /api/restore                ⚠️ CẦN IMPLEMENT
```

**Request:** Multipart form-data with file upload

**Expected Response:**
```json
{
  "success": true,
  "message": "Restore thành công"
}
```

**Logic:**
1. Validate uploaded file (.sql extension)
2. Save temporary file
3. Import SQL to database using mysql command
4. Clear cache/sessions
5. Return success

**Implementation Suggestion:**
```php
public function restore(Request $request) {
    $request->validate([
        'file' => 'required|file|mimes:sql'
    ]);
    
    $file = $request->file('file');
    $path = $file->store('temp');
    
    // Import database
    $command = sprintf(
        'mysql -u%s -p%s %s < %s',
        config('database.connections.mysql.username'),
        config('database.connections.mysql.password'),
        config('database.connections.mysql.database'),
        storage_path('app/' . $path)
    );
    
    exec($command);
    
    // Cleanup
    Storage::delete($path);
    
    return response()->json([
        'success' => true,
        'message' => 'Restore thành công'
    ]);
}
```

#### 3. Get Backup History
```
GET    /api/backups                ⚠️ CẦN IMPLEMENT
```

**Expected Response:**
```json
{
  "success": true,
  "data": [
    {
      "file": "backup_2025-12-07_14-30-00.sql",
      "size": 2048576,
      "created_at": "2025-12-07 14:30:00"
    },
    {
      "file": "backup_2025-12-06_10-15-00.sql",
      "size": 1998000,
      "created_at": "2025-12-06 10:15:00"
    }
  ]
}
```

**Logic:**
1. List all files in `storage/app/backups/`
2. Get file info (size, date)
3. Sort by date descending
4. Return array

#### 4. Download Backup File
```
GET    /api/backup/download/{filename}   ⚠️ CẦN IMPLEMENT
```

**Logic:**
1. Validate filename exists
2. Return file download response
3. Set proper headers (Content-Type, Content-Disposition)

**Implementation:**
```php
public function downloadBackup($filename) {
    $path = storage_path('app/backups/' . $filename);
    
    if (!file_exists($path)) {
        return response()->json(['error' => 'File not found'], 404);
    }
    
    return response()->download($path);
}
```

---

## 🔐 SECURITY CONSIDERATIONS

### 1. Authentication Required
All endpoints (except login/register) should check for valid token:
```php
Route::middleware('auth:sanctum')->group(function() {
    // Protected routes here
});
```

### 2. Role-Based Access Control
```php
// Only Teacher can create random exams
Route::middleware(['auth:sanctum', 'role:giaovien'])->group(function() {
    Route::post('/de-thi/random', [ExamController::class, 'generateRandom']);
});

// Only Admin can backup/restore
Route::middleware(['auth:sanctum', 'role:admin'])->group(function() {
    Route::post('/backup', [BackupController::class, 'create']);
    Route::post('/restore', [BackupController::class, 'restore']);
    Route::get('/backups', [BackupController::class, 'history']);
});
```

### 3. Input Validation
Always validate user input:
```php
$request->validate([
    'TenDe' => 'required|string|max:255',
    'SoCauHoi' => 'required|integer|min:10|max:50',
    'ThoiGian' => 'required|integer|min:30|max:180',
    'ChuDe' => 'required|string',
    'DoKho' => 'required|in:De,Trung Binh,Kho'
]);
```

### 4. File Upload Security
```php
$request->validate([
    'file' => 'required|file|mimes:sql|max:10240' // Max 10MB
]);

// Sanitize filename
$filename = Str::slug($request->file('file')->getClientOriginalName());
```

### 5. CSRF Protection
Frontend should send CSRF token with requests:
```javascript
headers: {
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
}
```

---

## 📊 DASHBOARD DATA SOURCES

Frontend calls these endpoints in parallel for dashboard:
```javascript
Promise.all([
    fetch('/api/nguoi-dung'),       // Total users
    fetch('/api/de-thi'),           // Total exams
    fetch('/api/lich-su-thi'),      // Total submissions
    fetch('/api/cau-hoi')           // Total questions
])
```

Make sure all return consistent structure:
```json
{
  "success": true,
  "data": [ /* array of items */ ]
}
```

---

## 🧪 TESTING ENDPOINTS

### Using Postman/Insomnia

#### 1. Test Random Exam Generation
```
POST http://localhost:8000/api/de-thi/random
Headers:
  Authorization: Bearer {token}
  Content-Type: application/json
Body:
{
  "TenDe": "Đề thi thử lần 1",
  "ThoiGian": 60,
  "ChuDe": "Tin học đại cương",
  "SoCauHoi": 20,
  "DoKho": "Trung Binh"
}
```

#### 2. Test Backup
```
POST http://localhost:8000/api/backup
Headers:
  Authorization: Bearer {admin_token}
```

#### 3. Test Restore
```
POST http://localhost:8000/api/restore
Headers:
  Authorization: Bearer {admin_token}
  Content-Type: multipart/form-data
Body:
  file: [select .sql file]
```

#### 4. Test Get Backup History
```
GET http://localhost:8000/api/backups
Headers:
  Authorization: Bearer {admin_token}
```

---

## 🚀 ROUTE REGISTRATION (Laravel)

Add to `routes/api.php`:

```php
// Random Exam Generation (Teacher only)
Route::middleware(['auth:sanctum', 'role:giaovien,admin'])->group(function() {
    Route::post('/de-thi/random', [DeThiController::class, 'generateRandom']);
});

// Backup & Restore (Admin only)
Route::middleware(['auth:sanctum', 'role:admin'])->group(function() {
    Route::post('/backup', [BackupController::class, 'create']);
    Route::post('/restore', [BackupController::class, 'restore']);
    Route::get('/backups', [BackupController::class, 'history']);
    Route::get('/backup/download/{filename}', [BackupController::class, 'download']);
});
```

---

## 📋 CONTROLLER STRUCTURE

### DeThiController.php
```php
class DeThiController extends Controller {
    public function index() { /* Existing */ }
    public function show($id) { /* Existing */ }
    public function store(Request $request) { /* Existing */ }
    
    // NEW
    public function generateRandom(Request $request) {
        // Validate
        // Query questions by ChuDe & DoKho
        // Random select
        // Create exam
        // Return response
    }
}
```

### BackupController.php (NEW)
```php
class BackupController extends Controller {
    public function create() {
        // Generate filename
        // Export database
        // Log to history
        // Return response
    }
    
    public function restore(Request $request) {
        // Validate file
        // Import to database
        // Clear cache
        // Return response
    }
    
    public function history() {
        // List backup files
        // Return array
    }
    
    public function download($filename) {
        // Validate file exists
        // Return download response
    }
}
```

---

## 🎯 PRIORITY IMPLEMENTATION ORDER

1. **HIGH PRIORITY** (Cần cho tính năng chính):
   - ✅ Authentication endpoints (Done)
   - ✅ Exam management (Done)
   - ✅ Question bank (Done)
   - ✅ User management (Done)

2. **MEDIUM PRIORITY** (Tính năng mới, cần sớm):
   - ⚠️ Random exam generation
   - ⚠️ Backup/Restore system

3. **LOW PRIORITY** (Nice to have):
   - Email notifications
   - Export reports (Excel/PDF)
   - Advanced analytics

---

## 📝 NOTES

### Database Requirements for Backup
- Make sure MySQL/MariaDB commands are accessible
- Check user permissions for mysqldump/mysql
- Ensure storage/app/backups/ directory exists and writable

### Random Exam Algorithm
```sql
SELECT * FROM cau_hoi
WHERE ChuDe = ? AND DoKho = ?
ORDER BY RAND()
LIMIT ?
```

### Backup File Naming
Format: `backup_YYYY-MM-DD_HH-mm-ss.sql`
Example: `backup_2025-12-07_14-30-00.sql`

---

## ✅ CHECKLIST BACKEND

- [ ] Implement POST /api/de-thi/random
- [ ] Implement POST /api/backup
- [ ] Implement POST /api/restore
- [ ] Implement GET /api/backups
- [ ] Implement GET /api/backup/download/{filename}
- [ ] Add role middleware for Teacher (giaovien)
- [ ] Add role middleware for Admin (admin)
- [ ] Test all new endpoints with Postman
- [ ] Add validation rules
- [ ] Handle errors properly
- [ ] Add logging for critical operations

---

*Generated: December 7, 2025*  
*For backend developers to implement missing endpoints*
