<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\DeThi;
use App\Models\CauHoi;
use App\Models\BaiLam;
use App\Models\KetQua;
use Carbon\Carbon;

class BaiThiController extends Controller
{
    /**
     * Constructor - Yêu cầu authentication
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Nộp bài thi và chấm điểm tự động (UR-02.2 & UR-02.3)
     * * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function nopBai(Request $request)
    {
        // 1. VALIDATE DỮ LIỆU ĐẦU VÀO
        $validator = Validator::make($request->all(), [
            'MaDe' => 'required|string|exists:DeThi,MaDe',
            'MaHS' => 'required|string|exists:HocSinh,MaHS',
            'CauTraLoi' => 'required|array',
            'CauTraLoi.*.MaCH' => 'required|string|exists:CauHoi,MaCH',
            'CauTraLoi.*.DapAnChon' => 'required|string|in:A,B,C,D',
            'ThoiGianBatDau' => 'sometimes|date',
        ], [
            'MaDe.required' => 'Mã đề thi không được để trống',
            'MaDe.exists' => 'Đề thi không tồn tại',
            'MaHS.required' => 'Mã học sinh không được để trống',
            'MaHS.exists' => 'Học sinh không tồn tại',
            'CauTraLoi.required' => 'Danh sách câu trả lời không được để trống',
            'CauTraLoi.array' => 'Danh sách câu trả lời phải là mảng',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Bắt đầu transaction
            DB::beginTransaction();

            $user = $request->user();
            $maDe = $request->MaDe;
            $maHS = $request->MaHS;
            $cauTraLoi = $request->CauTraLoi;

            // 2. KIỂM TRA QUYỀN - Chỉ học sinh mới được nộp bài
            if ($user->Role !== 'hocsinh') {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ học sinh mới được phép nộp bài thi'
                ], 403);
            }

            // Lấy thông tin học sinh từ MaHS
            $hocSinh = \App\Models\HocSinh::find($maHS);
            if (!$hocSinh) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy thông tin học sinh'
                ], 404);
            }

            // Kiểm tra học sinh có phải là người đang đăng nhập không
            if ($user->hocSinh && $user->hocSinh->MaHS !== $maHS) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền nộp bài cho học sinh khác'
                ], 403);
            }

            // 3. LẤY THÔNG TIN ĐỀ THI VÀ CÂU HỎI
            $deThi = DeThi::with('cauHoi')->find($maDe);
            
            if (!$deThi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy đề thi'
                ], 404);
            }

            // 4. LẤY ĐÁP ÁN ĐÚNG TỪ DATABASE
            $dapAnDung = [];
            foreach ($deThi->cauHoi as $cauHoi) {
                $dapAnDung[$cauHoi->MaCH] = $cauHoi->DapAn;
            }

            // 5. CHẤM ĐIỂM TỰ ĐỘNG
            $tongSoCau = count($dapAnDung);
            $soCauDung = 0;
            $soCauSai = 0;
            $soCauKhongLam = 0;
            
            // Tạo mảng để lưu chi tiết câu trả lời
            $chiTietCauTraLoi = [];
            $danhSachCauDaLam = [];

            foreach ($cauTraLoi as $traLoi) {
                $maCH = $traLoi['MaCH'];
                $dapAnChon = $traLoi['DapAnChon'];
                
                $chiTietCauTraLoi[] = [
                    'MaCH' => $maCH,
                    'DapAnChon' => $dapAnChon,
                    'DapAnDung' => $dapAnDung[$maCH] ?? null,
                    'KetQua' => ($dapAnDung[$maCH] ?? null) === $dapAnChon ? 'Dung' : 'Sai'
                ];

                // So sánh với đáp án đúng
                if (isset($dapAnDung[$maCH]) && $dapAnDung[$maCH] === $dapAnChon) {
                    $soCauDung++;
                } else {
                    $soCauSai++;
                }

                $danhSachCauDaLam[] = $maCH;
            }

            // Tính số câu không làm
            $soCauKhongLam = $tongSoCau - count($danhSachCauDaLam);

            // [SỬA] Tính điểm (thang điểm 10) - Ép kiểu float rõ ràng theo Class KetQua
            $diem = $tongSoCau > 0 ? (float)round(($soCauDung / $tongSoCau) * 10, 2) : 0.0;

            // 6. TẠO MÃ BÀI LÀM VÀ MÃ KẾT QUẢ
            $maBaiLam = $this->generateMaBaiLam();
            $maKQ = $this->generateMaKetQua();

            // 7. LƯU VÀO BẢNG BaiLam
            $thoiGianBatDau = $request->ThoiGianBatDau 
                ? Carbon::parse($request->ThoiGianBatDau) 
                : Carbon::now()->subMinutes($deThi->ThoiGianLamBai);
            
            $baiLam = BaiLam::create([
                'MaBaiLam' => $maBaiLam,
                'DSCauTraLoi' => json_encode($chiTietCauTraLoi),
                'Diem' => $diem,
                'ThoiGianBatDau' => $thoiGianBatDau,
                'ThoiGianNop' => Carbon::now(),
                'TrangThai' => 'Đã nộp', // [SỬA] Đổi thành "Đã nộp" cho khớp Class BaiLam
                'MaHS' => $hocSinh->MaHS,
                'MaDe' => $maDe,
            ]);

            // 8. LƯU VÀO BẢNG KetQua
            $ketQua = KetQua::create([
                'MaKQ' => $maKQ,
                'Diem' => $diem, // Kiểu float
                'SoCauDung' => $soCauDung,
                'SoCauSai' => $soCauSai,
                'SoCauKhongLam' => $soCauKhongLam,
                'ThoiGianHoanThanh' => Carbon::now(),
                'MaHS' => $hocSinh->MaHS,
                'MaDe' => $maDe,
                'MaBaiLam' => $maBaiLam,
            ]);

            // Commit transaction
            DB::commit();

            // 9. TRẢ VỀ KẾT QUẢ
            return response()->json([
                'success' => true,
                'message' => 'Nộp bài thành công',
                'data' => [
                    'MaBaiLam' => $maBaiLam,
                    'MaKQ' => $maKQ,
                    'Diem' => $diem, // Trả về float (ví dụ 4.0)
                    'TrangThai' => 'Đã nộp', // [THÊM] Trả về field này để khớp báo cáo
                    'SoCauDung' => $soCauDung,
                    'SoCauSai' => $soCauSai,
                    'SoCauKhongLam' => $soCauKhongLam,
                    'TongSoCau' => $tongSoCau,
                    'ThoiGianNop' => $baiLam->ThoiGianNop,
                    'TenDe' => $deThi->TenDe,
                    'HocSinh' => [
                        'MaHS' => $hocSinh->MaHS,
                        'HoTen' => $hocSinh->HoTen,
                    ],
                    // Có thể ẩn chi tiết nếu chưa muốn show ngay
                     'ChiTiet' => $chiTietCauTraLoi 
                ]
            ], 201);

        } catch (\Exception $e) {
            // Rollback nếu có lỗi
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi nộp bài',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * [MỚI] API Lưu nháp bài làm (UR-05.2)
     * Dùng cho tính năng tự động lưu mỗi 1 phút
     */
    public function luuBaiLam(Request $request) 
    {
         // Validate cơ bản
         $validator = Validator::make($request->all(), [
            'MaDe' => 'required|string',
            'MaHS' => 'required|string',
            'CauTraLoi' => 'required|array',
        ]);

        if ($validator->fails()) return response()->json(['success' => false], 422);

        try {
            // Tìm bài làm đang làm dở (nếu có) hoặc tạo mới
            // Logic ở đây: Update field DSCauTraLoi, không tính điểm, trạng thái vẫn là "Đang làm"
            
            // Code demo (Bạn cần tùy chỉnh logic update DB của bạn ở đây)
            // ...
            
            return response()->json([
                'success' => true,
                'message' => 'Đã lưu nháp'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false], 500);
        }
    }

    /**
     * Sinh mã bài làm tự động
     * Format: BL + timestamp
     */
    private function generateMaBaiLam()
    {
        do {
            $ma = 'BL' . str_pad(rand(1, 99999999), 8, '0', STR_PAD_LEFT);
        } while (BaiLam::where('MaBaiLam', $ma)->exists());
        
        return $ma;
    }

    /**
     * Sinh mã kết quả tự động
     * Format: KQ + timestamp
     */
    private function generateMaKetQua()
    {
        do {
            $ma = 'KQ' . str_pad(rand(1, 99999999), 8, '0', STR_PAD_LEFT);
        } while (KetQua::where('MaKQ', $ma)->exists());
        
        return $ma;
    }

    /**
     * Lấy kết quả bài thi của học sinh
     */
    public function getKetQua(Request $request, $maBaiLam)
    {
        $user = $request->user();
        
        $baiLam = BaiLam::with(['deThi', 'hocSinh', 'ketQua'])
            ->where('MaBaiLam', $maBaiLam)
            ->first();

        if (!$baiLam) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy bài làm'
            ], 404);
        }

        // Kiểm tra quyền
        if ($user->Role === 'hocsinh' && $baiLam->MaHS !== $user->hocSinh->MaHS) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xem bài làm này'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'BaiLam' => $baiLam,
                'Diem' => (float)$baiLam->Diem, // Ép kiểu float khi hiển thị
                'ChiTietCauTraLoi' => json_decode($baiLam->DSCauTraLoi)
            ]
        ], 200);
    }
}