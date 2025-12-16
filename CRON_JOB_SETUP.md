# ⏰ Hướng dẫn cấu hình Cron Job cho Auto Backup

## 📋 Tổng quan

Hệ thống đã có command `backup:auto` để tự động backup database mỗi ngày lúc 2:00 AM.

## 🔧 Cấu hình trên Server Production

### Bước 1: Test command thủ công

```bash
cd /path/to/your/project
php artisan backup:auto
```

**Kết quả mong đợi:**
```
🔄 Bắt đầu tự động backup database...
✅ Backup thành công!
📁 File: auto_backup_database_2025-12-08_14-30-45.sql
📊 Dung lượng: 2.45 MB
🧹 Dọn dẹp backup cũ...
🗑️  Đã xóa 0 backup cũ (> 30 ngày)
```

### Bước 2: Mở crontab editor

```bash
crontab -e
```

### Bước 3: Thêm dòng sau vào crontab

**Cho Laravel Scheduler (Khuyến nghị):**
```bash
* * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1
```

**Thay `/path/to/your/project` bằng đường dẫn thực tế!**

Ví dụ:
```bash
* * * * * cd /var/www/html/trac-nghiem && php artisan schedule:run >> /dev/null 2>&1
```

### Bước 4: Lưu và thoát

- **Vim/Vi:** Nhấn `ESC`, gõ `:wq`, nhấn `Enter`
- **Nano:** Nhấn `Ctrl+X`, nhấn `Y`, nhấn `Enter`

### Bước 5: Kiểm tra crontab đã lưu

```bash
crontab -l
```

## 📅 Lịch Backup

| Thời gian | Mô tả |
|-----------|-------|
| **02:00 AM** mỗi ngày | Backup tự động |
| **30 ngày** | Xóa backup cũ tự động |

## 🗂️ Vị trí file backup

```
storage/app/backups/
├── auto_backup_database_2025-12-08_02-00-00.sql
├── auto_backup_database_2025-12-09_02-00-00.sql
└── ...
```

## 📊 Xem log backup

```bash
tail -f storage/logs/backup.log
```

## 🧪 Test Scheduler trên Local (Windows)

**Không cần cron trên Windows!** Chạy thủ công:

```bash
php artisan schedule:work
```

Hoặc test một lần:
```bash
php artisan backup:auto
```

## ⚙️ Tùy chỉnh lịch backup

Mở file `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule): void
{
    // Backup mỗi ngày 2:00 AM (mặc định)
    $schedule->command('backup:auto')->dailyAt('02:00');
    
    // Hoặc backup 2 lần/ngày (2 AM và 2 PM)
    // $schedule->command('backup:auto')->twiceDaily(2, 14);
    
    // Hoặc backup mỗi 6 giờ
    // $schedule->command('backup:auto')->everySixHours();
}
```

## 🔍 Troubleshooting

### Lỗi: "mysqldump command not found"

**Giải pháp:** Cài đặt MySQL client:

```bash
# Ubuntu/Debian
sudo apt-get install mysql-client

# CentOS/RHEL
sudo yum install mysql
```

### Lỗi: Permission denied

**Giải pháp:** Cấp quyền cho thư mục backup:

```bash
chmod 755 storage/app/backups
chown -R www-data:www-data storage/app/backups
```

### Cron không chạy

**Kiểm tra:**

1. Xem log cron:
   ```bash
   grep CRON /var/log/syslog
   ```

2. Kiểm tra PHP path:
   ```bash
   which php
   # Thay 'php' bằng đường dẫn đầy đủ nếu cần: /usr/bin/php
   ```

3. Test command với user www-data:
   ```bash
   sudo -u www-data php artisan backup:auto
   ```

## 📝 Best Practices

✅ **Khuyến nghị:**
- Backup mỗi ngày vào lúc ít traffic (2-4 AM)
- Giữ backup trong 30 ngày
- Lưu backup quan trọng ra server khác hoặc cloud storage
- Test restore định kỳ để đảm bảo backup hoạt động

⚠️ **Lưu ý:**
- Backup file có thể rất lớn (vài trăm MB)
- Kiểm tra dung lượng ổ đĩa thường xuyên
- Không commit file backup vào Git

## 🚀 Tính năng bổ sung (Optional)

### 1. Backup ra Google Drive / AWS S3

Cài đặt package:
```bash
composer require spatie/laravel-backup
```

### 2. Nhận thông báo Email khi backup

Thêm vào command:
```php
Mail::to('admin@example.com')->send(new BackupCompleteMail());
```

### 3. Monitor backup qua Slack/Discord

Sử dụng Laravel Notifications.

---

## 📞 Hỗ trợ

Nếu gặp vấn đề, kiểm tra:
1. Log Laravel: `storage/logs/laravel.log`
2. Log Backup: `storage/logs/backup.log`
3. Log Cron: `/var/log/syslog` hoặc `/var/log/cron`

**Chúc may mắn!** 🎉
