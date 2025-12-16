<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== KIỂM TRA ĐỀ THI MỚI NHẤT ===\n\n";

$deThiList = DB::table('DeThi')
    ->select('MaDe', 'TenDe', 'SoLuongCauHoi', 'NgayTao')
    ->orderBy('NgayTao', 'desc')
    ->limit(5)
    ->get();

foreach ($deThiList as $deThi) {
    $count = DB::table('DETHI_CAUHOI')
        ->where('MaDe', $deThi->MaDe)
        ->count();
    
    $status = $count == $deThi->SoLuongCauHoi ? '✅' : '❌';
    
    echo sprintf(
        "%s | %s | %s\n   Khai báo: %d câu | Thực tế: %d câu %s\n\n",
        $deThi->MaDe,
        $deThi->TenDe,
        $deThi->NgayTao,
        $deThi->SoLuongCauHoi,
        $count,
        $status
    );
}
