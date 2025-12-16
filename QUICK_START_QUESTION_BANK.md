# QUICK START: Question Bank Management 🚀

## 5-Minute Setup & Usage Guide

---

## 📋 PREREQUISITES

✅ Laravel application running  
✅ Database migrated  
✅ User authenticated with Sanctum  
✅ User role is `giaovien` (teacher) or `admin`

---

## 🎯 BASIC USAGE

### 1. Create Your First Question (Auto-Generated ID)

**Request:**
```http
POST http://localhost:8000/api/cau-hoi
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

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

**Response:**
```json
{
  "success": true,
  "message": "Tạo câu hỏi thành công",
  "data": {
    "MaCH": "CH001",  // ← Auto-generated!
    "NoiDung": "Python là ngôn ngữ lập trình thuộc loại nào?",
    "DapAnDung": "B",
    "MucDo": "De",
    ...
  }
}
```

---

### 2. View All Questions

**Request:**
```http
GET http://localhost:8000/api/cau-hoi
Authorization: Bearer YOUR_TOKEN
```

**Response:**
```json
{
  "success": true,
  "message": "Lấy danh sách câu hỏi thành công",
  "data": [
    {
      "MaCH": "CH001",
      "NoiDung": "Python là ngôn ngữ...",
      "DapAnDung": "B",
      "MucDo": "De",
      ...
    }
  ]
}
```

---

### 3. Filter Questions

**By Subject:**
```http
GET http://localhost:8000/api/cau-hoi?MaMon=NH001
```

**By Difficulty:**
```http
GET http://localhost:8000/api/cau-hoi?MucDo=De
GET http://localhost:8000/api/cau-hoi?MucDo=TrungBinh
GET http://localhost:8000/api/cau-hoi?MucDo=Kho
```

**Combined:**
```http
GET http://localhost:8000/api/cau-hoi?MaMon=NH001&MucDo=De
```

---

### 4. Update Question

**Partial Update (only change what you need):**
```http
PUT http://localhost:8000/api/cau-hoi/CH001
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

{
  "MucDo": "TrungBinh",
  "DapAnDung": "C"
}
```

**Full Update:**
```http
PUT http://localhost:8000/api/cau-hoi/CH001
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

{
  "NoiDung": "Updated question content",
  "DapAnA": "New option A",
  "DapAnB": "New option B",
  "DapAnC": "New option C",
  "DapAnD": "New option D",
  "DapAnDung": "B",
  "MucDo": "Kho",
  "MaMon": "NH001"
}
```

---

### 5. Delete Question

```http
DELETE http://localhost:8000/api/cau-hoi/CH001
Authorization: Bearer YOUR_TOKEN
```

**Success:**
```json
{
  "success": true,
  "message": "Xóa câu hỏi thành công"
}
```

**If used in exams:**
```json
{
  "success": false,
  "message": "Không thể xóa câu hỏi vì đã được sử dụng trong 3 đề thi"
}
```

---

## 🎨 FIELD NAMES CHEAT SHEET

You can use **EITHER** set of field names:

| What You Want | Option 1 (Friendly) | Option 2 (Database) |
|---------------|---------------------|---------------------|
| Correct Answer | `DapAnDung` | `DapAn` |
| Difficulty | `MucDo` | `DoKho` |
| Subject/Bank | `MaMon` | `MaNH` |

### Difficulty Values

| Vietnamese | English | Database Value |
|------------|---------|----------------|
| Dễ | Easy | `De` |
| Trung Bình | Medium | `TrungBinh` or `TB` |
| Khó | Hard | `Kho` |

**System automatically converts `TrungBinh` ↔ `TB`**

---

## ✅ VALIDATION QUICK CHECK

### Required Fields (Create)
- ✅ `NoiDung` - Question content
- ✅ `DapAnA`, `DapAnB`, `DapAnC`, `DapAnD` - All 4 answer options
- ✅ `DapAnDung` or `DapAn` - Correct answer (A, B, C, or D)
- ✅ `MucDo` or `DoKho` - Difficulty (De, TrungBinh/TB, Kho)
- ✅ `MaMon` or `MaNH` - Question bank ID (must exist)

### Optional Fields (Update)
- All fields optional
- Only update what you provide
- Same validation rules apply

---

## 🔐 PERMISSIONS

| Action | Student | Teacher | Admin |
|--------|---------|---------|-------|
| View | ✅ | ✅ | ✅ |
| Create | ❌ | ✅ | ✅ |
| Update | ❌ | ✅ | ✅ |
| Delete | ❌ | ✅ | ✅ |

---

## 🚨 COMMON ERRORS

### 1. "Bạn không có quyền truy cập..."
**Problem:** You're a student trying to create/update/delete  
**Solution:** Login as teacher or admin

### 2. "Dữ liệu không hợp lệ"
**Problem:** Missing required field or invalid value  
**Solution:** Check validation requirements above

### 3. "Không tìm thấy câu hỏi"
**Problem:** Question ID doesn't exist  
**Solution:** Verify MaCH is correct (case-sensitive)

### 4. "Không thể xóa câu hỏi..."
**Problem:** Question is used in exams  
**Solution:** Remove from exams first, or keep the question

---

## 🧪 TEST IN 60 SECONDS

1. **Copy test file:** Open `test-question-bank.http`

2. **Update variables:**
   ```
   @baseUrl = http://localhost:8000/api
   @token = YOUR_ACTUAL_TOKEN
   ```

3. **Run tests:** Click "Send Request" on each test

4. **Verify:**
   - First question creates `CH001`
   - Second question creates `CH002`
   - Filters return correct subsets
   - Updates modify only specified fields
   - Delete works or shows protection message

---

## 💡 PRO TIPS

### Tip 1: Use User-Friendly Names
```json
{
  "DapAnDung": "B",
  "MucDo": "De",
  "MaMon": "NH001"
}
```
Easier to read and understand!

### Tip 2: Partial Updates Save Time
```json
{
  "MucDo": "TrungBinh"
}
```
Only change difficulty, keep everything else!

### Tip 3: Filter Before Creating Exams
```http
GET /api/cau-hoi?MaMon=NH001&MucDo=De
```
Get all easy questions from a subject to build balanced exams!

### Tip 4: Check Before Delete
```http
GET /api/cau-hoi/CH001
# Check if question is good to delete
DELETE /api/cau-hoi/CH001
```

---

## 📞 NEED HELP?

1. **Check full documentation:** `QUESTION_BANK_FEATURE.md`
2. **Run all tests:** `test-question-bank.http`
3. **Check logs:** `storage/logs/laravel.log`
4. **Database issues:** Verify `CauHoi` and `NganHangCauHoi` tables exist

---

## 🎉 YOU'RE READY!

You now know how to:
- ✅ Create questions (with auto-generated IDs)
- ✅ View and filter questions
- ✅ Update questions (partial or full)
- ✅ Delete questions (with protection)
- ✅ Use flexible field names
- ✅ Handle errors

**Start building your question bank!** 🚀

---

**Quick Reference Card:**
```
GET    /api/cau-hoi           → List all questions
GET    /api/cau-hoi?MaMon=X   → Filter by subject
GET    /api/cau-hoi?MucDo=X   → Filter by difficulty
POST   /api/cau-hoi           → Create question (auto ID)
PUT    /api/cau-hoi/{id}      → Update question
DELETE /api/cau-hoi/{id}      → Delete question
```

**Remember:** Teacher/Admin only for POST/PUT/DELETE!
