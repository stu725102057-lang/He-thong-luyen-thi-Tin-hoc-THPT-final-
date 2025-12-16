<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HocSinh extends Model
{
    use HasFactory;

    protected $table = 'HocSinh';
    
    // Cấu hình khóa chính CHAR(10)
    protected $primaryKey = 'MaHS';
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'MaHS',
        'MaTK',
        'HoTen',
        'Lop',
        'Truong',
    ];

    // ============================================
    // RELATIONSHIPS
    // ============================================

    /**
     * Quan hệ với TaiKhoan (Kế thừa)
     */
    public function taiKhoan()
    {
        return $this->belongsTo(TaiKhoan::class, 'MaTK', 'MaTK');
    }

    /**
     * Quan hệ 1-n với BaiLam
     * Một học sinh có thể làm nhiều bài thi
     */
    public function baiLam()
    {
        return $this->hasMany(BaiLam::class, 'MaHS', 'MaHS');
    }

    /**
     * Quan hệ 1-n với KetQua
     * Một học sinh có thể có nhiều kết quả
     */
    public function ketQua()
    {
        return $this->hasMany(KetQua::class, 'MaHS', 'MaHS');
    }

    // ============================================
    // METHODS THEO BIỂU ĐỒ LỚP & YÊU CẦU
    // ============================================

    /**
     * UR-02.1: Chọn bài thi
     * + ChonDe()
     */
    public function chonDe($maDe)
    {
        $deThi = DeThi::find($maDe);
        if (!$deThi || !$deThi->TrangThai) {
            return ['success' => false, 'message' => 'Đề thi không tồn tại hoặc không khả dụng'];
        }
        
        return ['success' => true, 'de_thi' => $deThi];
    }

    /**
     * + LamBai($maDe)
     */
    public function lamBai($maDe)
    {
        // Tạo bài làm mới
        $maBaiLam = 'BL' . str_pad(rand(1, 99999999), 8, '0', STR_PAD_LEFT);
        
        $baiLam = BaiLam::create([
            'MaBaiLam' => $maBaiLam,
            'MaHS' => $this->MaHS,
            'MaDe' => $maDe,
            'ThoiGianBatDau' => now(),
            'TrangThai' => 'DangLam',
            'SoLanViPham' => 0,
        ]);
        
        // Tạo thời gian làm bài
        $deThi = DeThi::find($maDe);
        ThoiGian::create([
            'ThoiGianBatDau' => now(),
            'TongThoiGian' => $deThi->ThoiGianLamBai,
            'MaBaiLam' => $maBaiLam,
        ]);
        
        return $baiLam;
    }

    /**
     * UR-02.2: Nộp bài
     * + NopBai($maBaiLam)
     */
    public function nopBai($maBaiLam)
    {
        $baiLam = BaiLam::find($maBaiLam);
        if (!$baiLam || $baiLam->MaHS !== $this->MaHS) {
            return ['success' => false, 'message' => 'Bài làm không hợp lệ'];
        }
        
        // Cập nhật trạng thái và thời gian nộp
        $baiLam->ThoiGianNop = now();
        $baiLam->TrangThai = 'DaNop';
        $baiLam->save();
        
        // Cập nhật thời gian kết thúc
        $thoiGian = ThoiGian::where('MaBaiLam', $maBaiLam)->first();
        if ($thoiGian) {
            $thoiGian->ketThuc();
        }
        
        // Tự động chấm điểm
        $baiLam->tinhDiem();
        
        return ['success' => true, 'bai_lam' => $baiLam];
    }

    /**
     * UR-02.4: Xem lại bài làm chi tiết
     * + XemBaiLam($maBaiLam)
     */
    public function xemBaiLam($maBaiLam)
    {
        $baiLam = BaiLam::with(['deThi.cauHoi'])->find($maBaiLam);
        
        if (!$baiLam || $baiLam->MaHS !== $this->MaHS) {
            return ['success' => false, 'message' => 'Bài làm không tồn tại'];
        }
        
        return ['success' => true, 'bai_lam' => $baiLam];
    }

    /**
     * UR-02.5: Thống kê tiến độ cá nhân
     * + XemKetQua()
     */
    public function xemKetQua()
    {
        return $this->ketQua()->with('deThi')->orderBy('created_at', 'desc')->get();
    }

    /**
     * + ThongKe()
     */
    public function thongKe()
    {
        $ketQua = $this->ketQua;
        
        $tongBaiLam = $ketQua->count();
        $diemTrungBinh = $ketQua->avg('Diem');
        $diemCaoNhat = $ketQua->max('Diem');
        $diemThapNhat = $ketQua->min('Diem');
        
        return [
            'tong_bai_lam' => $tongBaiLam,
            'diem_trung_binh' => round($diemTrungBinh, 2),
            'diem_cao_nhat' => $diemCaoNhat,
            'diem_thap_nhat' => $diemThapNhat,
            'lich_su' => $ketQua,
        ];
    }
}
