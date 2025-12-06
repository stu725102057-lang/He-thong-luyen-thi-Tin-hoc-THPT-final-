<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeThi extends Model
{
    use HasFactory;

    protected $table = 'DeThi';
    
    // Cấu hình khóa chính CHAR(10)
    protected $primaryKey = 'MaDe';
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'MaDe',
        'TenDe',
        'ThoiGianLamBai',
        'NgayTao',
        'SoLuongCauHoi',
        'MaGV',
        'MoTa',
        'TrangThai',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'NgayTao' => 'datetime',
        'TrangThai' => 'boolean',
        'ThoiGianLamBai' => 'integer',
        'SoLuongCauHoi' => 'integer',
    ];

    // ============================================
    // RELATIONSHIPS
    // ============================================

    /**
     * Quan hệ với GiaoVien
     * Đề thi được tạo bởi một giáo viên
     */
    public function giaoVien()
    {
        return $this->belongsTo(GiaoVien::class, 'MaGV', 'MaGV');
    }

    /**
     * Quan hệ n-n với CauHoi (qua bảng trung gian DETHI_CAUHOI)
     * Một đề thi có nhiều câu hỏi
     */
    public function cauHoi()
    {
        return $this->belongsToMany(
            CauHoi::class,
            'DETHI_CAUHOI',  // Tên bảng trung gian
            'MaDe',          // Foreign key của bảng hiện tại trong bảng trung gian
            'MaCH',          // Foreign key của bảng đích trong bảng trung gian
            'MaDe',          // Local key của bảng hiện tại
            'MaCH'           // Local key của bảng đích
        )
        ->withPivot('ThuTu')  // Lấy thêm cột ThuTu từ bảng trung gian
        ->withTimestamps()
        ->orderBy('ThuTu');   // Sắp xếp theo thứ tự
    }

    /**
     * Quan hệ 1-n với BaiLam
     * Một đề thi có thể được nhiều học sinh làm
     */
    public function baiLam()
    {
        return $this->hasMany(BaiLam::class, 'MaDe', 'MaDe');
    }

    /**
     * Quan hệ 1-n với KetQua
     */
    public function ketQua()
    {
        return $this->hasMany(KetQua::class, 'MaDe', 'MaDe');
    }
}
