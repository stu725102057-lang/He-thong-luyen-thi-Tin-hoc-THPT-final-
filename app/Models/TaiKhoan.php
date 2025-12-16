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

    protected $table = 'taikhoan';
    
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
     * Quan hệ 1-n với Loi (Log hệ thống)
     */
    public function loi()
    {
        return $this->hasMany(Loi::class, 'MaTK', 'MaTK');
    }

    // ============================================
    // METHODS THEO BIỂU ĐỒ LỚP & YÊU CẦU
    // ============================================

    /**
     * UR-01.1: Đăng nhập
     * + DangNhap()
     */
    public function dangNhap($tenDangNhap, $matKhau)
    {
        // Xác thực thông tin
        if ($this->TenDangNhap === $tenDangNhap && 
            password_verify($matKhau, $this->MatKhau)) {
            
            // Cập nhật lần đăng nhập cuối
            $this->LanDangNhapCuoi = now();
            $this->save();
            
            // Log hoạt động
            Loi::luuLogLoi('Info', 'Đăng nhập thành công', 'User: ' . $this->TenDangNhap, $this->MaTK);
            
            return true;
        }
        
        // Log thất bại
        Loi::luuLogLoi('Warning', 'Đăng nhập thất bại', 'User: ' . $tenDangNhap, null);
        
        return false;
    }

    /**
     * + DangXuat()
     */
    public function dangXuat()
    {
        Loi::luuLogLoi('Info', 'Đăng xuất', 'User: ' . $this->TenDangNhap, $this->MaTK);
        return true;
    }

    /**
     * UR-01.3: Khôi phục mật khẩu
     * + CapNhatThongTin()
     */
    public function capNhatThongTin($data)
    {
        foreach ($data as $key => $value) {
            if (in_array($key, $this->fillable) && $key !== 'MaTK') {
                $this->$key = $value;
            }
        }
        return $this->save();
    }

    /**
     * UR-05.3: Mã hóa mật khẩu
     * + KiemTra()
     */
    public function kiemTra()
    {
        // Kiểm tra trạng thái tài khoản
        if (!$this->TrangThai) {
            return ['valid' => false, 'message' => 'Tài khoản đã bị khóa'];
        }
        
        // Kiểm tra mật khẩu đã được mã hóa chưa
        if (strlen($this->MatKhau) < 60) {
            return ['valid' => false, 'message' => 'Mật khẩu chưa được mã hóa'];
        }
        
        return ['valid' => true, 'message' => 'Tài khoản hợp lệ'];
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

    /**
     * Override để tự động hash password khi tạo/cập nhật
     */
    public function setMatKhauAttribute($value)
    {
        // UR-05.3: Mã hóa mật khẩu
        if (strlen($value) < 60) {
            $this->attributes['MatKhau'] = bcrypt($value);
        } else {
            $this->attributes['MatKhau'] = $value;
        }
    }
}
