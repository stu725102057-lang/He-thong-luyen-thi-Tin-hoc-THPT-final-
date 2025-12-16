<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KetQua extends Model
{
    use HasFactory;

    protected $table = 'KetQua';
    
    // Cấu hình khóa chính CHAR(10)
    protected $primaryKey = 'MaKQ';
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'MaKQ',
        'Diem',
        'SoCauDung',
        'SoCauSai',
        'SoCauKhongLam',
        'ThoiGianHoanThanh',
        'MaHS',
        'MaDe',
        'MaBaiLam',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'Diem' => 'float',
        'SoCauDung' => 'integer',
        'SoCauSai' => 'integer',
        'SoCauKhongLam' => 'integer',
        'ThoiGianHoanThanh' => 'datetime',
    ];

    // ============================================
    // RELATIONSHIPS
    // ============================================

    /**
     * Quan hệ với HocSinh
     * Kết quả thuộc về một học sinh
     */
    public function hocSinh()
    {
        return $this->belongsTo(HocSinh::class, 'MaHS', 'MaHS');
    }

    /**
     * Quan hệ với DeThi
     * Kết quả từ một đề thi
     */
    public function deThi()
    {
        return $this->belongsTo(DeThi::class, 'MaDe', 'MaDe');
    }

    /**
     * Quan hệ với BaiLam
     * Kết quả từ một bài làm
     */
    public function baiLam()
    {
        return $this->belongsTo(BaiLam::class, 'MaBaiLam', 'MaBaiLam');
    }

    // ============================================
    // METHODS THEO BIỂU ĐỒ LỚP
    // ============================================

    /**
     * + HienThiKetQua()
     */
    public function hienThiKetQua()
    {
        return [
            'ma_ket_qua' => $this->MaKQ,
            'diem' => $this->Diem,
            'so_cau_dung' => $this->SoCauDung,
            'so_cau_sai' => $this->SoCauSai,
            'so_cau_khong_lam' => $this->SoCauKhongLam,
            'thoi_gian_hoan_thanh' => $this->ThoiGianHoanThanh,
            'hoc_sinh' => $this->hocSinh,
            'de_thi' => $this->deThi,
        ];
    }

    /**
     * + XuatSaoCao()
     */
    public function xuatSaoCao()
    {
        return [
            'ma_ket_qua' => $this->MaKQ,
            'diem' => $this->Diem,
            'so_cau_dung' => $this->SoCauDung,
            'so_cau_sai' => $this->SoCauSai,
            'so_cau_khong_lam' => $this->SoCauKhongLam,
            'tong_cau' => $this->SoCauDung + $this->SoCauSai + $this->SoCauKhongLam,
            'ti_le_dung' => round(($this->SoCauDung / ($this->SoCauDung + $this->SoCauSai + $this->SoCauKhongLam)) * 100, 2) . '%',
            'thoi_gian' => $this->ThoiGianHoanThanh->format('d/m/Y H:i:s'),
            'hoc_sinh' => $this->hocSinh->HoTen ?? 'N/A',
            'de_thi' => $this->deThi->TenDe ?? 'N/A',
        ];
    }
}
