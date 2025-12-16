<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

try {
    // Xóa nếu đã tồn tại
    DB::table('giaovien')->where('MaTK', 'TK00000002')->delete();
    DB::table('taikhoan')->where('MaTK', 'TK00000002')->delete();
    
    // Tạo tài khoản giáo viên
    DB::table('taikhoan')->insert([
        'MaTK' => 'TK00000002',
        'TenDangNhap' => 'giaovien',
        'Email' => 'giaovien@thpt.edu.vn',
        'MatKhau' => Hash::make('123456'),
        'Role' => 'giaovien',
        'TrangThai' => 1,
        'created_at' => now(),
        'updated_at' => now()
    ]);
    
    echo "✓ TaiKhoan created: TK00000002 (giaovien/123456)\n";
    
    // Tạo GiaoVien record
    DB::table('giaovien')->insert([
        'MaGV' => 'GV00000001',
        'MaTK' => 'TK00000002',
        'HoTen' => 'Nguyen Van Giao Vien',
        'SoDienThoai' => '0987654321',
        'ChuyenMon' => 'Tin hoc',
        'created_at' => now(),
        'updated_at' => now()
    ]);
    
    echo "✓ GiaoVien created: GV00000001\n";
    
    // Kiểm tra
    $check = DB::table('taikhoan')
        ->leftJoin('giaovien', 'taikhoan.MaTK', '=', 'giaovien.MaTK')
        ->where('taikhoan.Role', 'giaovien')
        ->select('taikhoan.MaTK', 'taikhoan.TenDangNhap', 'taikhoan.Email', 'giaovien.MaGV', 'giaovien.HoTen')
        ->get();
    
    echo "\n=== Teacher Accounts ===\n";
    foreach ($check as $row) {
        echo "MaTK: {$row->MaTK}, Username: {$row->TenDangNhap}, MaGV: {$row->MaGV}, Name: {$row->HoTen}\n";
    }
    
    echo "\n✓ Teacher account created successfully!\n";
    echo "Login: giaovien / 123456\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
