<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DeThi;
use App\Models\GiaoVien;
use App\Models\HocSinh;
use App\Models\BaiLam;
use App\Models\KetQua;
use App\Models\CauHoi;
use Carbon\Carbon;

class DeThiVaBaiLamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy giáo viên
        $giaoVien = GiaoVien::first();
        
        if (!$giaoVien) {
            $this->command->error('Không tìm thấy giáo viên. Vui lòng chạy TestUserSeeder trước!');
            return;
        }

        // Tạo đề thi mẫu
        $deThi = DeThi::firstOrCreate(
            ['MaDe' => 'DE001'],
            [
                'TenDe' => 'Đề thi thử Tin học THPT Quốc gia 2025',
                'ThoiGianLamBai' => 50,
                'NgayTao' => Carbon::now(),
                'SoLuongCauHoi' => 10,
                'MaGV' => $giaoVien->MaGV,
                'MoTa' => 'Đề thi thử bao gồm các chuyên đề: Hệ điều hành, Mạng máy tính, Cơ sở dữ liệu, Lập trình',
                'TrangThai' => true
            ]
        );

        // Lấy 10 câu hỏi đầu tiên
        $cauHois = CauHoi::limit(10)->get();
        
        if ($cauHois->count() < 10) {
            $this->command->error('Không đủ câu hỏi. Vui lòng chạy CauHoiSeeder trước!');
            return;
        }

        // Gán câu hỏi vào đề thi
        foreach ($cauHois as $index => $cauHoi) {
            \DB::table('DETHI_CAUHOI')->updateOrInsert(
                [
                    'MaDe' => $deThi->MaDe,
                    'MaCH' => $cauHoi->MaCH
                ],
                [
                    'ThuTu' => $index + 1,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]
            );
        }

        $this->command->info('Đã tạo đề thi ' . $deThi->MaDe . ' với 10 câu hỏi!');

        // Tạo bài làm mẫu cho học sinh
        $hocSinh = HocSinh::first();
        
        if (!$hocSinh) {
            $this->command->warn('Không tìm thấy học sinh. Bỏ qua tạo bài làm mẫu.');
            return;
        }

        // Tạo 3 bài làm mẫu với kết quả khác nhau
        $dsBaiLam = [
            [
                'MaBaiLam' => 'BL001',
                'Diem' => 7.0,
                'SoCauDung' => 7,
                'DSCauTraLoi' => $this->taoTraLoiNgauNhien($cauHois, 7)
            ],
            [
                'MaBaiLam' => 'BL002',
                'Diem' => 8.5,
                'SoCauDung' => 8,
                'DSCauTraLoi' => $this->taoTraLoiNgauNhien($cauHois, 8)
            ],
            [
                'MaBaiLam' => 'BL003',
                'Diem' => 6.0,
                'SoCauDung' => 6,
                'DSCauTraLoi' => $this->taoTraLoiNgauNhien($cauHois, 6)
            ]
        ];

        foreach ($dsBaiLam as $index => $bailam) {
            $thoiGianBatDau = Carbon::now()->subDays(3 - $index)->subMinutes(50);
            $thoiGianNop = Carbon::now()->subDays(3 - $index);

            // Tạo bài làm
            $bl = BaiLam::firstOrCreate(
                ['MaBaiLam' => $bailam['MaBaiLam']],
                [
                    'DSCauTraLoi' => $bailam['DSCauTraLoi'],
                    'Diem' => $bailam['Diem'],
                    'ThoiGianBatDau' => $thoiGianBatDau,
                    'ThoiGianNop' => $thoiGianNop,
                    'TrangThai' => 'ChamDiem',
                    'SoLanViPham' => 0,
                    'MaHS' => $hocSinh->MaHS,
                    'MaDe' => $deThi->MaDe
                ]
            );

            // Tạo kết quả
            KetQua::firstOrCreate(
                ['MaKQ' => 'KQ' . str_pad($index + 1, 3, '0', STR_PAD_LEFT)],
                [
                    'Diem' => $bailam['Diem'],
                    'SoCauDung' => $bailam['SoCauDung'],
                    'SoCauSai' => 10 - $bailam['SoCauDung'],
                    'SoCauKhongLam' => 0,
                    'ThoiGianHoanThanh' => $thoiGianNop,
                    'MaHS' => $hocSinh->MaHS,
                    'MaDe' => $deThi->MaDe,
                    'MaBaiLam' => $bl->MaBaiLam
                ]
            );
        }

        $this->command->info('Đã tạo ' . count($dsBaiLam) . ' bài làm mẫu cho học sinh ' . $hocSinh->MaHS . '!');
    }

    /**
     * Tạo danh sách trả lời ngẫu nhiên
     */
    private function taoTraLoiNgauNhien($cauHois, $soCauDung)
    {
        $dsCauTraLoi = [];
        $dapAn = ['A', 'B', 'C', 'D'];
        
        foreach ($cauHois as $index => $cauHoi) {
            if ($index < $soCauDung) {
                // Trả lời đúng
                $traLoi = $cauHoi->DapAn;
            } else {
                // Trả lời sai (chọn đáp án khác với đáp án đúng)
                $dapAnSai = array_values(array_diff($dapAn, [$cauHoi->DapAn]));
                $traLoi = $dapAnSai[array_rand($dapAnSai)];
            }
            
            $dsCauTraLoi[] = [
                'MaCH' => $cauHoi->MaCH,
                'TraLoi' => $traLoi
            ];
        }
        
        return json_encode($dsCauTraLoi);
    }
}
