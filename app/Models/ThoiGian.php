<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThoiGian extends Model
{
    use HasFactory;

    protected $table = 'ThoiGian';
    protected $primaryKey = 'MaThoiGian';
    public $incrementing = true;

    protected $fillable = [
        'ThoiGianBatDau',
        'ThoiGianKetThuc',
        'TongThoiGian',
        'MaBaiLam',
    ];

    protected $casts = [
        'ThoiGianBatDau' => 'datetime',
        'ThoiGianKetThuc' => 'datetime',
    ];

    /**
     * Relationships theo biểu đồ lớp
     */
    
    // Quan hệ với BaiLam (n-1)
    public function baiLam()
    {
        return $this->belongsTo(BaiLam::class, 'MaBaiLam', 'MaBaiLam');
    }

    /**
     * Methods theo biểu đồ lớp
     */
    
    // + BatDau()
    public function batDau()
    {
        $this->ThoiGianBatDau = now();
        $this->save();
        return $this;
    }

    // + DemNguoc()
    public function demNguoc()
    {
        if (!$this->ThoiGianBatDau || !$this->TongThoiGian) {
            return 0;
        }

        $thoiGianDaLam = now()->diffInMinutes($this->ThoiGianBatDau);
        $thoiGianConLai = $this->TongThoiGian - $thoiGianDaLam;
        
        return max(0, $thoiGianConLai);
    }

    // + KetThuc()
    public function ketThuc()
    {
        $this->ThoiGianKetThuc = now();
        if ($this->ThoiGianBatDau) {
            $this->TongThoiGian = $this->ThoiGianBatDau->diffInMinutes($this->ThoiGianKetThuc);
        }
        $this->save();
        return $this;
    }

    // + TinhThoiGian()
    public function tinhThoiGian()
    {
        if ($this->ThoiGianBatDau && $this->ThoiGianKetThuc) {
            return $this->ThoiGianBatDau->diffInMinutes($this->ThoiGianKetThuc);
        }
        return 0;
    }
}
