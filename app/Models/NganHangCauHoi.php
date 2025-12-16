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

    // ============================================
    // METHODS THEO BIỂU ĐỒ LỚP & YÊU CẦU UR-03.1
    // ============================================

    /**
     * + ThemCauHoi($data)
     */
    public function themCauHoi($data)
    {
        $maCH = 'CH' . str_pad(rand(1, 99999999), 8, '0', STR_PAD_LEFT);
        
        return CauHoi::create(array_merge($data, [
            'MaCH' => $maCH,
            'MaNH' => $this->MaNH,
        ]));
    }

    /**
     * + XoaCauHoi($maCH)
     */
    public function xoaCauHoi($maCH)
    {
        $cauHoi = $this->cauHoi()->where('MaCH', $maCH)->first();
        if ($cauHoi) {
            $cauHoi->delete();
            return ['success' => true, 'message' => 'Đã xóa câu hỏi'];
        }
        return ['success' => false, 'message' => 'Câu hỏi không tồn tại'];
    }

    /**
     * + TimKiemCauHoi($keyword, $doKho)
     */
    public function timKiemCauHoi($keyword = null, $doKho = null)
    {
        $query = $this->cauHoi();
        
        if ($keyword) {
            $query->where('NoiDung', 'like', '%' . $keyword . '%');
        }
        
        if ($doKho) {
            $query->where('DoKho', $doKho);
        }
        
        return $query->get();
    }
}
