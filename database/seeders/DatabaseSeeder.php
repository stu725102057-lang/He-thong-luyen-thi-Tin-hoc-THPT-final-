<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\TaiKhoan;
use App\Models\QuanTriVien;
use App\Models\GiaoVien;
use App\Models\HocSinh;
use App\Models\NganHangCauHoi;
use App\Models\CauHoi;
use App\Models\DeThi;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ============================================
        // 1. TẠO TÀI KHOẢN ADMIN
        // ============================================
        $taiKhoanAdmin = TaiKhoan::create([
            'MaTK' => 'TK001',
            'TenDangNhap' => 'admin',
            'MatKhau' => Hash::make('123456'),
            'Email' => 'admin@tracnghiem.com',
            'Role' => 'admin',
            'TrangThai' => true,
            'LanDangNhapCuoi' => null,
        ]);

        QuanTriVien::create([
            'MaQTV' => 'QTV001',
            'MaTK' => $taiKhoanAdmin->MaTK,
        ]);

        $this->command->info('✓ Đã tạo tài khoản Admin: admin / 123456');

        // ============================================
        // 2. TẠO TÀI KHOẢN GIÁO VIÊN
        // ============================================
        $taiKhoanGV = TaiKhoan::create([
            'MaTK' => 'TK002',
            'TenDangNhap' => 'giaovien1',
            'MatKhau' => Hash::make('123456'),
            'Email' => 'giaovien1@tracnghiem.com',
            'Role' => 'giaovien',
            'TrangThai' => true,
            'LanDangNhapCuoi' => null,
        ]);

        GiaoVien::create([
            'MaGV' => 'GV001',
            'MaTK' => $taiKhoanGV->MaTK,
            'HoTen' => 'Nguyễn Văn An',
            'SoDienThoai' => '0912345678',
            'ChuyenMon' => 'Tin học',
        ]);

        $this->command->info('✓ Đã tạo tài khoản Giáo viên: giaovien1 / 123456');

        // ============================================
        // 3. TẠO TÀI KHOẢN HỌC SINH
        // ============================================
        $taiKhoanHS = TaiKhoan::create([
            'MaTK' => 'TK003',
            'TenDangNhap' => 'hocsinh1',
            'MatKhau' => Hash::make('123456'),
            'Email' => 'hocsinh1@tracnghiem.com',
            'Role' => 'hocsinh',
            'TrangThai' => true,
            'LanDangNhapCuoi' => null,
        ]);

        HocSinh::create([
            'MaHS' => 'HS001',
            'MaTK' => $taiKhoanHS->MaTK,
            'HoTen' => 'Trần Thị Bình',
            'Lop' => '12A1',
            'Truong' => 'THPT Nguyễn Huệ',
        ]);

        $this->command->info('✓ Đã tạo tài khoản Học sinh: hocsinh1 / 123456');

        // ============================================
        // 4. TẠO NGÂN HÀNG CÂU HỎI
        // ============================================
        $nganHang = NganHangCauHoi::create([
            'MaNH' => 'NH001',
            'TenNH' => 'Tin học đại cương',
            'MoTa' => 'Ngân hàng câu hỏi về tin học đại cương cho học sinh THPT',
        ]);

        $this->command->info('✓ Đã tạo Ngân hàng câu hỏi: Tin học đại cương');

        // ============================================
        // 5. TẠO 5 CÂU HỎI MẪU
        // ============================================
        $cauHoiList = [
            [
                'MaCH' => 'CH001',
                'NoiDung' => 'CPU là viết tắt của từ gì?',
                'DapAnA' => 'Central Processing Unit',
                'DapAnB' => 'Computer Personal Unit',
                'DapAnC' => 'Central Program Unit',
                'DapAnD' => 'Control Processing Unit',
                'DapAn' => 'A',
                'DoKho' => 'De',
            ],
            [
                'MaCH' => 'CH002',
                'NoiDung' => 'RAM là loại bộ nhớ nào?',
                'DapAnA' => 'Bộ nhớ chỉ đọc',
                'DapAnB' => 'Bộ nhớ truy xuất ngẫu nhiên',
                'DapAnC' => 'Bộ nhớ ngoài',
                'DapAnD' => 'Bộ nhớ cache',
                'DapAn' => 'B',
                'DoKho' => 'De',
            ],
            [
                'MaCH' => 'CH003',
                'NoiDung' => 'Đơn vị nhỏ nhất của thông tin trong máy tính là gì?',
                'DapAnA' => 'Byte',
                'DapAnB' => 'Bit',
                'DapAnC' => 'Kilobyte',
                'DapAnD' => 'Megabyte',
                'DapAn' => 'B',
                'DoKho' => 'TB',
            ],
            [
                'MaCH' => 'CH004',
                'NoiDung' => 'Hệ điều hành nào sau đây là của Microsoft?',
                'DapAnA' => 'macOS',
                'DapAnB' => 'Linux',
                'DapAnC' => 'Windows',
                'DapAnD' => 'Unix',
                'DapAn' => 'C',
                'DoKho' => 'De',
            ],
            [
                'MaCH' => 'CH005',
                'NoiDung' => 'Trong Excel, hàm nào dùng để tính tổng các giá trị?',
                'DapAnA' => 'AVERAGE',
                'DapAnB' => 'COUNT',
                'DapAnC' => 'SUM',
                'DapAnD' => 'MAX',
                'DapAn' => 'C',
                'DoKho' => 'TB',
            ],
        ];

        foreach ($cauHoiList as $cauHoiData) {
            CauHoi::create([
                'MaCH' => $cauHoiData['MaCH'],
                'NoiDung' => $cauHoiData['NoiDung'],
                'DapAnA' => $cauHoiData['DapAnA'],
                'DapAnB' => $cauHoiData['DapAnB'],
                'DapAnC' => $cauHoiData['DapAnC'],
                'DapAnD' => $cauHoiData['DapAnD'],
                'DapAn' => $cauHoiData['DapAn'],
                'DoKho' => $cauHoiData['DoKho'],
                'MaNH' => $nganHang->MaNH,
            ]);
        }

        $this->command->info('✓ Đã tạo 5 câu hỏi mẫu bằng Tiếng Việt');

        // ============================================
        // 6. TẠO ĐỀ THI MẪU
        // ============================================
        $deThi = DeThi::create([
            'MaDe' => 'DT001',
            'TenDe' => 'Kiểm tra Tin học đại cương',
            'ThoiGianLamBai' => 30, // 30 phút
            'NgayTao' => Carbon::now(),
            'SoLuongCauHoi' => 5,
            'MaGV' => 'GV001',
            'MoTa' => 'Đề thi kiểm tra kiến thức tin học cơ bản',
            'TrangThai' => true,
        ]);

        // Gắn câu hỏi vào đề thi (bảng trung gian)
        $deThi->cauHoi()->attach([
            'CH001' => ['ThuTu' => 1],
            'CH002' => ['ThuTu' => 2],
            'CH003' => ['ThuTu' => 3],
            'CH004' => ['ThuTu' => 4],
            'CH005' => ['ThuTu' => 5],
        ]);

        $this->command->info('✓ Đã tạo đề thi mẫu: Kiểm tra Tin học đại cương (5 câu)');

        // ============================================
        // HOÀN THÀNH
        // ============================================
        $this->command->info('');
        $this->command->info('========================================');
        $this->command->info('SEED DỮ LIỆU THÀNH CÔNG!');
        $this->command->info('========================================');
        $this->command->info('Tài khoản đăng nhập:');
        $this->command->info('  Admin:      admin / 123456');
        $this->command->info('  Giáo viên:  giaovien1 / 123456');
        $this->command->info('  Học sinh:   hocsinh1 / 123456');
        $this->command->info('========================================');
    }
}
