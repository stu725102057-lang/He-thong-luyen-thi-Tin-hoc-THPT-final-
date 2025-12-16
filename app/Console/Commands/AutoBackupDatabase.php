<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\BackupHistory;

class AutoBackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:auto';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tự động backup database hàng ngày (UR-04.4)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Bắt đầu tự động backup database...');
        
        try {
            $dbHost = env('DB_HOST');
            $dbPort = env('DB_PORT', 3306);
            $dbName = env('DB_DATABASE');
            $dbUser = env('DB_USERNAME');
            $dbPass = env('DB_PASSWORD');

            // Tạo tên file backup với timestamp
            $timestamp = date('Y-m-d_H-i-s');
            $filename = "auto_backup_{$dbName}_{$timestamp}.sql";
            $backupPath = storage_path("app/backups/{$filename}");

            // Tạo thư mục nếu chưa tồn tại
            if (!file_exists(storage_path('app/backups'))) {
                mkdir(storage_path('app/backups'), 0755, true);
            }

            // Chạy lệnh mysqldump
            $command = sprintf(
                'mysqldump --host=%s --port=%s --user=%s --password=%s %s > %s 2>&1',
                escapeshellarg($dbHost),
                escapeshellarg($dbPort),
                escapeshellarg($dbUser),
                escapeshellarg($dbPass),
                escapeshellarg($dbName),
                escapeshellarg($backupPath)
            );

            exec($command, $output, $returnVar);

            if ($returnVar !== 0) {
                $this->error('❌ Backup thất bại!');
                $this->error('Output: ' . implode("\n", $output));
                return 1;
            }

            // Kiểm tra file đã được tạo
            if (!file_exists($backupPath) || filesize($backupPath) === 0) {
                $this->error('❌ File backup rỗng hoặc không tồn tại!');
                return 1;
            }

            $fileSize = filesize($backupPath);

            // Lưu vào database
            BackupHistory::create([
                'filename' => $filename,
                'file_path' => $backupPath,
                'file_size' => $fileSize,
                'status' => 'success',
                'created_by' => 'system_auto', // Auto backup
                'backup_type' => 'auto'
            ]);

            $this->info('✅ Backup thành công!');
            $this->info("📁 File: {$filename}");
            $this->info("📊 Dung lượng: " . $this->formatBytes($fileSize));
            
            // Xóa backup cũ hơn 30 ngày
            $this->cleanOldBackups();

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Lỗi: ' . $e->getMessage());
            return 1;
        }
    }
    
    /**
     * Xóa backup cũ hơn 30 ngày
     */
    private function cleanOldBackups()
    {
        $this->info('🧹 Dọn dẹp backup cũ...');
        
        $oldBackups = BackupHistory::where('created_at', '<', now()->subDays(30))
                                    ->where('backup_type', 'auto')
                                    ->get();
        
        foreach ($oldBackups as $backup) {
            if (file_exists($backup->file_path)) {
                unlink($backup->file_path);
            }
            $backup->delete();
        }
        
        $this->info("🗑️  Đã xóa {$oldBackups->count()} backup cũ (> 30 ngày)");
    }
    
    /**
     * Format file size
     */
    private function formatBytes($bytes)
    {
        if ($bytes < 1024) return $bytes . ' B';
        if ($bytes < 1024 * 1024) return round($bytes / 1024, 2) . ' KB';
        return round($bytes / (1024 * 1024), 2) . ' MB';
    }
}

