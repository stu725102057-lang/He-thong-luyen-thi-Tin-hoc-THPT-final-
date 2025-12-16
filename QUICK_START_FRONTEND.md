# 🚀 Quick Start Guide - Frontend Testing

## ✅ Prerequisites Checklist

- [x] Laravel backend configured
- [x] Database connected
- [x] API routes defined (`routes/api.php`)
- [x] Frontend SPA created (`resources/views/app.blade.php`)
- [x] Web route configured (`routes/web.php`)

## 🎯 Start Testing in 3 Steps

### Step 1: Start Laravel Server
```bash
php artisan serve
```

**Expected Output:**
```
Starting Laravel development server: http://127.0.0.1:8000
[TIMESTAMP] Laravel development server started on http://127.0.0.1:8000
```

### Step 2: Open Browser
Navigate to: **http://localhost:8000**

You should see the **Home Screen** with:
- Purple gradient background
- "Hệ thống Luyện thi THPT" title
- Two cards: "Đề thi mẫu" and "Đăng nhập"

### Step 3: Test Features

## 🧪 Testing Scenarios

### ✅ Scenario 1: Guest User (No Login)

#### Test A: View Home Page
1. Open `http://localhost:8000`
2. ✓ See home screen with 2 cards
3. ✓ Navbar shows: "Đề thi mẫu" and "Đăng nhập"

#### Test B: View Sample Exams
1. Click "Đề thi mẫu" button
2. ✓ Loading spinner appears
3. ✓ Exam cards displayed (or "Chưa có đề thi mẫu")

**Test API Manually:**
```bash
curl http://localhost:8000/api/de-thi-mau
```

### ✅ Scenario 2: Student Login

#### Test A: Login
1. Click "Đăng nhập" button
2. Enter credentials:
   - Username: `student001` (or your test student)
   - Password: `password123`
3. Click "Đăng nhập"
4. ✓ Alert: "Đăng nhập thành công!"
5. ✓ Navbar changes to: "Làm bài thi", "Lịch sử thi", "Đăng xuất"
6. ✓ Username appears in navbar

**Check LocalStorage:**
```javascript
// Open Browser Console (F12)
localStorage.getItem('token')
localStorage.getItem('user')
```

#### Test B: View Exam History
1. After login, click "Lịch sử thi"
2. ✓ Table shows past exam attempts
3. ✓ Shows: Exam name, Date, Score, "Xem chi tiết" button

**Test API Manually:**
```bash
curl -H "Authorization: Bearer YOUR_TOKEN" http://localhost:8000/api/lich-su-thi
```

#### Test C: View Result Details
1. Click "Xem chi tiết" on any exam
2. ✓ Alert popup shows detailed result
3. (TODO: Modal implementation)

#### Test D: Logout
1. Click "Đăng xuất"
2. ✓ Alert: "Đã đăng xuất"
3. ✓ Redirected to Home screen
4. ✓ Navbar reverts to guest menu

### ✅ Scenario 3: Teacher Login

#### Test A: Login
1. Go to Login screen
2. Enter teacher credentials:
   - Username: `teacher001`
   - Password: `teachpass123`
3. ✓ Navbar shows: "Quản lý câu hỏi", "Tạo đề thi", "Đăng xuất"

#### Test B: Import Questions
1. Click "Quản lý câu hỏi"
2. Select an Excel/CSV file
3. Click "Import câu hỏi"
4. ✓ Success alert or error message

**Prepare Test File:**
Create `test-questions.csv`:
```csv
NoiDung,DapAn1,DapAn2,DapAn3,DapAn4,DapAnDung,DoKho,MaMon
"What is 2+2?","3","4","5","6","B","de","TIN"
```

#### Test C: Create Exam
1. Click "Tạo đề thi"
2. Fill form:
   - Tên đề thi: "Đề thi thử số 1"
   - Môn học: "Tin học"
   - Thời gian: 60
   - Số câu hỏi: 20
   - Độ khó: Trung bình
3. Click "Tạo đề thi"
4. ✓ Success alert

**Test API Manually:**
```bash
curl -X POST http://localhost:8000/api/tao-de-thi \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "TenDe": "Test Exam",
    "MaMon": "Tin học",
    "ThoiGianLamBai": 60,
    "SoCauHoi": 20,
    "MucDo": "trungbinh"
  }'
```

### ✅ Scenario 4: Admin Login

#### Test A: Login
1. Go to Login screen
2. Enter admin credentials:
   - Username: `admin`
   - Password: `admin123456`
3. ✓ Navbar shows: "Quản lý người dùng", "Backup", "Đăng xuất"

#### Test B: View All Users
1. Click "Quản lý người dùng"
2. ✓ Table shows all users
3. ✓ Shows: MaTK, Username, Email, Role, Status

#### Test C: Filter by Role
1. Select "Học sinh" from dropdown
2. ✓ Table refreshes showing only students
3. Try "Giáo viên" and "Quản trị viên"

**Test API Manually:**
```bash
curl -H "Authorization: Bearer YOUR_TOKEN" http://localhost:8000/api/users
curl -H "Authorization: Bearer YOUR_TOKEN" http://localhost:8000/api/users?Role=hocsinh
```

#### Test D: Toggle User Status
1. Click "Khóa" button on a student
2. ✓ Confirmation dialog appears
3. Click OK
4. ✓ Status badge changes to "Đã khóa"
5. Button text changes to "Mở"

**Test API Manually:**
```bash
curl -X POST http://localhost:8000/api/users/TK001/toggle-status \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## 🐛 Troubleshooting

### Issue 1: Blank White Screen
**Cause:** JavaScript error  
**Solution:**
1. Open Browser Console (F12)
2. Check for errors in Console tab
3. Common fixes:
   - Clear browser cache (Ctrl+Shift+Delete)
   - Check `app.blade.php` syntax
   - Restart `php artisan serve`

### Issue 2: API Returns 404
**Cause:** Routes not loaded  
**Solution:**
```bash
php artisan route:clear
php artisan route:cache
php artisan route:list
```

Check that API routes exist:
```
POST   api/login
GET    api/de-thi-mau
GET    api/lich-su-thi
POST   api/cau-hoi/import
POST   api/tao-de-thi
GET    api/users
POST   api/users/{id}/toggle-status
```

### Issue 3: Login Fails
**Cause:** Invalid credentials or API error  
**Solution:**
1. Check Network tab in DevTools
2. Look at response:
   - 401: Wrong credentials
   - 422: Validation error
   - 500: Server error
3. Check Laravel logs: `storage/logs/laravel.log`

### Issue 4: CORS Error
**Cause:** API called from different origin  
**Solution:**
- Frontend MUST be served from same domain as API
- Use `http://localhost:8000` (NOT file://)
- Check `config/cors.php`

### Issue 5: Token Expired
**Cause:** JWT token expired  
**Solution:**
```javascript
// Clear session in Console
localStorage.clear();
location.reload();
```

## 📊 Browser DevTools Cheatsheet

### Console Tab
```javascript
// Check authentication
localStorage.getItem('token')
JSON.parse(localStorage.getItem('user'))

// Manually change role for testing
let user = JSON.parse(localStorage.getItem('user'));
user.Role = 'giaovien';
localStorage.setItem('user', JSON.stringify(user));
location.reload();

// Clear session
localStorage.clear();
```

### Network Tab
1. Open DevTools (F12) → Network
2. Filter by "Fetch/XHR"
3. Click on a request to see:
   - **Headers**: Check Authorization token
   - **Preview**: See JSON response
   - **Response**: Raw response data

### Application Tab
1. Open DevTools → Application
2. Expand "Local Storage"
3. Select `http://localhost:8000`
4. See stored `token` and `user`

## ✅ Feature Checklist

### Core Features
- [ ] Home page loads
- [ ] Login works
- [ ] Token saved to localStorage
- [ ] Navigation updates based on role
- [ ] Logout clears session

### Guest Features
- [ ] View sample exams (GET /api/de-thi-mau)

### Student Features
- [ ] View exam history (GET /api/lich-su-thi)
- [ ] View result details (GET /api/baithi/{id}/ketqua)
- [ ] Take exam (TODO)

### Teacher Features
- [ ] Import questions (POST /api/cau-hoi/import)
- [ ] Create exam (POST /api/tao-de-thi)
- [ ] View question bank (TODO)

### Admin Features
- [ ] View all users (GET /api/users)
- [ ] Filter users by role
- [ ] Toggle user status (POST /api/users/{id}/toggle-status)
- [ ] Create user (TODO)
- [ ] Backup database (TODO)

## 📱 Mobile Testing

### Test on Mobile
1. Find your computer's local IP:
   ```bash
   ipconfig  # Windows
   ifconfig  # Mac/Linux
   ```
2. Start server with:
   ```bash
   php artisan serve --host=0.0.0.0 --port=8000
   ```
3. On mobile browser, go to: `http://YOUR_IP:8000`

### Responsive Breakpoints to Test
- 📱 iPhone SE (375px width)
- 📱 iPhone 12 Pro (390px width)
- 📱 iPad (768px width)
- 💻 Desktop (1920px width)

**Chrome DevTools:**
1. F12 → Click device icon
2. Select device from dropdown
3. Test navigation menu (hamburger icon)

## 🎯 Quick API Test Script

Create `test-api.ps1` (PowerShell):
```powershell
# Test Login
$loginResponse = Invoke-RestMethod -Uri "http://localhost:8000/api/login" `
  -Method POST `
  -ContentType "application/json" `
  -Body '{"TenDangNhap":"student001","MatKhau":"password123"}'

$token = $loginResponse.data.token
Write-Host "Token: $token"

# Test Exam History
$headers = @{
  "Authorization" = "Bearer $token"
}

$history = Invoke-RestMethod -Uri "http://localhost:8000/api/lich-su-thi" `
  -Headers $headers

Write-Host "Exam History:"
$history.data | Format-Table
```

Run: `.\test-api.ps1`

## 📞 Need Help?

### Check These First:
1. ✅ Server running? (`php artisan serve`)
2. ✅ Database connected? (check `.env`)
3. ✅ Browser console errors? (F12)
4. ✅ Network requests failing? (DevTools → Network)

### Log Files:
```
storage/logs/laravel.log       # Laravel errors
Browser Console (F12)          # JavaScript errors
DevTools → Network             # API errors
```

### Common Commands:
```bash
# Clear all caches
php artisan cache:clear
php artisan route:clear
php artisan config:clear
php artisan view:clear

# Restart server
# Press Ctrl+C to stop
php artisan serve

# Check routes
php artisan route:list --columns=method,uri,name
```

---

## 🎉 Success Criteria

You'll know it's working when:
- ✅ Home page loads with purple gradient
- ✅ Login redirects to role-specific screen
- ✅ Navigation updates dynamically
- ✅ Data loads from API (check Network tab)
- ✅ Alerts appear on actions
- ✅ No errors in Console

**Happy Testing! 🚀**

---

**Last Updated**: December 7, 2025  
**Version**: 1.0.0
