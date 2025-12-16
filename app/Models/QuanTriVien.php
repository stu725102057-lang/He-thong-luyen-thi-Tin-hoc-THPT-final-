<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuanTriVien extends Model
{
    use HasFactory;

    protected $table = 'QuanTriVien';
    
    // Cấu hình khóa chính CHAR(10)
    protected $primaryKey = 'MaQTV';
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'MaQTV',
        'MaTK',
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
     * Quan hệ với SaoLuu
     */
    public function saoLuu()
    {
        return $this->hasMany(SaoLuu::class, 'MaQTV', 'MaQTV');
    }

    // ============================================
    // METHODS THEO BIỂU ĐỒ LỚP & YÊU CẦU MODULE 4
    // ============================================

    /**
     * UR-04.1 & UR-01.2: Quản lý tài khoản người dùng
     * + QuanLyNguoiDung()
     */
    public function quanLyNguoiDung()
    {
        return TaiKhoan::with(['quanTriVien', 'giaoVien', 'hocSinh'])->get();
    }

    /**
     * + DangKyNguoiDung($data)
     */
    public function dangKyNguoiDung($data)
    {
        try {
            // Tạo tài khoản
            $maTK = 'TK' . str_pad(rand(1, 99999999), 8, '0', STR_PAD_LEFT);
            
            $taiKhoan = TaiKhoan::create([
                'MaTK' => $maTK,
                'TenDangNhap' => $data['TenDangNhap'],
                'MatKhau' => $data['MatKhau'], // Sẽ tự động hash
                'Email' => $data['Email'],
                'Role' => $data['Role'],
                'TrangThai' => 1,
            ]);
            
            // Tạo bản ghi tương ứng theo role
            if ($data['Role'] === 'admin') {
                $maQTV = 'QTV' . str_pad(rand(1, 99999999), 8, '0', STR_PAD_LEFT);
                QuanTriVien::create([
                    'MaQTV' => $maQTV,
                    'MaTK' => $maTK,
                ]);
            } elseif ($data['Role'] === 'giaovien') {
                $maGV = 'GV' . str_pad(rand(1, 99999999), 8, '0', STR_PAD_LEFT);
                GiaoVien::create([
                    'MaGV' => $maGV,
                    'MaTK' => $maTK,
                    'HoTen' => $data['HoTen'] ?? null,
                    'SoDienThoai' => $data['SoDienThoai'] ?? null,
                    'ChuyenMon' => $data['ChuyenMon'] ?? null,
                ]);
            } elseif ($data['Role'] === 'hocsinh') {
                $maHS = 'HS' . str_pad(rand(1, 99999999), 8, '0', STR_PAD_LEFT);
                HocSinh::create([
                    'MaHS' => $maHS,
                    'MaTK' => $maTK,
                    'HoTen' => $data['HoTen'],
                    'Lop' => $data['Lop'] ?? null,
                    'Truong' => $data['Truong'] ?? null,
                ]);
            }
            
            Loi::luuLogLoi('Info', 'Đăng ký người dùng mới: ' . $maTK, 'Admin: ' . $this->MaQTV, $this->MaTK);
            
            return ['success' => true, 'tai_khoan' => $taiKhoan];
            
        } catch (\Exception $e) {
            Loi::luuLogLoi('Error', 'Đăng ký người dùng thất bại: ' . $e->getMessage(), $e->getTraceAsString(), $this->MaTK);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * + CapNhatNguoiDung($maTK, $data)
     */
    public function capNhatNguoiDung($maTK, $data)
    {
        $taiKhoan = TaiKhoan::find($maTK);
        if (!$taiKhoan) {
            return ['success' => false, 'message' => 'Tài khoản không tồn tại'];
        }
        
        $taiKhoan->capNhatThongTin($data);
        
        Loi::luuLogLoi('Info', 'Cập nhật người dùng: ' . $maTK, 'Admin: ' . $this->MaQTV, $this->MaTK);
        
        return ['success' => true, 'tai_khoan' => $taiKhoan];
    }

    /**
     * + XoaNguoiDung($maTK)
     */
    public function xoaNguoiDung($maTK)
    {
        $taiKhoan = TaiKhoan::find($maTK);
        if (!$taiKhoan) {
            return ['success' => false, 'message' => 'Tài khoản không tồn tại'];
        }
        
        $taiKhoan->delete();
        
        Loi::luuLogLoi('Info', 'Xóa người dùng: ' . $maTK, 'Admin: ' . $this->MaQTV, $this->MaTK);
        
        return ['success' => true, 'message' => 'Đã xóa người dùng'];
    }

    /**
     * UR-04.2: Phân quyền người dùng
     * + KhoaTaiKhoan($maTK)
     */
    public function khoaTaiKhoan($maTK)
    {
        $taiKhoan = TaiKhoan::find($maTK);
        if (!$taiKhoan) {
            return ['success' => false, 'message' => 'Tài khoản không tồn tại'];
        }
        
        $taiKhoan->TrangThai = 0;
        $taiKhoan->save();
        
        Loi::luuLogLoi('Info', 'Khóa tài khoản: ' . $maTK, 'Admin: ' . $this->MaQTV, $this->MaTK);
        
        return ['success' => true, 'message' => 'Đã khóa tài khoản'];
    }

    /**
     * + MoKhoaTaiKhoan($maTK)
     */
    public function moKhoaTaiKhoan($maTK)
    {
        $taiKhoan = TaiKhoan::find($maTK);
        if (!$taiKhoan) {
            return ['success' => false, 'message' => 'Tài khoản không tồn tại'];
        }
        
        $taiKhoan->TrangThai = 1;
        $taiKhoan->save();
        
        Loi::luuLogLoi('Info', 'Mở khóa tài khoản: ' . $maTK, 'Admin: ' . $this->MaQTV, $this->MaTK);
        
        return ['success' => true, 'message' => 'Đã mở khóa tài khoản'];
    }

    /**
     * UR-04.3: Giám sát hệ thống
     * + GiamSatHeThong()
     */
    public function giamSatHeThong()
    {
        return [
            'tong_nguoi_dung' => TaiKhoan::count(),
            'tong_hoc_sinh' => HocSinh::count(),
            'tong_giao_vien' => GiaoVien::count(),
            'tong_de_thi' => DeThi::count(),
            'tong_bai_lam' => BaiLam::count(),
            'nguoi_dung_hoat_dong' => TaiKhoan::where('LanDangNhapCuoi', '>=', now()->subDay())->count(),
        ];
    }

    /**
     * UR-04.4: Sao lưu dữ liệu
     * + TaoSaoLuu()
     */
    public function taoSaoLuu()
    {
        $saoLuu = SaoLuu::create([
            'MaQTV' => $this->MaQTV,
        ]);
        
        $result = $saoLuu->thuHienSaoLuu();
        
        if ($result) {
            return ['success' => true, 'sao_luu' => $saoLuu];
        }
        
        return ['success' => false, 'message' => 'Sao lưu thất bại'];
    }

    /**
     * UR-04.5: Phục hồi dữ liệu
     * + PhucHoiSaoLuu($maSaoLuu)
     */
    public function phucHoiSaoLuu($maSaoLuu)
    {
        $saoLuu = SaoLuu::find($maSaoLuu);
        if (!$saoLuu) {
            return ['success' => false, 'message' => 'Bản sao lưu không tồn tại'];
        }
        
        $result = $saoLuu->khoiPhucSaoLuu();
        
        if ($result) {
            return ['success' => true, 'message' => 'Phục hồi dữ liệu thành công'];
        }
        
        return ['success' => false, 'message' => 'Phục hồi dữ liệu thất bại'];
    }
}
