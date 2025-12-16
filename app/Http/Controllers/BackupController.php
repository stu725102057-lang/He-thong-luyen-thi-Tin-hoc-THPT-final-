<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Models\SaoLuu;
use Carbon\Carbon;

class BackupController extends Controller
{
    /**
     * Constructor - Admin only
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware(function ($request, $next) {
            $user = $request->user();
            
            if ($user->Role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ quản trị viên mới có quyền backup/restore'
                ], 403);
            }
            
            return $next($request);
        });
    }

    /**
     * Tạo backup database
     */
    public function createBackup(Request $request)
    {
        try {
            $user = $request->user();
            
            // Tạo thư mục backup nếu chưa có
            $backupPath = storage_path('app/backups');
            if (!File::exists($backupPath)) {
                File::makeDirectory($backupPath, 0755, true);
            }

            // Tạo tên file backup
            $timestamp = Carbon::now()->format('Y-m-d_His');
            $filename = "backup_{$timestamp}.sql";
            $filepath = $backupPath . '/' . $filename;

            // Lấy thông tin database từ config
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');
            $host = config('database.connections.mysql.host');
            $port = config('database.connections.mysql.port', 3306);

            // Tạo lệnh mysqldump
            $command = sprintf(
                'mysqldump --user=%s --password=%s --host=%s --port=%s %s > %s 2>&1',
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($host),
                escapeshellarg($port),
                escapeshellarg($database),
                escapeshellarg($filepath)
            );

            // Thực thi lệnh
            $output = [];
            $returnVar = 0;
            exec($command, $output, $returnVar);

            // Kiểm tra mysqldump có thành công không
            $mysqldumpSuccess = false;
            
            if (File::exists($filepath) && File::size($filepath) > 0) {
                // Đọc vài dòng đầu để kiểm tra có phải SQL dump thật không
                $firstLines = file_get_contents($filepath, false, null, 0, 500);
                
                // Nếu chứa "mysqldump" hoặc error message → mysqldump failed
                if (stripos($firstLines, 'is not recognized') !== false || 
                    stripos($firstLines, 'command not found') !== false ||
                    stripos($firstLines, 'No such file') !== false) {
                    $mysqldumpSuccess = false;
                    \Log::warning('mysqldump command not found, using PHP fallback');
                } 
                // Kiểm tra có SQL syntax hợp lệ
                else if (stripos($firstLines, 'CREATE TABLE') !== false || 
                         stripos($firstLines, 'MySQL dump') !== false ||
                         stripos($firstLines, 'DROP TABLE') !== false) {
                    $mysqldumpSuccess = true;
                } else {
                    $mysqldumpSuccess = false;
                }
            }
            
            // Nếu mysqldump thất bại → Fallback sang PHP export
            if (!$mysqldumpSuccess) {
                \Log::info('Using PHP manual export as fallback');
                $this->exportDatabaseManually($filepath);
            }

            // Lấy kích thước file
            $filesize = File::exists($filepath) ? File::size($filepath) : 0;

            if ($filesize === 0) {
                throw new \Exception('Không thể tạo file backup. Vui lòng kiểm tra quyền ghi file.');
            }

            // Lưu thông tin backup vào database
            $saoLuu = SaoLuu::create([
                'TenFile' => $filename,
                'DuongDan' => $filepath,
                'KichThuoc' => $filesize,
                'ThoiGianSaoLuu' => Carbon::now(),
                'TrangThai' => 'ThanhCong',
                'MaQTV' => $user->quanTriVien->MaQTV ?? null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Backup database thành công',
                'data' => [
                    'MaSaoLuu' => $saoLuu->MaSaoLuu,
                    'TenFile' => $filename,
                    'KichThuoc' => $this->formatBytes($filesize),
                    'ThoiGian' => $saoLuu->ThoiGianSaoLuu->format('d/m/Y H:i:s')
                ]
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Backup error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Không thể tạo file backup: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export database manually (fallback method)
     */
    private function exportDatabaseManually($filepath)
    {
        $tables = DB::select('SHOW TABLES');
        $database = config('database.connections.mysql.database');
        
        $sql = "-- MySQL Backup\n";
        $sql .= "-- Date: " . date('Y-m-d H:i:s') . "\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $table) {
            $tableName = $table->{"Tables_in_{$database}"};
            
            // Get CREATE TABLE statement
            $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
            $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
            $sql .= $createTable[0]->{'Create Table'} . ";\n\n";
            
            // Get table data
            $rows = DB::table($tableName)->get();
            
            if ($rows->count() > 0) {
                foreach ($rows as $row) {
                    $values = array_map(function($value) {
                        return is_null($value) ? 'NULL' : "'" . addslashes($value) . "'";
                    }, (array)$row);
                    
                    $sql .= "INSERT INTO `{$tableName}` VALUES (" . implode(', ', $values) . ");\n";
                }
                $sql .= "\n";
            }
        }

        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

        File::put($filepath, $sql);
    }

    /**
     * Lấy danh sách backup
     */
    public function listBackups(Request $request)
    {
        try {
            $backups = SaoLuu::with('quanTriVien.taiKhoan')
                ->orderBy('ThoiGianSaoLuu', 'desc')
                ->get()
                ->map(function($backup) {
                    return [
                        'MaSaoLuu' => $backup->MaSaoLuu,
                        'TenFile' => $backup->TenFile,
                        'KichThuoc' => $this->formatBytes($backup->KichThuoc),
                        'ThoiGian' => $backup->ThoiGianSaoLuu->format('d/m/Y H:i:s'),
                        'TrangThai' => $backup->TrangThai,
                        'NguoiTao' => $backup->quanTriVien && $backup->quanTriVien->taiKhoan 
                            ? $backup->quanTriVien->taiKhoan->TenDangNhap 
                            : 'N/A'
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'Lấy danh sách backup thành công',
                'data' => $backups
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy danh sách backup',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download backup file
     */
    public function downloadBackup(Request $request, $maSaoLuu)
    {
        try {
            $backup = SaoLuu::find($maSaoLuu);
            
            if (!$backup) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy file backup'
                ], 404);
            }

            if (!File::exists($backup->DuongDan)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File backup không tồn tại'
                ], 404);
            }

            return response()->download($backup->DuongDan, $backup->TenFile);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tải file backup',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete backup file
     */
    public function deleteBackup(Request $request, $maSaoLuu)
    {
        try {
            $backup = SaoLuu::find($maSaoLuu);
            
            if (!$backup) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy file backup'
                ], 404);
            }

            // Xóa file vật lý
            if (File::exists($backup->DuongDan)) {
                File::delete($backup->DuongDan);
            }

            // Xóa record trong database
            $backup->delete();

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa file backup thành công'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi xóa file backup',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Restore database từ file backup
     */
    public function restoreBackup(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:sql,txt|max:102400' // Max 100MB
            ]);
            
            $user = $request->user();
            $file = $request->file('file');
            
            // Lưu file tạm
            $tempPath = $file->store('temp_backups');
            $fullPath = storage_path('app/' . $tempPath);
            
            // Tạo backup an toàn trước khi restore
            $safetyBackupFile = $this->createSafetyBackup($user);
            
            // Lấy thông tin database
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');
            $host = config('database.connections.mysql.host');
            $port = config('database.connections.mysql.port', 3306);
            
            // Thử mysql command trước
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
            
            // Nếu mysql command không tìm thấy, dùng PHP fallback
            if ($returnVar !== 0 && strpos(implode(' ', $output), 'not recognized') !== false) {
                \Log::warning('mysql command not found, using PHP fallback');
                
                $result = $this->restoreDatabaseManually($fullPath);
                
                // Xóa file tạm
                \Storage::delete($tempPath);
                
                if ($result) {
                    \Log::info('Database restore successful (PHP fallback)', [
                        'user' => $user->TenDangNhap,
                        'file' => $file->getClientOriginalName(),
                        'safety_backup' => $safetyBackupFile
                    ]);
                    
                    return response()->json([
                        'success' => true,
                        'message' => 'Khôi phục database thành công',
                        'method' => 'PHP fallback',
                        'safety_backup' => $safetyBackupFile
                    ], 200);
                } else {
                    throw new \Exception('PHP restore failed');
                }
            }
            
            // Xóa file tạm
            \Storage::delete($tempPath);
            
            if ($returnVar === 0) {
                \Log::info('Database restore successful (mysql command)', [
                    'user' => $user->TenDangNhap,
                    'file' => $file->getClientOriginalName(),
                    'safety_backup' => $safetyBackupFile
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Khôi phục database thành công',
                    'method' => 'mysql command',
                    'safety_backup' => $safetyBackupFile
                ], 200);
            } else {
                throw new \Exception('Restore failed: ' . implode("\n", $output));
            }
            
        } catch (\Exception $e) {
            \Log::error('Database restore error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Không thể khôi phục database: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Restore database manually using PHP (fallback when mysql command not available)
     */
    private function restoreDatabaseManually($filepath)
    {
        try {
            \Log::info('Starting PHP restore from: ' . $filepath);
            
            // Tắt foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            
            // Đọc file SQL
            $sql = file_get_contents($filepath);
            
            if (!$sql) {
                throw new \Exception('Cannot read SQL file');
            }
            
            \Log::info('SQL file size: ' . strlen($sql) . ' bytes');
            
            // Remove comments and split by semicolon
            $lines = explode("\n", $sql);
            $templine = '';
            $count = 0;
            $errors = 0;
            
            foreach ($lines as $line) {
                // Skip comments and empty lines
                if (substr(trim($line), 0, 2) == '--' || trim($line) == '' || substr(trim($line), 0, 2) == '/*') {
                    continue;
                }
                
                // Add this line to current segment
                $templine .= $line;
                
                // If line ends with semicolon, execute the query
                if (substr(trim($line), -1, 1) == ';') {
                    try {
                        DB::statement($templine);
                        $count++;
                    } catch (\Exception $e) {
                        $errors++;
                        \Log::warning('SQL statement failed (statement ' . $count . '): ' . $e->getMessage());
                        \Log::warning('Failed SQL: ' . substr($templine, 0, 200) . '...');
                    }
                    
                    // Reset temp variable
                    $templine = '';
                }
            }
            
            // Bật lại foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            
            \Log::info("PHP restore completed: $count statements executed, $errors errors");
            
            // Return true nếu có ít nhất 1 statement thành công
            return $count > 0;
            
        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1'); // Ensure re-enable
            \Log::error('Manual restore failed: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return false;
        }
    }

    /**
     * Tạo backup an toàn trước khi restore
     */
    private function createSafetyBackup($user)
    {
        try {
            // Tạo thư mục backup nếu chưa có
            $backupPath = storage_path('app/backups');
            if (!File::exists($backupPath)) {
                File::makeDirectory($backupPath, 0755, true);
            }

            // Tạo tên file backup với prefix SAFETY_
            $timestamp = Carbon::now()->format('Y-m-d_His');
            $filename = "SAFETY_backup_{$timestamp}.sql";
            $filepath = $backupPath . '/' . $filename;

            // Lấy thông tin database từ config
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');
            $host = config('database.connections.mysql.host');
            $port = config('database.connections.mysql.port', 3306);

            // Tạo lệnh mysqldump
            $command = sprintf(
                'mysqldump --user=%s --password=%s --host=%s --port=%s %s > %s 2>&1',
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($host),
                escapeshellarg($port),
                escapeshellarg($database),
                escapeshellarg($filepath)
            );

            // Thực thi lệnh
            $output = [];
            $returnVar = 0;
            exec($command, $output, $returnVar);

            // Kiểm tra mysqldump có thành công không
            $mysqldumpSuccess = false;
            
            if (File::exists($filepath) && File::size($filepath) > 0) {
                $firstLines = file_get_contents($filepath, false, null, 0, 500);
                
                if (stripos($firstLines, 'is not recognized') !== false || 
                    stripos($firstLines, 'command not found') !== false ||
                    stripos($firstLines, 'No such file') !== false) {
                    $mysqldumpSuccess = false;
                } 
                else if (stripos($firstLines, 'CREATE TABLE') !== false || 
                         stripos($firstLines, 'MySQL dump') !== false ||
                         stripos($firstLines, 'DROP TABLE') !== false) {
                    $mysqldumpSuccess = true;
                } else {
                    $mysqldumpSuccess = false;
                }
            }
            
            // Fallback sang PHP export nếu cần
            if (!$mysqldumpSuccess) {
                $this->exportDatabaseManually($filepath);
            }

            // Lấy kích thước file
            $filesize = File::exists($filepath) ? File::size($filepath) : 0;

            // Lưu thông tin backup vào database
            SaoLuu::create([
                'TenFile' => $filename,
                'DuongDan' => $filepath,
                'KichThuoc' => $filesize,
                'ThoiGianSaoLuu' => Carbon::now(),
                'TrangThai' => 'ThanhCong',
                'MaQTV' => $user->quanTriVien->MaQTV ?? null,
            ]);

            return $filename;

        } catch (\Exception $e) {
            \Log::error('Safety backup error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Format bytes to human readable
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
