<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NganHangCauHoi extends Model
{
    use HasFactory;

    protected $table = 'NganHangCauHoi';
    
    // Cấu hình khóa chính CHAR(10)
    protected $primaryKey = 'MaNH';
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'MaNH',
        'TenNH',
        'MoTa',
    ];

    // ============================================
    // RELATIONSHIPS
    // ============================================

    /**
     * Quan hệ 1-n với CauHoi
     * Một ngân hàng câu hỏi có nhiều câu hỏi
     */
    public function cauHoi()
    {
        return $this->hasMany(CauHoi::class, 'MaNH', 'MaNH');
    }
}
