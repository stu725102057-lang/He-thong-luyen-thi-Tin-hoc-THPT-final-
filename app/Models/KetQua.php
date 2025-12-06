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
}
