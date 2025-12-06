<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaiLam extends Model
{
    use HasFactory;

    protected $table = 'BaiLam';
    
    // Cấu hình khóa chính CHAR(10)
    protected $primaryKey = 'MaBaiLam';
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'MaBaiLam',
        'DSCauTraLoi',
        'Diem',
        'ThoiGianBatDau',
        'ThoiGianNop',
        'TrangThai',
        'MaHS',
        'MaDe',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'DSCauTraLoi' => 'array',  // JSON array
        'Diem' => 'float',
        'ThoiGianBatDau' => 'datetime',
        'ThoiGianNop' => 'datetime',
    ];

    // ============================================
    // RELATIONSHIPS
    // ============================================

    /**
     * Quan hệ với HocSinh
     * Bài làm thuộc về một học sinh
     */
    public function hocSinh()
    {
        return $this->belongsTo(HocSinh::class, 'MaHS', 'MaHS');
    }

    /**
     * Quan hệ với DeThi
     * Bài làm được tạo từ một đề thi
     */
    public function deThi()
    {
        return $this->belongsTo(DeThi::class, 'MaDe', 'MaDe');
    }

    /**
     * Quan hệ 1-1 với KetQua
     * Một bài làm có một kết quả
     */
    public function ketQua()
    {
        return $this->hasOne(KetQua::class, 'MaBaiLam', 'MaBaiLam');
    }

    /**
     * Quan hệ 1-1 với ThoiGian
     */
    public function thoiGian()
    {
        return $this->hasOne(ThoiGian::class, 'MaBaiLam', 'MaBaiLam');
    }

    // ============================================
    // HELPER METHODS
    // ============================================

    /**
     * Kiểm tra trạng thái bài làm
     */
    public function isDangLam()
    {
        return $this->TrangThai === 'DangLam';
    }

    public function isDaNop()
    {
        return $this->TrangThai === 'DaNop';
    }

    public function isChamDiem()
    {
        return $this->TrangThai === 'ChamDiem';
    }
}
