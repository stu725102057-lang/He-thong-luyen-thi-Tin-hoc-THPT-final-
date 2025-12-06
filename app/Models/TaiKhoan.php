<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class TaiKhoan extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'TaiKhoan';
    
    // Cấu hình khóa chính CHAR(10)
    protected $primaryKey = 'MaTK';
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'MaTK',
        'TenDangNhap',
        'MatKhau',
        'Email',
        'Role',
        'TrangThai',
        'LanDangNhapCuoi',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'MatKhau',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'TrangThai' => 'boolean',
        'LanDangNhapCuoi' => 'datetime',
    ];

    /**
     * Get the password for authentication.
     */
    public function getAuthPassword()
    {
        return $this->MatKhau;
    }

    // ============================================
    // RELATIONSHIPS - Quan hệ kế thừa
    // ============================================

    /**
     * Quan hệ 1-1 với QuanTriVien
     */
    public function quanTriVien()
    {
        return $this->hasOne(QuanTriVien::class, 'MaTK', 'MaTK');
    }

    /**
     * Quan hệ 1-1 với GiaoVien
     */
    public function giaoVien()
    {
        return $this->hasOne(GiaoVien::class, 'MaTK', 'MaTK');
    }

    /**
     * Quan hệ 1-1 với HocSinh
     */
    public function hocSinh()
    {
        return $this->hasOne(HocSinh::class, 'MaTK', 'MaTK');
    }

    /**
     * Kiểm tra role
     */
    public function isAdmin()
    {
        return $this->Role === 'admin';
    }

    public function isGiaoVien()
    {
        return $this->Role === 'giaovien';
    }

    public function isHocSinh()
    {
        return $this->Role === 'hocsinh';
    }
}
