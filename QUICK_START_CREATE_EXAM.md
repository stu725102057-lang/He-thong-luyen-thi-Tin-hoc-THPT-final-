# 🚀 Quick Start - Create Exam Feature (UR-03.3)

## ✅ Implementation Complete!

The "Create Exam" feature for teachers has been successfully implemented.

---

## 📦 What Was Implemented

### Backend Components:
- ✅ **Controller**: `DeThiController.php` with `taoDeThi()` method
- ✅ **Route**: `POST /api/tao-de-thi` (auth:sanctum protected)
- ✅ **Validation**: TenDe, ThoiGianLamBai validation
- ✅ **Authorization**: Only teachers can create exams
- ✅ **Auto-generation**: MaDe codes (DT001, DT002, ...)
- ✅ **Error Handling**: 401, 403, 422, 500 responses

---

## 🎯 Quick API Reference

### Endpoint
```
POST /api/tao-de-thi
```

### Headers
```
Authorization: Bearer {teacher_token}
Content-Type: application/json
```

### Request Body
```json
{
  "TenDe": "Đề thi Tin học THPT 2025",
  "ThoiGianLamBai": 90,
  "MoTa": "Đề thi giữa kỳ..." // Optional
}
```

### Success Response (201)
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
    "MoTa": "Đề thi giữa kỳ...",
    "TrangThai": "Hoạt động"
  }
}
```

---

## 🧪 How to Test

### Step 1: Start Server
```bash
php artisan serve
```

### Step 2: Login as Teacher
Open `test-create-exam.http` in VS Code and run:
```http
POST http://localhost:8000/api/login
Content-Type: application/json

{
  "TenDN": "giaovien1",
  "MatKhau": "123456"
}
```

### Step 3: Copy Token
From the response, copy the `token` value.

### Step 4: Create Exam
Replace `YOUR_TEACHER_TOKEN_HERE` with your actual token and run:
```http
POST http://localhost:8000/api/tao-de-thi
Authorization: Bearer YOUR_TEACHER_TOKEN_HERE
Content-Type: application/json

{
  "TenDe": "Đề thi Tin học THPT 2025",
  "ThoiGianLamBai": 90,
  "MoTa": "Đề thi giữa kỳ môn Tin học lớp 12"
}
```

### Step 5: Verify
Check the response for `MaDe` (should be DT001, DT002, etc.)

---

## ⚙️ Validation Rules

| Field | Type | Required | Rules | Description |
|-------|------|----------|-------|-------------|
| TenDe | string | ✅ Yes | max:200 | Exam name |
| ThoiGianLamBai | integer | ✅ Yes | 1-300 | Duration in minutes |
| MoTa | string | ❌ No | nullable | Exam description |

---

## 🔐 Authorization

### Who Can Create Exams?
- ✅ **Teachers** (users in GiaoVien table)
- ❌ **Students** (403 Forbidden)
- ❌ **Admins** (403 Forbidden - unless also a teacher)

### How It Works:
1. User must be authenticated (have valid token)
2. System checks if user exists in `GiaoVien` table
3. If yes → allow creation
4. If no → return 403 error

---

## 🔢 Auto-Generated MaDe

### Format: `DT###`
- **DT** = "Đề Thi" (prefix)
- **###** = 3-digit number (001, 002, ...)

### Examples:
| Order | MaDe |
|-------|------|
| 1st exam | DT001 |
| 2nd exam | DT002 |
| 10th exam | DT010 |
| 100th exam | DT100 |

---

## ❌ Common Errors

### Error 401: Unauthenticated
**Cause**: No token or invalid token  
**Solution**: Login first and use the token

### Error 403: Forbidden
**Cause**: User is not a teacher  
**Solution**: Login with a teacher account (giaovien1)

### Error 422: Validation Error
**Cause**: Missing or invalid fields  
**Solution**: Check TenDe and ThoiGianLamBai fields

### Error 500: Server Error
**Cause**: Database or server issue  
**Solution**: Check logs and database connection

---

## 📊 Use Case Examples

### 1. Quick Quiz (15 minutes)
```json
{
  "TenDe": "Kiểm tra 15 phút - Chương 1",
  "ThoiGianLamBai": 15
}
```

### 2. Mid-term Exam (45 minutes)
```json
{
  "TenDe": "Kiểm tra giữa kỳ 1",
  "ThoiGianLamBai": 45,
  "MoTa": "Bao gồm bài 1-5"
}
```

### 3. Final Exam (90 minutes)
```json
{
  "TenDe": "Đề thi học kỳ 1",
  "ThoiGianLamBai": 90,
  "MoTa": "Đề thi tổng hợp học kỳ 1"
}
```

### 4. Practice Test (120 minutes)
```json
{
  "TenDe": "Đề thi thử THPT Quốc gia",
  "ThoiGianLamBai": 120,
  "MoTa": "Đề thi thử theo cấu trúc Bộ GD&ĐT"
}
```

---

## 📁 Files Created/Modified

| File | Status | Purpose |
|------|--------|---------|
| `app/Http/Controllers/DeThiController.php` | ✅ Created | Controller with taoDeThi method |
| `routes/api.php` | ✅ Modified | Added route and import |
| `CREATE_EXAM_FEATURE.md` | ✅ Created | Complete documentation |
| `test-create-exam.http` | ✅ Created | 20 test cases |
| `QUICK_START_CREATE_EXAM.md` | ✅ Created | This guide |

---

## 🎓 Test Credentials

| Username | Password | Role | ID |
|----------|----------|------|-----|
| giaovien1 | 123456 | Teacher | GV001 |
| hocsinh1 | 123456 | Student | HS001 |
| admin | 123456 | Admin | - |

**Note**: Only `giaovien1` can create exams!

---

## 🔄 Typical Workflow

```mermaid
graph TD
    A[Teacher Login] --> B[Get Token]
    B --> C[Call /tao-de-thi API]
    C --> D[System Validates Input]
    D --> E[System Checks Teacher Role]
    E --> F[Generate MaDe]
    F --> G[Create DeThi Record]
    G --> H[Return Success Response]
```

### Steps:
1. **Login**: Get authentication token
2. **Validate**: System checks input fields
3. **Authorize**: System verifies teacher role
4. **Generate**: System creates unique MaDe
5. **Create**: System saves exam to database
6. **Respond**: System returns exam details

---

## 🚨 Important Notes

1. ⚠️ **Only teachers** can create exams
2. ⚠️ **MaDe** is auto-generated (don't provide it)
3. ⚠️ **SoLuongCauHoi** starts at 0 (add questions separately)
4. ⚠️ **TrangThai** defaults to true (active)
5. ⚠️ **NgayTao** is set automatically
6. ✅ **MoTa** is optional (can be omitted)
7. ✅ **ThoiGianLamBai** is in minutes (1-300)
8. ✅ Token expires after some time (re-login if needed)

---

## 🎯 Next Features to Implement

After creating an exam, you'll need:

1. **Add Questions to Exam** - Link CauHoi to DeThi
2. **List All Exams** - View all exams by teacher
3. **View Exam Details** - See exam with questions
4. **Edit Exam** - Update exam information
5. **Delete Exam** - Remove exam
6. **Publish/Unpublish** - Change TrangThai

---

## 📚 Documentation Links

- **Complete Documentation**: `CREATE_EXAM_FEATURE.md`
- **Test Cases**: `test-create-exam.http` (20 scenarios)
- **Quick Start**: `QUICK_START_CREATE_EXAM.md` (this file)

---

## ✅ Quick Checklist

Before testing:
- [ ] Server is running (`php artisan serve`)
- [ ] Database is migrated and seeded
- [ ] Teacher account exists (giaovien1)
- [ ] REST Client extension installed in VS Code

For each test:
- [ ] Login as teacher first
- [ ] Copy the token from response
- [ ] Replace `YOUR_TEACHER_TOKEN_HERE` in requests
- [ ] Check response status (201 = success)
- [ ] Verify `MaDe` in response

---

## 💡 Tips

1. **Save Token**: Store the token in a variable at the top of your .http file
2. **Test Order**: Run success cases first, then error cases
3. **Check Database**: Query `SELECT * FROM DeThi` to verify
4. **Multiple Exams**: Create several exams to see MaDe increment
5. **Role Testing**: Try with student token to verify 403 error

---

## 🎉 Success Criteria

You know it's working when:
- ✅ Login as teacher returns a token
- ✅ Create exam returns 201 status
- ✅ Response includes auto-generated MaDe (DT001, DT002, ...)
- ✅ Database has new DeThi record
- ✅ Student account gets 403 error
- ✅ No token gets 401 error
- ✅ Invalid input gets 422 error

---

**Feature**: Create Exam (UR-03.3)  
**Status**: ✅ Ready to Test  
**Date**: December 6, 2025  
**Version**: 1.0.0
