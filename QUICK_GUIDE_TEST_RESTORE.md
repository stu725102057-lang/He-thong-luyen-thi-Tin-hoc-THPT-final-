# ⚡ QUICK GUIDE: Test Restore An Toàn

## 🎯 3 CÁCH TEST RESTORE KHÔNG ẢNH HƯỞNG PRODUCTION

### ✅ CÁCH 1: TEST VỚI DATABASE RIÊNG (KHUYẾN NGHỊ)

```bash
# Bước 1: Tạo database test
mysql -u root -p
CREATE DATABASE hethong_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;

# Bước 2: Tạo backup từ DB chính
mysqldump -u root -p hethong_luyenthi > test_backup.sql

# Bước 3: Restore vào DB test
mysql -u root -p hethong_test < test_backup.sql

# Bước 4: Verify
mysql -u root -p hethong_test -e "SHOW TABLES; SELECT COUNT(*) FROM TaiKhoan;"
```

**✅ Hoàn toàn an toàn - DB chính không bị động chạm!**

---

### ✅ CÁCH 2: BACKUP TRƯỚC KHI RESTORE

```bash
# Bước 1: Backup DB hiện tại (SAFETY NET)
mysqldump -u root -p hethong_luyenthi > SAFETY_BACKUP_$(date +%Y%m%d_%H%M%S).sql

# Bước 2: Test restore qua UI hoặc command
# Nếu lỗi → Restore lại từ SAFETY_BACKUP

# Bước 3: Rollback nếu cần
mysql -u root -p hethong_luyenthi < SAFETY_BACKUP_20241214_150000.sql
```

**✅ Có kế hoạch rollback - An toàn 90%**

---

### ✅ CÁCH 3: SỬ DỤNG TÍNH NĂNG TỰ ĐỘNG CỦA HỆ THỐNG

Hệ thống đã được implement với:
- ✅ Tự động tạo backup với prefix `SAFETY_` trước khi restore
- ✅ Confirm dialog 2 lần
- ✅ Validation file .sql
- ✅ Log đầy đủ trong `storage/logs/laravel.log`

**Cách dùng:**
1. Đăng nhập admin
2. Vào "Sao lưu & Khôi phục"
3. Click "Khôi phục Database"
4. Chọn file backup
5. ✅ Check "Tôi hiểu rủi ro..."
6. Click "Bắt đầu Restore"
7. ⏳ Đợi 10-30 giây
8. ✅ Hệ thống tự động tạo SAFETY_backup trước khi restore

**✅ An toàn 95% - Có SAFETY backup tự động**

---

## 🆘 ROLLBACK NẾU RESTORE THẤT BẠI

### Cách 1: Từ SAFETY backup (được tạo tự động)

```bash
# Tìm file SAFETY backup mới nhất
ls -lt storage/app/backups/SAFETY_*.sql | head -1

# Restore
mysql -u root -p hethong_luyenthi < storage/app/backups/SAFETY_backup_20241214_152030.sql
```

### Cách 2: Từ backup thủ công

```bash
mysql -u root -p hethong_luyenthi < SAFETY_BACKUP_20241214_150000.sql
```

### Cách 3: Từ phpMyAdmin (nếu có)

1. Mở phpMyAdmin → Database `hethong_luyenthi`
2. Tab "Import"
3. Chọn file SAFETY backup
4. Click "Go"

---

## ⚠️ CHECKLIST TRƯỚC KHI RESTORE

- [ ] Đã tạo backup an toàn (manual hoặc tự động)
- [ ] Đã test restore trên DB riêng thành công
- [ ] File backup đã được validate (mở bằng text editor)
- [ ] Đã thông báo users về downtime (nếu có)
- [ ] Đã đóng tất cả connections đến DB (nếu có thể)
- [ ] Có kế hoạch rollback rõ ràng

---

## 📞 HỖ TRỢ KHẨN CẤP

**Nếu restore lỗi và không rollback được:**

1. Check log: `storage/logs/laravel.log`
2. Check MySQL error: `C:\xampp\mysql\data\mysql_error.log` (Windows)
3. Tìm file SAFETY backup gần nhất:
   ```bash
   ls -lt storage/app/backups/ | grep SAFETY
   ```
4. Restore manual:
   ```bash
   mysql -u root -p hethong_luyenthi < [SAFETY_FILE]
   ```

---

## 🎓 TÓM TẮT

| Phương pháp | An toàn | Thời gian | Khuyến nghị |
|-------------|---------|-----------|-------------|
| Test DB riêng | 100% | 5 phút | ⭐⭐⭐⭐⭐ |
| Backup manual trước | 90% | 10 phút | ⭐⭐⭐⭐ |
| SAFETY auto backup | 95% | 2 phút | ⭐⭐⭐⭐⭐ |

**Khuyến nghị cuối cùng:** Test trên DB riêng trước, sau đó dùng tính năng tự động với SAFETY backup!

