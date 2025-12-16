<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== FIX ĐỀ THI DE006 ===\n\n";

$maDe = 'DE006';

// Kiểm tra đề thi
$deThi = DB::table('DeThi')->where('MaDe', $maDe)->first();
if (!$deThi) {
    echo "Không tìm thấy đề thi $maDe\n";
    exit;
}

echo "Đề thi: {$deThi->TenDe}\n";
echo "Số câu khai báo: {$deThi->SoLuongCauHoi}\n";

// Kiểm tra câu hỏi hiện có
$currentCount = DB::table('DETHI_CAUHOI')->where('MaDe', $maDe)->count();
echo "Số câu hiện có: $currentCount\n\n";

if ($currentCount >= $deThi->SoLuongCauHoi) {
    echo "Đề thi đã có đủ câu hỏi!\n";
    exit;
}

// Lấy câu hỏi ngẫu nhiên
$soLuongCanThem = $deThi->SoLuongCauHoi - $currentCount;
echo "Cần thêm: $soLuongCanThem câu\n";

$cauHoi = DB::table('CauHoi')
    ->inRandomOrder()
    ->limit($soLuongCanThem)
    ->get(['MaCH']);

if ($cauHoi->count() < $soLuongCanThem) {
    echo "CẢNH BÁO: Chỉ có {$cauHoi->count()} câu hỏi trong database!\n";
}

// Thêm câu hỏi vào đề thi
$thuTuBatDau = $currentCount + 1;
foreach ($cauHoi as $index => $ch) {
    DB::table('DETHI_CAUHOI')->insert([
        'MaDe' => $maDe,
        'MaCH' => $ch->MaCH,
        'ThuTu' => $thuTuBatDau + $index,
        'created_at' => now(),
        'updated_at' => now()
    ]);
    echo "  + Thêm câu {$ch->MaCH}\n";
}

echo "\n✅ Đã thêm {$cauHoi->count()} câu hỏi vào đề thi $maDe\n";
echo "Tổng số câu hiện tại: " . DB::table('DETHI_CAUHOI')->where('MaDe', $maDe)->count() . "\n";
