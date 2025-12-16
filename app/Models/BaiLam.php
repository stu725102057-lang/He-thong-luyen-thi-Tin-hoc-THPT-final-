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
        'SoLanViPham',
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
    // METHODS THEO BIỂU ĐỒ LỚP & YÊU CẦU
    // ============================================

    /**
     * UR-05.2: Tự động lưu bài làm
     * + LuuBaiLam($cauTraLoi)
     */
    public function luuBaiLam($cauTraLoi = null)
    {
        if ($cauTraLoi !== null) {
            $this->DSCauTraLoi = $cauTraLoi;
        }
        
        $this->save();
        
        return ['success' => true, 'message' => 'Đã lưu bài làm'];
    }

    /**
     * + NopBai()
     */
    public function nopBai()
    {
        $this->ThoiGianNop = now();
        $this->TrangThai = 'DaNop';
        $this->save();
        
        // Tự động chấm điểm
        $this->tinhDiem();
        
        return ['success' => true, 'diem' => $this->Diem];
    }

    /**
     * UR-02.3: Xem kết quả tức thì - Tính điểm tự động
     * + TinhDiem()
     */
    public function tinhDiem()
    {
        $deThi = $this->deThi()->with('cauHoi')->first();
        if (!$deThi) {
            return 0;
        }
        
        $cauHoiList = $deThi->cauHoi;
        $dsTraLoi = $this->DSCauTraLoi ?? [];
        
        $soCauDung = 0;
        $soCauSai = 0;
        $soCauKhongLam = 0;
        
        foreach ($cauHoiList as $cauHoi) {
            $traLoi = collect($dsTraLoi)->firstWhere('MaCH', $cauHoi->MaCH);
            
            if (!$traLoi || !isset($traLoi['TraLoi'])) {
                $soCauKhongLam++;
            } elseif ($traLoi['TraLoi'] === $cauHoi->DapAn) {
                $soCauDung++;
            } else {
                $soCauSai++;
            }
        }
        
        $tongCau = $cauHoiList->count();
        $diem = $tongCau > 0 ? ($soCauDung / $tongCau) * 10 : 0;
        
        // Cập nhật điểm và trạng thái
        $this->Diem = round($diem, 2);
        $this->TrangThai = 'ChamDiem';
        $this->save();
        
        // Tạo hoặc cập nhật kết quả
        KetQua::updateOrCreate(
            ['MaBaiLam' => $this->MaBaiLam],
            [
                'MaKQ' => 'KQ' . str_pad(rand(1, 99999999), 8, '0', STR_PAD_LEFT),
                'Diem' => $this->Diem,
                'SoCauDung' => $soCauDung,
                'SoCauSai' => $soCauSai,
                'SoCauKhongLam' => $soCauKhongLam,
                'ThoiGianHoanThanh' => $this->ThoiGianNop ?? now(),
                'MaHS' => $this->MaHS,
                'MaDe' => $this->MaDe,
            ]
        );
        
        return $this->Diem;
    }

    /**
     * UR-05.1: Cảnh báo gian lận
     * + CanhBaoGianLan()
     */
    public function canhBaoGianLan()
    {
        $this->SoLanViPham++;
        $this->save();
        
        // Log vi phạm
        Loi::luuLogLoi(
            'Warning',
            'Phát hiện hành vi gian lận',
            'Học sinh: ' . $this->MaHS . ', Bài làm: ' . $this->MaBaiLam . ', Lần vi phạm: ' . $this->SoLanViPham,
            $this->hocSinh->MaTK
        );
        
        // Nếu vi phạm quá 5 lần, tự động nộp bài
        if ($this->SoLanViPham >= 5) {
            $this->nopBai();
            return [
                'success' => false, 
                'message' => 'Bài làm đã bị nộp tự động do vi phạm quá nhiều lần',
                'auto_submit' => true
            ];
        }
        
        return [
            'success' => true, 
            'message' => 'Đã ghi nhận vi phạm lần ' . $this->SoLanViPham,
            'vi_pham' => $this->SoLanViPham
        ];
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

    /**
     * Mutator: ensure any incoming TrangThai values are normalized to the
     * ENUM values used in the database. This prevents accidental inserts of
     * localized strings (e.g. 'Đã nộp') which cause MySQL "Data truncated"
     * errors when the column is ENUM('DangLam','DaNop','ChamDiem').
     */
    public function setTrangThaiAttribute($value)
    {
        if ($value === null) {
            $this->attributes['TrangThai'] = null;
            return;
        }

        // Common mappings from UI/localized values to DB enum values
        $map = [
            // Vietnamese labels -> enum keys
            'Đã nộp' => 'DaNop',
            'Da nop' => 'DaNop',
            'Đang làm' => 'DangLam',
            'Dang lam' => 'DangLam',
            'Chấm điểm' => 'ChamDiem',
            'Cham diem' => 'ChamDiem',

            // Accept already-correct enum values as-is
            'DangLam' => 'DangLam',
            'DaNop' => 'DaNop',
            'ChamDiem' => 'ChamDiem',
            'danglam' => 'DangLam',
            'danop' => 'DaNop',
            'chamdium' => 'ChamDiem'
        ];

        if (is_bool($value)) {
            // Defensive: boolean shouldn't be used here but map to a safe default
            $this->attributes['TrangThai'] = $value ? 'DaNop' : 'DangLam';
            return;
        }

        $trimmed = trim($value);

        if (isset($map[$trimmed])) {
            $this->attributes['TrangThai'] = $map[$trimmed];
            return;
        }

        // Fallback: if value contains non-ascii characters (e.g. accented)
        // try a simple replace to map common Vietnamese characters to ascii
        $normalized = str_replace(['Đ', 'đ', 'á', 'à', 'ã', 'ạ', 'â', 'ấ', 'ậ', 'ầ', 'ã', 'ă', 'ắ'], ['D','d','a','a','a','a','a','a','a','a','a','a','a'], $trimmed);
        if (isset($map[$normalized])) {
            $this->attributes['TrangThai'] = $map[$normalized];
            return;
        }

        // Last resort: if it already looks like an enum key (ASCII letters), use it
        if (in_array($trimmed, ['DangLam','DaNop','ChamDiem'])) {
            $this->attributes['TrangThai'] = $trimmed;
            return;
        }

        // If nothing matches, default to 'DangLam' to avoid DB enum errors
        $this->attributes['TrangThai'] = 'DangLam';
    }
}
