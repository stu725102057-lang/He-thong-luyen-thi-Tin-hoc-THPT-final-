<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== SỬA CÁC ĐỀ THI BỊ LỖI ===\n\n";

$brokenExams = ['DE010', 'DE011', 'DE012'];

foreach ($brokenExams as $maDe) {
    echo "Đang sửa đề {$maDe}...\n";
    
    // Lấy thông tin đề thi
    $deThi = DB::table('DeThi')->where('MaDe', $maDe)->first();
    
    if (!$deThi) {
        echo "  ❌ Không tìm thấy đề thi!\n\n";
        continue;
    }
    
    // Xóa câu hỏi cũ (nếu có)
    $deleted = DB::table('DETHI_CAUHOI')->where('MaDe', $maDe)->delete();
    
    // Lấy câu hỏi ngẫu nhiên
    $cauHoi = DB::table('CauHoi')
        ->inRandomOrder()
        ->limit($deThi->SoLuongCauHoi)
        ->get(['MaCH']);
    
    if ($cauHoi->count() < $deThi->SoLuongCauHoi) {
        echo "  ⚠️  Chỉ có {$cauHoi->count()}/{$deThi->SoLuongCauHoi} câu hỏi\n";
    }
    
    // Thêm câu hỏi mới
    foreach ($cauHoi as $index => $ch) {
        DB::table('DETHI_CAUHOI')->insert([
            'MaDe' => $maDe,
            'MaCH' => $ch->MaCH,
            'ThuTu' => $index + 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
    
    echo "  ✅ Đã thêm {$cauHoi->count()} câu hỏi\n\n";
}

echo "=== HOÀN TẤT ===\n";
