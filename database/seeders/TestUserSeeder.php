<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TaiKhoan;
use App\Models\HocSinh;
use App\Models\GiaoVien;
use App\Models\QuanTriVien;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. TẠO TÀI KHOẢN ADMIN (Idempotent - không tạo duplicate)
        $taiKhoanAdmin = TaiKhoan::firstOrCreate(
            ['MaTK' => 'TK00000001'],
            [
                'TenDangNhap' => 'admin',
                'MatKhau' => Hash::make('admin123'),
                'Email' => 'admin@thpt.edu.vn',
                'Role' => 'admin',
                'TrangThai' => 1,
            ]
        );

        QuanTriVien::firstOrCreate(
            ['MaQTV' => 'QTV0000001'],
            ['MaTK' => $taiKhoanAdmin->MaTK]
        );

        echo "✅ Tạo admin thành công: admin / admin123\n";

        // 2. TẠO TÀI KHOẢN GIÁO VIÊN (Idempotent)
        $taiKhoanGV = TaiKhoan::firstOrCreate(
            ['MaTK' => 'TK00000002'],
            [
                'TenDangNhap' => 'giaovien',
                'MatKhau' => Hash::make('123456'),
                'Email' => 'giaovien@thpt.edu.vn',
                'Role' => 'giaovien',
                'TrangThai' => 1,
            ]
        );

        GiaoVien::firstOrCreate(
            ['MaGV' => 'GV00000001'],
            [
                'MaTK' => $taiKhoanGV->MaTK,
                'HoTen' => 'Nguyễn Văn Giáo',
                'SoDienThoai' => '0123456789',
                'ChuyenMon' => 'Tin học',
            ]
        );

        echo "✅ Tạo giáo viên thành công: giaovien / 123456\n";

        // 3. TẠO TÀI KHOẢN HỌC SINH (Idempotent)
        $taiKhoanHS = TaiKhoan::firstOrCreate(
            ['MaTK' => 'TK00000003'],
            [
                'TenDangNhap' => 'hocsinh',
                'MatKhau' => Hash::make('123456'),
                'Email' => 'hocsinh@thpt.edu.vn',
                'Role' => 'hocsinh',
                'TrangThai' => 1,
            ]
        );

        HocSinh::firstOrCreate(
            ['MaHS' => 'HS00000001'],
            [
                'MaTK' => $taiKhoanHS->MaTK,
                'HoTen' => 'Trần Thị Học',
                'Lop' => '12A1',
                'Truong' => 'THPT Nguyễn Trãi',
            ]
        );

        echo "✅ Tạo học sinh thành công: hocsinh / 123456\n";

        echo "\n========================================\n";
        echo "📝 TÀI KHOẢN TEST ĐÃ TẠO:\n";
        echo "========================================\n";
        echo "1. Admin:\n";
        echo "   - Username: admin\n";
        echo "   - Password: admin123\n";
        echo "   - Email: admin@thpt.edu.vn\n\n";
        echo "2. Giáo viên:\n";
        echo "   - Username: giaovien\n";
        echo "   - Password: 123456\n";
        echo "   - Email: giaovien@thpt.edu.vn\n\n";
        echo "3. Học sinh:\n";
        echo "   - Username: hocsinh\n";
        echo "   - Password: 123456\n";
        echo "   - Email: hocsinh@thpt.edu.vn\n";
        echo "========================================\n";
    }
}
