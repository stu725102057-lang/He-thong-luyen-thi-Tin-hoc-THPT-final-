# 🚀 Quick Start Guide - Cheating Detection Feature

## ✅ Implementation Complete!

The Cheating Detection feature (UR-05.1) has been successfully implemented. Here's how to use it:

---

## 📦 What Was Implemented

### 1. Database Changes
- ✅ Added `SoLanViPham` column to `BaiLam` table (integer, default: 0)

### 2. Backend Logic
- ✅ Created `ghiNhanGianLan()` method in `BaiThiController`
- ✅ Added validation for `MaDe` and `MaHS`
- ✅ Implemented violation counter logic
- ✅ Added comprehensive error handling

### 3. API Endpoint
- ✅ Route: `POST /api/ghi-nhan-gian-lan`
- ✅ Protected by `auth:sanctum` middleware
- ✅ Returns JSON response with violation count

---

## 🔧 How to Test

### Step 1: Run Migrations (if not already done)
```bash
cd "D:\Hệ thống luyện thi THPT môn Tin học"
php artisan migrate:refresh --seed
```

### Step 2: Start the Server
```bash
php artisan serve
```

### Step 3: Login to Get Token
Use the REST Client extension in VS Code to open `test-cheating-detection.http`:

1. Click on "Send Request" above the login section
2. Copy the `token` from the response
3. Replace `YOUR_TOKEN_HERE` with your actual token

### Step 4: Test the Endpoint
Click "Send Request" on any of the test cases in `test-cheating-detection.http`

---

## 💡 Usage Example

### Request:
```http
POST http://localhost:8000/api/ghi-nhan-gian-lan
Authorization: Bearer 1|abc123...
Content-Type: application/json

{
  "MaDe": "DT001",
  "MaHS": "HS001"
}
```

### Response (Success):
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

---

## 🎯 When to Call This API

This endpoint should be called from your frontend when detecting:

1. **Tab Switching** - Student leaves the exam tab
2. **Window Blur** - Student clicks outside the browser
3. **Copy/Paste** - Student tries to copy or paste
4. **Right Click** - Student opens context menu
5. **DevTools** - Student opens browser developer tools
6. **Screenshot Tools** - Detection of screen capture attempts

---

## 📊 Frontend Integration Example

```javascript
// Detect when student switches tabs
document.addEventListener('visibilitychange', async () => {
  if (document.hidden && examInProgress) {
    await recordViolation();
  }
});

// Detect window blur
window.addEventListener('blur', async () => {
  if (examInProgress) {
    await recordViolation();
  }
});

// Detect copy/paste
document.addEventListener('copy', async (e) => {
  e.preventDefault();
  await recordViolation();
  alert('Cảnh báo: Không được phép sao chép!');
});

async function recordViolation() {
  const token = localStorage.getItem('authToken');
  const examId = localStorage.getItem('currentExamId');
  const studentId = localStorage.getItem('studentId');

  try {
    const response = await fetch('/api/ghi-nhan-gian-lan', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        MaDe: examId,
        MaHS: studentId
      })
    });

    const data = await response.json();
    
    if (data.success) {
      console.log(`Violations: ${data.data.SoLanViPham}`);
      
      // Show warning after 3 violations
      if (data.data.SoLanViPham >= 3) {
        alert('CẢNH BÁO: Bạn đã vi phạm quy định thi 3 lần!');
      }
      
      // Auto-submit after 5 violations
      if (data.data.SoLanViPham >= 5) {
        alert('Bài thi sẽ tự động nộp do vi phạm quá nhiều lần!');
        submitExam();
      }
    }
  } catch (error) {
    console.error('Error recording violation:', error);
  }
}
```

---

## 📁 Files Modified

1. **Migration**: `database/migrations/2025_12_06_112340_create_all_tables_for_trac_nghiem_system.php`
   - Added `SoLanViPham` column

2. **Model**: `app/Models/BaiLam.php`
   - Added `SoLanViPham` to fillable array

3. **Controller**: `app/Http/Controllers/BaiThiController.php`
   - Created `ghiNhanGianLan()` method

4. **Routes**: `routes/api.php`
   - Added `/ghi-nhan-gian-lan` endpoint

---

## 📝 Test Credentials

Default accounts from seeder:

| Role | Username | Password | MaHS/MaGV |
|------|----------|----------|-----------|
| Student | hocsinh1 | 123456 | HS001 |
| Teacher | giaovien1 | 123456 | GV001 |
| Admin | admin | 123456 | - |

---

## 🔍 Monitoring Violations

Query to check violations in database:

```sql
SELECT 
    b.MaBaiLam,
    b.MaDe,
    b.MaHS,
    b.SoLanViPham,
    b.TrangThai,
    b.Diem,
    h.HoTen as TenHocSinh
FROM BaiLam b
JOIN HocSinh h ON b.MaHS = h.MaHS
WHERE b.SoLanViPham > 0
ORDER BY b.SoLanViPham DESC;
```

---

## ⚠️ Important Notes

1. **Only Active Exams**: Violations are only recorded for exams with `TrangThai = 'DangLam'`
2. **Authentication Required**: All requests must include a valid Bearer token
3. **Increment by 1**: Each successful call adds exactly 1 to the counter
4. **No Limit**: There's no maximum violation count (implement in frontend if needed)
5. **Persistent**: Violation count is saved permanently in the database

---

## 🐛 Troubleshooting

### Problem: "Không tìm thấy bài làm đang thực hiện"

**Solution**: Make sure you have a `BaiLam` record with:
- `TrangThai = 'DangLam'`
- Matching `MaDe` and `MaHS`

Create test data:
```bash
php artisan tinker

App\Models\BaiLam::create([
    'MaBaiLam' => 'BL001',
    'MaHS' => 'HS001',
    'MaDe' => 'DT001',
    'TrangThai' => 'DangLam',
    'ThoiGianBatDau' => now(),
    'DSCauTraLoi' => [],
    'SoLanViPham' => 0
]);
```

### Problem: "Unauthenticated"

**Solution**: Make sure you:
1. Login first to get a token
2. Include the token in Authorization header
3. Token format: `Bearer {your_token}`

### Problem: Column 'SoLanViPham' doesn't exist

**Solution**: Run migrations:
```bash
php artisan migrate:refresh --seed
```

---

## 📚 Documentation Files

- `CHEATING_DETECTION_SUMMARY.md` - Complete implementation details
- `test-cheating-detection.http` - All test cases
- `QUICK_START_CHEATING_DETECTION.md` - This file

---

## ✅ Next Steps

1. ✅ Implementation is complete
2. 🔄 Run migrations if needed: `php artisan migrate:refresh --seed`
3. 🧪 Test using `test-cheating-detection.http`
4. 🎨 Integrate with your frontend application
5. 📊 Add reporting dashboard for teachers/admins

---

**Status**: ✅ Ready for Production  
**Last Updated**: December 6, 2025  
**Version**: 1.0.0
