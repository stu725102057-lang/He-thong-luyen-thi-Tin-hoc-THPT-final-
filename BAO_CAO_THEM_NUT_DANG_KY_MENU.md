# THEM NUT DANG KY VAO THANH MENU TRANG CHU

## NGAY: 14/12/2025 - 19:50

## YEU CAU:
Thêm nút "Đăng ký" vào thanh menu trang chủ (navbar), để người dùng có thể đăng ký tài khoản ngay từ trang chủ.

## THAY DOI:

### TRUOC:
```html
Đề thi mẫu | Đăng nhập
```

### SAU:
```html
Đề thi mẫu | Đăng ký | Đăng nhập
```

## CODE DA THEM:
```html
<li class="nav-item">
    <a class="nav-link" href="#" onclick="app.showScreen('register')">
        <i class="bi bi-person-plus"></i> Đăng ký
    </a>
</li>
```

## VI TRI:
- File: resources/views/app.blade.php
- Section: Guest Menu (navbar)
- Dòng: ~1010-1021

## ICON:
- Sử dụng Bootstrap Icon: `bi-person-plus` (biểu tượng người + dấu cộng)
- Phù hợp với chức năng đăng ký tài khoản mới

## THU TU MENU:
1. 📄 Đề thi mẫu (xem trước không cần đăng nhập)
2. ➕ Đăng ký (tạo tài khoản mới)
3. 🔐 Đăng nhập (đăng nhập vào hệ thống)

## TEST:
1. Reload trang (Ctrl+F5)
2. Kiểm tra thanh menu trên cùng
3. Thấy 3 nút: "Đề thi mẫu", "Đăng ký", "Đăng nhập"
4. Click "Đăng ký" → Mở form đăng ký tài khoản

## KET QUA MONG DOI:
✅ Menu hiển thị 3 nút rõ ràng
✅ Nút "Đăng ký" có icon person-plus
✅ Click vào "Đăng ký" hiển thị form đăng ký
✅ Giao diện thân thiện, dễ sử dụng hơn

## UX IMPROVEMENTS:
- Người dùng mới có thể đăng ký ngay từ trang chủ
- Không cần vào form đăng nhập mới thấy link đăng ký
- Flow rõ ràng hơn: Xem đề mẫu → Đăng ký → Đăng nhập → Làm bài
