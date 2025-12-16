<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loi extends Model
{
    use HasFactory;

    protected $table = 'Loi';
    protected $primaryKey = 'MaLoi';
    public $incrementing = true;

    protected $fillable = [
        'LoaiLoi',
        'NoiDung',
        'NguyenNhan',
        'ThoiGian',
        'MaTK',
    ];

    protected $casts = [
        'ThoiGian' => 'datetime',
    ];

    /**
     * Relationships theo biểu đồ lớp
     */
    
    // Quan hệ với TaiKhoan (n-1)
    public function taiKhoan()
    {
        return $this->belongsTo(TaiKhoan::class, 'MaTK', 'MaTK');
    }

    /**
     * Methods theo biểu đồ lớp
     */
    
    // + ThongBaoLoi()
    public function thongBaoLoi()
    {
        return [
            'ma_loi' => $this->MaLoi,
            'loai_loi' => $this->LoaiLoi,
            'noi_dung' => $this->NoiDung,
            'nguyen_nhan' => $this->NguyenNhan,
            'thoi_gian' => $this->ThoiGian,
            'tai_khoan' => $this->MaTK,
        ];
    }

    // + LuuLogLoi($loaiLoi, $noiDung, $nguyenNhan, $maTK)
    public static function luuLogLoi($loaiLoi, $noiDung, $nguyenNhan = null, $maTK = null)
    {
        return self::create([
            'LoaiLoi' => $loaiLoi,
            'NoiDung' => $noiDung,
            'NguyenNhan' => $nguyenNhan,
            'ThoiGian' => now(),
            'MaTK' => $maTK,
        ]);
    }

    // + XoaLoi()
    public function xoaLoi()
    {
        return $this->delete();
    }
}
