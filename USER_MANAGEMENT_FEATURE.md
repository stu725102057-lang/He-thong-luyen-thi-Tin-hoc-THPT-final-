# USER MANAGEMENT (UR-04.1)
## Complete Implementation Documentation - Admin Only

---

## 📋 TABLE OF CONTENTS
1. [Overview](#overview)
2. [Features](#features)
3. [API Endpoints](#api-endpoints)
4. [Auto-Generation System](#auto-generation-system)
5. [Password Security (UR-05.3)](#password-security)
6. [Validation Rules](#validation-rules)
7. [Permission System](#permission-system)
8. [Response Format](#response-format)
9. [Testing Guide](#testing-guide)
10. [Error Handling](#error-handling)

---

## 🎯 OVERVIEW

The User Management feature (UR-04.1) provides comprehensive admin-only CRUD operations for managing system users. This feature allows administrators to:
- View all users with role-based filtering
- Create new users (with automatic record creation in HocSinh/GiaoVien tables)
- Update user information
- Lock/Unlock user accounts
- Manage password security with automatic hashing (UR-05.3)

**Location:** `app/Http/Controllers/UserController.php`

**Database Tables:** 
- `TaiKhoan` (Main user accounts)
- `HocSinh` (Student details)
- `GiaoVien` (Teacher details)
- `QuanTriVien` (Admin details)

**Authentication:** Required (Laravel Sanctum)

**Authorization:** **Admin Only** - All operations restricted to users with `Role = 'admin'`

---

## ✨ FEATURES

### 1. **Admin-Only Access Control**
- All user management operations restricted to admins
- Teachers and students receive 403 Forbidden
- Clear permission error messages

### 2. **Auto-Generated IDs**
- System automatically generates unique codes
- Format: `TK001`, `TK002`, `TK003`, etc. for accounts
- Format: `HS001`, `HS002`, etc. for students
- Format: `GV001`, `GV002`, etc. for teachers
- Format: `QTV001`, `QTV002`, etc. for admins

### 3. **Automatic Related Record Creation**
- Creating `hocsinh` user → automatically creates `HocSinh` record
- Creating `giaovien` user → automatically creates `GiaoVien` record
- Creating `admin` user → automatically creates `QuanTriVien` record
- Uses database transactions for data integrity

### 4. **Password Security (UR-05.3)**
- Passwords automatically hashed before storage
- Uses Laravel's `Hash::make()` (bcrypt)
- Never stores plain text passwords
- Applies to both create and update operations

### 5. **Role-Based Filtering**
- Filter users by Role: `admin`, `giaovien`, `hocsinh`
- Returns users with their related details
- Efficient database queries with eager loading

### 6. **Account Status Management**
- Lock/Unlock user accounts
- Toggle `TrangThai` (true = active, false = locked)
- Protection against locking admin accounts
- Clear status messages

### 7. **Transaction Safety**
- All create operations use database transactions
- Automatic rollback on errors
- Ensures data consistency between related tables

### 8. **Comprehensive Validation**
- Username uniqueness checks
- Email uniqueness and format validation
- Password minimum length (6 characters)
- Role enum validation
- Required fields based on role

---

## 🔌 API ENDPOINTS

### Base URL
```
http://localhost:8000/api
```

### Endpoints Summary

| Method | Endpoint | Description | Auth Required | Role Required |
|--------|----------|-------------|---------------|---------------|
| GET | `/users` | Get all users (with filter) | ✅ | Admin |
| POST | `/users` | Create new user | ✅ | Admin |
| PUT | `/users/{id}` | Update user | ✅ | Admin |
| POST | `/users/{id}/toggle-status` | Lock/Unlock account | ✅ | Admin |

---

### 1. GET ALL USERS (index)

**Endpoint:** `GET /api/users`

**Query Parameters:**
- `Role` (optional): Filter by role
  - Values: `admin`, `giaovien`, `hocsinh`

**Examples:**
```http
# Get all users
GET /api/users

# Get only students
GET /api/users?Role=hocsinh

# Get only teachers
GET /api/users?Role=giaovien

# Get only admins
GET /api/users?Role=admin
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Lấy danh sách người dùng thành công",
  "data": [
    {
      "MaTK": "TK001",
      "TenDangNhap": "nguyenvana",
      "Email": "nguyenvana@example.com",
      "Role": "hocsinh",
      "TrangThai": true,
      "LanDangNhapCuoi": null,
      "created_at": "2025-12-07T10:30:00.000000Z",
      "updated_at": "2025-12-07T10:30:00.000000Z",
      "ThongTinHocSinh": {
        "MaHS": "HS001",
        "MaTK": "TK001",
        "HoTen": "Nguyen Van A",
        "Lop": "10A1",
        "Truong": "THPT Nguyen Hue"
      }
    },
    {
      "MaTK": "TK002",
      "TenDangNhap": "tranthib",
      "Email": "tranthib@example.com",
      "Role": "giaovien",
      "TrangThai": true,
      "LanDangNhapCuoi": "2025-12-06T14:20:00.000000Z",
      "created_at": "2025-12-07T09:15:00.000000Z",
      "updated_at": "2025-12-07T09:15:00.000000Z",
      "ThongTinGiaoVien": {
        "MaGV": "GV001",
        "MaTK": "TK002",
        "HoTen": "Tran Thi B",
        "SoDienThoai": "0123456789",
        "ChuyenMon": "Tin học"
      }
    }
  ],
  "total": 2
}
```

**Invalid Role Response (400):**
```json
{
  "success": false,
  "message": "Role không hợp lệ. Chỉ chấp nhận: admin, giaovien, hocsinh"
}
```

---

### 2. CREATE USER (store)

**Endpoint:** `POST /api/users`

**Headers:**
```
Authorization: Bearer {admin_token}
Content-Type: application/json
```

**Request Body (Student):**
```json
{
  "TenDangNhap": "nguyenvana",
  "Email": "nguyenvana@example.com",
  "MatKhau": "password123",
  "Role": "hocsinh",
  "HoTen": "Nguyen Van A",
  "Lop": "10A1",
  "Truong": "THPT Nguyen Hue"
}
```

**Request Body (Teacher):**
```json
{
  "TenDangNhap": "tranthib",
  "Email": "tranthib@example.com",
  "MatKhau": "teacher123",
  "Role": "giaovien",
  "HoTen": "Tran Thi B",
  "SoDienThoai": "0123456789",
  "ChuyenMon": "Tin học"
}
```

**Request Body (Admin):**
```json
{
  "TenDangNhap": "admin2",
  "Email": "admin2@example.com",
  "MatKhau": "admin123456",
  "Role": "admin"
}
```

**Field Requirements:**

| Field | Required For | Type | Max Length | Description |
|-------|-------------|------|------------|-------------|
| `TenDangNhap` | All | String | 50 | Username (unique) |
| `Email` | All | Email | 100 | Email address (unique) |
| `MatKhau` | All | String | - | Password (min 6 chars) |
| `Role` | All | Enum | - | admin/giaovien/hocsinh |
| `HoTen` | Student/Teacher | String | 100 | Full name |
| `Lop` | Student (optional) | String | 20 | Class |
| `Truong` | Student (optional) | String | 100 | School |
| `SoDienThoai` | Teacher (optional) | String | 15 | Phone number |
| `ChuyenMon` | Teacher (optional) | String | 100 | Specialization |

**Success Response (201):**
```json
{
  "success": true,
  "message": "Tạo người dùng thành công",
  "data": {
    "TaiKhoan": {
      "MaTK": "TK003",
      "TenDangNhap": "nguyenvana",
      "Email": "nguyenvana@example.com",
      "Role": "hocsinh",
      "TrangThai": true,
      "created_at": "2025-12-07T11:00:00.000000Z"
    },
    "RoleData": {
      "MaHS": "HS003",
      "MaTK": "TK003",
      "HoTen": "Nguyen Van A",
      "Lop": "10A1",
      "Truong": "THPT Nguyen Hue",
      "created_at": "2025-12-07T11:00:00.000000Z",
      "updated_at": "2025-12-07T11:00:00.000000Z"
    }
  }
}
```

**What Happens:**
1. System generates unique `MaTK` (e.g., TK003)
2. Password is hashed using bcrypt (UR-05.3)
3. `TaiKhoan` record created
4. Based on Role:
   - `hocsinh` → Creates `HocSinh` record with auto-generated `MaHS`
   - `giaovien` → Creates `GiaoVien` record with auto-generated `MaGV`
   - `admin` → Creates `QuanTriVien` record with auto-generated `MaQTV`
5. All operations wrapped in database transaction

**Validation Error Response (422):**
```json
{
  "success": false,
  "message": "Dữ liệu không hợp lệ",
  "errors": {
    "TenDangNhap": ["Tên đăng nhập đã tồn tại"],
    "Email": ["Email đã được sử dụng"],
    "MatKhau": ["Mật khẩu phải có ít nhất 6 ký tự"],
    "HoTen": ["Họ tên không được để trống đối với học sinh và giáo viên"]
  }
}
```

---

### 3. UPDATE USER (update)

**Endpoint:** `PUT /api/users/{id}`

**Path Parameters:**
- `id`: User ID (MaTK), e.g., `TK001`

**Headers:**
```
Authorization: Bearer {admin_token}
Content-Type: application/json
```

**Request Body (Partial Update):**
```json
{
  "Email": "newemail@example.com",
  "TrangThai": false,
  "MatKhau": "newpassword123"
}
```

**Updatable Fields:**
- `Email` (must be unique)
- `TrangThai` (boolean: true/false)
- `MatKhau` (will be hashed automatically)

**Non-Updatable Fields:**
- `TenDangNhap` (username cannot be changed)
- `Role` (cannot be changed - must create new account)

**Success Response (200):**
```json
{
  "success": true,
  "message": "Cập nhật người dùng thành công",
  "data": {
    "MaTK": "TK001",
    "TenDangNhap": "nguyenvana",
    "Email": "newemail@example.com",
    "Role": "hocsinh",
    "TrangThai": false,
    "updated_at": "2025-12-07T11:30:00.000000Z"
  }
}
```

**Role Change Attempt (400):**
```json
{
  "success": false,
  "message": "Không thể thay đổi Role của người dùng. Vui lòng tạo tài khoản mới với Role mong muốn."
}
```

**Not Found Response (404):**
```json
{
  "success": false,
  "message": "Không tìm thấy người dùng"
}
```

---

### 4. TOGGLE STATUS (toggleStatus)

**Endpoint:** `POST /api/users/{id}/toggle-status`

**Path Parameters:**
- `id`: User ID (MaTK), e.g., `TK002`

**Headers:**
```
Authorization: Bearer {admin_token}
```

**Success Response (200) - Locked:**
```json
{
  "success": true,
  "message": "Đã khóa tài khoản thành công",
  "data": {
    "MaTK": "TK002",
    "TenDangNhap": "nguyenvana",
    "Email": "nguyenvana@example.com",
    "Role": "hocsinh",
    "TrangThai": false,
    "StatusText": "Đã khóa"
  }
}
```

**Success Response (200) - Unlocked:**
```json
{
  "success": true,
  "message": "Đã mở khóa tài khoản thành công",
  "data": {
    "MaTK": "TK002",
    "TenDangNhap": "nguyenvana",
    "Email": "nguyenvana@example.com",
    "Role": "hocsinh",
    "TrangThai": true,
    "StatusText": "Hoạt động"
  }
}
```

**Admin Protection (400):**
```json
{
  "success": false,
  "message": "Không thể khóa tài khoản quản trị viên"
}
```

**How It Works:**
- Toggles `TrangThai` between `true` (active) and `false` (locked)
- Locked users cannot login to the system
- Protection: Cannot lock admin accounts (to prevent self-lockout)

---

## 🔢 AUTO-GENERATION SYSTEM

### Account ID (MaTK) Generation

**Format:** `TKXXX` where XXX is a 3-digit number

**Algorithm:**
1. Query last account ordered by `MaTK` DESC
2. Extract numeric part from last `MaTK`
3. Increment by 1
4. Pad with leading zeros to 3 digits
5. Prefix with `TK`

**Examples:**
```
No accounts exist    → TK001
Last account: TK001  → TK002
Last account: TK099  → TK100
Last account: TK999  → TK1000 (expands beyond 3 digits)
```

### Student ID (MaHS) Generation

**Format:** `HSXXX` (e.g., HS001, HS002, HS003...)

**Triggered:** When creating user with `Role = 'hocsinh'`

### Teacher ID (MaGV) Generation

**Format:** `GVXXX` (e.g., GV001, GV002, GV003...)

**Triggered:** When creating user with `Role = 'giaovien'`

### Admin ID (MaQTV) Generation

**Format:** `QTVXXX` (e.g., QTV001, QTV002, QTV003...)

**Triggered:** When creating user with `Role = 'admin'`

---

## 🔐 PASSWORD SECURITY (UR-05.3)

### Automatic Password Hashing

**Implementation:**
```php
// On create
'MatKhau' => Hash::make($request->MatKhau)

// On update
if ($request->has('MatKhau')) {
    $updateData['MatKhau'] = Hash::make($request->MatKhau);
}
```

**Hashing Algorithm:** BCrypt (via Laravel's Hash facade)

**Hash Format:** `$2y$10$...` (60 characters)

### Security Features

✅ **Never Stores Plain Text:**
- All passwords hashed before database storage
- Original password cannot be recovered
- Hashing applied to both create and update operations

✅ **Strong Algorithm:**
- BCrypt with cost factor of 10
- Resistant to brute-force attacks
- Industry-standard security

✅ **Hidden from Responses:**
- `MatKhau` field in `$hidden` array of TaiKhoan model
- Never returned in API responses

### Example

**User provides:**
```
MatKhau: "password123"
```

**Stored in database:**
```
MatKhau: "$2y$10$e0MYzXyjpJS7Pd0RVvHwHeFcN8UqhK8xIBQSIunpkTwl6g2qHkj7u"
```

**API Response:**
```json
{
  "TenDangNhap": "nguyenvana",
  "Email": "nguyenvana@example.com"
  // MatKhau NOT included
}
```

---

## ✅ VALIDATION RULES

### Create User (POST)

| Field | Rule | Description | Error Message |
|-------|------|-------------|---------------|
| `TenDangNhap` | required, string, max:50, unique | Username | "Tên đăng nhập đã tồn tại" |
| `Email` | required, email, max:100, unique | Email address | "Email đã được sử dụng" |
| `MatKhau` | required, string, min:6 | Password | "Mật khẩu phải có ít nhất 6 ký tự" |
| `Role` | required, in:admin,giaovien,hocsinh | User role | "Role phải là admin, giaovien hoặc hocsinh" |
| `HoTen` | required_if:Role,hocsinh,giaovien | Full name | "Họ tên không được để trống..." |
| `Lop` | sometimes, string, max:20 | Class (optional) | - |
| `Truong` | sometimes, string, max:100 | School (optional) | - |
| `SoDienThoai` | sometimes, string, max:15 | Phone (optional) | - |
| `ChuyenMon` | sometimes, string, max:100 | Specialization (optional) | - |

### Update User (PUT)

**All fields optional** (uses `sometimes` rule)

| Field | Rule | Description | Error Message |
|-------|------|-------------|---------------|
| `Email` | sometimes, email, max:100, unique | Email address | "Email đã được sử dụng" |
| `TrangThai` | sometimes, boolean | Account status | - |
| `MatKhau` | sometimes, string, min:6 | Password | "Mật khẩu phải có ít nhất 6 ký tự" |
| `Role` | (blocked) | Cannot be changed | "Không thể thay đổi Role..." |

---

## 🔐 PERMISSION SYSTEM

### Admin-Only Access Control

**Implementation:** Controller constructor
```php
public function __construct()
{
    $this->middleware('auth:sanctum');
    $this->middleware(function ($request, $next) {
        $user = $request->user();
        
        if ($user->Role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ quản trị viên mới có quyền quản lý người dùng'
            ], 403);
        }
        
        return $next($request);
    });
}
```

### Permission Matrix

| Action | Method | Endpoint | Student | Teacher | Admin |
|--------|--------|----------|---------|---------|-------|
| View users | GET | `/users` | ❌ | ❌ | ✅ |
| Create user | POST | `/users` | ❌ | ❌ | ✅ |
| Update user | PUT | `/users/{id}` | ❌ | ❌ | ✅ |
| Toggle status | POST | `/users/{id}/toggle-status` | ❌ | ❌ | ✅ |

### Error Response (403 Forbidden)
```json
{
  "success": false,
  "message": "Chỉ quản trị viên mới có quyền quản lý người dùng"
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

### Success Response with Count
```json
{
  "success": true,
  "message": "Lấy danh sách người dùng thành công",
  "data": [...],
  "total": 10
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
| 200 | OK | Successful GET, PUT, POST (toggle) |
| 201 | Created | Successful POST (create) |
| 400 | Bad Request | Invalid operation (e.g., change role, lock admin) |
| 403 | Forbidden | Non-admin trying to access |
| 404 | Not Found | User ID not found |
| 422 | Unprocessable Entity | Validation failed |
| 500 | Internal Server Error | Unexpected error |

---

## 🧪 TESTING GUIDE

### Test File Location
```
test-user-management.http
```

### Testing Workflow

#### 1. **Setup**
```http
# Update these variables in test-user-management.http
@baseUrl = http://localhost:8000/api
@adminToken = YOUR_ADMIN_TOKEN
@teacherToken = YOUR_TEACHER_TOKEN
@studentToken = YOUR_STUDENT_TOKEN
```

#### 2. **Basic CRUD Tests**
```http
# Test 1: Create student
POST /api/users
# Verify: TaiKhoan and HocSinh records created

# Test 2: Create teacher
POST /api/users
# Verify: TaiKhoan and GiaoVien records created

# Test 3: Get all users
GET /api/users
# Verify: Returns all users with details

# Test 4: Filter by role
GET /api/users?Role=hocsinh
# Verify: Only students returned

# Test 5: Update user
PUT /api/users/TK001
# Verify: Fields updated successfully

# Test 6: Toggle status
POST /api/users/TK001/toggle-status
# Verify: Status changed
```

#### 3. **Password Hashing Tests (UR-05.3)**
```http
# Test 7: Create user and check hash
POST /api/users
Body: { "MatKhau": "plainpassword" }
# Verify in database: MatKhau starts with $2y$10$

# Test 8: Update password and check hash
PUT /api/users/TK001
Body: { "MatKhau": "newpassword" }
# Verify in database: MatKhau changed and hashed
```

#### 4. **Permission Tests**
```http
# Test 9: Teacher tries to create (should fail)
POST /api/users
Authorization: Bearer {teacherToken}
# Verify: 403 Forbidden

# Test 10: Student tries to view (should fail)
GET /api/users
Authorization: Bearer {studentToken}
# Verify: 403 Forbidden

# Test 11: Admin creates successfully
POST /api/users
Authorization: Bearer {adminToken}
# Verify: 201 Created
```

#### 5. **Validation Tests**
```http
# Test 12: Duplicate username
POST /api/users
Body: { "TenDangNhap": "existing" }
# Verify: 422 "Tên đăng nhập đã tồn tại"

# Test 13: Invalid email
POST /api/users
Body: { "Email": "invalid" }
# Verify: 422 "Email không đúng định dạng"

# Test 14: Short password
POST /api/users
Body: { "MatKhau": "12345" }
# Verify: 422 "Mật khẩu phải có ít nhất 6 ký tự"

# Test 15: Missing HoTen for student
POST /api/users
Body: { "Role": "hocsinh" } // no HoTen
# Verify: 422 "Họ tên không được để trống..."
```

#### 6. **Auto-Generation Tests**
```http
# Test 16: Create multiple students
POST /api/users (student 1)
POST /api/users (student 2)
POST /api/users (student 3)
# Verify: MaHS = HS001, HS002, HS003

# Test 17: Create multiple teachers
POST /api/users (teacher 1)
POST /api/users (teacher 2)
# Verify: MaGV = GV001, GV002
```

#### 7. **Toggle Status Tests**
```http
# Test 18: Lock account
POST /api/users/TK002/toggle-status
# Verify: TrangThai = false, message "Đã khóa"

# Test 19: Unlock account
POST /api/users/TK002/toggle-status
# Verify: TrangThai = true, message "Đã mở khóa"

# Test 20: Try to lock admin (should fail)
POST /api/users/TK001/toggle-status (where TK001 is admin)
# Verify: 400 "Không thể khóa tài khoản quản trị viên"
```

### Expected Test Results

✅ **All tests should pass if:**
- Admin can perform all operations
- Non-admins blocked with 403
- Passwords are hashed (check database)
- Related records created (HocSinh/GiaoVien)
- IDs auto-generated sequentially
- Validation catches all errors
- Toggle status works (except for admins)
- Role cannot be changed
- Transactions rollback on errors

---

## ⚠️ ERROR HANDLING

### Common Errors and Solutions

#### 1. **Permission Denied**
**Error:** 403 "Chỉ quản trị viên mới có quyền quản lý người dùng"
**Cause:** Non-admin trying to access
**Solution:** 
- Login as admin user
- Verify token is from admin account
- Check user `Role` field in database

#### 2. **Duplicate Username**
**Error:** 422 "Tên đăng nhập đã tồn tại"
**Cause:** Username already in use
**Solution:**
- Choose different username
- Check existing usernames first
- Use unique naming convention

#### 3. **Duplicate Email**
**Error:** 422 "Email đã được sử dụng"
**Cause:** Email already registered
**Solution:**
- Use different email address
- Verify email not in TaiKhoan table

#### 4. **Cannot Change Role**
**Error:** 400 "Không thể thay đổi Role của người dùng..."
**Cause:** Trying to update Role field
**Solution:**
- Create new account with desired role
- Cannot convert existing user to different role
- This is by design for data integrity

#### 5. **Cannot Lock Admin**
**Error:** 400 "Không thể khóa tài khoản quản trị viên"
**Cause:** Trying to toggle status of admin account
**Solution:**
- This is a protection feature
- Admin accounts cannot be locked
- Prevents accidental self-lockout

#### 6. **Transaction Rollback**
**Error:** 500 with rollback message
**Cause:** Error during related record creation
**Solution:**
- Check database constraints
- Verify foreign keys
- Check for database connection issues
- Review error logs

---

## 📊 DATABASE SCHEMA

### TaiKhoan Table Structure

```sql
CREATE TABLE TaiKhoan (
    MaTK CHAR(10) PRIMARY KEY,
    TenDangNhap VARCHAR(50) UNIQUE NOT NULL,
    MatKhau VARCHAR(255) NOT NULL,  -- Hashed password
    Email VARCHAR(100) UNIQUE NOT NULL,
    Role ENUM('admin', 'giaovien', 'hocsinh') NOT NULL,
    TrangThai BOOLEAN DEFAULT 1,
    LanDangNhapCuoi DATETIME NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

### Related Tables

```sql
-- HocSinh (Student details)
CREATE TABLE HocSinh (
    MaHS CHAR(10) PRIMARY KEY,
    MaTK CHAR(10) NOT NULL,
    HoTen VARCHAR(100) NOT NULL,
    Lop VARCHAR(20),
    Truong VARCHAR(100),
    FOREIGN KEY (MaTK) REFERENCES TaiKhoan(MaTK) ON DELETE CASCADE
);

-- GiaoVien (Teacher details)
CREATE TABLE GiaoVien (
    MaGV CHAR(10) PRIMARY KEY,
    MaTK CHAR(10) NOT NULL,
    HoTen VARCHAR(100),
    SoDienThoai VARCHAR(15),
    ChuyenMon VARCHAR(100),
    FOREIGN KEY (MaTK) REFERENCES TaiKhoan(MaTK) ON DELETE CASCADE
);

-- QuanTriVien (Admin details)
CREATE TABLE QuanTriVien (
    MaQTV CHAR(10) PRIMARY KEY,
    MaTK CHAR(10) NOT NULL,
    FOREIGN KEY (MaTK) REFERENCES TaiKhoan(MaTK) ON DELETE CASCADE
);
```

### Relationships

```
TaiKhoan (One) → (One) HocSinh
  - Access via: $taiKhoan->hocSinh

TaiKhoan (One) → (One) GiaoVien
  - Access via: $taiKhoan->giaoVien

TaiKhoan (One) → (One) QuanTriVien
  - Access via: $taiKhoan->quanTriVien
```

---

## 🎓 USAGE EXAMPLES

### Example 1: Create Student Account
```http
POST /api/users
Authorization: Bearer {admin_token}
Content-Type: application/json

{
  "TenDangNhap": "nguyenvana",
  "Email": "nguyenvana@school.edu.vn",
  "MatKhau": "student123",
  "Role": "hocsinh",
  "HoTen": "Nguyen Van A",
  "Lop": "10A1",
  "Truong": "THPT Nguyen Hue"
}
```

**What happens:**
1. Creates `TaiKhoan` with MaTK = TK001
2. Password "student123" hashed → `$2y$10$...`
3. Creates `HocSinh` with MaHS = HS001
4. Returns both records in response

### Example 2: Lock User Account
```http
POST /api/users/TK002/toggle-status
Authorization: Bearer {admin_token}
```

**Result:** User cannot login until unlocked

### Example 3: Filter Teachers
```http
GET /api/users?Role=giaovien
Authorization: Bearer {admin_token}
```

**Returns:** All teachers with their GiaoVien details

### Example 4: Update User Email
```http
PUT /api/users/TK001
Authorization: Bearer {admin_token}
Content-Type: application/json

{
  "Email": "newemail@example.com"
}
```

**Result:** Only email updated, everything else unchanged

---

## 🔍 TROUBLESHOOTING

### Problem: "Chỉ quản trị viên mới có quyền..."
**Solution:**
- Verify you're logged in as admin
- Check token validity
- Verify user `Role = 'admin'` in database

### Problem: Transaction rollback errors
**Solution:**
- Check database foreign key constraints
- Verify all related tables exist
- Check for database locks
- Review Laravel logs

### Problem: Password not hashing
**Solution:**
- Verify Hash::make() is being called
- Check MatKhau is not in fillable array issue
- Verify bcrypt is enabled in PHP

### Problem: Related records not created
**Solution:**
- Check transaction is committing
- Verify auto-generation logic
- Check database constraints
- Review error logs

---

## 📝 NOTES

1. **Admin Protection:**
   - Admin accounts cannot be locked
   - Prevents system lockout
   - Use with caution

2. **Role Changes:**
   - Cannot change user role after creation
   - Must create new account for role change
   - Prevents data inconsistency

3. **Password Security:**
   - All passwords hashed automatically
   - Never log plain text passwords
   - Use strong passwords in production

4. **Transaction Safety:**
   - All creates use DB transactions
   - Automatic rollback on errors
   - Ensures data integrity

5. **Performance:**
   - Uses eager loading for relationships
   - Efficient queries with indexes
   - Consider pagination for large datasets

---

## 🔗 RELATED FEATURES

- **UR-05.3:** Password Security (implemented in this feature)
- **Authentication:** Uses Sanctum tokens
- **Question Bank Management:** Teachers created here can manage questions
- **Exam Management:** Teachers can create exams
- **Student Exams:** Students created here can take exams

---

## 👥 AUTHOR

**Feature:** User Management (UR-04.1)  
**Implementation Date:** December 7, 2025  
**Last Updated:** December 7, 2025  
**Version:** 1.0.0

---

**END OF DOCUMENTATION**
