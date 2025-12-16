/**
 * ==============================================================================
 * ROUTES BỔ SUNG - COPY VÀO routes/api.php
 * ==============================================================================
 * 
 * Thêm các routes sau vào trong group auth:sanctum
 * 
 * Location: routes/api.php
 * Position: Trong Route::middleware('auth:sanctum')->group(function () { ... })
 * 
 * ==============================================================================
 */

// ========== MODULE LÀM BÀI THI (UR-02) ==========

// Lấy danh sách đề thi (học sinh)
Route::get('/de-thi', [DeThiController::class, 'layDanhSachDeThi']);

// Xem chi tiết đề thi trước khi làm
Route::get('/de-thi/{maDe}', [DeThiController::class, 'layChiTietDeThi']);

// Bắt đầu làm bài (tạo BaiLam record)
Route::post('/de-thi/{maDe}/bat-dau', [DeThiController::class, 'batDauLamBai']);

// ========== MODULE LỊCH SỬ & THỐNG KÊ (UR-05) ==========

// Xem chi tiết bài làm (sau khi nộp)
Route::get('/bai-lam/{maBaiLam}/chi-tiet', [BaiLamController::class, 'xemChiTiet']);

// Lịch sử làm bài của học sinh
Route::get('/bai-lam/lich-su', [BaiLamController::class, 'lichSu']);

// Thống kê cá nhân (cho biểu đồ)
Route::get('/bai-lam/thong-ke-ca-nhan', [BaiLamController::class, 'thongKeCaNhan']);

// Xóa bài làm (admin/giáo viên)
Route::delete('/bai-lam/{maBaiLam}', [BaiLamController::class, 'xoa']);

// Export bài làm ra PDF
Route::get('/bai-lam/{maBaiLam}/export-pdf', [BaiLamController::class, 'exportPDF']);

// ========== MODULE BACKUP & RESTORE (UR-07) ==========

// Backup toàn bộ database
Route::post('/backup/full', [BackupController::class, 'backupFull']);

// Backup chỉ người dùng
Route::post('/backup/users', [BackupController::class, 'backupUsers']);

// Backup câu hỏi và đề thi
Route::post('/backup/exams', [BackupController::class, 'backupExams']);

// Restore từ file backup
Route::post('/backup/restore', [BackupController::class, 'restore']);

// Danh sách các file backup
Route::get('/backup/list', [BackupController::class, 'list']);

// Xóa file backup
Route::delete('/backup/{fileName}', [BackupController::class, 'delete']);

// Download file backup
Route::get('/backup/download/{fileName}', [BackupController::class, 'download']);

/**
 * ==============================================================================
 * HOÀN THÀNH!
 * ==============================================================================
 * 
 * Tổng: 17 routes mới
 * 
 * Sau khi thêm xong, chạy:
 * php artisan route:clear
 * php artisan config:clear
 * 
 * Kiểm tra routes:
 * php artisan route:list | grep "de-thi\|bai-lam\|backup"
 * 
 * ==============================================================================
 */
