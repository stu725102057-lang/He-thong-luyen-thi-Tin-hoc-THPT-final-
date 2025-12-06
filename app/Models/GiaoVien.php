<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiaoVien extends Model
{
    use HasFactory;

    protected $table = 'GiaoVien';
    
    // Cấu hình khóa chính CHAR(10)
    protected $primaryKey = 'MaGV';
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'MaGV',
        'MaTK',
        'HoTen',
        'SoDienThoai',
        'ChuyenMon',
    ];

    // ============================================
    // RELATIONSHIPS
    // ============================================

    /**
     * Quan hệ với TaiKhoan (Kế thừa)
     */
    public function taiKhoan()
    {
        return $this->belongsTo(TaiKhoan::class, 'MaTK', 'MaTK');
    }

    /**
     * Quan hệ 1-n với DeThi
     * Một giáo viên có thể tạo nhiều đề thi
     */
    public function deThi()
    {
        return $this->hasMany(DeThi::class, 'MaGV', 'MaGV');
    }
}
