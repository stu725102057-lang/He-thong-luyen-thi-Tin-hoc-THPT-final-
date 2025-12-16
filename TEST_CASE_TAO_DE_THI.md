### ✅ TEST CASE: Tạo Đề Thi (Teacher)

## 🎯 Mục tiêu
Kiểm tra chức năng "Tạo đề thi" hoạt động đúng cho tài khoản giáo viên.

## 📋 Điều kiện tiên quyết
- Server đang chạy: `php artisan serve`
- Browser: Chrome/Edge/Firefox
- Account giáo viên tồn tại trong database

---

## TEST 1: Truy cập màn hình "Tạo đề thi"

### Bước thực hiện:
1. Mở browser: `http://localhost:8000`
2. Click "Đăng nhập"
3. Nhập:
   - Username: `teacher001`
   - Password: `teachpass123`
4. Click "Đăng nhập"
5. **Click vào menu "Tạo đề thi"**

### Kết quả mong đợi:
✅ Màn hình "Tạo đề thi mới" hiển thị với:
- Tiêu đề: "➕ Tạo đề thi mới"
- Form có các trường:
  - Tên đề thi (text input)
  - Môn học (text input, default: "Tin học")
  - Thời gian phút (number input)
  - Số câu hỏi (number input)
  - Độ khó (dropdown: Dễ/Trung bình/Khó)
- Button: "✅ Tạo đề thi"

### Kết quả thực tế:
- [ ] PASS
- [ ] FAIL (ghi lỗi: _________________)

---

## TEST 2: Submit form "Tạo đề thi"

### Bước thực hiện:
1. Từ màn hình "Tạo đề thi"
2. Điền thông tin:
   - Tên đề thi: `Đề thi thử số 1`
   - Môn học: `Tin học`
   - Thời gian: `60`
   - Số câu hỏi: `20`
   - Độ khó: `Trung bình`
3. Click "Tạo đề thi"

### Kết quả mong đợi:
✅ Một trong các trường hợp:
- **Success**: Toast alert hiển thị "Tạo đề thi thành công!"
- **Error**: Toast alert hiển thị lỗi từ API (ví dụ: "Không đủ câu hỏi")

### Kết quả thực tế:
- [ ] PASS - Success alert
- [ ] PASS - Error alert (có message)
- [ ] FAIL - Không có phản hồi
- [ ] FAIL - JavaScript error (check Console)

---

## TEST 3: Validation form

### Test 3.1: Bỏ trống trường bắt buộc
**Bước**: Bỏ trống "Tên đề thi", click "Tạo đề thi"
**Mong đợi**: ✅ Browser validation hiển thị "Please fill out this field"
**Kết quả**: [ ] PASS / [ ] FAIL

### Test 3.2: Nhập số âm vào "Thời gian"
**Bước**: Nhập `-10` vào "Thời gian", click "Tạo đề thi"
**Mong đợi**: ✅ Validation error (browser hoặc API)
**Kết quả**: [ ] PASS / [ ] FAIL

### Test 3.3: Nhập 0 vào "Số câu hỏi"
**Bước**: Nhập `0` vào "Số câu hỏi", click "Tạo đề thi"
**Mong đợi**: ✅ Validation error
**Kết quả**: [ ] PASS / [ ] FAIL

---

## TEST 4: API Integration

### Kiểm tra Network Request
1. Mở DevTools (F12) → Network tab
2. Filter: XHR/Fetch
3. Điền form và submit
4. Kiểm tra request:

**Request details:**
```
Method: POST
URL: http://localhost:8000/api/tao-de-thi
Headers:
  Authorization: Bearer {token}
  Content-Type: application/json

Body:
{
  "TenDe": "Đề thi thử số 1",
  "MaMon": "Tin học",
  "ThoiGianLamBai": 60,
  "SoCauHoi": 20,
  "MucDo": "trungbinh"
}
```

**Expected Response (Success):**
```json
{
  "success": true,
  "message": "Tạo đề thi thành công",
  "data": {
    "MaDe": "DE001",
    "TenDe": "Đề thi thử số 1",
    ...
  }
}
```

### Kết quả:
- [ ] Request sent correctly
- [ ] Response received (200 OK)
- [ ] Response has correct format
- [ ] Toast alert displayed

---

## TEST 5: Navigation

### Test 5.1: Quay lại màn hình khác
**Bước**: Từ "Tạo đề thi", click "Quản lý câu hỏi"
**Mong đợi**: ✅ Chuyển sang màn hình "Quản lý câu hỏi"
**Kết quả**: [ ] PASS / [ ] FAIL

### Test 5.2: Quay lại "Tạo đề thi"
**Bước**: Click lại "Tạo đề thi" trong menu
**Mong đợi**: ✅ Màn hình "Tạo đề thi" hiển thị (form trống/reset)
**Kết quả**: [ ] PASS / [ ] FAIL

---

## TEST 6: Console Errors

### Kiểm tra JavaScript Errors
1. Mở Console (F12)
2. Click "Tạo đề thi" trong menu
3. Kiểm tra console

**Mong đợi**: ✅ Không có lỗi đỏ
**Các lỗi thường gặp**:
- ❌ `Cannot read property of undefined`
- ❌ `getElementById(...) is null`
- ❌ `showScreen is not a function`

### Kết quả:
- [ ] No errors ✅
- [ ] Has errors (ghi chi tiết): _________________

---

## 📊 Tổng kết Test

| Test Case | Status | Notes |
|-----------|--------|-------|
| TEST 1: Truy cập screen | ⬜ | |
| TEST 2: Submit form | ⬜ | |
| TEST 3.1: Validation required | ⬜ | |
| TEST 3.2: Validation negative | ⬜ | |
| TEST 3.3: Validation zero | ⬜ | |
| TEST 4: API Integration | ⬜ | |
| TEST 5.1: Navigation away | ⬜ | |
| TEST 5.2: Navigation back | ⬜ | |
| TEST 6: Console errors | ⬜ | |

**Overall Result**: ⬜ PASS / ⬜ FAIL

---

## 🐛 Bug Report Template (nếu FAIL)

**Bug Title**: _________________________________

**Steps to Reproduce**:
1. 
2. 
3. 

**Expected Result**: 

**Actual Result**: 

**Screenshots/Error Messages**:

**Browser**: Chrome/Edge/Firefox version: _______

**Console Errors** (if any):
```

```

---

## ✅ Sign-off

**Tester Name**: _________________  
**Test Date**: _________________  
**Test Result**: PASS ⬜ / FAIL ⬜  
**Comments**: 

---

**Note**: Sau khi fix lỗi screen ID, chức năng này sẽ hoạt động đúng! 🚀
