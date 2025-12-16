# 🎓 Create Exam Feature (UR-03.3) - Implementation Complete

## ✅ Implementation Status: COMPLETE

The "Create Exam" feature for teachers has been successfully implemented.

---

## 📋 Implementation Details

### 1. **Controller Method** ✅
**File**: `app/Http/Controllers/DeThiController.php`

**Method**: `taoDeThi(Request $request)`

#### Features Implemented:
- ✅ Input validation for TenDe, ThoiGianLamBai, MoTa
- ✅ Authentication verification (user must be logged in)
- ✅ Authorization check (only teachers can create exams)
- ✅ Automatic MaDe generation (DT001, DT002, DT003, ...)
- ✅ Creates DeThi record with all required fields
- ✅ Returns success response with exam details
- ✅ Comprehensive error handling (403, 422, 500)

#### Validation Rules:
- **TenDe**: Required string, max 200 characters
- **ThoiGianLamBai**: Required integer, between 1-300 minutes
- **MoTa**: Optional string (nullable)

#### Auto-generated Fields:
- **MaDe**: Auto-incremented (DT001, DT002, ...)
- **NgayTao**: Current timestamp
- **SoLuongCauHoi**: Initially 0
- **MaGV**: Extracted from authenticated teacher
- **TrangThai**: Default true (active)

---

### 2. **API Route** ✅
**File**: `routes/api.php`

**Route**: 
```php
Route::post('/tao-de-thi', [DeThiController::class, 'taoDeThi']);
```

**Location**: Inside `auth:sanctum` middleware group

**Full Path**: `POST /api/tao-de-thi`

---

## 📊 API Specification

### Endpoint
```
POST /api/tao-de-thi
```

### Authentication
```
Authorization: Bearer {token}
```
**Note**: Must be a teacher account

### Request Headers
```
Authorization: Bearer {your_teacher_token}
Content-Type: application/json
```

### Request Body
```json
{
  "TenDe": "Đề thi Tin học THPT 2025",
  "ThoiGianLamBai": 90,
  "MoTa": "Đề thi giữa kỳ môn Tin học lớp 12"
}
```

### Success Response (201 Created)
```json
{
  "success": true,
  "message": "Tạo đề thi thành công",
  "data": {
    "MaDe": "DT001",
    "TenDe": "Đề thi Tin học THPT 2025",
    "ThoiGianLamBai": 90,
    "NgayTao": "2025-12-06 15:30:45",
    "SoLuongCauHoi": 0,
    "MaGV": "GV001",
    "TenGiaoVien": "Nguyễn Văn A",
    "MoTa": "Đề thi giữa kỳ môn Tin học lớp 12",
    "TrangThai": "Hoạt động"
  }
}
```

---

## ❌ Error Responses

### 1. Validation Error (422)
**Scenario**: Missing or invalid fields

```json
{
  "success": false,
  "message": "Dữ liệu không hợp lệ",
  "errors": {
    "TenDe": ["Tên đề thi không được để trống"],
    "ThoiGianLamBai": ["Thời gian làm bài phải ít nhất 1 phút"]
  }
}
```

### 2. Authorization Error (403)
**Scenario**: User is not a teacher

```json
{
  "success": false,
  "message": "Chỉ giáo viên mới có thể tạo đề thi"
}
```

### 3. Authentication Error (401)
**Scenario**: No token or invalid token

```json
{
  "message": "Unauthenticated."
}
```

### 4. Server Error (500)
**Scenario**: Database or server issue

```json
{
  "success": false,
  "message": "Có lỗi xảy ra khi tạo đề thi",
  "error": "Error details..."
}
```

---

## 🔐 Authorization Logic

### Step-by-Step Verification:
1. **Authentication Check**: Verifies user has valid Sanctum token
2. **User Retrieval**: Gets authenticated user from token
3. **Teacher Verification**: Checks if user exists in `GiaoVien` table
4. **MaGV Extraction**: Gets teacher ID from GiaoVien record
5. **Permission Grant**: Allows exam creation if all checks pass

### Code Flow:
```php
$user = $request->user();                              // Get authenticated user
$giaoVien = GiaoVien::where('MaTK', $user->MaTK)->first(); // Find teacher record
if (!$giaoVien) {
    return 403 Forbidden;                              // Not a teacher
}
```

---

## 🔢 Automatic Code Generation

### MaDe Format: `DT###`
- **DT**: Prefix for "Đề Thi"
- **###**: 3-digit padded number (001, 002, ..., 999)

### Generation Logic:
```php
// Find last exam code
$lastDeThi = DeThi::orderBy('MaDe', 'desc')->first();

// Extract number and increment
$lastNumber = intval(substr($lastDeThi->MaDe, 2)); // "DT001" -> 1
$newNumber = $lastNumber + 1;                       // 1 -> 2

// Format new code
$maDe = 'DT' . str_pad($newNumber, 3, '0', STR_PAD_LEFT); // "DT002"
```

### Examples:
- First exam: `DT001`
- Second exam: `DT002`
- Tenth exam: `DT010`
- Hundredth exam: `DT100`

---

## 📁 Files Modified

| File | Purpose | Lines |
|------|---------|-------|
| `app/Http/Controllers/DeThiController.php` | Created controller with taoDeThi method | 1-126 |
| `routes/api.php` | Added route and import statement | 8, 50 |

---

## 🧪 Testing Instructions

### Prerequisites:
1. Start server: `php artisan serve`
2. Login as a teacher to get token
3. Use the token in Authorization header

### Test Credentials (from seeder):
| Username | Password | Role | MaGV |
|----------|----------|------|------|
| giaovien1 | 123456 | Teacher | GV001 |

---

## 📝 Test Cases

### Test Case 1: Successful Exam Creation ✅
```http
POST http://localhost:8000/api/tao-de-thi
Authorization: Bearer {teacher_token}
Content-Type: application/json

{
  "TenDe": "Đề thi Tin học THPT 2025",
  "ThoiGianLamBai": 90,
  "MoTa": "Đề thi giữa kỳ môn Tin học lớp 12"
}
```
**Expected**: 201 Created with exam data

---

### Test Case 2: Missing TenDe ❌
```http
POST http://localhost:8000/api/tao-de-thi
Authorization: Bearer {teacher_token}
Content-Type: application/json

{
  "ThoiGianLamBai": 90
}
```
**Expected**: 422 Validation Error

---

### Test Case 3: Invalid ThoiGianLamBai (too short) ❌
```http
POST http://localhost:8000/api/tao-de-thi
Authorization: Bearer {teacher_token}
Content-Type: application/json

{
  "TenDe": "Đề thi Tin học",
  "ThoiGianLamBai": 0
}
```
**Expected**: 422 Validation Error (min 1 phút)

---

### Test Case 4: Invalid ThoiGianLamBai (too long) ❌
```http
POST http://localhost:8000/api/tao-de-thi
Authorization: Bearer {teacher_token}
Content-Type: application/json

{
  "TenDe": "Đề thi Tin học",
  "ThoiGianLamBai": 500
}
```
**Expected**: 422 Validation Error (max 300 phút)

---

### Test Case 5: Student Trying to Create Exam ❌
```http
POST http://localhost:8000/api/tao-de-thi
Authorization: Bearer {student_token}
Content-Type: application/json

{
  "TenDe": "Đề thi Tin học",
  "ThoiGianLamBai": 90
}
```
**Expected**: 403 Forbidden

---

### Test Case 6: No Authentication ❌
```http
POST http://localhost:8000/api/tao-de-thi
Content-Type: application/json

{
  "TenDe": "Đề thi Tin học",
  "ThoiGianLamBai": 90
}
```
**Expected**: 401 Unauthenticated

---

### Test Case 7: Minimal Valid Request ✅
```http
POST http://localhost:8000/api/tao-de-thi
Authorization: Bearer {teacher_token}
Content-Type: application/json

{
  "TenDe": "Đề thi nhanh",
  "ThoiGianLamBai": 45
}
```
**Expected**: 201 Created (MoTa is optional)

---

### Test Case 8: Long TenDe ✅
```http
POST http://localhost:8000/api/tao-de-thi
Authorization: Bearer {teacher_token}
Content-Type: application/json

{
  "TenDe": "Đề thi kiểm tra kiến thức môn Tin học THPT năm học 2025-2026 - Học kỳ 1 - Lớp 12A1",
  "ThoiGianLamBai": 60
}
```
**Expected**: 201 Created (within 200 chars)

---

## 🔄 Database Impact

### Table: `DeThi`
Each successful request creates a new record:

```sql
INSERT INTO DeThi (
    MaDe,
    TenDe,
    ThoiGianLamBai,
    NgayTao,
    SoLuongCauHoi,
    MaGV,
    MoTa,
    TrangThai,
    created_at,
    updated_at
) VALUES (
    'DT001',
    'Đề thi Tin học THPT 2025',
    90,
    '2025-12-06 15:30:45',
    0,
    'GV001',
    'Đề thi giữa kỳ...',
    1,
    NOW(),
    NOW()
);
```

---

## 💡 Use Cases

### 1. Create Basic Exam
Teacher creates a simple exam with name and duration:
```json
{
  "TenDe": "Đề thi thử THPT",
  "ThoiGianLamBai": 60
}
```

### 2. Create Exam with Description
Teacher adds detailed description:
```json
{
  "TenDe": "Đề thi học kỳ 1",
  "ThoiGianLamBai": 90,
  "MoTa": "Đề thi bao gồm 50 câu trắc nghiệm về lập trình Python và cơ sở dữ liệu"
}
```

### 3. Create Quick Test
Teacher creates a short quiz:
```json
{
  "TenDe": "Kiểm tra 15 phút",
  "ThoiGianLamBai": 15
}
```

### 4. Create Final Exam
Teacher creates comprehensive exam:
```json
{
  "TenDe": "Đề thi cuối kỳ môn Tin học 12",
  "ThoiGianLamBai": 120,
  "MoTa": "Đề thi tổng hợp kiến thức cả năm học"
}
```

---

## 🚀 Next Steps

After creating an exam, teachers typically need to:

1. **Add Questions**: Use API to add questions to the exam
2. **Review Exam**: Check exam details and question count
3. **Publish Exam**: Change TrangThai if needed
4. **Assign to Students**: Make exam available to students
5. **Monitor Progress**: Track student submissions

---

## 📚 Related Features

### Already Implemented:
- ✅ Authentication (Login/Logout)
- ✅ Question Management (CRUD)
- ✅ Exam Submission
- ✅ Auto Grading
- ✅ Cheating Detection

### To Be Implemented:
- ⏳ Add Questions to Exam
- ⏳ List All Exams
- ⏳ View Exam Details
- ⏳ Edit Exam
- ⏳ Delete Exam
- ⏳ Publish/Unpublish Exam

---

## ✅ Checklist

- [x] Controller created with taoDeThi method
- [x] Input validation implemented
- [x] Authentication required
- [x] Teacher authorization check
- [x] Automatic MaDe generation
- [x] DeThi record creation
- [x] Success response with complete data
- [x] Error handling (401, 403, 422, 500)
- [x] Route added to api.php
- [x] DeThiController imported
- [x] No syntax errors
- [x] Follows Laravel conventions
- [x] Documentation complete

---

**Implementation Date**: December 6, 2025  
**Status**: ✅ Production Ready  
**Feature**: Create Exam (UR-03.3)  
**Version**: 1.0.0
