<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DANH SÁCH ĐỀ THI ===\n\n";

$exams = DB::table('dethi')
    ->orderBy('created_at', 'desc')
    ->limit(10)
    ->get();

if ($exams->isEmpty()) {
    echo "❌ Chưa có đề thi nào!\n";
} else {
    echo "Tổng số đề thi: " . DB::table('dethi')->count() . "\n\n";
    
    foreach ($exams as $index => $exam) {
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "📋 ĐỀ THI #" . ($index + 1) . "\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "🆔 Mã đề:        " . $exam->MaDe . "\n";
        echo "📝 Tên đề:       " . $exam->TenDe . "\n";
        echo "📚 Chủ đề:       " . ($exam->ChuDe ?? 'N/A') . "\n";
        echo "👨‍🏫 Giáo viên:   " . $exam->MaGV . "\n";
        echo "⏱️  Thời gian:    " . $exam->ThoiGianLamBai . " phút\n";
        echo "❓ Số câu hỏi:   " . $exam->SoLuongCauHoi . "\n";
        
        // Đếm số câu hỏi thực tế
        $questionCount = DB::table('dethi_cauhoi')
            ->where('MaDe', $exam->MaDe)
            ->count();
        echo "✅ Đã liên kết:  " . $questionCount . " câu hỏi\n";
        
        echo "📅 Ngày tạo:     " . $exam->NgayTao . "\n";
        echo "🔘 Trạng thái:   " . ($exam->TrangThai == 1 ? '🟢 Kích hoạt' : '🔴 Vô hiệu') . "\n";
        
        if ($exam->MoTa) {
            echo "📄 Mô tả:        " . $exam->MoTa . "\n";
        }
        
        echo "\n";
    }
    
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    
    // Xem chi tiết đề thi mới nhất
    $latestExam = $exams->first();
    echo "📌 CHI TIẾT ĐỀ THI MỚI NHẤT: " . $latestExam->TenDe . "\n\n";
    
    $questions = DB::table('dethi_cauhoi')
        ->join('cauhoi', 'dethi_cauhoi.MaCH', '=', 'cauhoi.MaCH')
        ->where('dethi_cauhoi.MaDe', $latestExam->MaDe)
        ->orderBy('dethi_cauhoi.ThuTu')
        ->select('dethi_cauhoi.*', 'cauhoi.NoiDung', 'cauhoi.DapAnDung')
        ->get();
    
    if ($questions->isEmpty()) {
        echo "⚠️  Chưa có câu hỏi nào!\n";
    } else {
        foreach ($questions as $q) {
            echo "Câu " . $q->ThuTu . ": " . substr($q->NoiDung, 0, 80) . "...\n";
            echo "        Đáp án: " . $q->DapAnDung . "\n\n";
        }
    }
}

echo "\n🎯 Để xem trong web, vào menu:\n";
echo "   - Giáo viên: 'Quản lý đề thi' hoặc 'Tạo đề thi'\n";
echo "   - Học sinh: 'Danh sách đề thi' hoặc 'Làm bài thi'\n";
