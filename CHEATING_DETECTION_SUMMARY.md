# Cheating Detection Feature (UR-05.1) - Implementation Summary

## ✅ Implementation Status: COMPLETE

All required components for the Cheating Detection feature have been successfully implemented.

---

## 📋 Implementation Details

### 1. **Database Schema** ✅
**File**: `database/migrations/2025_12_06_112340_create_all_tables_for_trac_nghiem_system.php`

Added `SoLanViPham` column to the `BaiLam` table:
```php
$table->integer('SoLanViPham')->default(0); // Số lần vi phạm (gian lận)
```

### 2. **Model Configuration** ✅
**File**: `app/Models/BaiLam.php`

Added `SoLanViPham` to the `$fillable` array:
```php
protected $fillable = [
    'MaBaiLam',
    'DSCauTraLoi',
    'Diem',
    'ThoiGianBatDau',
    'ThoiGianNop',
    'TrangThai',
    'SoLanViPham',  // ✅ Added for cheating detection
    'MaHS',
    'MaDe',
];
```

### 3. **Controller Method** ✅
**File**: `app/Http/Controllers/BaiThiController.php`

Implemented `ghiNhanGianLan(Request $request)` method with:

#### Features:
- ✅ Request validation for `MaDe` and `MaHS` (required strings)
- ✅ Finds `BaiLam` record with `TrangThai = 'DangLam'`
- ✅ Increments `SoLanViPham` counter by 1
- ✅ Returns success response with updated count
- ✅ Returns 404 error if no active exam found
- ✅ Returns 422 error for validation failures
- ✅ Returns 500 error for server exceptions

#### Method Logic:
```php
public function ghiNhanGianLan(Request $request)
{
    // 1. Validate MaDe and MaHS
    // 2. Find BaiLam where TrangThai = 'DangLam'
    // 3. Increment SoLanViPham
    // 4. Return success response with data
}
```

### 4. **API Route** ✅
**File**: `routes/api.php`

Added protected route inside `auth:sanctum` middleware group:
```php
Route::post('/ghi-nhan-gian-lan', [BaiThiController::class, 'ghiNhanGianLan']);
```

---

## 🔧 API Endpoint Specification

### **POST** `/api/ghi-nhan-gian-lan`

**Authentication**: Required (Bearer Token via Sanctum)

**Request Headers**:
```
Authorization: Bearer {your_token}
Content-Type: application/json
```

**Request Body**:
```json
{
  "MaDe": "DT001",
  "MaHS": "HS001"
}
```

**Success Response** (200):
```json
{
  "success": true,
  "message": "Đã ghi nhận hành vi gian lận",
  "data": {
    "MaBaiLam": "BL001",
    "MaDe": "DT001",
    "MaHS": "HS001",
    "SoLanViPham": 1,
    "ThoiGianGhiNhan": "2025-12-06 14:30:45"
  }
}
```

**Error Responses**:

1. **Validation Error** (422):
```json
{
  "success": false,
  "message": "Dữ liệu không hợp lệ",
  "errors": {
    "MaDe": ["Mã đề thi không được để trống"],
    "MaHS": ["Mã học sinh không được để trống"]
  }
}
```

2. **Not Found** (404):
```json
{
  "success": false,
  "message": "Không tìm thấy bài làm đang thực hiện"
}
```

3. **Server Error** (500):
```json
{
  "success": false,
  "message": "Có lỗi xảy ra khi ghi nhận gian lận",
  "error": "Error details..."
}
```

---

## 🧪 Testing Instructions

### Prerequisites:
1. Start the Laravel server: `php artisan serve`
2. Create a student account and login to get a token
3. Start an exam (create a `BaiLam` record with `TrangThai = 'DangLam'`)

### Test Cases:

#### **Test 1: Successful Cheating Detection**
```bash
POST http://localhost:8000/api/ghi-nhan-gian-lan
Authorization: Bearer {token}

{
  "MaDe": "DT001",
  "MaHS": "HS001"
}
```
**Expected**: 200 OK, `SoLanViPham` increments

#### **Test 2: Missing MaDe**
```bash
POST http://localhost:8000/api/ghi-nhan-gian-lan
Authorization: Bearer {token}

{
  "MaHS": "HS001"
}
```
**Expected**: 422 Validation Error

#### **Test 3: No Active Exam**
```bash
POST http://localhost:8000/api/ghi-nhan-gian-lan
Authorization: Bearer {token}

{
  "MaDe": "DT999",
  "MaHS": "HS001"
}
```
**Expected**: 404 Not Found

#### **Test 4: Multiple Violations**
Call the endpoint multiple times with the same `MaDe` and `MaHS`:
- First call: `SoLanViPham = 1`
- Second call: `SoLanViPham = 2`
- Third call: `SoLanViPham = 3`

---

## 🎯 Use Cases

This feature should be triggered when the frontend detects suspicious behavior such as:

1. **Tab Switching**: Student switches to another browser tab
2. **Window Focus Loss**: Student clicks outside the exam window
3. **Copy/Paste Actions**: Student attempts to copy or paste
4. **Right-Click**: Student tries to open context menu
5. **Keyboard Shortcuts**: Student uses Ctrl+C, Ctrl+V, etc.
6. **Multiple Monitors**: Detection of screen capture or second display
7. **Browser DevTools**: Opening developer console

### Frontend Integration Example:
```javascript
// Detect tab switching
document.addEventListener('visibilitychange', async () => {
  if (document.hidden && examInProgress) {
    await recordCheating(examId, studentId);
  }
});

async function recordCheating(MaDe, MaHS) {
  const response = await fetch('/api/ghi-nhan-gian-lan', {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ MaDe, MaHS })
  });
  
  const data = await response.json();
  console.log(`Violations: ${data.data.SoLanViPham}`);
  
  // Optional: Show warning to student
  if (data.data.SoLanViPham >= 3) {
    alert('Cảnh báo: Bạn đã vi phạm quy định thi quá 3 lần!');
  }
}
```

---

## 🔄 Database Migration

If you haven't run the migration yet, execute:

```bash
php artisan migrate:refresh --seed
```

This will create all tables including the `SoLanViPham` column in the `BaiLam` table.

---

## 📊 Monitoring & Reporting

The `SoLanViPham` data can be used for:

1. **Real-time Monitoring**: Track violations during exam
2. **Post-Exam Review**: Analyze cheating patterns
3. **Automatic Sanctions**: Flag exams with high violation counts
4. **Statistical Reports**: Generate cheating analytics

### Query Example:
```sql
-- Find exams with high violation counts
SELECT MaBaiLam, MaHS, MaDe, SoLanViPham, Diem
FROM BaiLam
WHERE SoLanViPham >= 3
ORDER BY SoLanViPham DESC;
```

---

## ✅ Checklist

- [x] Database column `SoLanViPham` added to `BaiLam` table
- [x] Model updated with `SoLanViPham` in fillable array
- [x] Controller method `ghiNhanGianLan()` implemented
- [x] Route `/api/ghi-nhan-gian-lan` added with auth:sanctum middleware
- [x] Request validation for MaDe and MaHS
- [x] Error handling for 404, 422, and 500 responses
- [x] Success response with updated violation count
- [x] Only tracks violations for active exams (`TrangThai = 'DangLam'`)

---

## 📝 Notes

- The feature only records violations for exams with `TrangThai = 'DangLam'` (in progress)
- Violations are NOT recorded for completed exams
- Each call increments the counter by exactly 1
- The counter starts at 0 (default value)
- Authentication is required via Sanctum token
- The endpoint is protected by the `auth:sanctum` middleware

---

**Implementation Date**: December 6, 2025  
**Status**: ✅ Production Ready  
**Version**: 1.0.0
