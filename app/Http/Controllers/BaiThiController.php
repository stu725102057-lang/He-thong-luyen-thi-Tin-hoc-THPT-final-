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
        \Log::info('=== NỘP BÀI THI START ===');
        \Log::info('Request data:', $request->all());
        
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
            \Log::error('Validation failed:', $validator->errors()->toArray());
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
            \Log::info('User:', ['MaTK' => $user->MaTK, 'Role' => $user->Role]);
            
            $maDe = $request->MaDe;
            $maHS = $request->MaHS;
            $cauTraLoi = $request->CauTraLoi;

            // 2. KIỂM TRA QUYỀN - Chỉ học sinh mới được nộp bài
            if ($user->Role !== 'hocsinh') {
                \Log::warning('User role is not hocsinh:', ['Role' => $user->Role]);
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ học sinh mới được phép nộp bài thi'
                ], 403);
            }

            // Lấy thông tin học sinh từ MaHS
            $hocSinh = \App\Models\HocSinh::find($maHS);
            if (!$hocSinh) {
                \Log::error('Student not found:', ['MaHS' => $maHS]);
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy thông tin học sinh'
                ], 404);
            }
            
            \Log::info('Found student:', ['MaHS' => $hocSinh->MaHS, 'HoTen' => $hocSinh->HoTen]);

            // Kiểm tra học sinh có phải là người đang đăng nhập không
            if ($user->hocSinh && $user->hocSinh->MaHS !== $maHS) {
                \Log::warning('User trying to submit for different student');
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
                'TrangThai' => 'DaNop', // [SỬA] Phải khớp với ENUM: 'DangLam', 'DaNop', 'ChamDiem'
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
            
            \Log::error('=== NỘP BÀI THI ERROR ===');
            \Log::error('Error message: ' . $e->getMessage());
            \Log::error('Error file: ' . $e->getFile());
            \Log::error('Error line: ' . $e->getLine());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi nộp bài: ' . $e->getMessage(),
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * [MỚI] API Lưu nháp bài làm (UR-05.2)
     * Dùng cho tính năng tự động lưu mỗi 1 phút
     */
    public function luuBaiLam(Request $request) 
    {
        \Log::info('=== LƯU NHÁP BÀI LÀM START ===');
        \Log::info('Request data:', $request->all());
        
        // 1. VALIDATE DỮ LIỆU ĐẦU VÀO
        $validator = Validator::make($request->all(), [
            'MaBaiLam' => 'required|string|exists:BaiLam,MaBaiLam',
            'CauTraLoi' => 'required|array',
        ], [
            'MaBaiLam.required' => 'Mã bài làm không được để trống',
            'MaBaiLam.exists' => 'Bài làm không tồn tại',
            'CauTraLoi.required' => 'Danh sách câu trả lời không được để trống',
            'CauTraLoi.array' => 'Danh sách câu trả lời phải là mảng',
        ]);

        if ($validator->fails()) {
            \Log::error('Validation failed:', $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = $request->user();
            $maBaiLam = $request->MaBaiLam;
            $cauTraLoi = $request->CauTraLoi;

            // 2. TÌM BÀI LÀM ĐANG LÀM DỞ
            $baiLam = BaiLam::where('MaBaiLam', $maBaiLam)
                ->where('TrangThai', 'DangLam') // Chỉ lưu nếu đang làm
                ->first();

            if (!$baiLam) {
                \Log::error('BaiLam not found or already submitted:', ['MaBaiLam' => $maBaiLam]);
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy bài làm hoặc bài làm đã nộp'
                ], 404);
            }

            // 3. KIỂM TRA QUYỀN
            $hocSinh = \App\Models\HocSinh::where('MaTK', $user->MaTK)->first();
            
            if (!$hocSinh || $baiLam->MaHS !== $hocSinh->MaHS) {
                \Log::warning('User trying to save for different student');
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền lưu bài làm này'
                ], 403);
            }

            // 4. CẬP NHẬT DSCauTraLoi (JSON)
            // Format: [{"MaCH": "CH00000001", "TraLoi": "A"}, ...]
            $dsCauTraLoiMoi = [];
            foreach ($cauTraLoi as $maCH => $dapAn) {
                if (!empty($dapAn)) { // Chỉ lưu câu đã chọn
                    $dsCauTraLoiMoi[] = [
                        'MaCH' => $maCH,
                        'TraLoi' => $dapAn
                    ];
                }
            }

            // 5. LƯU VÀO DATABASE
            $baiLam->DSCauTraLoi = json_encode($dsCauTraLoiMoi);
            $baiLam->updated_at = now(); // Đánh dấu thời gian lưu gần nhất
            $baiLam->save();

            \Log::info('BaiLam saved successfully:', [
                'MaBaiLam' => $maBaiLam,
                'SoCauDaLam' => count($dsCauTraLoiMoi)
            ]);

            // 6. TRẢ VỀ KẾT QUẢ
            return response()->json([
                'success' => true,
                'message' => 'Đã lưu nháp thành công',
                'data' => [
                    'MaBaiLam' => $maBaiLam,
                    'SoCauDaLam' => count($dsCauTraLoiMoi),
                    'ThoiGianLuu' => $baiLam->updated_at->toDateTimeString()
                ]
            ], 200);

        } catch (\Exception $e) {
            \Log::error('=== LƯU NHÁP ERROR ===');
            \Log::error('Error message: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lưu nháp: ' . $e->getMessage()
            ], 500);
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

    /**
     * Ghi nhận gian lận (UR-05.1 - Cheating Detection)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ghiNhanGianLan(Request $request)
    {
        // 1. VALIDATE DỮ LIỆU ĐẦU VÀO
        $validator = Validator::make($request->all(), [
            'MaDe' => 'required|string',
            'MaHS' => 'required|string',
        ], [
            'MaDe.required' => 'Mã đề thi không được để trống',
            'MaDe.string' => 'Mã đề thi phải là chuỗi',
            'MaHS.required' => 'Mã học sinh không được để trống',
            'MaHS.string' => 'Mã học sinh phải là chuỗi',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // 2. TÌM BÀI LÀM DỰA TRÊN MaDe VÀ MaHS
            $baiLam = BaiLam::where('MaDe', $request->MaDe)
                ->where('MaHS', $request->MaHS)
                ->where('TrangThai', 'DangLam') // Chỉ ghi nhận khi đang làm bài
                ->first();

            if (!$baiLam) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy bài làm đang thực hiện'
                ], 404);
            }

            // 3. TĂNG SỐ LẦN VI PHẠM
            $baiLam->SoLanViPham = ($baiLam->SoLanViPham ?? 0) + 1;
            $baiLam->save();

            // 4. TRẢ VỀ KẾT QUẢ
            return response()->json([
                'success' => true,
                'message' => 'Đã ghi nhận hành vi gian lận',
                'data' => [
                    'MaBaiLam' => $baiLam->MaBaiLam,
                    'MaDe' => $baiLam->MaDe,
                    'MaHS' => $baiLam->MaHS,
                    'SoLanViPham' => $baiLam->SoLanViPham,
                    'ThoiGianGhiNhan' => now()->toDateTimeString()
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi ghi nhận gian lận',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * UR-02.5: Xem lịch sử thi của học sinh
     */
    public function layLichSuThi(Request $request)
    {
        $user = $request->user();
        
        // FIXED: Nếu không phải học sinh, trả về empty array thay vì lỗi
        if ($user->Role !== 'hocsinh') {
            return response()->json([
                'success' => true,
                'message' => 'Lịch sử thi chỉ dành cho học sinh',
                'data' => []
            ], 200);
        }
        
        // Tìm thông tin học sinh dựa trên tài khoản đang đăng nhập
       // Lấy MaTK từ user đang đăng nhập (vì bảng TaiKhoan dùng MaTK làm khóa chính)
$hocSinh = \App\Models\HocSinh::where('MaTK', $user->MaTK)->first(); // Hoặc logic lấy MaHS của bạn

        if (!$hocSinh) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy thông tin học sinh'], 404);
        }

        // Lấy danh sách kết quả, sắp xếp mới nhất lên đầu
        $lichSu = KetQua::with('deThi') // Kèm thông tin đề thi
            ->where('MaHS', $hocSinh->MaHS)
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Thêm TongSoCau vào mỗi kết quả (SoCauDung + SoCauSai + SoCauKhongLam)
        $lichSu = $lichSu->map(function($item) {
            $item->TongSoCau = $item->SoCauDung + $item->SoCauSai + $item->SoCauKhongLam;
            return $item;
        });

        return response()->json([
            'success' => true,
            'message' => 'Lấy lịch sử thi thành công',
            'data' => $lichSu
        ]);
    }

    /**
     * Lấy chi tiết bài làm (UR-03.1 Enhanced)
     * @param string $maBaiLam
     * @return \Illuminate\Http\JsonResponse
     */
    public function chiTietBaiLam($maBaiLam)
    {
        try {
            $user = auth()->user();
            
            // Tìm bài làm
            $baiLam = BaiLam::with(['deThi'])->find($maBaiLam);
            
            if (!$baiLam) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy bài làm'
                ], 404);
            }
            
            // Kiểm tra quyền xem: chỉ học sinh chủ bài làm hoặc giáo viên
            $hocSinh = \App\Models\HocSinh::where('MaTK', $user->MaTK)->first();
            
            if ($user->Role === 'hocsinh' && $baiLam->MaHS !== $hocSinh->MaHS) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền xem bài làm này'
                ], 403);
            }
            
            // Lấy danh sách câu hỏi từ đề thi
            $danhSachCauHoi = DB::table('dethi_cauhoi as dc')
                ->join('cauhoi as ch', 'dc.MaCH', '=', 'ch.MaCH')
                ->where('dc.MaDe', $baiLam->MaDe)
                ->orderBy('dc.ThuTu')
                ->select(
                    'ch.MaCH',
                    'ch.NoiDung',
                    'ch.DapAnA',
                    'ch.DapAnB',
                    'ch.DapAnC',
                    'ch.DapAnD',
                    'ch.DapAn as DapAnDung',  // Đổi tên: DapAn -> DapAnDung
                    'ch.DoKho'
                )
                ->get();
            
            // Lấy đáp án đã chọn của học sinh từ DSCauTraLoi (JSON)
            $dsCauTraLoi = json_decode($baiLam->DSCauTraLoi, true) ?? [];
            $dapAnDaChon = [];
            foreach ($dsCauTraLoi as $traLoi) {
                // Hỗ trợ cả 2 format: 'DapAnChon' (từ nộp bài) và 'TraLoi' (từ lưu nháp)
                if (isset($traLoi['MaCH'])) {
                    $dapAnDaChon[$traLoi['MaCH']] = $traLoi['DapAnChon'] ?? $traLoi['TraLoi'] ?? null;
                }
            }
            
            // Ghép thông tin câu hỏi với đáp án đã chọn
            $chiTietCauHoi = $danhSachCauHoi->map(function($cauHoi) use ($dapAnDaChon) {
                $dapAnChon = $dapAnDaChon[$cauHoi->MaCH] ?? null;
                
                // Chuẩn hóa để so sánh (trim và uppercase)
                $dapAnChonNormalized = $dapAnChon ? strtoupper(trim($dapAnChon)) : null;
                $dapAnDungNormalized = $cauHoi->DapAnDung ? strtoupper(trim($cauHoi->DapAnDung)) : null;
                
                $isDung = $dapAnChonNormalized && $dapAnChonNormalized === $dapAnDungNormalized;
                
                \Log::info("So sánh đáp án", [
                    'MaCH' => $cauHoi->MaCH,
                    'DapAnChon' => $dapAnChon,
                    'DapAnChonNormalized' => $dapAnChonNormalized,
                    'DapAnDung' => $cauHoi->DapAnDung,
                    'DapAnDungNormalized' => $dapAnDungNormalized,
                    'IsDung' => $isDung
                ]);
                
                return [
                    'MaCH' => $cauHoi->MaCH,
                    'NoiDung' => $cauHoi->NoiDung,
                    'DapAnA' => $cauHoi->DapAnA,
                    'DapAnB' => $cauHoi->DapAnB,
                    'DapAnC' => $cauHoi->DapAnC,
                    'DapAnD' => $cauHoi->DapAnD,
                    'DapAnDung' => strtoupper(trim($cauHoi->DapAnDung ?? '')), // Chuẩn hóa
                    'DapAnChon' => $dapAnChon ? strtoupper(trim($dapAnChon)) : null, // Chuẩn hóa
                    'IsDung' => $isDung,
                    'GiaiThich' => null,  // Không có cột này trong DB
                    'DoKho' => $cauHoi->DoKho,
                    'ChuyenDe' => null  // Không có cột này trong DB
                ];
            });
            
            // Tính toán kết quả nếu chưa có trong bảng KetQua
            $ketQua = KetQua::where('MaBaiLam', $maBaiLam)->first();
            
            $tongSoCau = $danhSachCauHoi->count();
            $soCauDung = $chiTietCauHoi->where('IsDung', true)->count();
            $soCauSai = $tongSoCau - $soCauDung;
            $diem = $baiLam->Diem ?? ($tongSoCau > 0 ? round(($soCauDung / $tongSoCau) * 10, 2) : 0);
            
            $ketQuaData = [
                'Diem' => $diem,
                'TongSoCau' => $tongSoCau,
                'SoCauDung' => $soCauDung,
                'SoCauSai' => $soCauSai,
                'TiLeDung' => $tongSoCau > 0 ? round(($soCauDung / $tongSoCau) * 100, 2) : 0
            ];
            
            return response()->json([
                'success' => true,
                'message' => 'Lấy chi tiết bài làm thành công',
                'data' => [
                    'baiLam' => [
                        'MaBaiLam' => $baiLam->MaBaiLam,
                        'MaDe' => $baiLam->MaDe,
                        'TenDe' => $baiLam->deThi->TenDe ?? 'N/A',
                        'ThoiGianBatDau' => $baiLam->ThoiGianBatDau,
                        'ThoiGianNop' => $baiLam->ThoiGianNop,
                        'ThoiGianLamBai' => $baiLam->ThoiGianNop ? 
                            round((strtotime($baiLam->ThoiGianNop) - strtotime($baiLam->ThoiGianBatDau)) / 60, 2) : 0
                    ],
                    'ketQua' => $ketQuaData,
                    'cauHoi' => $chiTietCauHoi
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
     * Thống kê cá nhân học sinh (UR-03.2 Enhanced)
     * Cung cấp báo cáo, biểu đồ trực quan về lịch sử làm bài, 
     * điểm số qua các lần thi, và phân tích điểm mạnh/yếu theo từng chuyên đề
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function thongKeCanhan()
    {
        try {
            $user = auth()->user();
            
            \Log::info('=== THỐNG KÊ CÁ NHÂN START ===');
            \Log::info('User MaTK: ' . $user->MaTK);
            
            // Lấy thông tin học sinh
            $hocSinh = \App\Models\HocSinh::where('MaTK', $user->MaTK)->first();
            
            if (!$hocSinh) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy thông tin học sinh'
                ], 404);
            }
            
            \Log::info('HocSinh MaHS: ' . $hocSinh->MaHS);
            
            // Lấy tất cả kết quả thi
            $ketQuas = KetQua::where('MaHS', $hocSinh->MaHS)
                ->with('deThi')
                ->orderBy('created_at', 'asc')
                ->get();
            
            \Log::info('Số kết quả thi: ' . $ketQuas->count());
            
            if ($ketQuas->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Chưa có dữ liệu thống kê',
                    'data' => [
                        'thongTinChung' => [
                            'tongSoBaiLam' => 0,
                            'diemTrungBinh' => 0,
                            'diemCaoNhat' => 0,
                            'diemThapNhat' => 0,
                            'tiLeDungTrungBinh' => 0,
                            'tongSoCauDaLam' => 0,
                            'tongSoCauDung' => 0,
                            'tongSoCauSai' => 0
                        ],
                        'lichSuDiem' => [],
                        'tyLeDungSai' => ['dung' => 0, 'sai' => 0, 'khongLam' => 0],
                        'phanTichChuyenDe' => [],
                        'diemManhYeu' => [
                            'diemManh' => [],
                            'diemYeu' => [],
                            'khuyenNghi' => 'Hãy bắt đầu làm bài thi để có thống kê'
                        ],
                        'bienDoDiem' => []
                    ]
                ]);
            }
            
            // ============================================
            // 1. THỐNG KÊ TỔNG QUAN
            // ============================================
            
            $tongSoBaiLam = $ketQuas->count();
            $tongDiem = $ketQuas->sum('Diem');
            $diemTrungBinh = round($tongDiem / $tongSoBaiLam, 2);
            $diemCaoNhat = $ketQuas->max('Diem');
            $diemThapNhat = $ketQuas->min('Diem');
            
            // Tổng số câu từ KetQua
            $tongSoCauDung = $ketQuas->sum('SoCauDung');
            $tongSoCauSai = $ketQuas->sum('SoCauSai');
            $tongSoCauKhongLam = $ketQuas->sum('SoCauKhongLam');
            $tongSoCauDaLam = $tongSoCauDung + $tongSoCauSai + $tongSoCauKhongLam;
            
            $tiLeDungTrungBinh = $tongSoCauDaLam > 0 
                ? round(($tongSoCauDung / $tongSoCauDaLam) * 100, 2) 
                : 0;
            
            $thongTinChung = [
                'tongSoBaiLam' => $tongSoBaiLam,
                'diemTrungBinh' => floatval($diemTrungBinh),
                'diemCaoNhat' => floatval($diemCaoNhat),
                'diemThapNhat' => floatval($diemThapNhat),
                'tiLeDungTrungBinh' => $tiLeDungTrungBinh,
                'tongSoCauDaLam' => $tongSoCauDaLam,
                'tongSoCauDung' => $tongSoCauDung,
                'tongSoCauSai' => $tongSoCauSai,
                'tongSoCauKhongLam' => $tongSoCauKhongLam
            ];
            
            // ============================================
            // 2. LỊCH SỬ ĐIỂM THEO THỜI GIAN (cho biểu đồ line chart)
            // ============================================
            
            $lichSuDiem = $ketQuas->map(function($kq, $index) {
                return [
                    'lanThi' => $index + 1,
                    'ngay' => date('d/m/Y', strtotime($kq->created_at)),
                    'ngayRutGon' => date('d/m', strtotime($kq->created_at)),
                    'diem' => floatval($kq->Diem),
                    'tenDe' => $kq->deThi->TenDe ?? 'N/A',
                    'maDe' => $kq->MaDe,
                    'soCauDung' => $kq->SoCauDung,
                    'soCauSai' => $kq->SoCauSai,
                    'tongSoCau' => $kq->SoCauDung + $kq->SoCauSai + $kq->SoCauKhongLam
                ];
            })->values()->toArray();
            
            // ============================================
            // 3. TỶ LỆ ĐÚNG/SAI (cho biểu đồ pie chart)
            // ============================================
            
            $tyLeDungSai = [
                'dung' => $tongSoCauDung,
                'sai' => $tongSoCauSai,
                'khongLam' => $tongSoCauKhongLam,
                'phanTram' => [
                    'dung' => $tongSoCauDaLam > 0 ? round(($tongSoCauDung / $tongSoCauDaLam) * 100, 2) : 0,
                    'sai' => $tongSoCauDaLam > 0 ? round(($tongSoCauSai / $tongSoCauDaLam) * 100, 2) : 0,
                    'khongLam' => $tongSoCauDaLam > 0 ? round(($tongSoCauKhongLam / $tongSoCauDaLam) * 100, 2) : 0
                ]
            ];
            
            // ============================================
            // 4. PHÂN TÍCH THEO CHUYÊN ĐỀ (điểm mạnh/yếu)
            // ============================================
            
            $chuyenDeStats = [];
            
            foreach ($ketQuas as $kq) {
                // Lấy bài làm với DSCauTraLoi (JSON)
                $bailam = \App\Models\BaiLam::where('MaBaiLam', $kq->MaBaiLam)->first();
                
                if (!$bailam || !$bailam->DSCauTraLoi) {
                    \Log::warning('BaiLam not found or no DSCauTraLoi: ' . $kq->MaBaiLam);
                    continue;
                }
                
                // Parse JSON DSCauTraLoi
                $dsCauTraLoi = json_decode($bailam->DSCauTraLoi, true);
                
                if (!is_array($dsCauTraLoi)) {
                    \Log::warning('DSCauTraLoi not array: ' . $kq->MaBaiLam);
                    continue;
                }
                
                // Duyệt qua từng câu trả lời
                foreach ($dsCauTraLoi as $cauTraLoi) {
                    $maCH = $cauTraLoi['MaCH'] ?? null;
                    // Hỗ trợ cả 2 format: DapAnChon và TraLoi
                    $dapAnChon = $cauTraLoi['DapAnChon'] ?? $cauTraLoi['TraLoi'] ?? null;
                    
                    if (!$maCH) {
                        continue;
                    }
                    
                    // Lấy thông tin câu hỏi
                    $cauHoi = \App\Models\CauHoi::find($maCH);
                    
                    if (!$cauHoi) {
                        \Log::warning('CauHoi not found: ' . $maCH);
                        continue;
                    }
                    
                    // Lấy chuyên đề (nếu có cột ChuyenDe)
                    $chuyenDe = $cauHoi->ChuyenDe ?? 'Chung';
                    
                    if (!isset($chuyenDeStats[$chuyenDe])) {
                        $chuyenDeStats[$chuyenDe] = [
                            'tong' => 0,
                            'dung' => 0,
                            'sai' => 0
                        ];
                    }
                    
                    $chuyenDeStats[$chuyenDe]['tong']++;
                    
                    // Chuẩn hóa để so sánh
                    $dapAnChonNormalized = $dapAnChon ? strtoupper(trim($dapAnChon)) : null;
                    $dapAnDungNormalized = $cauHoi->DapAn ? strtoupper(trim($cauHoi->DapAn)) : null;
                    
                    // Kiểm tra đáp án đúng
                    if ($dapAnChonNormalized && $dapAnChonNormalized === $dapAnDungNormalized) {
                        $chuyenDeStats[$chuyenDe]['dung']++;
                    } else {
                        $chuyenDeStats[$chuyenDe]['sai']++;
                    }
                }
            }
            
            // Format dữ liệu chuyên đề
            $phanTichChuyenDe = [];
            foreach ($chuyenDeStats as $tenCD => $stats) {
                $tyLeDung = $stats['tong'] > 0 
                    ? round(($stats['dung'] / $stats['tong']) * 100, 2) 
                    : 0;
                
                $phanTichChuyenDe[] = [
                    'tenChuyenDe' => $tenCD,
                    'tyLeDung' => $tyLeDung,
                    'soCauDung' => $stats['dung'],
                    'soCauSai' => $stats['sai'],
                    'tongSoCau' => $stats['tong'],
                    'xepLoai' => $this->xepLoaiChuyenDe($tyLeDung)
                ];
            }
            
            // Sắp xếp theo tỷ lệ đúng (từ thấp đến cao)
            usort($phanTichChuyenDe, function($a, $b) {
                return $a['tyLeDung'] <=> $b['tyLeDung'];
            });
            
            // ============================================
            // 5. PHÂN TÍCH ĐIỂM MẠNH/YẾU
            // ============================================
            
            $diemYeu = array_filter($phanTichChuyenDe, function($cd) {
                return $cd['tyLeDung'] < 50; // Dưới 50% là yếu
            });
            
            $diemManh = array_filter($phanTichChuyenDe, function($cd) {
                return $cd['tyLeDung'] >= 70; // Trên 70% là mạnh
            });
            
            // Đảo ngược để hiển thị điểm mạnh nhất trước
            $diemManh = array_reverse($diemManh);
            
            // Khuyến nghị
            $khuyenNghi = $this->generateKhuyenNghi($diemYeu, $diemManh, $diemTrungBinh);
            
            $diemManhYeu = [
                'diemManh' => array_values($diemManh),
                'diemYeu' => array_values($diemYeu),
                'khuyenNghi' => $khuyenNghi
            ];
            
            // ============================================
            // 6. BIẾN ĐỘ ĐIỂM (để phát hiện xu hướng tiến bộ)
            // ============================================
            
            $bienDoDiem = [];
            if (count($lichSuDiem) >= 2) {
                for ($i = 1; $i < count($lichSuDiem); $i++) {
                    $bienDoDiem[] = [
                        'lanThi' => $i + 1,
                        'diemHienTai' => $lichSuDiem[$i]['diem'],
                        'diemTruoc' => $lichSuDiem[$i-1]['diem'],
                        'chenhLech' => round($lichSuDiem[$i]['diem'] - $lichSuDiem[$i-1]['diem'], 2),
                        'xuHuong' => $lichSuDiem[$i]['diem'] > $lichSuDiem[$i-1]['diem'] ? 'Tăng' : ($lichSuDiem[$i]['diem'] < $lichSuDiem[$i-1]['diem'] ? 'Giảm' : 'Không đổi')
                    ];
                }
            }
            
            // ============================================
            // 7. TRẢ VỀ DỮ LIỆU
            // ============================================
            
            return response()->json([
                'success' => true,
                'message' => 'Lấy thống kê cá nhân thành công',
                'data' => [
                    'thongTinChung' => $thongTinChung,
                    'lichSuDiem' => $lichSuDiem,
                    'tyLeDungSai' => $tyLeDungSai,
                    'phanTichChuyenDe' => $phanTichChuyenDe,
                    'diemManhYeu' => $diemManhYeu,
                    'bienDoDiem' => $bienDoDiem
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('=== THỐNG KÊ CÁ NHÂN ERROR ===');
            \Log::error('Error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy thống kê: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Xếp loại chuyên đề dựa trên tỷ lệ đúng
     */
    private function xepLoaiChuyenDe($tyLeDung)
    {
        if ($tyLeDung >= 80) return 'Xuất sắc';
        if ($tyLeDung >= 70) return 'Giỏi';
        if ($tyLeDung >= 60) return 'Khá';
        if ($tyLeDung >= 50) return 'Trung bình';
        return 'Yếu';
    }
    
    /**
     * Tạo khuyến nghị dựa trên điểm mạnh/yếu
     */
    private function generateKhuyenNghi($diemYeu, $diemManh, $diemTrungBinh)
    {
        $khuyenNghi = [];
        
        // Nhận xét chung
        if ($diemTrungBinh >= 8.0) {
            $khuyenNghi[] = "🎉 Xuất sắc! Bạn đang có thành tích rất tốt.";
        } elseif ($diemTrungBinh >= 6.5) {
            $khuyenNghi[] = "👍 Tốt! Bạn đang tiến bộ đều đặn.";
        } elseif ($diemTrungBinh >= 5.0) {
            $khuyenNghi[] = "💪 Hãy cố gắng hơn nữa! Bạn có thể làm tốt hơn.";
        } else {
            $khuyenNghi[] = "⚠️ Cần nỗ lực nhiều hơn! Hãy luyện tập thường xuyên.";
        }
        
        // Khuyến nghị về điểm yếu
        if (count($diemYeu) > 0) {
            $tenCD = array_map(function($cd) { return $cd['tenChuyenDe']; }, $diemYeu);
            $khuyenNghi[] = "📚 Tập trung ôn tập các chuyên đề: " . implode(", ", array_slice($tenCD, 0, 3));
        }
        
        // Khen ngợi điểm mạnh
        if (count($diemManh) > 0) {
            $tenCD = array_map(function($cd) { return $cd['tenChuyenDe']; }, $diemManh);
            $khuyenNghi[] = "✨ Điểm mạnh của bạn: " . implode(", ", array_slice($tenCD, 0, 3));
        }
        
        // Khuyến nghị hành động
        if (count($diemYeu) > 0) {
            $khuyenNghi[] = "💡 Gợi ý: Làm thêm ít nhất 3 đề thi về chuyên đề yếu mỗi tuần.";
        }
        
        return implode(" ", $khuyenNghi);
    }

    /**
     * UR-02.3: Xem kết quả chi tiết sau khi nộp bài
     * Bao gồm: điểm số, đáp án đúng/sai, so sánh với đáp án học sinh chọn
     * 
     * @param Request $request
     * @param string $maBaiLam
     * @return \Illuminate\Http\JsonResponse
     */
    public function xemKetQua(Request $request, $maBaiLam)
    {
        try {
            \Log::info('=== XEM KET QUA ===');
            \Log::info('MaBaiLam: ' . $maBaiLam);

            $user = $request->user();
            
            // Lấy thông tin bài làm
            $baiLam = DB::table('BaiLam')
                ->where('MaBaiLam', $maBaiLam)
                ->first();

            if (!$baiLam) {
                \Log::error('Bai lam not found: ' . $maBaiLam);
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy bài làm'
                ], 404);
            }

            // Kiểm tra quyền xem (chỉ học sinh làm bài hoặc giáo viên/admin)
            if ($user->Role === 'hocsinh') {
                $hocSinh = DB::table('HocSinh')->where('MaTK', $user->MaTK)->first();
                if (!$hocSinh || $hocSinh->MaHS !== $baiLam->MaHS) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Bạn không có quyền xem bài làm này'
                    ], 403);
                }
            }

            // Lấy thông tin đề thi
            $deThi = DB::table('DeThi')->where('MaDe', $baiLam->MaDe)->first();
            
            if (!$deThi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy đề thi'
                ], 404);
            }

            // Lấy thông tin học sinh
            $hocSinhInfo = DB::table('HocSinh')
                ->join('TaiKhoan', 'HocSinh.MaTK', '=', 'TaiKhoan.MaTK')
                ->where('HocSinh.MaHS', $baiLam->MaHS)
                ->select('HocSinh.*', 'TaiKhoan.Email')
                ->first();

            // Parse danh sách câu trả lời
            $cauTraLoi = json_decode($baiLam->DSCauTraLoi, true) ?? [];

            // Lấy tất cả câu hỏi trong đề thi với đáp án đúng
            $cauHoiList = DB::table('DETHI_CAUHOI as dc')
                ->join('CauHoi as ch', 'dc.MaCH', '=', 'ch.MaCH')
                ->where('dc.MaDe', $baiLam->MaDe)
                ->orderBy('dc.ThuTu', 'asc')
                ->select(
                    'ch.MaCH',
                    'ch.NoiDung',
                    'ch.DapAnA',
                    'ch.DapAnB',
                    'ch.DapAnC',
                    'ch.DapAnD',
                    'ch.DapAn as DapAnDung',
                    'ch.DoKho',
                    'dc.ThuTu'
                )
                ->get();

            // Tạo map câu trả lời của học sinh
            $cauTraLoiMap = [];
            foreach ($cauTraLoi as $tl) {
                $cauTraLoiMap[$tl['MaCH']] = $tl;
            }

            // Tạo danh sách câu hỏi với thông tin chi tiết
            $chiTietCauHoi = [];
            $soCauDung = 0;
            $soCauSai = 0;
            $soCauKhongLam = 0;

            foreach ($cauHoiList as $index => $ch) {
                $traLoi = $cauTraLoiMap[$ch->MaCH] ?? null;
                $dapAnChon = $traLoi['DapAnChon'] ?? null;
                $isDung = $dapAnChon && ($dapAnChon === $ch->DapAnDung);

                if ($dapAnChon) {
                    if ($isDung) {
                        $soCauDung++;
                    } else {
                        $soCauSai++;
                    }
                } else {
                    $soCauKhongLam++;
                }

                $chiTietCauHoi[] = [
                    'STT' => $index + 1,
                    'MaCH' => $ch->MaCH,
                    'NoiDung' => $ch->NoiDung,
                    'DapAnA' => $ch->DapAnA,
                    'DapAnB' => $ch->DapAnB,
                    'DapAnC' => $ch->DapAnC,
                    'DapAnD' => $ch->DapAnD,
                    'DapAnDung' => $ch->DapAnDung,
                    'DapAnChon' => $dapAnChon,
                    'KetQua' => $dapAnChon ? ($isDung ? 'Đúng' : 'Sai') : 'Không làm',
                    'DoKho' => $ch->DoKho
                ];
            }

            $tongSoCau = count($cauHoiList);
            $diem = $baiLam->Diem;

            \Log::info('Ket qua: ' . $soCauDung . '/' . $tongSoCau);

            return response()->json([
                'success' => true,
                'message' => 'Lấy kết quả thành công',
                'data' => [
                    'MaBaiLam' => $baiLam->MaBaiLam,
                    'MaDe' => $baiLam->MaDe,
                    'TenDe' => $deThi->TenDe,
                    'MoTa' => $deThi->MoTa,
                    'ThoiGianLamBai' => $deThi->ThoiGianLamBai,
                    'HocSinh' => [
                        'MaHS' => $hocSinhInfo->MaHS,
                        'HoTen' => $hocSinhInfo->HoTen,
                        'Lop' => $hocSinhInfo->Lop,
                        'Email' => $hocSinhInfo->Email
                    ],
                    'ThoiGianBatDau' => $baiLam->ThoiGianBatDau,
                    'ThoiGianNop' => $baiLam->ThoiGianNop,
                    'TrangThai' => $baiLam->TrangThai,
                    'KetQua' => [
                        'Diem' => (float) $diem,
                        'TongSoCau' => $tongSoCau,
                        'SoCauDung' => $soCauDung,
                        'SoCauSai' => $soCauSai,
                        'SoCauKhongLam' => $soCauKhongLam,
                        'TyLeDung' => $tongSoCau > 0 ? round(($soCauDung / $tongSoCau) * 100, 2) : 0
                    ],
                    'ChiTietCauHoi' => $chiTietCauHoi,
                    'SoLanViPham' => $baiLam->SoLanViPham ?? 0
                ]
            ], 200);

        } catch (\Exception $e) {
            \Log::error('=== ERROR in xemKetQua ===');
            \Log::error('Exception: ' . $e->getMessage());
            \Log::error('Trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xem kết quả',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}


