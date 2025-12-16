<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== SỬA ĐỀ THI DE007 ===\n\n";

// Xóa các câu hỏi cũ (nếu có)
$deleted = DB::table('DETHI_CAUHOI')->where('MaDe', 'DE007')->delete();
echo "Đã xóa {$deleted} câu hỏi cũ\n";

// Lấy 5 câu hỏi ngẫu nhiên
$cauHoi = DB::table('CauHoi')
    ->inRandomOrder()
    ->limit(5)
    ->get(['MaCH']);

echo "Đang thêm {$cauHoi->count()} câu hỏi mới:\n";

foreach ($cauHoi as $index => $ch) {
    DB::table('DETHI_CAUHOI')->insert([
        'MaDe' => 'DE007',
        'MaCH' => $ch->MaCH,
        'ThuTu' => $index + 1,
        'created_at' => now(),
        'updated_at' => now()
    ]);
    echo "  + Câu {$ch->MaCH}\n";
}

// Kiểm tra lại
$count = DB::table('DETHI_CAUHOI')->where('MaDe', 'DE007')->count();
echo "\n✅ Hoàn tất! DE007 hiện có {$count} câu hỏi\n";
