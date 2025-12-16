<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== FIX TẤT CẢ ĐỀ THI BỊ LỖI ===\n\n";

// Tìm tất cả đề thi không có câu hỏi
$allDeThiRaw = DB::select("
    SELECT DeThi.MaDe, DeThi.TenDe, DeThi.SoLuongCauHoi, 
           COUNT(DETHI_CAUHOI.MaCH) as SoCauHieuCo
    FROM DeThi
    LEFT JOIN DETHI_CAUHOI ON DeThi.MaDe = DETHI_CAUHOI.MaDe
    GROUP BY DeThi.MaDe, DeThi.TenDe, DeThi.SoLuongCauHoi
    HAVING SoCauHieuCo = 0
");
$deThiList = collect($allDeThiRaw);

if ($deThiList->isEmpty()) {
    echo "✅ Không có đề thi nào bị lỗi!\n";
    exit;
}

echo "Tìm thấy {$deThiList->count()} đề thi bị lỗi:\n\n";

foreach ($deThiList as $deThi) {
    echo "Đang fix đề thi: {$deThi->MaDe} - {$deThi->TenDe}\n";
    echo "  Số câu cần: {$deThi->SoLuongCauHoi}\n";
    
    $cauHoi = DB::table('CauHoi')
        ->inRandomOrder()
        ->limit($deThi->SoLuongCauHoi)
        ->get(['MaCH']);
    
    foreach ($cauHoi as $index => $ch) {
        DB::table('DETHI_CAUHOI')->insert([
            'MaDe' => $deThi->MaDe,
            'MaCH' => $ch->MaCH,
            'ThuTu' => $index + 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
    
    echo "  ✅ Đã thêm {$cauHoi->count()} câu hỏi\n\n";
}

echo "=== HOÀN TẤT ===\n";
