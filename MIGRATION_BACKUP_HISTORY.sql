-- ==============================================================================
-- MIGRATION: TẠO BẢNG BACKUP_HISTORY
-- ==============================================================================
-- 
-- File: database/migrations/xxxx_xx_xx_create_backup_history_table.php
-- 
-- Cách tạo migration:
-- php artisan make:migration create_backup_history_table
-- 
-- Hoặc chạy trực tiếp SQL này vào MySQL:
-- ==============================================================================

CREATE TABLE IF NOT EXISTS `backup_history` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `file_name` VARCHAR(255) NOT NULL COMMENT 'Tên file backup',
    `file_size` BIGINT NOT NULL DEFAULT 0 COMMENT 'Kích thước file (bytes)',
    `backup_type` ENUM('full', 'users', 'exams', 'auto') NOT NULL COMMENT 'Loại backup',
    `created_by` VARCHAR(20) NULL COMMENT 'Người tạo backup (MaNguoiDung hoặc system)',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Thời gian tạo',
    `description` TEXT NULL COMMENT 'Mô tả backup',
    
    INDEX `idx_backup_type` (`backup_type`),
    INDEX `idx_created_at` (`created_at`),
    INDEX `idx_created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Lịch sử backup hệ thống';

-- ==============================================================================
-- HOẶC SỬ DỤNG LARAVEL MIGRATION (RECOMMENDED)
-- ==============================================================================

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('backup_history', function (Blueprint $table) {
            $table->id();
            $table->string('file_name')->comment('Tên file backup');
            $table->bigInteger('file_size')->default(0)->comment('Kích thước file (bytes)');
            $table->enum('backup_type', ['full', 'users', 'exams', 'auto'])
                  ->comment('Loại backup');
            $table->string('created_by', 20)->nullable()
                  ->comment('Người tạo backup (MaNguoiDung hoặc system)');
            $table->text('description')->nullable()->comment('Mô tả backup');
            $table->timestamps();
            
            // Indexes
            $table->index('backup_type');
            $table->index('created_at');
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backup_history');
    }
};

-- ==============================================================================
-- CÁCH SỬ DỤNG
-- ==============================================================================

-- Cách 1: Chạy trực tiếp SQL
-- mysql -u root -p database_name < this_file.sql

-- Cách 2: Dùng Laravel Migration
-- 1. Copy phần PHP code vào file migration
-- 2. Chạy: php artisan migrate

-- Kiểm tra bảng đã tạo:
-- SHOW TABLES LIKE 'backup_history';
-- DESCRIBE backup_history;

-- ==============================================================================
-- TEST DATA (Optional)
-- ==============================================================================

INSERT INTO `backup_history` 
(`file_name`, `file_size`, `backup_type`, `created_by`, `description`) 
VALUES
('backup_full_2024-01-15_10-00-00.sql', 2500000, 'full', 'admin', 'Backup định kỳ hàng ngày'),
('backup_users_2024-01-15_11-00-00.json', 150000, 'users', 'admin', 'Backup dữ liệu người dùng'),
('backup_exams_2024-01-15_12-00-00.json', 800000, 'exams', 'admin', 'Backup đề thi và câu hỏi');

-- ==============================================================================
-- QUERIES THƯỜNG DÙNG
-- ==============================================================================

-- Xem danh sách backup
SELECT 
    id,
    file_name,
    CONCAT(ROUND(file_size / 1024 / 1024, 2), ' MB') as size,
    backup_type,
    created_by,
    DATE_FORMAT(created_at, '%d/%m/%Y %H:%i:%s') as created_date
FROM backup_history
ORDER BY created_at DESC;

-- Xóa backup cũ hơn 30 ngày
DELETE FROM backup_history 
WHERE backup_type = 'auto' 
AND created_at < DATE_SUB(NOW(), INTERVAL 30 DAY);

-- Thống kê backup
SELECT 
    backup_type,
    COUNT(*) as total_backups,
    SUM(file_size) as total_size,
    CONCAT(ROUND(SUM(file_size) / 1024 / 1024, 2), ' MB') as total_size_mb
FROM backup_history
GROUP BY backup_type;

-- ==============================================================================
-- CRON JOB CHO TỰ ĐỘNG BACKUP (Optional)
-- ==============================================================================

-- Thêm vào crontab:
-- 0 2 * * * cd /path/to/project && php artisan backup:auto

-- Hoặc trong Laravel Scheduler (app/Console/Kernel.php):
/*
protected function schedule(Schedule $schedule)
{
    // Backup tự động mỗi ngày lúc 2h sáng
    $schedule->call(function () {
        app(BackupController::class)->autoBackup();
    })->dailyAt('02:00');
}
*/

-- ==============================================================================
-- LƯU Ý BẢO MẬT
-- ==============================================================================

-- 1. Thư mục backup phải được bảo vệ (không public)
-- 2. File backup nên được mã hóa
-- 3. Chỉ admin mới có quyền truy cập
-- 4. Xóa backup cũ định kỳ để tiết kiệm dung lượng
-- 5. Lưu backup ngoài server (cloud storage) để an toàn

-- ==============================================================================
