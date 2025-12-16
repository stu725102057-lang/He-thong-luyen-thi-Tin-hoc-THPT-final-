# 🧪 HƯỚNG DẪN TEST FRONTEND NHANH

## 📋 CHUẨN BỊ

1. Start Laravel backend:
```powershell
cd "d:\Hệ thống luyện thi THPT môn Tin học"
php artisan serve
```

2. Open browser: `http://localhost:8000`

3. Have 3 test accounts ready:
   - **Student**: `hocsinh1` / `password`
   - **Teacher**: `giaovien1` / `password`
   - **Admin**: `admin` / `password`

---

## ✅ TEST WORKFLOW 1: HỌC SINH (Student)

### Step 1: Đăng ký tài khoản mới
1. Click "Đăng ký tài khoản"
2. Fill form:
   - Tên đăng nhập: `student_test`
   - Email: `student@test.com`
   - Mật khẩu: `123456`
   - Vai trò: **Học sinh**
   - Họ tên: `Nguyễn Văn Test`
   - Lớp: `12A1`
   - Ngày sinh: `01/01/2007`
3. Click "Đăng ký"
4. **Expected**: Alert success, redirect to login

### Step 2: Đăng nhập
1. Enter: `student_test` / `123456`
2. Click "Đăng nhập"
3. **Expected**: Redirect to "Chọn Đề Thi" screen
4. **Check**: Navigation shows student menu with 4 items

### Step 3: Chọn đề thi
1. See list of exams with cards
2. Use search: type "Tin học"
3. Use filter: select "Dễ"
4. **Expected**: List updates with matching exams
5. Click "Bắt đầu" on any exam
6. **Expected**: Modal opens with exam info

### Step 4: Xác nhận và làm bài
1. Read exam preview (name, time, questions)
2. Click "Bắt đầu làm bài"
3. **Expected**: 
   - Modal closes
   - Navigate to "Làm bài thi" screen
   - Timer starts counting down
   - Questions load

### Step 5: Làm bài
1. Answer some questions (select radio buttons)
2. Click "Câu sau" to navigate
3. Wait 60 seconds
4. **Expected**: Alert "Tự động lưu thành công"
5. Click "Nộp bài"
6. Confirm in dialog
7. **Expected**: Navigate to results screen

### Step 6: Xem kết quả
1. See score card with confetti (if > 5.0)
2. Check summary stats (score, correct answers, time)
3. Scroll down to review questions
4. **Expected**: 
   - Green background for correct answers
   - Red for wrong answers
   - Correct answer highlighted

### Step 7: Xem thống kê ⭐ NEW
1. Click "Thống kê" in navigation
2. **Expected**: See 4 stat cards:
   - Total exams done
   - Average score
   - Highest score
   - Average accuracy
3. **Check charts**:
   - Line chart shows score over time
   - Pie chart shows result distribution
   - Bar chart shows scores by subject
4. **Check table**: Shows 10 recent exams
5. Click "Làm bài mới" → Navigate to choose exam

**✅ PASS if all 7 steps work without errors**

---

## ✅ TEST WORKFLOW 2: GIÁO VIÊN (Teacher)

### Step 1: Đăng nhập
1. Login as teacher: `giaovien1` / `password`
2. **Expected**: Redirect to "Quản lý câu hỏi"
3. **Check**: Navigation shows teacher menu

### Step 2: Xem danh sách câu hỏi
1. See table with existing questions
2. **Expected**: Columns show NoiDung, DapAn, DoKho

### Step 3: Thêm câu hỏi thủ công
1. Click "Thêm câu hỏi mới"
2. Form appears
3. Fill all fields:
   - Nội dung: "Test question"
   - Đáp án A, B, C, D
   - Đáp án đúng: A
   - Độ khó: Dễ
   - Chủ đề: Tin học đại cương
4. Click "Thêm câu hỏi"
5. **Expected**: Alert success, form closes, table refreshes

### Step 4: Import câu hỏi
1. Click "Import từ file"
2. Upload Excel/JSON file
3. **Expected**: Alert success, questions added

### Step 5: Tạo đề ngẫu nhiên ⭐ NEW
1. Click "Tạo đề ngẫu nhiên"
2. **Expected**: Modal opens
3. Fill form:
   - Tên đề: "Đề test ngẫu nhiên"
   - Thời gian: 45 phút
   - Chủ đề: "Tin học đại cương"
   - Số câu: 15
   - Độ khó: Trung bình
4. Click "Tạo đề thi"
5. **Expected**: 
   - Alert "Đang tạo đề..."
   - Then "Tạo thành công"
   - Modal closes

**✅ PASS if can create random exam**

---

## ✅ TEST WORKFLOW 3: ADMIN

### Step 1: Đăng nhập
1. Login as admin: `admin` / `password`
2. **Expected**: Redirect to **Dashboard** ⭐ NEW
3. **Check**: Navigation shows admin menu (4 items)

### Step 2: Xem Dashboard ⭐ NEW
1. See 4 gradient stat cards:
   - Total users (purple)
   - Total exams (pink)
   - Total submissions (blue)
   - Total questions (green)
2. **Check numbers**: Should match database
3. See activity line chart (6 months)
4. See user pie chart (by role)
5. See recent submissions table
6. See system alerts

**✅ PASS if all data loads correctly**

### Step 3: Quản lý người dùng
1. Click "Quản lý người dùng"
2. See table with all users
3. Click "Thêm người dùng"
4. Fill form and create user
5. **Expected**: User added to table
6. Click "Sửa" on a user
7. Update email
8. **Expected**: User updated
9. Click "Xóa" on test user
10. Confirm deletion
11. **Expected**: User removed

### Step 4: Backup Database ⭐ NEW
1. Click "Backup" in navigation
2. See 2 cards: Backup and Restore
3. Click "Tạo Backup ngay" (blue card)
4. **Expected**: Modal opens
5. Click "Bắt đầu Backup"
6. **Expected**:
   - Progress bar animates
   - Success message appears
   - Modal auto-closes after 2s
7. **Check table**: New backup appears in history
8. Click "Tải về" on backup
9. **Expected**: File downloads (.sql)

### Step 5: Restore Database ⚠️ DANGEROUS ⭐ NEW
1. Click "Restore từ file" (red card)
2. **Expected**: Modal opens with warning
3. Select a .sql file
4. Click "Bắt đầu Restore"
5. Confirm in danger dialog
6. **Expected**:
   - Progress bar animates
   - Success message
   - Auto logout after 2s
7. Login again
8. **Expected**: Data restored successfully

**✅ PASS if backup/restore works without errors**

---

## 🔍 VISUAL CHECKS

### Chart.js Integration
- [ ] All charts render properly
- [ ] No console errors about Chart.js
- [ ] Charts are responsive (resize window)
- [ ] Colors match design (blue, green, yellow, red)
- [ ] Tooltips show on hover

### Stat Cards
- [ ] Dashboard stat cards have gradient backgrounds
- [ ] Hover effect works (lift animation)
- [ ] Icons display correctly (Bootstrap Icons)
- [ ] Numbers format correctly

### Modals
- [ ] All modals open/close smoothly
- [ ] Backdrop works (click outside to close)
- [ ] Forms validate before submit
- [ ] Close button (X) works
- [ ] Multiple modals don't conflict

### Navigation
- [ ] Menu items highlight on active screen
- [ ] Role-based menus show correctly
- [ ] Screen transitions smooth
- [ ] Auto-load data on screen change

### Alerts
- [ ] Success alerts are green
- [ ] Error alerts are red
- [ ] Warning alerts are yellow
- [ ] Info alerts are blue
- [ ] Alerts auto-dismiss after 5 seconds

---

## 🐛 COMMON ISSUES & FIXES

### Issue 1: Charts don't render
**Symptom**: Canvas elements empty, no charts visible  
**Check**: 
```javascript
// Open browser console (F12)
// Look for errors like "Chart is not defined"
```
**Fix**: Chart.js CDN loaded? Check HEAD section

### Issue 2: Modal doesn't open
**Symptom**: Click button, nothing happens  
**Check**: 
```html
data-bs-toggle="modal" 
data-bs-target="#modalId"
```
**Fix**: Verify modal ID matches target

### Issue 3: API calls fail (401)
**Symptom**: "Phiên đăng nhập đã hết hạn"  
**Fix**: 
```javascript
localStorage.getItem('token') // Check if token exists
// Re-login if null
```

### Issue 4: Auto-save doesn't work
**Symptom**: No alert after 60 seconds  
**Check**: 
```javascript
// Console: "Tự động lưu bài làm..."
```
**Fix**: Check `setInterval` in `startExam()`

### Issue 5: Backup fails
**Symptom**: Error "Backup thất bại"  
**Check**: 
- Backend endpoint exists: POST /api/backup
- mysqldump command available
- storage/app/backups/ writable
**Fix**: Implement backend endpoint (see API_ENDPOINTS_TODO.md)

---

## 📊 PERFORMANCE CHECKS

### Load Times
- [ ] Initial page load: < 2s
- [ ] Login/transition: < 500ms
- [ ] API calls: < 1s
- [ ] Chart render: < 500ms

### Browser Console
- [ ] No errors (0 errors)
- [ ] No warnings (except minor)
- [ ] API calls show 200 OK
- [ ] Token sent with requests

### Network Tab (F12)
- [ ] Check API responses
- [ ] Verify data structure
- [ ] No 404/500 errors
- [ ] CORS headers correct

---

## ✅ FINAL CHECKLIST

### All Roles
- [ ] Registration works
- [ ] Login/logout works
- [ ] Navigation menu correct for role
- [ ] All screens accessible
- [ ] No console errors
- [ ] Responsive on mobile (test on phone)

### Student Specific
- [ ] Can choose exam with search/filter ⭐ NEW
- [ ] Can start exam with confirmation ⭐ NEW
- [ ] Can take exam with auto-save
- [ ] Can submit and view results
- [ ] Can view statistics with charts ⭐ NEW

### Teacher Specific
- [ ] Can manage questions (CRUD)
- [ ] Can import questions
- [ ] Can create random exam ⭐ NEW

### Admin Specific
- [ ] Dashboard loads with charts ⭐ NEW
- [ ] Can manage users (CRUD)
- [ ] Can create backup ⭐ NEW
- [ ] Can restore backup ⭐ NEW
- [ ] Can view backup history ⭐ NEW

---

## 🎯 ACCEPTANCE CRITERIA

### Must Pass (Critical):
✅ All 3 workflows complete without errors  
✅ No console errors in browser  
✅ All API calls return 200 OK  
✅ All charts render correctly  
✅ All modals open/close properly  

### Should Pass (Important):
✅ Charts update with real data  
✅ Auto-save works during exam  
✅ Backup/restore functional  
✅ Random exam generation works  

### Nice to Have:
✅ Smooth animations  
✅ Fast load times (< 2s)  
✅ Mobile responsive  
✅ Accessibility features  

---

## 📝 TEST REPORT TEMPLATE

```
===== FRONTEND TEST REPORT =====
Date: ____________
Tester: ____________

WORKFLOW 1: Student
[ ] Step 1-7 all passed
[ ] Charts render correctly
[ ] Statistics accurate
Issues: ________________

WORKFLOW 2: Teacher
[ ] Can manage questions
[ ] Can create random exam
Issues: ________________

WORKFLOW 3: Admin
[ ] Dashboard loads
[ ] Backup/Restore works
Issues: ________________

OVERALL:
Pass: _____ / Fail: _____
Ready for production: [ ] Yes [ ] No

Notes:
_________________________
_________________________
```

---

## 🚀 QUICK TEST SCRIPT

For rapid testing, run this sequence:

```
1. Login as student → Choose exam → Start → Answer 2 questions → Submit → Check stats
   Time: 3 minutes

2. Login as teacher → Add question → Create random exam
   Time: 2 minutes

3. Login as admin → View dashboard → Create backup
   Time: 2 minutes

Total: 7 minutes for smoke test
```

**If all pass → Ready for demo! 🎉**

---

*Generated: December 7, 2025*  
*Quick testing guide for frontend features*
