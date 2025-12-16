# QUESTION BANK MANAGEMENT (UR-03.1)
## Complete Implementation Documentation

---

## 📋 TABLE OF CONTENTS
1. [Overview](#overview)
2. [Features](#features)
3. [API Endpoints](#api-endpoints)
4. [Field Name Mapping](#field-name-mapping)
5. [Auto-Generation System](#auto-generation-system)
6. [Validation Rules](#validation-rules)
7. [Permission System](#permission-system)
8. [Response Format](#response-format)
9. [Testing Guide](#testing-guide)
10. [Error Handling](#error-handling)

---

## 🎯 OVERVIEW

The Question Bank Management feature (UR-03.1) provides comprehensive CRUD operations for managing exam questions. This feature allows teachers and administrators to:
- Create questions with auto-generated IDs
- View and filter questions by subject or difficulty
- Update existing questions
- Delete unused questions (with protection for questions in active exams)

**Location:** `app/Http/Controllers/CauHoiController.php`

**Database Table:** `CauHoi`

**Authentication:** Required (Laravel Sanctum)

**Authorization:** 
- View: All authenticated users
- Create/Update/Delete: Teachers (`giaovien`) and Administrators (`admin`) only

---

## ✨ FEATURES

### 1. **Auto-Generated Question IDs**
- System automatically generates unique `MaCH` codes
- Format: `CH001`, `CH002`, `CH003`, etc.
- No manual ID input required

### 2. **Dual Field Name Support**
- Accepts both user-friendly names AND database field names
- Provides backward compatibility
- See [Field Name Mapping](#field-name-mapping)

### 3. **Flexible Filtering**
- Filter by subject/question bank (`MaMon` or `MaNH`)
- Filter by difficulty level (`MucDo` or `DoKho`)
- Combine multiple filters

### 4. **Smart Validation**
- Validates all answer options (A, B, C, D)
- Ensures correct answer is valid (A/B/C/D only)
- Checks difficulty level enum values
- Verifies subject/question bank exists

### 5. **Protection Against Cascade Issues**
- Prevents deletion of questions used in active exams
- Shows how many exams use the question
- Safe delete operation

---

## 🔌 API ENDPOINTS

### Base URL
```
http://localhost:8000/api
```

### Endpoints Summary

| Method | Endpoint | Description | Auth Required | Role Required |
|--------|----------|-------------|---------------|---------------|
| GET | `/cau-hoi` | Get all questions (with filters) | ✅ | All |
| POST | `/cau-hoi` | Create new question | ✅ | Teacher/Admin |
| PUT | `/cau-hoi/{id}` | Update question | ✅ | Teacher/Admin |
| DELETE | `/cau-hoi/{id}` | Delete question | ✅ | Teacher/Admin |

---

### 1. GET ALL QUESTIONS (index)

**Endpoint:** `GET /api/cau-hoi`

**Query Parameters:**
- `MaMon` or `MaNH` (optional): Filter by subject/question bank
- `MucDo` or `DoKho` (optional): Filter by difficulty level
  - Values: `De` (Easy), `TrungBinh` or `TB` (Medium), `Kho` (Hard)

**Examples:**
```http
# Get all questions
GET /api/cau-hoi

# Filter by subject
GET /api/cau-hoi?MaMon=NH001

# Filter by difficulty
GET /api/cau-hoi?MucDo=De

# Combined filters
GET /api/cau-hoi?MaMon=NH001&MucDo=TrungBinh
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Lấy danh sách câu hỏi thành công",
  "data": [
    {
      "MaCH": "CH001",
      "NoiDung": "Python là ngôn ngữ lập trình thuộc loại nào?",
      "DapAnA": "Ngôn ngữ bậc thấp",
      "DapAnB": "Ngôn ngữ bậc cao",
      "DapAnC": "Ngôn ngữ máy",
      "DapAnD": "Ngôn ngữ Assembly",
      "DapAnDung": "B",
      "MucDo": "De",
      "MaNH": "NH001",
      "NganHangCauHoi": {
        "MaNH": "NH001",
        "TenNganHang": "Ngân hàng câu hỏi Tin học 10"
      },
      "created_at": "2025-12-07T10:30:00.000000Z",
      "updated_at": "2025-12-07T10:30:00.000000Z"
    }
  ]
}
```

---

### 2. CREATE QUESTION (store)

**Endpoint:** `POST /api/cau-hoi`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body (User-Friendly Names):**
```json
{
  "NoiDung": "Python là ngôn ngữ lập trình thuộc loại nào?",
  "DapAnA": "Ngôn ngữ bậc thấp",
  "DapAnB": "Ngôn ngữ bậc cao",
  "DapAnC": "Ngôn ngữ máy",
  "DapAnD": "Ngôn ngữ Assembly",
  "DapAnDung": "B",
  "MucDo": "De",
  "MaMon": "NH001"
}
```

**Request Body (Database Names - Backward Compatible):**
```json
{
  "NoiDung": "Python là ngôn ngữ lập trình thuộc loại nào?",
  "DapAnA": "Ngôn ngữ bậc thấp",
  "DapAnB": "Ngôn ngữ bậc cao",
  "DapAnC": "Ngôn ngữ máy",
  "DapAnD": "Ngôn ngữ Assembly",
  "DapAn": "B",
  "DoKho": "De",
  "MaNH": "NH001"
}
```

**Success Response (201):**
```json
{
  "success": true,
  "message": "Tạo câu hỏi thành công",
  "data": {
    "MaCH": "CH003",
    "NoiDung": "Python là ngôn ngữ lập trình thuộc loại nào?",
    "DapAnA": "Ngôn ngữ bậc thấp",
    "DapAnB": "Ngôn ngữ bậc cao",
    "DapAnC": "Ngôn ngữ máy",
    "DapAnD": "Ngôn ngữ Assembly",
    "DapAnDung": "B",
    "MucDo": "De",
    "MaNH": "NH001",
    "NganHangCauHoi": {
      "MaNH": "NH001",
      "TenNganHang": "Ngân hàng câu hỏi Tin học 10"
    },
    "created_at": "2025-12-07T10:45:00.000000Z",
    "updated_at": "2025-12-07T10:45:00.000000Z"
  }
}
```

**Validation Error Response (422):**
```json
{
  "success": false,
  "message": "Dữ liệu không hợp lệ",
  "errors": {
    "NoiDung": ["Nội dung câu hỏi không được để trống"],
    "DapAnDung": ["Đáp án đúng phải là A, B, C hoặc D"]
  }
}
```

---

### 3. UPDATE QUESTION (update)

**Endpoint:** `PUT /api/cau-hoi/{id}`

**Path Parameters:**
- `id`: Question ID (MaCH), e.g., `CH001`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body (Partial Update):**
```json
{
  "NoiDung": "Updated question content",
  "MucDo": "TrungBinh",
  "DapAnDung": "C"
}
```

**Notes:**
- All fields are optional (partial update supported)
- Only provided fields will be updated
- Supports both user-friendly and database field names

**Success Response (200):**
```json
{
  "success": true,
  "message": "Cập nhật câu hỏi thành công",
  "data": {
    "MaCH": "CH001",
    "NoiDung": "Updated question content",
    "DapAnA": "Ngôn ngữ bậc thấp",
    "DapAnB": "Ngôn ngữ bậc cao",
    "DapAnC": "Ngôn ngữ máy",
    "DapAnD": "Ngôn ngữ Assembly",
    "DapAnDung": "C",
    "MucDo": "TrungBinh",
    "MaNH": "NH001",
    "NganHangCauHoi": { ... },
    "created_at": "2025-12-07T10:30:00.000000Z",
    "updated_at": "2025-12-07T11:00:00.000000Z"
  }
}
```

**Not Found Response (404):**
```json
{
  "success": false,
  "message": "Không tìm thấy câu hỏi"
}
```

---

### 4. DELETE QUESTION (destroy)

**Endpoint:** `DELETE /api/cau-hoi/{id}`

**Path Parameters:**
- `id`: Question ID (MaCH), e.g., `CH001`

**Headers:**
```
Authorization: Bearer {token}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Xóa câu hỏi thành công"
}
```

**Used in Exams Error (400):**
```json
{
  "success": false,
  "message": "Không thể xóa câu hỏi vì đã được sử dụng trong 3 đề thi"
}
```

**Not Found Response (404):**
```json
{
  "success": false,
  "message": "Không tìm thấy câu hỏi"
}
```

---

## 🔄 FIELD NAME MAPPING

The system supports **dual naming conventions** for flexibility and backward compatibility:

| User-Friendly Name | Database Field | Description | Values |
|-------------------|----------------|-------------|---------|
| `DapAnDung` | `DapAn` | Correct answer | A, B, C, D |
| `MucDo` | `DoKho` | Difficulty level | De, TrungBinh/TB, Kho |
| `MaMon` | `MaNH` | Subject/Question Bank ID | CHAR(10) |

### How It Works:

1. **Incoming Requests:**
   - System accepts BOTH field names
   - Priority: User-friendly names first, then database names
   - Example: If both `DapAnDung` and `DapAn` provided, uses `DapAnDung`

2. **Difficulty Level Mapping:**
   - `TrungBinh` → automatically converted to `TB`
   - `TB` → automatically converted to `TrungBinh` in responses
   - `De` and `Kho` remain unchanged

3. **Outgoing Responses:**
   - Always returns user-friendly field names
   - `DapAn` → returned as `DapAnDung`
   - `DoKho` → returned as `MucDo` (with TB→TrungBinh conversion)
   - `MaNH` → remains as `MaNH` (both names same concept)

### Example Request/Response Flow:

**Request (User-Friendly):**
```json
{
  "MucDo": "TrungBinh",
  "DapAnDung": "B",
  "MaMon": "NH001"
}
```

**Stored in Database:**
```
DoKho = "TB"
DapAn = "B"
MaNH = "NH001"
```

**Response (User-Friendly):**
```json
{
  "MucDo": "TrungBinh",
  "DapAnDung": "B",
  "MaNH": "NH001"
}
```

---

## 🔢 AUTO-GENERATION SYSTEM

### Question ID (MaCH) Generation

**Format:** `CHXXX` where XXX is a 3-digit number

**Algorithm:**
1. Query last question ordered by `MaCH` DESC
2. Extract numeric part from last `MaCH`
3. Increment by 1
4. Pad with leading zeros to 3 digits
5. Prefix with `CH`

**Examples:**
```
No questions exist     → CH001
Last question: CH001   → CH002
Last question: CH009   → CH010
Last question: CH099   → CH100
Last question: CH999   → CH1000 (expands beyond 3 digits)
```

**Implementation:**
```php
$lastCauHoi = CauHoi::orderBy('MaCH', 'desc')->first();

if ($lastCauHoi) {
    $lastNumber = (int)substr($lastCauHoi->MaCH, 2);
    $newNumber = $lastNumber + 1;
} else {
    $newNumber = 1;
}

$newMaCH = 'CH' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
```

---

## ✅ VALIDATION RULES

### Create Question (POST)

| Field | Rule | Description | Error Message |
|-------|------|-------------|---------------|
| `NoiDung` | required, string | Question content | "Nội dung câu hỏi không được để trống" |
| `DapAnA` | required, string | Answer option A | "Đáp án A không được để trống" |
| `DapAnB` | required, string | Answer option B | "Đáp án B không được để trống" |
| `DapAnC` | required, string | Answer option C | "Đáp án C không được để trống" |
| `DapAnD` | required, string | Answer option D | "Đáp án D không được để trống" |
| `DapAnDung`/`DapAn` | required, in:A,B,C,D | Correct answer | "Đáp án đúng phải là A, B, C hoặc D" |
| `MucDo` | required, in:De,TrungBinh,Kho | Difficulty (user) | "Mức độ phải là De, TrungBinh hoặc Kho" |
| `DoKho` | required, in:De,TB,Kho | Difficulty (DB) | "Độ khó phải là De, TB hoặc Kho" |
| `MaMon`/`MaNH` | required, exists:NganHangCauHoi,MaNH | Question bank FK | "Ngân hàng câu hỏi không tồn tại" |

### Update Question (PUT)

**All fields optional** (uses `sometimes` rule)
- Same validation as create, but all fields marked as `sometimes`
- Allows partial updates
- Only validates provided fields

---

## 🔐 PERMISSION SYSTEM

### Role-Based Access Control

**Implementation:** Controller constructor
```php
public function __construct()
{
    $this->middleware('auth:sanctum');
    
    $this->middleware(function ($request, $next) {
        $user = Auth::user();
        
        if (!in_array($user->Role, ['admin', 'giaovien'])) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền truy cập chức năng này'
            ], 403);
        }
        
        return $next($request);
    })->only(['store', 'update', 'destroy']);
}
```

### Permission Matrix

| Action | Method | Endpoint | Student | Teacher | Admin |
|--------|--------|----------|---------|---------|-------|
| View questions | GET | `/cau-hoi` | ✅ | ✅ | ✅ |
| Create question | POST | `/cau-hoi` | ❌ | ✅ | ✅ |
| Update question | PUT | `/cau-hoi/{id}` | ❌ | ✅ | ✅ |
| Delete question | DELETE | `/cau-hoi/{id}` | ❌ | ✅ | ✅ |

### Error Response (403 Forbidden)
```json
{
  "success": false,
  "message": "Bạn không có quyền truy cập chức năng này"
}
```

---

## 📤 RESPONSE FORMAT

All API responses follow a consistent JSON structure:

### Success Response
```json
{
  "success": true,
  "message": "Success message in Vietnamese",
  "data": {
    // Response data here
  }
}
```

### Error Response (Validation)
```json
{
  "success": false,
  "message": "Dữ liệu không hợp lệ",
  "errors": {
    "field_name": ["Error message 1", "Error message 2"]
  }
}
```

### Error Response (General)
```json
{
  "success": false,
  "message": "Error message in Vietnamese",
  "error": "Technical error details (optional)"
}
```

### HTTP Status Codes

| Code | Description | When Used |
|------|-------------|-----------|
| 200 | OK | Successful GET, PUT, DELETE |
| 201 | Created | Successful POST |
| 400 | Bad Request | Cannot delete question in use |
| 403 | Forbidden | Insufficient permissions |
| 404 | Not Found | Question ID not found |
| 422 | Unprocessable Entity | Validation failed |
| 500 | Internal Server Error | Unexpected error |

---

## 🧪 TESTING GUIDE

### Test File Location
```
test-question-bank.http
```

### Testing Workflow

#### 1. **Setup**
```http
# Update these variables in test-question-bank.http
@baseUrl = http://localhost:8000/api
@token = YOUR_AUTH_TOKEN_HERE
```

#### 2. **Basic CRUD Tests**
```http
# Test 1: Create question with auto-generated ID
POST /api/cau-hoi
# Verify: Response contains MaCH = CH001 (or next number)

# Test 2: Get all questions
GET /api/cau-hoi
# Verify: Response contains newly created question

# Test 3: Update question
PUT /api/cau-hoi/CH001
# Verify: Response shows updated fields

# Test 4: Delete question
DELETE /api/cau-hoi/CH001
# Verify: Success message returned
```

#### 3. **Filter Tests**
```http
# Test 5: Filter by subject
GET /api/cau-hoi?MaMon=NH001
# Verify: Only questions from NH001 returned

# Test 6: Filter by difficulty
GET /api/cau-hoi?MucDo=De
# Verify: Only easy questions returned

# Test 7: Combined filters
GET /api/cau-hoi?MaMon=NH001&MucDo=TrungBinh
# Verify: Only medium difficulty questions from NH001
```

#### 4. **Validation Tests**
```http
# Test 8: Missing required field
POST /api/cau-hoi
Body: { "DapAnA": "A" }  # Missing NoiDung
# Verify: 422 error with specific message

# Test 9: Invalid DapAnDung
POST /api/cau-hoi
Body: { ..., "DapAnDung": "E" }
# Verify: 422 error "Đáp án đúng phải là A, B, C hoặc D"

# Test 10: Invalid MucDo
POST /api/cau-hoi
Body: { ..., "MucDo": "VeryHard" }
# Verify: 422 error about invalid MucDo
```

#### 5. **Field Name Mapping Tests**
```http
# Test 11: Create with user-friendly names
POST /api/cau-hoi
Body: { "DapAnDung": "B", "MucDo": "TrungBinh", "MaMon": "NH001" }
# Verify: Success

# Test 12: Create with database names
POST /api/cau-hoi
Body: { "DapAn": "B", "DoKho": "TB", "MaNH": "NH001" }
# Verify: Success

# Test 13: Verify response format
GET /api/cau-hoi
# Verify: Response uses "DapAnDung" and "MucDo" (user-friendly)
```

#### 6. **Permission Tests**
```http
# Test 14: Student tries to create (should fail)
# Login as student, then:
POST /api/cau-hoi
# Verify: 403 Forbidden

# Test 15: Teacher creates (should succeed)
# Login as teacher, then:
POST /api/cau-hoi
# Verify: 201 Created

# Test 16: Student views questions (should succeed)
# Login as student, then:
GET /api/cau-hoi
# Verify: 200 OK with data
```

#### 7. **Delete Protection Tests**
```http
# Test 17: Delete unused question (should succeed)
DELETE /api/cau-hoi/CH001
# Verify: Success message

# Test 18: Delete question in exam (should fail)
# First add question to exam, then:
DELETE /api/cau-hoi/CH002
# Verify: 400 error "đã được sử dụng trong X đề thi"
```

### Expected Test Results

✅ **All tests should pass if:**
- Auto-generation creates sequential IDs
- Both field name conventions work
- TrungBinh↔TB conversion works
- Filters return correct subsets
- Validation catches all errors
- Permissions block students from write operations
- Delete protection prevents cascade issues

---

## ⚠️ ERROR HANDLING

### Common Errors and Solutions

#### 1. **Auto-Generation Failure**
**Error:** "Có lỗi xảy ra khi tạo câu hỏi"
**Cause:** Database error during MaCH generation
**Solution:** 
- Check database connection
- Verify `CauHoi` table exists
- Check for database locks

#### 2. **Validation Errors**
**Error:** 422 with validation messages
**Cause:** Invalid input data
**Solution:**
- Check all required fields are provided
- Verify DapAnDung is A/B/C/D
- Verify MucDo is De/TrungBinh/Kho
- Verify MaMon/MaNH exists in NganHangCauHoi table

#### 3. **Permission Denied**
**Error:** 403 "Bạn không có quyền truy cập..."
**Cause:** Student trying to create/update/delete
**Solution:**
- Verify user role is 'giaovien' or 'admin'
- Check authentication token is valid
- Verify middleware is working correctly

#### 4. **Question Not Found**
**Error:** 404 "Không tìm thấy câu hỏi"
**Cause:** Invalid MaCH provided
**Solution:**
- Verify question ID exists in database
- Check for typos in MaCH (case-sensitive)
- Ensure question wasn't deleted

#### 5. **Cannot Delete Question**
**Error:** 400 "Không thể xóa câu hỏi vì đã được sử dụng trong X đề thi"
**Cause:** Question is referenced in DETHI_CAUHOI pivot table
**Solution:**
- Remove question from all exams first
- Or keep the question and don't delete it
- This is a data integrity protection feature

#### 6. **Field Name Confusion**
**Error:** Validation error or unexpected behavior
**Cause:** Mixing field name conventions incorrectly
**Solution:**
- Use consistent naming: either all user-friendly OR all database names
- System handles both, but consistency is clearer
- Check documentation for correct field names

---

## 📊 DATABASE SCHEMA

### CauHoi Table Structure

```sql
CREATE TABLE CauHoi (
    MaCH CHAR(10) PRIMARY KEY,
    NoiDung TEXT NOT NULL,
    DapAnA TEXT,
    DapAnB TEXT,
    DapAnC TEXT,
    DapAnD TEXT,
    DapAn VARCHAR(1) NOT NULL,  -- Stores: A, B, C, or D
    DoKho ENUM('De', 'TB', 'Kho') NOT NULL DEFAULT 'TB',
    MaNH CHAR(10) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (MaNH) REFERENCES NganHangCauHoi(MaNH) ON DELETE CASCADE
);
```

### Relationships

```
CauHoi (Many) ← → (One) NganHangCauHoi
  - One question belongs to one question bank
  - Access via: $cauHoi->nganHangCauHoi

CauHoi (Many) ← → (Many) DeThi
  - Questions can be used in multiple exams
  - Exams can have multiple questions
  - Pivot table: DETHI_CAUHOI
  - Access via: $cauHoi->deThi()
```

---

## 🎓 USAGE EXAMPLES

### Example 1: Create Question with Easy Difficulty
```php
POST /api/cau-hoi
{
  "NoiDung": "Câu lệnh nào in ra màn hình trong Python?",
  "DapAnA": "echo()",
  "DapAnB": "printf()",
  "DapAnC": "print()",
  "DapAnD": "System.out.println()",
  "DapAnDung": "C",
  "MucDo": "De",
  "MaMon": "NH001"
}
```

### Example 2: Update Question Difficulty
```php
PUT /api/cau-hoi/CH001
{
  "MucDo": "TrungBinh"
}
```

### Example 3: Filter Questions for Exam Creation
```php
// Get all hard questions from subject NH001
GET /api/cau-hoi?MaMon=NH001&MucDo=Kho

// Response can be used to randomly select questions for exam
```

### Example 4: Bulk Question Import
```php
// Create multiple questions in sequence
foreach ($questions as $q) {
    POST /api/cau-hoi
    {
        "NoiDung": $q->content,
        "DapAnA": $q->optionA,
        "DapAnB": $q->optionB,
        "DapAnC": $q->optionC,
        "DapAnD": $q->optionD,
        "DapAnDung": $q->correctAnswer,
        "MucDo": $q->difficulty,
        "MaMon": $q->subject
    }
}
// Each question gets auto-generated sequential MaCH
```

---

## 🔍 TROUBLESHOOTING

### Problem: Questions not filtering correctly
**Solution:**
- Verify filter parameters: `MaMon` or `MaNH` for subject
- Verify difficulty: `MucDo` or `DoKho` with correct values
- Check URL encoding for query parameters

### Problem: MaCH not incrementing
**Solution:**
- Check database connection
- Verify last question record exists
- Check MaCH format in database (should be CH001, CH002, etc.)

### Problem: TrungBinh not working
**Solution:**
- System should auto-convert to TB
- Try using `DoKho=TB` if issue persists
- Check migration has ENUM('De','TB','Kho')

### Problem: Permission errors for teachers
**Solution:**
- Verify user `Role` column = 'giaovien' (lowercase)
- Check authentication token is valid
- Verify middleware is applied correctly

---

## 📝 NOTES

1. **Case Sensitivity:** 
   - MaCH values are case-sensitive (CH001, not ch001)
   - Field names in JSON are case-sensitive

2. **Character Encoding:**
   - All Vietnamese characters (UTF-8) supported
   - Store question content with proper encoding

3. **Performance:**
   - Index on `MaNH` for faster filtering
   - Index on `DoKho` for difficulty filtering
   - Pagination recommended for large question sets (not yet implemented)

4. **Future Enhancements:**
   - Add pagination to index() method
   - Add search functionality for question content
   - Add bulk import/export features
   - Add question duplication feature
   - Add question preview/review workflow

---

## 🔗 RELATED FEATURES

- **UR-03.3:** Create Exam (uses questions from this feature)
- **UR-03.5:** Exam Statistics (analyzes question difficulty)
- **UR-05.1:** Cheating Detection (monitors answer patterns)

---

## 👥 AUTHOR

**Feature:** Question Bank Management (UR-03.1)  
**Implementation Date:** December 7, 2025  
**Last Updated:** December 7, 2025  
**Version:** 1.0.0

---

## 📞 SUPPORT

For issues or questions about this feature:
1. Check this documentation first
2. Review test cases in `test-question-bank.http`
3. Check error logs in `storage/logs/laravel.log`
4. Verify database schema matches specification

---

**END OF DOCUMENTATION**
