# QUICK START: User Management (Admin Only) 🚀

## 5-Minute Setup & Usage Guide

---

## 📋 PREREQUISITES

✅ Laravel application running  
✅ Database migrated  
✅ Logged in as **Admin** (Role = 'admin')  
✅ Admin authentication token obtained

**⚠️ IMPORTANT:** Only admins can access these endpoints!

---

## 🎯 BASIC USAGE

### 1. Get All Users

**Request:**
```http
GET http://localhost:8000/api/users
Authorization: Bearer YOUR_ADMIN_TOKEN
```

**Response:**
```json
{
  "success": true,
  "message": "Lấy danh sách người dùng thành công",
  "data": [
    {
      "MaTK": "TK001",
      "TenDangNhap": "student1",
      "Email": "student1@example.com",
      "Role": "hocsinh",
      "TrangThai": true,
      "ThongTinHocSinh": {
        "MaHS": "HS001",
        "HoTen": "Nguyen Van A",
        "Lop": "10A1"
      }
    }
  ],
  "total": 1
}
```

---

### 2. Create New Student

**Request:**
```http
POST http://localhost:8000/api/users
Authorization: Bearer YOUR_ADMIN_TOKEN
Content-Type: application/json

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

**Response:**
```json
{
  "success": true,
  "message": "Tạo người dùng thành công",
  "data": {
    "TaiKhoan": {
      "MaTK": "TK001",  // ← Auto-generated!
      "TenDangNhap": "nguyenvana",
      "Email": "nguyenvana@example.com",
      "Role": "hocsinh",
      "TrangThai": true
    },
    "RoleData": {
      "MaHS": "HS001",  // ← Auto-generated!
      "HoTen": "Nguyen Van A",
      "Lop": "10A1",
      "Truong": "THPT Nguyen Hue"
    }
  }
}
```

**What happened:**
- ✅ Account created with auto-generated `MaTK`
- ✅ Password hashed (not stored as plain text)
- ✅ `HocSinh` record created automatically
- ✅ Student can now login

---

### 3. Create New Teacher

**Request:**
```http
POST http://localhost:8000/api/users
Authorization: Bearer YOUR_ADMIN_TOKEN
Content-Type: application/json

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

**What happens:**
- ✅ Creates `TaiKhoan` (TK002)
- ✅ Creates `GiaoVien` record (GV001)
- ✅ Password hashed automatically
- ✅ Teacher can manage questions/exams

---

### 4. Create New Admin

**Request:**
```http
POST http://localhost:8000/api/users
Authorization: Bearer YOUR_ADMIN_TOKEN
Content-Type: application/json

{
  "TenDangNhap": "admin2",
  "Email": "admin2@example.com",
  "MatKhau": "admin123456",
  "Role": "admin"
}
```

**What happens:**
- ✅ Creates `TaiKhoan` (TK003)
- ✅ Creates `QuanTriVien` record (QTV001)
- ✅ New admin can manage users

---

### 5. Filter Users by Role

**Get Students Only:**
```http
GET http://localhost:8000/api/users?Role=hocsinh
Authorization: Bearer YOUR_ADMIN_TOKEN
```

**Get Teachers Only:**
```http
GET http://localhost:8000/api/users?Role=giaovien
Authorization: Bearer YOUR_ADMIN_TOKEN
```

**Get Admins Only:**
```http
GET http://localhost:8000/api/users?Role=admin
Authorization: Bearer YOUR_ADMIN_TOKEN
```

---

### 6. Update User

**Update Email:**
```http
PUT http://localhost:8000/api/users/TK001
Authorization: Bearer YOUR_ADMIN_TOKEN
Content-Type: application/json

{
  "Email": "newemail@example.com"
}
```

**Update Password:**
```http
PUT http://localhost:8000/api/users/TK001
Authorization: Bearer YOUR_ADMIN_TOKEN
Content-Type: application/json

{
  "MatKhau": "newpassword123"
}
```

**Note:** Password will be hashed automatically!

---

### 7. Lock/Unlock User Account

**Lock Account:**
```http
POST http://localhost:8000/api/users/TK002/toggle-status
Authorization: Bearer YOUR_ADMIN_TOKEN
```

**Response:**
```json
{
  "success": true,
  "message": "Đã khóa tài khoản thành công",
  "data": {
    "MaTK": "TK002",
    "TrangThai": false,
    "StatusText": "Đã khóa"
  }
}
```

**Unlock Account (run same request again):**
```http
POST http://localhost:8000/api/users/TK002/toggle-status
Authorization: Bearer YOUR_ADMIN_TOKEN
```

**Result:** User locked/unlocked (cannot login when locked)

---

## 🎨 FIELD REQUIREMENTS CHEAT SHEET

### For All Users (Required)
| Field | Description | Example |
|-------|-------------|---------|
| `TenDangNhap` | Username (unique) | "nguyenvana" |
| `Email` | Email (unique) | "user@example.com" |
| `MatKhau` | Password (min 6 chars) | "password123" |
| `Role` | Role | "hocsinh", "giaovien", "admin" |

### For Students (hocsinh)
| Field | Required | Description | Example |
|-------|----------|-------------|---------|
| `HoTen` | ✅ Yes | Full name | "Nguyen Van A" |
| `Lop` | ❌ No | Class | "10A1" |
| `Truong` | ❌ No | School | "THPT Nguyen Hue" |

### For Teachers (giaovien)
| Field | Required | Description | Example |
|-------|----------|-------------|---------|
| `HoTen` | ✅ Yes | Full name | "Tran Thi B" |
| `SoDienThoai` | ❌ No | Phone | "0123456789" |
| `ChuyenMon` | ❌ No | Specialization | "Tin học" |

### For Admins (admin)
No additional fields required!

---

## 🔐 PASSWORD SECURITY (UR-05.3)

### ✅ Automatic Hashing

**You provide:**
```json
{
  "MatKhau": "password123"
}
```

**Stored in database:**
```
$2y$10$e0MYzXyjpJS7Pd0RVvHwHeFcN8UqhK8xIBQSIunpkTwl6g2qHkj7u
```

**Why this matters:**
- ✅ Passwords never stored as plain text
- ✅ Cannot be recovered if forgotten
- ✅ Secure even if database compromised
- ✅ Industry-standard BCrypt encryption

---

## ✅ VALIDATION QUICK CHECK

### Common Errors

| Error | Meaning | Solution |
|-------|---------|----------|
| "Tên đăng nhập đã tồn tại" | Username taken | Use different username |
| "Email đã được sử dụng" | Email exists | Use different email |
| "Mật khẩu phải có ít nhất 6 ký tự" | Password too short | Use 6+ characters |
| "Họ tên không được để trống..." | Missing name | Add HoTen field |
| "Email không đúng định dạng" | Invalid email | Fix email format |

---

## 🔐 PERMISSIONS

| Action | Student | Teacher | Admin |
|--------|---------|---------|-------|
| View users | ❌ | ❌ | ✅ |
| Create user | ❌ | ❌ | ✅ |
| Update user | ❌ | ❌ | ✅ |
| Lock/Unlock | ❌ | ❌ | ✅ |

**⚠️ Only admins can manage users!**

If you're not admin:
```json
{
  "success": false,
  "message": "Chỉ quản trị viên mới có quyền quản lý người dùng"
}
```

---

## 🚨 COMMON ERRORS

### 1. "Chỉ quản trị viên mới có quyền..."
**Problem:** You're not logged in as admin  
**Solution:** Login with admin account

### 2. "Tên đăng nhập đã tồn tại"
**Problem:** Username already used  
**Solution:** Choose different username

### 3. "Email đã được sử dụng"
**Problem:** Email already registered  
**Solution:** Use different email

### 4. "Không thể thay đổi Role..."
**Problem:** Trying to change user role  
**Solution:** Cannot change role - create new account

### 5. "Không thể khóa tài khoản quản trị viên"
**Problem:** Trying to lock admin account  
**Solution:** Admin accounts protected from locking

---

## 🧪 TEST IN 60 SECONDS

1. **Copy test file:** Open `test-user-management.http`

2. **Update token:**
   ```
   @adminToken = YOUR_ACTUAL_ADMIN_TOKEN
   ```

3. **Run tests:** Click "Send Request" on each test

4. **Verify:**
   - ✅ Students get HS001, HS002, HS003...
   - ✅ Teachers get GV001, GV002, GV003...
   - ✅ Accounts get TK001, TK002, TK003...
   - ✅ Passwords are hashed in database
   - ✅ Toggle status locks/unlocks accounts
   - ✅ Non-admins get 403 errors

---

## 💡 PRO TIPS

### Tip 1: Check Password Hashing
```sql
-- Run in database
SELECT MaTK, TenDangNhap, MatKhau FROM TaiKhoan LIMIT 1;

-- MatKhau should look like:
-- $2y$10$e0MYzXyjpJS7Pd0RVvHwHe...
```

### Tip 2: Create Users in Bulk
```http
# Create multiple students rapidly
POST /api/users (student 1)
POST /api/users (student 2)
POST /api/users (student 3)
# IDs auto-increment: TK001, TK002, TK003
```

### Tip 3: Filter Before Managing
```http
# Get all inactive accounts
GET /api/users?Role=hocsinh
# Then lock/unlock as needed
```

### Tip 4: Cannot Delete Users
Currently only Lock/Unlock available. To "remove" a user:
```http
POST /api/users/{id}/toggle-status
# Sets TrangThai = false
# User cannot login
```

---

## 🎉 YOU'RE READY!

You now know how to:
- ✅ Create students, teachers, admins
- ✅ View users with filtering
- ✅ Update user information
- ✅ Lock/Unlock accounts
- ✅ Understand password security
- ✅ Handle errors

**Start managing your users!** 🚀

---

## 📞 NEED HELP?

1. **Check full documentation:** `USER_MANAGEMENT_FEATURE.md`
2. **Run all tests:** `test-user-management.http`
3. **Check logs:** `storage/logs/laravel.log`
4. **Database issues:** Verify `TaiKhoan`, `HocSinh`, `GiaoVien`, `QuanTriVien` tables exist

---

**Quick Reference Card:**
```
GET    /api/users              → List all users (filter: ?Role=X)
POST   /api/users              → Create user (auto IDs + password hash)
PUT    /api/users/{id}         → Update user (email, status, password)
POST   /api/users/{id}/toggle-status → Lock/Unlock account
```

**Remember:** Admin only! 🔐
