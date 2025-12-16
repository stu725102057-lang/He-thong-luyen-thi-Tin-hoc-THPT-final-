### HƯỚNG DẪN DEBUG

## BẠN HÃY LÀM THEO:

### 1. Mở F12 Developer Tools

### 2. Tab Console
- Xem có log nào như:
  - 🔍 Loading teacher exams...
  - 📊 API Response: ...
  - ❌ Lỗi màu đỏ?
  
→ Chụp màn hình Console

### 3. Tab Network  
- Tìm request `/de-thi/teacher`
- Click vào request đó
- Xem tab Response
- Xem Status Code (200? 500? 404?)

→ Chụp màn hình Response

### 4. Tab Elements
- Tìm element `<div id="danhsachdetthiScreen">`
- Xem có class "active" không?
- Xem có nội dung HTML bên trong không?

→ Chụp màn hình Elements

---

## HOẶC đơn giản hơn:

Gửi cho tôi 3 ảnh:
1. Tab Console (F12)
2. Tab Network (F12) 
3. Tab Elements (F12) - tìm "danhsachdetthiScreen"
