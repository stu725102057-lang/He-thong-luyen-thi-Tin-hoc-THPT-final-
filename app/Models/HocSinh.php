<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HocSinh extends Model
{
    use HasFactory;

    protected $table = 'HocSinh';
    
    // Cấu hình khóa chính CHAR(10)
    protected $primaryKey = 'MaHS';
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'MaHS',
        'MaTK',
        'HoTen',
        'Lop',
        'Truong',
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
     * Quan hệ 1-n với BaiLam
     * Một học sinh có thể làm nhiều bài thi
     */
    public function baiLam()
    {
        return $this->hasMany(BaiLam::class, 'MaHS', 'MaHS');
    }

    /**
     * Quan hệ 1-n với KetQua
     * Một học sinh có thể có nhiều kết quả
     */
    public function ketQua()
    {
        return $this->hasMany(KetQua::class, 'MaHS', 'MaHS');
    }
}
