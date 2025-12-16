<?php
/**
 * ==============================================================================
 * API BACKUP VÀ RESTORE DỮ LIỆU - HOÀN CHỈNH
 * ==============================================================================
 * 
 * File: app/Http/Controllers/BackupController.php
 * 
 * Chức năng:
 * - Backup toàn bộ database ra file SQL
 * - Backup chỉ dữ liệu người dùng
 * - Backup chỉ câu hỏi và đề thi
 * - Restore từ file backup
 * - Lịch sử backup
 * - Tự động backup theo lịch
 * 
 * Yêu cầu quyền: Admin
 * 
 * ==============================================================================
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;

class BackupController extends Controller
{
    private $backupPath = 'backups';
    
    /**
     * Backup toàn bộ database
     * 
     * POST /api/backup/full
     * 
     * Response: {
     *   success: true,
     *   file_name: 'backup_2024-01-15_14-30-00.sql',
     *   file_size: '2.5 MB',
     *   download_url: '/storage/backups/...'
     * }
     */
    public function backupFull(Request $request)
    {
        try {
            // Kiểm tra quyền
            $user = $request->user();
            if ($user->VaiTro !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ admin mới có quyền backup'
                ], 403);
            }
            
            // Tạo tên file backup
            $timestamp = date('Y-m-d_H-i-s');
            $fileName = "backup_full_{$timestamp}.sql";
            $filePath = storage_path("app/{$this->backupPath}/{$fileName}");
            
            // Tạo thư mục nếu chưa có
            if (!file_exists(dirname($filePath))) {
                mkdir(dirname($filePath), 0755, true);
            }
            
            // Lấy thông tin database từ config
            $dbHost = env('DB_HOST', '127.0.0.1');
            $dbPort = env('DB_PORT', '3306');
            $dbName = env('DB_DATABASE');
            $dbUser = env('DB_USERNAME');
            $dbPass = env('DB_PASSWORD');
            
            // Command mysqldump
            $command = sprintf(
                'mysqldump -h%s -P%s -u%s -p%s %s > %s',
                escapeshellarg($dbHost),
                escapeshellarg($dbPort),
                escapeshellarg($dbUser),
                escapeshellarg($dbPass),
                escapeshellarg($dbName),
                escapeshellarg($filePath)
            );
            
            // Thực thi backup
            exec($command, $output, $returnCode);
            
            if ($returnCode !== 0) {
                throw new \Exception('Lỗi khi thực hiện backup. Mã lỗi: ' . $returnCode);
            }
            
            // Lưu thông tin backup vào database
            DB::table('backup_history')->insert([
                'file_name' => $fileName,
                'file_size' => filesize($filePath),
                'backup_type' => 'full',
                'created_by' => $user->MaNguoiDung,
                'created_at' => now(),
                'description' => 'Backup toàn bộ database'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Backup thành công',
                'file_name' => $fileName,
                'file_size' => $this->formatBytes(filesize($filePath)),
                'download_url' => "/storage/{$this->backupPath}/{$fileName}"
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi backup: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Backup chỉ dữ liệu người dùng
     * 
     * POST /api/backup/users
     */
    public function backupUsers(Request $request)
    {
        try {
            $user = $request->user();
            if ($user->VaiTro !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ admin mới có quyền backup'
                ], 403);
            }
            
            $timestamp = date('Y-m-d_H-i-s');
            $fileName = "backup_users_{$timestamp}.json";
            $filePath = storage_path("app/{$this->backupPath}/{$fileName}");
            
            // Tạo thư mục nếu chưa có
            if (!file_exists(dirname($filePath))) {
                mkdir(dirname($filePath), 0755, true);
            }
            
            // Lấy dữ liệu người dùng
            $users = DB::table('nguoidung')->get();
            
            // Lưu ra file JSON
            file_put_contents($filePath, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            
            // Lưu lịch sử
            DB::table('backup_history')->insert([
                'file_name' => $fileName,
                'file_size' => filesize($filePath),
                'backup_type' => 'users',
                'created_by' => $user->MaNguoiDung,
                'created_at' => now(),
                'description' => 'Backup dữ liệu người dùng'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Backup người dùng thành công',
                'file_name' => $fileName,
                'file_size' => $this->formatBytes(filesize($filePath)),
                'total_users' => count($users)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Backup câu hỏi và đề thi
     * 
     * POST /api/backup/exams
     */
    public function backupExams(Request $request)
    {
        try {
            $user = $request->user();
            if ($user->VaiTro !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ admin mới có quyền backup'
                ], 403);
            }
            
            $timestamp = date('Y-m-d_H-i-s');
            $fileName = "backup_exams_{$timestamp}.json";
            $filePath = storage_path("app/{$this->backupPath}/{$fileName}");
            
            // Tạo thư mục nếu chưa có
            if (!file_exists(dirname($filePath))) {
                mkdir(dirname($filePath), 0755, true);
            }
            
            // Lấy dữ liệu
            $data = [
                'cauhoi' => DB::table('cauhoi')->get(),
                'dethi' => DB::table('dethi')->get(),
                'chitietdethi' => DB::table('chitietdethi')->get()
            ];
            
            // Lưu ra file JSON
            file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            
            // Lưu lịch sử
            DB::table('backup_history')->insert([
                'file_name' => $fileName,
                'file_size' => filesize($filePath),
                'backup_type' => 'exams',
                'created_by' => $user->MaNguoiDung,
                'created_at' => now(),
                'description' => 'Backup câu hỏi và đề thi'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Backup đề thi thành công',
                'file_name' => $fileName,
                'file_size' => $this->formatBytes(filesize($filePath)),
                'total_questions' => count($data['cauhoi']),
                'total_exams' => count($data['dethi'])
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Restore từ file backup
     * 
     * POST /api/backup/restore
     * Body: { file_name: 'backup_xxx.sql' }
     */
    public function restore(Request $request)
    {
        try {
            $user = $request->user();
            if ($user->VaiTro !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ admin mới có quyền restore'
                ], 403);
            }
            
            $request->validate([
                'file_name' => 'required|string'
            ]);
            
            $fileName = $request->file_name;
            $filePath = storage_path("app/{$this->backupPath}/{$fileName}");
            
            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File backup không tồn tại'
                ], 404);
            }
            
            // Kiểm tra loại file
            $extension = pathinfo($fileName, PATHINFO_EXTENSION);
            
            if ($extension === 'sql') {
                // Restore từ file SQL
                $this->restoreFromSQL($filePath);
            } elseif ($extension === 'json') {
                // Restore từ file JSON
                $this->restoreFromJSON($filePath, $fileName);
            } else {
                throw new \Exception('Định dạng file không được hỗ trợ');
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Restore dữ liệu thành công'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi restore: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Lấy danh sách backup
     * 
     * GET /api/backup/list
     */
    public function list(Request $request)
    {
        try {
            $user = $request->user();
            if ($user->VaiTro !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ admin mới có quyền xem backup'
                ], 403);
            }
            
            $backups = DB::table('backup_history')
                        ->orderBy('created_at', 'desc')
                        ->get()
                        ->map(function($backup) {
                            $backup->file_size_formatted = $this->formatBytes($backup->file_size);
                            $backup->created_at_formatted = date('d/m/Y H:i:s', strtotime($backup->created_at));
                            return $backup;
                        });
            
            return response()->json([
                'success' => true,
                'data' => $backups
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Xóa file backup
     * 
     * DELETE /api/backup/{fileName}
     */
    public function delete(Request $request, $fileName)
    {
        try {
            $user = $request->user();
            if ($user->VaiTro !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ admin mới có quyền xóa backup'
                ], 403);
            }
            
            $filePath = storage_path("app/{$this->backupPath}/{$fileName}");
            
            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File không tồn tại'
                ], 404);
            }
            
            // Xóa file
            unlink($filePath);
            
            // Xóa khỏi lịch sử
            DB::table('backup_history')->where('file_name', $fileName)->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Xóa file backup thành công'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Download file backup
     * 
     * GET /api/backup/download/{fileName}
     */
    public function download(Request $request, $fileName)
    {
        try {
            $user = $request->user();
            if ($user->VaiTro !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ admin mới có quyền tải backup'
                ], 403);
            }
            
            $filePath = storage_path("app/{$this->backupPath}/{$fileName}");
            
            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File không tồn tại'
                ], 404);
            }
            
            return response()->download($filePath);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Tự động backup theo lịch
     * Chạy bằng cron: php artisan backup:auto
     */
    public function autoBackup()
    {
        try {
            // Tạo backup tự động
            $timestamp = date('Y-m-d_H-i-s');
            $fileName = "backup_auto_{$timestamp}.sql";
            $filePath = storage_path("app/{$this->backupPath}/{$fileName}");
            
            // Tạo thư mục nếu chưa có
            if (!file_exists(dirname($filePath))) {
                mkdir(dirname($filePath), 0755, true);
            }
            
            // Lấy thông tin database
            $dbHost = env('DB_HOST', '127.0.0.1');
            $dbPort = env('DB_PORT', '3306');
            $dbName = env('DB_DATABASE');
            $dbUser = env('DB_USERNAME');
            $dbPass = env('DB_PASSWORD');
            
            // Command mysqldump
            $command = sprintf(
                'mysqldump -h%s -P%s -u%s -p%s %s > %s',
                escapeshellarg($dbHost),
                escapeshellarg($dbPort),
                escapeshellarg($dbUser),
                escapeshellarg($dbPass),
                escapeshellarg($dbName),
                escapeshellarg($filePath)
            );
            
            exec($command, $output, $returnCode);
            
            if ($returnCode !== 0) {
                throw new \Exception('Lỗi backup tự động');
            }
            
            // Lưu lịch sử
            DB::table('backup_history')->insert([
                'file_name' => $fileName,
                'file_size' => filesize($filePath),
                'backup_type' => 'auto',
                'created_by' => 'system',
                'created_at' => now(),
                'description' => 'Backup tự động theo lịch'
            ]);
            
            // Xóa backup cũ (giữ lại 30 ngày)
            $this->deleteOldBackups(30);
            
            return true;
            
        } catch (\Exception $e) {
            \Log::error('Auto backup failed: ' . $e->getMessage());
            return false;
        }
    }
    
    // ================== HELPER METHODS ==================
    
    private function restoreFromSQL($filePath)
    {
        $dbHost = env('DB_HOST', '127.0.0.1');
        $dbPort = env('DB_PORT', '3306');
        $dbName = env('DB_DATABASE');
        $dbUser = env('DB_USERNAME');
        $dbPass = env('DB_PASSWORD');
        
        $command = sprintf(
            'mysql -h%s -P%s -u%s -p%s %s < %s',
            escapeshellarg($dbHost),
            escapeshellarg($dbPort),
            escapeshellarg($dbUser),
            escapeshellarg($dbPass),
            escapeshellarg($dbName),
            escapeshellarg($filePath)
        );
        
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            throw new \Exception('Lỗi khi restore database');
        }
    }
    
    private function restoreFromJSON($filePath, $fileName)
    {
        $content = file_get_contents($filePath);
        $data = json_decode($content, true);
        
        if (strpos($fileName, 'users') !== false) {
            // Restore users
            DB::table('nguoidung')->truncate();
            DB::table('nguoidung')->insert($data);
        } elseif (strpos($fileName, 'exams') !== false) {
            // Restore exams
            DB::table('cauhoi')->truncate();
            DB::table('dethi')->truncate();
            DB::table('chitietdethi')->truncate();
            
            DB::table('cauhoi')->insert($data['cauhoi']);
            DB::table('dethi')->insert($data['dethi']);
            DB::table('chitietdethi')->insert($data['chitietdethi']);
        }
    }
    
    private function deleteOldBackups($days)
    {
        $cutoffDate = now()->subDays($days);
        
        $oldBackups = DB::table('backup_history')
                       ->where('backup_type', 'auto')
                       ->where('created_at', '<', $cutoffDate)
                       ->get();
        
        foreach ($oldBackups as $backup) {
            $filePath = storage_path("app/{$this->backupPath}/{$backup->file_name}");
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            DB::table('backup_history')->where('id', $backup->id)->delete();
        }
    }
    
    private function formatBytes($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
}
