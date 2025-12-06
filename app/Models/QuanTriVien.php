<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuanTriVien extends Model
{
    use HasFactory;

    protected $table = 'QuanTriVien';
    
    // Cấu hình khóa chính CHAR(10)
    protected $primaryKey = 'MaQTV';
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'MaQTV',
        'MaTK',
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
     * Quan hệ với SaoLuu
     */
    public function saoLuu()
    {
        return $this->hasMany(SaoLuu::class, 'MaQTV', 'MaQTV');
    }
}
