<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SaoLuu extends Model
{
    use HasFactory;

    protected $table = 'SaoLuu';
    protected $primaryKey = 'MaSaoLuu';
    public $incrementing = true;

    protected $fillable = [
        'TenFile',
        'DuongDan',
        'ThoiGianSaoLuu',
        'KichThuoc',
        'TrangThai',
        'MaQTV',
    ];

    protected $casts = [
        'ThoiGianSaoLuu' => 'datetime',
    ];

    /**
     * Relationships theo biểu đồ lớp
     */
    
    // Quan hệ với QuanTriVien (n-1)
    public function quanTriVien()
    {
        return $this->belongsTo(QuanTriVien::class, 'MaQTV', 'MaQTV');
    }

    /**
     * Methods theo biểu đồ lớp - UR-04.4: Sao lưu dữ liệu
     */
    
    // + ThuHienSaoLuu()
    public function thuHienSaoLuu()
    {
        try {
            $database = env('DB_DATABASE');
            $username = env('DB_USERNAME');
            $password = env('DB_PASSWORD');
            $host = env('DB_HOST');
            
            $filename = 'backup_' . date('Y_m_d_His') . '.sql';
            $path = storage_path('app/backups/' . $filename);
            
            // Tạo thư mục nếu chưa có
            if (!file_exists(storage_path('app/backups'))) {
                mkdir(storage_path('app/backups'), 0755, true);
            }
            
            // Lệnh mysqldump
            $command = sprintf(
                'mysqldump -h%s -u%s %s %s > %s',
                escapeshellarg($host),
                escapeshellarg($username),
                $password ? '-p' . escapeshellarg($password) : '',
                escapeshellarg($database),
                escapeshellarg($path)
            );
            
            exec($command, $output, $returnVar);
            
            if ($returnVar === 0 && file_exists($path)) {
                $this->TenFile = $filename;
                $this->DuongDan = $path;
                $this->ThoiGianSaoLuu = now();
                $this->KichThuoc = filesize($path) / 1024; // KB
                $this->TrangThai = 'ThanhCong';
                $this->save();
                
                return true;
            }
            
            $this->TrangThai = 'ThatBai';
            $this->save();
            return false;
            
        } catch (\Exception $e) {
            $this->TrangThai = 'ThatBai';
            $this->save();
            
            // Log lỗi
            Loi::luuLogLoi('Error', 'Sao lưu thất bại: ' . $e->getMessage(), $e->getTraceAsString(), $this->MaQTV);
            
            return false;
        }
    }

    // + KhoiPhucSaoLuu() - UR-04.5: Phục hồi dữ liệu
    public function khoiPhucSaoLuu()
    {
        try {
            if (!file_exists($this->DuongDan)) {
                throw new \Exception('File sao lưu không tồn tại');
            }
            
            $database = env('DB_DATABASE');
            $username = env('DB_USERNAME');
            $password = env('DB_PASSWORD');
            $host = env('DB_HOST');
            
            $command = sprintf(
                'mysql -h%s -u%s %s %s < %s',
                escapeshellarg($host),
                escapeshellarg($username),
                $password ? '-p' . escapeshellarg($password) : '',
                escapeshellarg($database),
                escapeshellarg($this->DuongDan)
            );
            
            exec($command, $output, $returnVar);
            
            if ($returnVar === 0) {
                Loi::luuLogLoi('Info', 'Khôi phục dữ liệu thành công từ: ' . $this->TenFile, null, $this->MaQTV);
                return true;
            }
            
            throw new \Exception('Lệnh khôi phục thất bại');
            
        } catch (\Exception $e) {
            Loi::luuLogLoi('Error', 'Khôi phục dữ liệu thất bại: ' . $e->getMessage(), $e->getTraceAsString(), $this->MaQTV);
            return false;
        }
    }

    // + XemDSSaoLuu()
    public static function xemDSSaoLuu()
    {
        return self::orderBy('ThoiGianSaoLuu', 'desc')->get();
    }

    // + XoaBanSaoLuu()
    public function xoaBanSaoLuu()
    {
        try {
            if (file_exists($this->DuongDan)) {
                unlink($this->DuongDan);
            }
            return $this->delete();
        } catch (\Exception $e) {
            Loi::luuLogLoi('Error', 'Xóa bản sao lưu thất bại: ' . $e->getMessage(), $e->getTraceAsString());
            return false;
        }
    }
}
