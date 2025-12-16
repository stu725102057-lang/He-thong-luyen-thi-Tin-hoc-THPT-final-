<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiaoVien extends Model
{
    use HasFactory;

    protected $table = 'GiaoVien';
    
    // Cấu hình khóa chính CHAR(10)
    protected $primaryKey = 'MaGV';
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'MaGV',
        'MaTK',
        'HoTen',
        'SoDienThoai',
        'ChuyenMon',
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
     * Quan hệ 1-n với DeThi
     * Một giáo viên có thể tạo nhiều đề thi
     */
    public function deThi()
    {
        return $this->hasMany(DeThi::class, 'MaGV', 'MaGV');
    }

    // ============================================
    // METHODS THEO BIỂU ĐỒ LỚP & YÊU CẦU MODULE 3
    // ============================================

    /**
     * UR-03.1: Quản lý Ngân hàng câu hỏi
     * + ThemCauHoi($data)
     */
    public function themCauHoi($data)
    {
        $maCH = 'CH' . str_pad(rand(1, 99999999), 8, '0', STR_PAD_LEFT);
        
        $cauHoi = CauHoi::create(array_merge($data, ['MaCH' => $maCH]));
        
        Loi::luuLogLoi('Info', 'Thêm câu hỏi mới: ' . $maCH, 'Giáo viên: ' . $this->MaGV, $this->MaTK);
        
        return $cauHoi;
    }

    /**
     * + SuaCauHoi($maCH, $data)
     */
    public function suaCauHoi($maCH, $data)
    {
        $cauHoi = CauHoi::find($maCH);
        if (!$cauHoi) {
            return ['success' => false, 'message' => 'Câu hỏi không tồn tại'];
        }
        
        $cauHoi->update($data);
        
        Loi::luuLogLoi('Info', 'Sửa câu hỏi: ' . $maCH, 'Giáo viên: ' . $this->MaGV, $this->MaTK);
        
        return ['success' => true, 'cau_hoi' => $cauHoi];
    }

    /**
     * + XoaCauHoi($maCH)
     */
    public function xoaCauHoi($maCH)
    {
        $cauHoi = CauHoi::find($maCH);
        if (!$cauHoi) {
            return ['success' => false, 'message' => 'Câu hỏi không tồn tại'];
        }
        
        $cauHoi->delete();
        
        Loi::luuLogLoi('Info', 'Xóa câu hỏi: ' . $maCH, 'Giáo viên: ' . $this->MaGV, $this->MaTK);
        
        return ['success' => true, 'message' => 'Đã xóa câu hỏi'];
    }

    /**
     * UR-03.2: Nhập/Xuất câu hỏi
     * + XemDSBanSaoLuu() - Xem danh sách
     */
    public function xemDSBanSaoLuu()
    {
        return SaoLuu::xemDSSaoLuu();
    }

    /**
     * UR-03.3: Tạo đề thi thủ công
     * + TaoDeThi($data, $danhSachCauHoi)
     */
    public function taoDeThi($data, $danhSachCauHoi = [])
    {
        $maDe = 'DE' . str_pad(rand(1, 99999999), 8, '0', STR_PAD_LEFT);
        
        $deThi = DeThi::create(array_merge($data, [
            'MaDe' => $maDe,
            'MaGV' => $this->MaGV,
            'NgayTao' => now(),
            'SoLuongCauHoi' => count($danhSachCauHoi),
        ]));
        
        // Thêm câu hỏi vào đề thi
        if (!empty($danhSachCauHoi)) {
            $deThi->themCauHoi($danhSachCauHoi);
        }
        
        Loi::luuLogLoi('Info', 'Tạo đề thi mới: ' . $maDe, 'Giáo viên: ' . $this->MaGV, $this->MaTK);
        
        return $deThi;
    }

    /**
     * + CapNhatDeThi($maDe, $data)
     */
    public function capNhatDeThi($maDe, $data)
    {
        $deThi = DeThi::find($maDe);
        if (!$deThi || $deThi->MaGV !== $this->MaGV) {
            return ['success' => false, 'message' => 'Đề thi không tồn tại hoặc không có quyền'];
        }
        
        $deThi->update($data);
        
        Loi::luuLogLoi('Info', 'Cập nhật đề thi: ' . $maDe, 'Giáo viên: ' . $this->MaGV, $this->MaTK);
        
        return ['success' => true, 'de_thi' => $deThi];
    }

    /**
     * + XoaDeThi($maDe)
     */
    public function xoaDeThi($maDe)
    {
        $deThi = DeThi::find($maDe);
        if (!$deThi || $deThi->MaGV !== $this->MaGV) {
            return ['success' => false, 'message' => 'Đề thi không tồn tại hoặc không có quyền'];
        }
        
        $deThi->delete();
        
        Loi::luuLogLoi('Info', 'Xóa đề thi: ' . $maDe, 'Giáo viên: ' . $this->MaGV, $this->MaTK);
        
        return ['success' => true, 'message' => 'Đã xóa đề thi'];
    }

    /**
     * UR-03.5: Thống kê kết quả lớp học
     * + XemThongKe()
     */
    public function xemThongKe()
    {
        $danhSachDeThi = $this->deThi;
        $thongKe = [];
        
        foreach ($danhSachDeThi as $deThi) {
            $ketQua = $deThi->ketQua;
            
            $thongKe[] = [
                'ma_de' => $deThi->MaDe,
                'ten_de' => $deThi->TenDe,
                'so_luong_thi' => $ketQua->count(),
                'diem_trung_binh' => round($ketQua->avg('Diem'), 2),
                'diem_cao_nhat' => $ketQua->max('Diem'),
                'diem_thap_nhat' => $ketQua->min('Diem'),
            ];
        }
        
        return $thongKe;
    }

    /**
     * + TaoThongKe()
     */
    public function taoThongKe()
    {
        return $this->xemThongKe();
    }
}
