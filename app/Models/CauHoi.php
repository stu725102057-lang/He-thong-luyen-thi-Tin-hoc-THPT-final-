<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CauHoi extends Model
{
    use HasFactory;

    protected $table = 'CauHoi';
    
    // Cấu hình khóa chính CHAR(10)
    protected $primaryKey = 'MaCH';
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'MaCH',
        'NoiDung',
        'DapAn',
        'DapAnA',
        'DapAnB',
        'DapAnC',
        'DapAnD',
        'DoKho',
        'MaNH',
    ];

    // ============================================
    // RELATIONSHIPS
    // ============================================

    /**
     * Quan hệ với NganHangCauHoi
     */
    public function nganHangCauHoi()
    {
        return $this->belongsTo(NganHangCauHoi::class, 'MaNH', 'MaNH');
    }

    /**
     * Quan hệ n-n với DeThi (qua bảng trung gian DETHI_CAUHOI)
     * Một câu hỏi có thể xuất hiện trong nhiều đề thi
     */
    public function deThi()
    {
        return $this->belongsToMany(
            DeThi::class,
            'DETHI_CAUHOI',  // Tên bảng trung gian
            'MaCH',          // Foreign key của bảng hiện tại trong bảng trung gian
            'MaDe',          // Foreign key của bảng đích trong bảng trung gian
            'MaCH',          // Local key của bảng hiện tại
            'MaDe'           // Local key của bảng đích
        )
        ->withPivot('ThuTu')  // Lấy thêm cột ThuTu từ bảng trung gian
        ->withTimestamps();
    }
}
