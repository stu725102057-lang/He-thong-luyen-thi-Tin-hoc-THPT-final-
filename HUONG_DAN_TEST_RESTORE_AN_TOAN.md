# 🔒 HƯỚNG DẪN TEST RESTORE AN TOÀN

**Mục đích:** Test chức năng restore database mà KHÔNG ảnh hưởng đến tiến trình đang làm

---

## 🎯 CHIẾN LƯỢC

### Option 1: ⭐ KHUYẾN NGHỊ - Test trên database riêng
**Ưu điểm:**
- ✅ Hoàn toàn an toàn, không ảnh hưởng DB chính
- ✅ Có thể test nhiều lần
- ✅ Kiểm tra được file backup có hợp lệ không

**Nhược điểm:**
- ⚠️ Cần tạo database test riêng
- ⚠️ Không test được restore thật

---

## 📋 CÁCH 1: TEST VỚI DATABASE RIÊNG

### Bước 1: Tạo database test

```bash
# Mở MySQL command line
mysql -u root -p

# Tạo database test
CREATE DATABASE hethong_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

### Bước 2: Tạo backup từ DB hiện tại

```bash
# Backup DB chính để có file test
mysqldump -u root -p hethong_luyenthi > test_backup.sql
```

Hoặc dùng UI:
1. Đăng nhập admin
2. Vào "Sao lưu & Khôi phục"
3. Click "Tạo Backup ngay"
4. Download file backup vừa tạo

### Bước 3: Restore vào DB test

```bash
# Restore vào database test (KHÔNG phải DB chính)
mysql -u root -p hethong_test < test_backup.sql
```

### Bước 4: Kiểm tra kết quả

```bash
# Connect vào DB test
mysql -u root -p hethong_test

# Kiểm tra tables
SHOW TABLES;

# Kiểm tra data
SELECT COUNT(*) FROM TaiKhoan;
SELECT COUNT(*) FROM DeThi;
SELECT COUNT(*) FROM CauHoi;

EXIT;
```

**Kết quả mong đợi:**
- ✅ DB test có đầy đủ 13 tables
- ✅ Số lượng records giống DB chính
- ✅ Data đầy đủ, không bị lỗi

---

## 📋 CÁCH 2: BACKUP TRƯỚC KHI TEST RESTORE

**Nguyên tắc vàng:** LUÔN backup trước khi restore!

### Bước 1: Backup DB hiện tại (safety net)

```bash
# Tạo backup an toàn
mysqldump -u root -p hethong_luyenthi > SAFETY_BACKUP_$(date +%Y%m%d_%H%M%S).sql

# Hoặc copy thư mục database (nếu dùng XAMPP)
xcopy /E /I "C:\xampp\mysql\data\hethong_luyenthi" "C:\xampp\mysql\data\hethong_luyenthi_BACKUP"
```

### Bước 2: Test restore trên DB chính

⚠️ **CHỈ LÀM NẾU ĐÃ CÓ BACKUP AN TOÀN!**

1. Đăng nhập admin
2. Vào "Sao lưu & Khôi phục"
3. Click "Khôi phục Database"
4. Chọn file backup test
5. Click "Bắt đầu Restore"

### Bước 3: Nếu có lỗi → Khôi phục lại

```bash
# Restore lại backup an toàn
mysql -u root -p hethong_luyenthi < SAFETY_BACKUP_20241214_150000.sql
```

---

## 📋 CÁCH 3: SỬ DỤNG DRY-RUN MODE (Implement sau)

**Ý tưởng:** Thêm tính năng "kiểm tra file backup" không restore thật

```javascript
// Frontend
async validateBackup() {
    const response = await this.apiCall('/backup/validate', {
        method: 'POST',
        body: JSON.stringify({ file: selectedFile })
    });
    
    if (response.valid) {
        console.log('✅ File backup hợp lệ');
        console.log('Tables:', response.tables);
        console.log('Records:', response.records);
    }
}
```

```php
// Backend
public function validateBackup(Request $request) {
    // Đọc file .sql
    // Parse SQL statements
    // Kiểm tra cấu trúc
    // KHÔNG thực thi restore
    return response()->json([
        'valid' => true,
        'tables' => ['TaiKhoan', 'DeThi', ...],
        'records' => ['TaiKhoan' => 50, 'DeThi' => 20, ...]
    ]);
}
```

---

## 🧪 TEST CASES AN TOÀN

### Test Case 1: File backup hợp lệ

**Setup:**
1. Tạo backup từ DB hiện tại
2. Tạo database test: `hethong_test`

**Steps:**
```bash
# Restore vào DB test
mysql -u root -p hethong_test < backup_2024-12-14_150000.sql

# Verify
mysql -u root -p hethong_test -e "SHOW TABLES;"
```

**Expected:**
- ✅ 13 tables được tạo
- ✅ Data được import thành công
- ✅ Không có SQL errors

---

### Test Case 2: File backup bị lỗi

**Setup:**
1. Tạo file .sql bị lỗi (thiếu ; hoặc syntax error)

**Steps:**
```bash
# Restore vào DB test
mysql -u root -p hethong_test < broken_backup.sql
```

**Expected:**
- ❌ MySQL báo lỗi syntax
- ❌ Restore thất bại
- ✅ DB test không bị ảnh hưởng (vì đã có tables trước đó)

---

### Test Case 3: File backup từ phiên bản cũ

**Setup:**
1. Lấy backup từ version cũ của hệ thống (thiếu tables hoặc columns mới)

**Steps:**
```bash
mysql -u root -p hethong_test < old_backup.sql
```

**Expected:**
- ⚠️ Restore thành công nhưng thiếu tables/columns mới
- ⚠️ Cần chạy migrations sau restore

---

## 🔧 IMPLEMENT RESTORE CONTROLLER

### Thêm method restore vào BackupController:

```php
public function restoreBackup(Request $request)
{
    try {
        $request->validate([
            'file' => 'required|file|mimes:sql|max:102400' // Max 100MB
        ]);
        
        $user = $request->user();
        $file = $request->file('file');
        
        // Lưu file tạm
        $tempPath = $file->store('temp_backups');
        $fullPath = storage_path('app/' . $tempPath);
        
        // Lấy thông tin database
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port', 3306);
        
        // Tạo backup an toàn trước khi restore
        $safetyBackup = $this->createSafetyBackup();
        
        // Thực hiện restore
        $command = sprintf(
            'mysql --user=%s --password=%s --host=%s --port=%s %s < %s 2>&1',
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($database),
            escapeshellarg($fullPath)
        );
        
        $output = [];
        $returnVar = 0;
        exec($command, $output, $returnVar);
        
        // Xóa file tạm
        Storage::delete($tempPath);
        
        if ($returnVar === 0) {
            // Log restore thành công
            \Log::info('Restore successful', [
                'user' => $user->TenDangNhap,
                'file' => $file->getClientOriginalName(),
                'safety_backup' => $safetyBackup
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Khôi phục database thành công',
                'safety_backup' => $safetyBackup
            ], 200);
        } else {
            throw new \Exception('Restore failed: ' . implode("\n", $output));
        }
        
    } catch (\Exception $e) {
        \Log::error('Restore error: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Không thể khôi phục database: ' . $e->getMessage(),
            'error' => $e->getMessage()
        ], 500);
    }
}

/**
 * Tạo backup an toàn trước khi restore
 */
private function createSafetyBackup()
{
    // Tự động tạo backup với prefix "SAFETY_"
    $timestamp = Carbon::now()->format('Y-m-d_His');
    $filename = "SAFETY_backup_{$timestamp}.sql";
    $filepath = storage_path('app/backups/' . $filename);
    
    // ... (code tương tự createBackup)
    
    return $filename;
}
```

---

## 🎨 FRONTEND RESTORE MODAL

### Sửa function showRestoreModal():

```javascript
showRestoreModal() {
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.innerHTML = `
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-exclamation-triangle"></i>
                        Khôi phục Database
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <strong>⚠️ Cảnh báo:</strong> Restore sẽ ghi đè toàn bộ dữ liệu hiện tại.
                        Hệ thống sẽ tự động tạo backup an toàn trước khi restore.
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Chọn file backup (.sql) *</label>
                        <input type="file" class="form-control" id="restoreFile" accept=".sql" required>
                    </div>
                    
                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" id="confirmRestore" required>
                        <label class="form-check-label" for="confirmRestore">
                            Tôi hiểu rủi ro và muốn tiếp tục restore
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-danger" onclick="app.executeRestore()">
                        <i class="bi bi-arrow-clockwise"></i> Bắt đầu Restore
                    </button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
    
    // Cleanup after close
    modal.addEventListener('hidden.bs.modal', () => {
        document.body.removeChild(modal);
    });
}

async executeRestore() {
    const fileInput = document.getElementById('restoreFile');
    const confirmCheck = document.getElementById('confirmRestore');
    
    // Validation
    if (!fileInput.files[0]) {
        this.showAlert('Vui lòng chọn file backup', 'warning');
        return;
    }
    
    if (!confirmCheck.checked) {
        this.showAlert('Vui lòng xác nhận bạn hiểu rủi ro', 'warning');
        return;
    }
    
    // Confirm lần cuối
    if (!confirm('⚠️ BẠN CHẮC CHẮN MUỐN RESTORE?\n\nToàn bộ dữ liệu hiện tại sẽ bị thay thế!')) {
        return;
    }
    
    try {
        this.showAlert('🔄 Đang khôi phục database... (có thể mất 10-30 giây)', 'info');
        
        const formData = new FormData();
        formData.append('file', fileInput.files[0]);
        
        const response = await fetch('/api/restore', {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + this.token,
                'Accept': 'application/json'
            },
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            this.showAlert('✅ Khôi phục thành công! Backup an toàn: ' + data.safety_backup, 'success');
            
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.querySelector('.modal'));
            modal.hide();
            
            // Reload sau 2 giây
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            throw new Error(data.message || 'Restore failed');
        }
        
    } catch (error) {
        console.error('Restore error:', error);
        this.showAlert('❌ Không thể restore: ' + error.message, 'danger');
    }
}
```

---

## ✅ CHECKLIST TEST RESTORE AN TOÀN

Trước khi test restore trên DB chính:

- [ ] **Đã tạo backup an toàn của DB hiện tại**
  ```bash
  mysqldump -u root -p hethong_luyenthi > SAFETY_$(date +%Y%m%d_%H%M%S).sql
  ```

- [ ] **Đã test restore trên database riêng (hethong_test) thành công**

- [ ] **File backup đã được validate (mở bằng text editor, kiểm tra syntax)**

- [ ] **Đã thông báo cho team/user về downtime (nếu có)**

- [ ] **Đã đóng tất cả kết nối đến DB (đóng app, dừng server nếu có thể)**

- [ ] **Có kế hoạch rollback nếu restore thất bại**

---

## 🆘 ROLLBACK NẾU RESTORE THẤT BẠI

### Cách 1: Restore từ safety backup

```bash
mysql -u root -p hethong_luyenthi < SAFETY_backup_20241214_150000.sql
```

### Cách 2: Restore từ backup gần nhất trong hệ thống

1. Vào `storage/app/backups/`
2. Tìm file backup mới nhất có TrangThai = 'ThanhCong'
3. Restore bằng command line hoặc UI

### Cách 3: Restore từ phpMyAdmin (nếu có)

1. Mở phpMyAdmin
2. Chọn database `hethong_luyenthi`
3. Tab "Import"
4. Chọn file backup
5. Click "Go"

---

## 📝 NOTES QUAN TRỌNG

### 1. Khi nào NÊN restore?

✅ **Nên:**
- Sau khi test trên DB riêng thành công
- Khi cần rollback về version cũ
- Khi DB bị corrupt/lỗi nghiêm trọng
- Khi migrate sang server mới

❌ **KHÔNG nên:**
- Khi đang có users online
- Khi chưa có backup an toàn
- Khi không chắc chắn file backup hợp lệ
- Khi production đang stable

### 2. Thời gian restore

- Database nhỏ (<10MB): 5-10 giây
- Database vừa (10-50MB): 10-30 giây
- Database lớn (>50MB): 30 giây - 2 phút

**Khuyến nghị:** Test trước để ước tính thời gian downtime

### 3. Sau khi restore

✅ **Checklist:**
- [ ] Verify số lượng records
- [ ] Test login với tài khoản test
- [ ] Kiểm tra chức năng quan trọng
- [ ] Clear cache (nếu có)
- [ ] Chạy migrations nếu version khác nhau
- [ ] Thông báo users hệ thống đã sẵn sàng

---

## 🎓 BEST PRACTICES

1. **LUÔN backup trước khi restore**
2. **Test trên DB riêng trước**
3. **Chọn thời điểm ít user (đêm khuya, sáng sớm)**
4. **Thông báo trước cho users về downtime**
5. **Có kế hoạch rollback**
6. **Document mọi thay đổi**
7. **Verify sau restore**
8. **Giữ nhiều bản backup (7-30 ngày)**

---

## 📞 HỖ TRỢ

Nếu gặp vấn đề:

1. **Check Laravel logs:** `storage/logs/laravel.log`
2. **Check MySQL error log:** `C:\xampp\mysql\data\mysql_error.log` (Windows)
3. **Contact admin:** Cung cấp error message và steps đã thực hiện

---

**Tóm tắt:**
- ✅ Dùng database test riêng để thử nghiệm
- ✅ LUÔN backup trước khi restore
- ✅ Validate file backup trước
- ✅ Có kế hoạch rollback
- ✅ Test restore offline trước khi apply lên production

