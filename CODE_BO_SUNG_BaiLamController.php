<?php
/**
 * ==============================================================================
 * API BỔ SUNG CHO MODULE XEM LẠI BÀI CHI TIẾT
 * ==============================================================================
 * 
 * File: app/Http/Controllers/BaiLamController.php
 * 
 * Chức năng:
 * - Xem chi tiết bài làm (câu hỏi + đáp án đã chọn + đáp án đúng)
 * - Lịch sử làm bài của học sinh
 * - Thống kê cá nhân
 * 
 * ==============================================================================
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BaiLam;
use App\Models\ChiTietBaiLam;
use App\Models\CauHoi;
use App\Models\DeThi;
use Illuminate\Support\Facades\DB;

class BaiLamController extends Controller
{
    /**
     * Xem chi tiết bài làm (cho học sinh sau khi nộp bài)
     * 
     * GET /api/bai-lam/{maBaiLam}/chi-tiet
     * 
     * Response: {
     *   MaBaiLam, MaDe, TenDe, Diem, ThoiGianLamBai,
     *   DanhSachCauHoi: [
     *     { MaCauHoi, NoiDung, DapAnA, DapAnB, DapAnC, DapAnD,
     *       DapAnDung, DapAnChon, GiaiThich }
     *   ]
     * }
     */
    public function xemChiTiet(Request $request, $maBaiLam)
    {
        try {
            $user = $request->user();
            
            // Lấy bài làm
            $baiLam = BaiLam::where('MaBaiLam', $maBaiLam)->first();
            
            if (!$baiLam) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy bài làm'
                ], 404);
            }
            
            // Kiểm tra quyền (học sinh chỉ xem bài của mình)
            if ($user->VaiTro === 'hocsinh' && $baiLam->MaHS !== $user->MaNguoiDung) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền xem bài làm này'
                ], 403);
            }
            
            // Lấy thông tin đề thi
            $deThi = DeThi::where('MaDe', $baiLam->MaDe)->first();
            
            // Lấy chi tiết câu trả lời
            $chiTietBaiLam = ChiTietBaiLam::where('MaBaiLam', $maBaiLam)->get();
            
            // Lấy danh sách câu hỏi với đáp án đúng
            $danhSachCauHoi = [];
            
            foreach ($chiTietBaiLam as $ct) {
                $cauHoi = CauHoi::where('MaCauHoi', $ct->MaCauHoi)->first();
                
                if ($cauHoi) {
                    $danhSachCauHoi[] = [
                        'MaCauHoi' => $cauHoi->MaCauHoi,
                        'NoiDung' => $cauHoi->NoiDung,
                        'DapAnA' => $cauHoi->DapAnA,
                        'DapAnB' => $cauHoi->DapAnB,
                        'DapAnC' => $cauHoi->DapAnC,
                        'DapAnD' => $cauHoi->DapAnD,
                        'DapAnDung' => $cauHoi->DapAnDung,
                        'DapAnChon' => $ct->DapAnChon,
                        'GiaiThich' => $cauHoi->GiaiThich,
                        'IsDung' => ($ct->DapAnChon === $cauHoi->DapAnDung)
                    ];
                }
            }
            
            return response()->json([
                'success' => true,
                'MaBaiLam' => $baiLam->MaBaiLam,
                'MaDe' => $baiLam->MaDe,
                'TenDe' => $deThi->TenDe,
                'Diem' => $baiLam->Diem,
                'ThoiGianBatDau' => $baiLam->ThoiGianBatDau,
                'ThoiGianNop' => $baiLam->ThoiGianNop,
                'ThoiGianLamBai' => $baiLam->ThoiGianLamBai,
                'SoCauDung' => $baiLam->SoCauDung,
                'SoCauSai' => $baiLam->SoCauSai,
                'DanhSachCauHoi' => $danhSachCauHoi
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Lấy lịch sử làm bài của học sinh
     * 
     * GET /api/bai-lam/lich-su
     * 
     * Response: {
     *   data: [
     *     { MaBaiLam, MaDe, TenDe, Diem, ThoiGianNop, SoCauDung, SoCauSai }
     *   ],
     *   thong_ke: {
     *     TongSoBaiLam, DiemTrungBinh, DiemCaoNhat, DiemThapNhat
     *   }
     * }
     */
    public function lichSu(Request $request)
    {
        try {
            $user = $request->user();
            
            // Lấy danh sách bài làm
            $baiLams = BaiLam::where('MaHS', $user->MaNguoiDung)
                            ->where('TrangThai', 'daHoanThanh')
                            ->orderBy('ThoiGianNop', 'desc')
                            ->get();
            
            // Join với bảng DeThi để lấy tên đề
            $ketQua = [];
            foreach ($baiLams as $bl) {
                $deThi = DeThi::where('MaDe', $bl->MaDe)->first();
                
                $ketQua[] = [
                    'MaBaiLam' => $bl->MaBaiLam,
                    'MaDe' => $bl->MaDe,
                    'TenDe' => $deThi ? $deThi->TenDe : 'N/A',
                    'Diem' => $bl->Diem,
                    'ThoiGianNop' => $bl->ThoiGianNop,
                    'ThoiGianLamBai' => $bl->ThoiGianLamBai,
                    'SoCauDung' => $bl->SoCauDung,
                    'SoCauSai' => $bl->SoCauSai
                ];
            }
            
            // Tính thống kê
            $tongSo = $baiLams->count();
            $diemTrungBinh = $tongSo > 0 ? round($baiLams->avg('Diem'), 2) : 0;
            $diemCaoNhat = $tongSo > 0 ? $baiLams->max('Diem') : 0;
            $diemThapNhat = $tongSo > 0 ? $baiLams->min('Diem') : 0;
            
            return response()->json([
                'success' => true,
                'data' => $ketQua,
                'thong_ke' => [
                    'TongSoBaiLam' => $tongSo,
                    'DiemTrungBinh' => $diemTrungBinh,
                    'DiemCaoNhat' => $diemCaoNhat,
                    'DiemThapNhat' => $diemThapNhat
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Thống kê chi tiết cá nhân (cho biểu đồ)
     * 
     * GET /api/bai-lam/thong-ke-ca-nhan
     * 
     * Response: {
     *   bieu_do_diem: [{ ngay, diem }, ...],
     *   phan_bo_diem: { '0-5': 2, '5-7': 5, '7-8': 3, '8-9': 4, '9-10': 6 },
     *   xu_huong: 'tang' | 'giam' | 'on_dinh'
     * }
     */
    public function thongKeCaNhan(Request $request)
    {
        try {
            $user = $request->user();
            
            // Lấy bài làm 30 ngày gần nhất
            $baiLams = BaiLam::where('MaHS', $user->MaNguoiDung)
                            ->where('TrangThai', 'daHoanThanh')
                            ->where('ThoiGianNop', '>=', now()->subDays(30))
                            ->orderBy('ThoiGianNop', 'asc')
                            ->get();
            
            // Biểu đồ điểm theo thời gian
            $bieuDoDiem = [];
            foreach ($baiLams as $bl) {
                $bieuDoDiem[] = [
                    'ngay' => date('Y-m-d', strtotime($bl->ThoiGianNop)),
                    'diem' => $bl->Diem
                ];
            }
            
            // Phân bố điểm
            $phanBoDiem = [
                '0-5' => 0,
                '5-7' => 0,
                '7-8' => 0,
                '8-9' => 0,
                '9-10' => 0
            ];
            
            foreach ($baiLams as $bl) {
                if ($bl->Diem < 5) {
                    $phanBoDiem['0-5']++;
                } elseif ($bl->Diem < 7) {
                    $phanBoDiem['5-7']++;
                } elseif ($bl->Diem < 8) {
                    $phanBoDiem['7-8']++;
                } elseif ($bl->Diem < 9) {
                    $phanBoDiem['8-9']++;
                } else {
                    $phanBoDiem['9-10']++;
                }
            }
            
            // Xu hướng (so sánh 5 bài gần nhất với 5 bài trước đó)
            $xuHuong = 'on_dinh';
            if ($baiLams->count() >= 10) {
                $recent5 = $baiLams->slice(-5)->avg('Diem');
                $previous5 = $baiLams->slice(-10, 5)->avg('Diem');
                
                if ($recent5 > $previous5 + 0.5) {
                    $xuHuong = 'tang';
                } elseif ($recent5 < $previous5 - 0.5) {
                    $xuHuong = 'giam';
                }
            }
            
            return response()->json([
                'success' => true,
                'bieu_do_diem' => $bieuDoDiem,
                'phan_bo_diem' => $phanBoDiem,
                'xu_huong' => $xuHuong,
                'tong_so_bai' => $baiLams->count(),
                'diem_trung_binh' => round($baiLams->avg('Diem'), 2)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Xóa bài làm (chỉ admin/giáo viên)
     * 
     * DELETE /api/bai-lam/{maBaiLam}
     */
    public function xoa(Request $request, $maBaiLam)
    {
        DB::beginTransaction();
        
        try {
            $user = $request->user();
            
            // Kiểm tra quyền
            if (!in_array($user->VaiTro, ['admin', 'giaovien'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền xóa bài làm'
                ], 403);
            }
            
            $baiLam = BaiLam::where('MaBaiLam', $maBaiLam)->first();
            
            if (!$baiLam) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy bài làm'
                ], 404);
            }
            
            // Xóa chi tiết bài làm
            ChiTietBaiLam::where('MaBaiLam', $maBaiLam)->delete();
            
            // Xóa bài làm
            $baiLam->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Xóa bài làm thành công'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Export kết quả ra PDF (optional)
     * 
     * GET /api/bai-lam/{maBaiLam}/export-pdf
     * 
     * Yêu cầu: composer require barryvdh/laravel-dompdf
     */
    public function exportPDF($maBaiLam)
    {
        try {
            // Lấy chi tiết bài làm
            $baiLam = BaiLam::where('MaBaiLam', $maBaiLam)->first();
            
            if (!$baiLam) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy bài làm'
                ], 404);
            }
            
            // TODO: Implement PDF generation
            // $pdf = PDF::loadView('pdf.bai-lam', compact('baiLam'));
            // return $pdf->download('bai-lam-' . $maBaiLam . '.pdf');
            
            return response()->json([
                'success' => false,
                'message' => 'Tính năng xuất PDF đang được phát triển'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
}
