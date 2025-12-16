<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\DeThi;
use App\Models\GiaoVien;
use App\Models\KetQua;
use App\Models\CauHoi;
use Carbon\Carbon;

class DeThiController extends Controller
{
    /**
     * Constructor - Yêu cầu authentication
     * Loại trừ hàm 'layDeThiMau' để Khách có thể xem được
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['layDeThiMau']);
    }

    /**
     * UR-02.1: Lấy danh sách tất cả đề thi có sẵn cho học sinh
     */
    public function layDanhSachDeThi(Request $request)
    {
        try {
            $query = DeThi::where('TrangThai', true);

            if ($request->has('search') && !empty($request->search)) {
                $query->where('TenDe', 'like', '%' . $request->search . '%');
            }

            $query->orderBy('NgayTao', 'desc');
            $query->with(['giaoVien:MaGV,HoTen']);

            $perPage = $request->input('per_page', 20);
            $deThi = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Lấy danh sách đề thi thành công',
                'data' => $deThi
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy danh sách đề thi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * UR-02.1: Lấy thông tin chi tiết một đề thi
     */
    public function layChiTietDeThi(Request $request, $maDe)
    {
        try {
            $deThi = DeThi::where('MaDe', $maDe)
                ->with(['giaoVien' => function($query) {
                    $query->select('MaGV', 'HoTen');
                }])
                ->first();

            if (!$deThi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy đề thi'
                ], 404);
            }

            // Lấy số lượng câu hỏi
            $soLuongCauHoi = DB::table('dethi_cauhoi')
                ->where('MaDe', $maDe)
                ->count();

            return response()->json([
                'success' => true,
                'message' => 'Lấy chi tiết đề thi thành công',
                'data' => [
                    'MaDe' => $deThi->MaDe,
                    'TenDe' => $deThi->TenDe,
                    'MoTa' => $deThi->MoTa,
                    'SoLuongCauHoi' => $soLuongCauHoi,
                    'ThoiGianLamBai' => $deThi->ThoiGianLamBai,
                    'TenGiaoVien' => $deThi->giaoVien->HoTen ?? 'N/A',
                    'NgayTao' => $deThi->NgayTao
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * UR-02.1: Bắt đầu làm bài thi
     */
    public function batDauLamBai(Request $request, $maDe)
    {
        try {
            \Log::info('=== BAT DAU LAM BAI ===');
            \Log::info('Request MaDe: ' . $maDe);
            
            $user = $request->user();
            
            if (!$user) {
                \Log::error('User not authenticated');
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn cần đăng nhập để làm bài'
                ], 401);
            }
            
            // Log thông tin user để debug
            \Log::info('User info:', [
                'user' => $user,
                'MaTK' => $user->MaTK ?? 'NULL',
                'Role' => $user->Role ?? 'NULL'
            ]);

            // Lấy thông tin đề thi
            $deThi = DeThi::where('MaDe', $maDe)
                ->with(['giaoVien' => function($query) {
                    $query->select('MaGV', 'HoTen');
                }])
                ->first();
                
            if (!$deThi) {
                \Log::error('Exam not found: ' . $maDe);
                return response()->json(['success' => false, 'message' => 'Không tìm thấy đề thi'], 404);
            }
            
            \Log::info('Exam found:', ['MaDe' => $deThi->MaDe, 'TenDe' => $deThi->TenDe]);

            // Lấy MaHS từ bảng HocSinh dựa vào MaTK
            $hocSinh = DB::table('HocSinh')->where('MaTK', $user->MaTK)->first();
            
            \Log::info('HocSinh lookup:', [
                'MaTK' => $user->MaTK,
                'found' => $hocSinh ? 'YES' : 'NO',
                'MaHS' => $hocSinh->MaHS ?? 'NULL'
            ]);
            
            if (!$hocSinh) {
                \Log::error('Student not found for user: ' . $user->MaTK);
                return response()->json([
                    'success' => false,
                    'message' => "Không tìm thấy thông tin học sinh cho tài khoản {$user->MaTK}. Vui lòng liên hệ quản trị viên.",
                    'debug' => [
                        'MaTK' => $user->MaTK,
                        'Role' => $user->Role ?? 'unknown'
                    ]
                ], 404);
            }

            // Kiểm tra xem học sinh có bài đang làm dở không (CHỈ kiểm tra DangLam)
            $existingBaiLam = DB::table('BaiLam')
                ->where('MaHS', $hocSinh->MaHS)
                ->where('MaDe', $maDe)
                ->where('TrangThai', 'DangLam')  // CHỈ kiểm tra bài đang làm
                ->first();

            \Log::info('Check existing submission:', [
                'found' => $existingBaiLam ? 'YES' : 'NO',
                'status' => $existingBaiLam->TrangThai ?? 'NULL'
            ]);

            if ($existingBaiLam) {
                // Nếu đang làm dở, trả về thông tin để tiếp tục
                \Log::info('Continue existing exam');
                $cauHois = DB::table('DETHI_CAUHOI as dc')
                    ->join('CauHoi as ch', 'dc.MaCH', '=', 'ch.MaCH')
                    ->where('dc.MaDe', $maDe)
                    ->orderBy('dc.ThuTu', 'asc')
                    ->select(
                        'ch.MaCH as MaCauHoi',
                        'ch.NoiDung',
                        'ch.DapAnA',
                        'ch.DapAnB',
                        'ch.DapAnC',
                        'ch.DapAnD',
                        'ch.DoKho'
                    )
                    ->get();

                return response()->json([
                    'success' => true,
                    'message' => 'Tiếp tục làm bài thi',
                    'data' => [
                        'MaBT' => $existingBaiLam->MaBaiLam,
                        'MaDe' => $deThi->MaDe,
                        'TenDe' => $deThi->TenDe,
                        'ThoiGianLamBai' => $deThi->ThoiGianLamBai,
                        'ThoiGianBatDau' => $existingBaiLam->ThoiGianBatDau,
                        'TenGiaoVien' => $deThi->giaoVien->HoTen ?? 'N/A',
                        'CauHoi' => $cauHois
                    ]
                ], 200);
            }

            // Tạo bài làm mới
            \Log::info('Creating new exam submission');
            
            // Generate unique MaBaiLam
            $lastBaiLam = DB::table('BaiLam')->orderBy('MaBaiLam', 'desc')->first();
            if ($lastBaiLam && preg_match('/BL(\d+)/', $lastBaiLam->MaBaiLam, $matches)) {
                $nextNumber = intval($matches[1]) + 1;
            } else {
                $nextNumber = 1;
            }
            $maBaiLam = 'BL' . str_pad($nextNumber, 8, '0', STR_PAD_LEFT);
            
            // Ensure uniqueness
            while (DB::table('BaiLam')->where('MaBaiLam', $maBaiLam)->exists()) {
                $nextNumber++;
                $maBaiLam = 'BL' . str_pad($nextNumber, 8, '0', STR_PAD_LEFT);
            }
            
            \Log::info('Generated MaBaiLam: ' . $maBaiLam);

            DB::table('BaiLam')->insert([
                'MaBaiLam' => $maBaiLam,
                'MaHS' => $hocSinh->MaHS,
                'MaDe' => $maDe,
                'ThoiGianBatDau' => now(),
                'TrangThai' => 'DangLam',
                'Diem' => 0,
                'SoLanViPham' => 0,
                'DSCauTraLoi' => json_encode([]),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            \Log::info('Exam submission created successfully');

            // Lấy danh sách câu hỏi trong đề thi
            $cauHois = DB::table('DETHI_CAUHOI as dc')
                ->join('CauHoi as ch', 'dc.MaCH', '=', 'ch.MaCH')
                ->where('dc.MaDe', $maDe)
                ->orderBy('dc.ThuTu', 'asc')
                ->select(
                    'ch.MaCH as MaCauHoi',
                    'ch.NoiDung',
                    'ch.DapAnA',
                    'ch.DapAnB',
                    'ch.DapAnC',
                    'ch.DapAnD',
                    'ch.DoKho'
                )
                ->get();
            
            \Log::info('Retrieved ' . $cauHois->count() . ' questions for exam');
            
            if ($cauHois->isEmpty()) {
                \Log::warning('No questions found for exam: ' . $maDe);
                return response()->json([
                    'success' => false,
                    'message' => 'Đề thi chưa có câu hỏi nào. Vui lòng liên hệ giáo viên.'
                ], 400);
            }

            \Log::info('=== SUCCESS: Exam started successfully ===');
            return response()->json([
                'success' => true,
                'message' => 'Bắt đầu làm bài thi thành công',
                'data' => [
                    'MaBaiLam' => $maBaiLam,
                    'MaDe' => $deThi->MaDe,
                    'TenDe' => $deThi->TenDe,
                    'ThoiGianLamBai' => $deThi->ThoiGianLamBai,
                    'ThoiGianBatDau' => now()->toDateTimeString(),
                    'TenGiaoVien' => $deThi->giaoVien->HoTen ?? 'N/A',
                    'MaHS' => $hocSinh->MaHS,  // Thêm MaHS để frontend dùng khi nộp bài
                    'CauHoi' => $cauHois
                ]
            ], 201);

        } catch (\Exception $e) {
            \Log::error('=== ERROR in batDauLamBai ===');
            \Log::error('Exception message: ' . $e->getMessage());
            \Log::error('Exception trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi bắt đầu làm bài',
                'error' => $e->getMessage(),
                'debug' => config('app.debug') ? [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ] : null
            ], 500);
        }
    }

    /**
     * UR-03: Tạo đề thi (legacy method)
     */
    public function taoDeThi(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'TenDe' => 'required|string|max:255',
                'ChuDe' => 'required|string|max:255',
                'ThoiGianLamBai' => 'required|integer|min:1',
                'SoLuongCauHoi' => 'required|integer|min:1',
                'MoTa' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = $request->user();

            if (!in_array($user->Role, ['giaovien', 'admin'])) {
                return response()->json(['success' => false, 'message' => 'Chỉ giáo viên mới có quyền tạo đề thi'], 403);
            }

            DB::beginTransaction();

            // Generate unique MaDe
            $lastDeThi = DeThi::orderBy('MaDe', 'desc')->first();
            if ($lastDeThi && preg_match('/DE(\d+)/', $lastDeThi->MaDe, $matches)) {
                $nextNumber = intval($matches[1]) + 1;
            } else {
                $nextNumber = 1;
            }
            $maDe = 'DE' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            
            // Ensure uniqueness
            while (DeThi::where('MaDe', $maDe)->exists()) {
                $nextNumber++;
                $maDe = 'DE' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            }

            $deThi = DeThi::create([
                'MaDe' => $maDe,
                'TenDe' => $request->TenDe,
                'ChuDe' => $request->ChuDe,
                'ThoiGianLamBai' => $request->ThoiGianLamBai,
                'SoLuongCauHoi' => $request->SoLuongCauHoi,
                'MoTa' => $request->MoTa ?? '',
                'MaGV' => $user->MaTK,
                'NgayTao' => now(),
                'TrangThai' => 1
            ]);

            DB::commit();

            \Log::info('Tạo đề thi thành công', ['MaDe' => $maDe, 'TenDe' => $request->TenDe]);

            return response()->json([
                'success' => true,
                'message' => 'Tạo đề thi thành công',
                'data' => $deThi
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * UR-03.4: Tạo đề thi NGẪU NHIÊN - Tự động chọn câu hỏi từ ngân hàng
     */
    public function taoDeThiNgauNhien(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'TenDe' => 'required|string|max:255',
                'ChuDe' => 'required|string|max:255',
                'ThoiGianLamBai' => 'required|integer|min:1',
                'SoLuongCauHoi' => 'required|integer|min:1|max:100',
                'MoTa' => 'nullable|string',
                'MaNH' => 'nullable|string|exists:NganHangCauHoi,MaNH',
                'DoKho' => 'nullable|string|in:De,Trung binh,Kho'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = $request->user();

            if (!in_array($user->Role, ['giaovien', 'admin'])) {
                return response()->json(['success' => false, 'message' => 'Chỉ giáo viên mới có quyền tạo đề thi'], 403);
            }

            // Get MaGV from GiaoVien table
            $giaoVien = \App\Models\GiaoVien::where('MaTK', $user->MaTK)->first();
            
            if (!$giaoVien) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Không tìm thấy thông tin giáo viên'
                ], 404);
            }

            DB::beginTransaction();

            $query = CauHoi::query();

            if ($request->MaNH) {
                $query->where('MaNH', $request->MaNH);
            }

            if ($request->DoKho) {
                $query->where('DoKho', $request->DoKho);
            }

            $availableQuestions = $query->get();
            \Log::info("Tìm thấy {$availableQuestions->count()} câu hỏi, cần {$request->SoLuongCauHoi} câu");

            if ($availableQuestions->count() < $request->SoLuongCauHoi) {
                \Log::warning("KHÔNG ĐỦ CÂU HỎI!");
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => "Không đủ câu hỏi. Có {$availableQuestions->count()} câu, cần {$request->SoLuongCauHoi} câu"
                ], 400);
            }

            $selectedQuestions = $availableQuestions->random($request->SoLuongCauHoi);
            \Log::info("Đã chọn {$selectedQuestions->count()} câu hỏi ngẫu nhiên");

            // Generate unique MaDe
            $lastDeThi = DeThi::orderBy('MaDe', 'desc')->first();
            if ($lastDeThi && preg_match('/DE(\d+)/', $lastDeThi->MaDe, $matches)) {
                $nextNumber = intval($matches[1]) + 1;
            } else {
                $nextNumber = 1;
            }
            $maDe = 'DE' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            
            // Ensure uniqueness
            while (DeThi::where('MaDe', $maDe)->exists()) {
                $nextNumber++;
                $maDe = 'DE' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            }

            $deThi = DeThi::create([
                'MaDe' => $maDe,
                'TenDe' => $request->TenDe,
                'ChuDe' => $request->ChuDe,
                'ThoiGianLamBai' => $request->ThoiGianLamBai,
                'SoLuongCauHoi' => $request->SoLuongCauHoi,
                'MoTa' => $request->MoTa ?? '',
                'MaGV' => $giaoVien->MaGV,
                'NgayTao' => now(),
                'TrangThai' => 1
            ]);

            \Log::info("Đang thêm {$selectedQuestions->count()} câu hỏi vào đề {$maDe}");
            
            $insertedCount = 0;
            foreach ($selectedQuestions as $index => $question) {
                \Log::info("Thêm câu {$question->MaCH} vào vị trí " . ($index + 1));
                try {
                    DB::table('DETHI_CAUHOI')->insert([
                        'MaDe' => $maDe,
                        'MaCH' => $question->MaCH,
                        'ThuTu' => $index + 1,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    $insertedCount++;
                } catch (\Exception $e) {
                    \Log::error("Failed to insert question {$question->MaCH}: " . $e->getMessage());
                }
            }
            
            // Kiểm tra số câu đã insert
            $actualCount = DB::table('DETHI_CAUHOI')->where('MaDe', $maDe)->count();
            \Log::info("Số câu đã insert: {$actualCount}/{$request->SoLuongCauHoi}");
            
            if ($actualCount === 0) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể thêm câu hỏi vào đề thi. Vui lòng kiểm tra lại.'
                ], 500);
            }
            
            if ($actualCount != $request->SoLuongCauHoi) {
                \Log::warning("Chỉ insert được {$actualCount}/{$request->SoLuongCauHoi} câu hỏi");
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tạo đề thi ngẫu nhiên thành công',
                'data' => [
                    'MaDe' => $deThi->MaDe,
                    'TenDe' => $deThi->TenDe,
                    'SoLuongCauHoi' => $deThi->SoLuongCauHoi,
                    'ThoiGianLamBai' => $deThi->ThoiGianLamBai,
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * UR-03.3: Tạo đề thi THỦ CÔNG - Giáo viên tự chọn từng câu hỏi cụ thể
     */
    public function createManualExam(Request $request)
    {
        try {
            // Debug: Log request data
            \Log::info('Create Manual Exam Request', $request->all());
            
            $validator = Validator::make($request->all(), [
                'TenDe' => 'required|string|max:255',
                'ChuDe' => 'required|string|max:255',
                'ThoiGianLamBai' => 'required|integer|min:1',
                'DanhSachCauHoi' => 'required|array|min:1',
                'DanhSachCauHoi.*' => 'string|exists:CauHoi,MaCH',
                'MoTa' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                \Log::error('Validation Failed', $validator->errors()->toArray());
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = $request->user();

            if (!in_array($user->Role, ['giaovien', 'admin'])) {
                return response()->json(['success' => false, 'message' => 'Chỉ giáo viên mới có quyền tạo đề thi'], 403);
            }

            // Get MaGV from GiaoVien table based on MaTK
            $giaoVien = \App\Models\GiaoVien::where('MaTK', $user->MaTK)->first();
            
            if (!$giaoVien) {
                \Log::error('GiaoVien not found for MaTK: ' . $user->MaTK);
                return response()->json([
                    'success' => false, 
                    'message' => 'Không tìm thấy thông tin giáo viên. Vui lòng liên hệ quản trị viên.'
                ], 404);
            }

            DB::beginTransaction();

            // Generate unique MaDe by finding the max existing number
            $lastDeThi = DeThi::orderBy('MaDe', 'desc')->first();
            if ($lastDeThi && preg_match('/DE(\d+)/', $lastDeThi->MaDe, $matches)) {
                $nextNumber = intval($matches[1]) + 1;
            } else {
                $nextNumber = 1;
            }
            $maDe = 'DE' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            
            // Ensure uniqueness (in case of concurrent requests)
            while (DeThi::where('MaDe', $maDe)->exists()) {
                $nextNumber++;
                $maDe = 'DE' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            }
            
            \Log::info('Generated MaDe: ' . $maDe);

            $deThiData = [
                'MaDe' => $maDe,
                'TenDe' => $request->TenDe,
                'ChuDe' => $request->ChuDe,
                'ThoiGianLamBai' => $request->ThoiGianLamBai,
                'SoLuongCauHoi' => count($request->DanhSachCauHoi),
                'MoTa' => $request->MoTa ?? '',
                'MaGV' => $giaoVien->MaGV,
                'NgayTao' => now(),
                'TrangThai' => 1
            ];
            \Log::info('DeThi data to create:', $deThiData);

            $deThi = DeThi::create($deThiData);
            \Log::info('DeThi created successfully: ' . $deThi->MaDe);

            // Insert câu hỏi vào pivot table với kiểm tra lỗi
            $insertedCount = 0;
            foreach ($request->DanhSachCauHoi as $index => $maCH) {
                try {
                    DB::table('dethi_cauhoi')->insert([
                        'MaDe' => $maDe,
                        'MaCH' => $maCH,
                        'ThuTu' => $index + 1,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    $insertedCount++;
                } catch (\Exception $e) {
                    \Log::error("Failed to insert question {$maCH} into exam {$maDe}: " . $e->getMessage());
                }
            }

            \Log::info("Inserted {$insertedCount}/{$deThi->SoLuongCauHoi} questions for exam {$maDe}");

            // Kiểm tra nếu không có câu hỏi nào được insert
            if ($insertedCount === 0) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể thêm câu hỏi vào đề thi. Vui lòng kiểm tra lại danh sách câu hỏi.'
                ], 500);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tạo đề thi thủ công thành công',
                'data' => [
                    'MaDe' => $deThi->MaDe,
                    'TenDe' => $deThi->TenDe,
                    'SoLuongCauHoi' => $deThi->SoLuongCauHoi,
                    'ThoiGianLamBai' => $deThi->ThoiGianLamBai,
                    'DanhSachCauHoi' => $request->DanhSachCauHoi
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Create Manual Exam Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tạo đề thi',
                'error' => $e->getMessage(),
                'debug' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }

    /**
     * UR-03.5: Thống kê kết quả theo đề thi cụ thể
     */
    public function thongKeKetQua(Request $request, $maDe)
    {
        try {
            $user = $request->user();

            if (!in_array($user->Role, ['giaovien', 'admin'])) {
                return response()->json(['success' => false, 'message' => 'Chỉ giáo viên có quyền xem thống kê'], 403);
            }

            $deThi = DeThi::where('MaDe', $maDe)->first();
            if (!$deThi) {
                return response()->json(['success' => false, 'message' => 'Không tìm thấy đề thi'], 404);
            }

            $ketQua = DB::table('BaiThi')
                ->join('TaiKhoan', 'BaiThi.MaTK', '=', 'TaiKhoan.MaTK')
                ->where('BaiThi.MaDe', $maDe)
                ->where('BaiThi.TrangThai', 'hoanthanh')
                ->select('TaiKhoan.TenTK', 'BaiThi.Diem', 'BaiThi.ThoiGianBatDau', 'BaiThi.ThoiGianKetThuc')
                ->orderBy('BaiThi.Diem', 'desc')
                ->get();

            $totalStudents = $ketQua->count();
            $averageScore = $totalStudents > 0 ? round($ketQua->avg('Diem'), 2) : 0;
            $maxScore = $totalStudents > 0 ? $ketQua->max('Diem') : 0;
            $minScore = $totalStudents > 0 ? $ketQua->min('Diem') : 0;
            $passedStudents = $ketQua->where('Diem', '>=', 5)->count();
            $passRate = $totalStudents > 0 ? round(($passedStudents / $totalStudents) * 100, 2) : 0;

            return response()->json([
                'success' => true,
                'data' => [
                    'deThi' => $deThi,
                    'statistics' => [
                        'totalStudents' => $totalStudents,
                        'averageScore' => $averageScore,
                        'maxScore' => $maxScore,
                        'minScore' => $minScore,
                        'passRate' => $passRate
                    ],
                    'results' => $ketQua
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * UR-03.5: Thống kê lớp học - Dashboard giáo viên
     */
    public function getClassStatistics(Request $request)
    {
        try {
            $user = $request->user();

            if (!in_array($user->Role, ['giaovien', 'admin'])) {
                return response()->json(['success' => false, 'message' => 'Chỉ giáo viên có quyền xem thống kê lớp học'], 403);
            }

            $students = DB::table('HocSinh')
                ->join('TaiKhoan', 'HocSinh.MaTK', '=', 'TaiKhoan.MaTK')
                ->where('TaiKhoan.Role', 'hocsinh')
                ->select('TaiKhoan.MaTK', 'HocSinh.HoTen as TenTK', 'TaiKhoan.Email')
                ->get();

            if ($students->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Chưa có học sinh nào',
                    'data' => [
                        'summary' => ['totalStudents' => 0, 'averageScore' => 0, 'passRate' => 0, 'totalExams' => 0],
                        'topStudents' => [], 'weakStudents' => [], 'scoreDistribution' => [], 'studentDetails' => []
                    ]
                ]);
            }

            $studentScores = DB::table('BaiLam')
                ->join('HocSinh', 'BaiLam.MaHS', '=', 'HocSinh.MaHS')
                ->join('TaiKhoan', 'HocSinh.MaTK', '=', 'TaiKhoan.MaTK')
                ->select('TaiKhoan.MaTK', 'HocSinh.HoTen as TenTK', 'TaiKhoan.Email',
                    DB::raw('AVG(BaiLam.Diem) as avg_score'),
                    DB::raw('MAX(BaiLam.Diem) as max_score'),
                    DB::raw('MIN(BaiLam.Diem) as min_score'),
                    DB::raw('COUNT(*) as total_exams'))
                ->where('TaiKhoan.Role', 'hocsinh')
                ->whereIn('BaiLam.TrangThai', ['DaNop', 'ChamDiem'])
                ->whereNotNull('BaiLam.Diem')
                ->groupBy('TaiKhoan.MaTK', 'HocSinh.HoTen', 'TaiKhoan.Email')
                ->get();

            $totalStudents = $students->count();
            $studentsWithScores = $studentScores->count();
            $averageScore = $studentsWithScores > 0 ? round($studentScores->avg('avg_score'), 2) : 0;
            $passedStudents = $studentScores->filter(fn($s) => $s->avg_score >= 5)->count();
            $passRate = $studentsWithScores > 0 ? round(($passedStudents / $studentsWithScores) * 100, 2) : 0;
            $totalExams = DB::table('BaiLam')
                ->whereIn('TrangThai', ['DaNop', 'ChamDiem'])
                ->whereNotNull('Diem')
                ->count();

            $topStudents = $studentScores->sortByDesc('avg_score')->take(5)->values()->map(fn($s) => [
                'MaTK' => $s->MaTK, 'TenTK' => $s->TenTK, 'avg_score' => round($s->avg_score, 2),
                'max_score' => round($s->max_score, 2), 'total_exams' => $s->total_exams
            ]);

            $weakStudents = $studentScores->sortBy('avg_score')->take(5)->values()->map(fn($s) => [
                'MaTK' => $s->MaTK, 'TenTK' => $s->TenTK, 'avg_score' => round($s->avg_score, 2),
                'min_score' => round($s->min_score, 2), 'total_exams' => $s->total_exams
            ]);

            $scoreDistribution = [
                ['range' => '0-2', 'count' => 0, 'label' => 'Kém'],
                ['range' => '2-4', 'count' => 0, 'label' => 'Yếu'],
                ['range' => '4-5', 'count' => 0, 'label' => 'Trung bình'],
                ['range' => '5-6.5', 'count' => 0, 'label' => 'Khá'],
                ['range' => '6.5-8', 'count' => 0, 'label' => 'Khá Giỏi'],
                ['range' => '8-10', 'count' => 0, 'label' => 'Giỏi']
            ];

            foreach ($studentScores as $s) {
                $score = $s->avg_score;
                if ($score < 2) $scoreDistribution[0]['count']++;
                elseif ($score < 4) $scoreDistribution[1]['count']++;
                elseif ($score < 5) $scoreDistribution[2]['count']++;
                elseif ($score < 6.5) $scoreDistribution[3]['count']++;
                elseif ($score < 8) $scoreDistribution[4]['count']++;
                else $scoreDistribution[5]['count']++;
            }

            $studentDetails = $students->map(function($student) use ($studentScores) {
                $scoreData = $studentScores->firstWhere('MaTK', $student->MaTK);
                return [
                    'MaTK' => $student->MaTK, 'TenTK' => $student->TenTK, 'Email' => $student->Email,
                    'avg_score' => $scoreData ? round($scoreData->avg_score, 2) : 0,
                    'max_score' => $scoreData ? round($scoreData->max_score, 2) : 0,
                    'min_score' => $scoreData ? round($scoreData->min_score, 2) : 0,
                    'total_exams' => $scoreData ? $scoreData->total_exams : 0,
                    'status' => $scoreData ? ($scoreData->avg_score >= 5 ? 'Đạt' : 'Chưa đạt') : 'Chưa thi'
                ];
            })->sortByDesc('avg_score')->values();

            return response()->json([
                'success' => true,
                'message' => 'Lấy thống kê lớp học thành công',
                'data' => [
                    'summary' => [
                        'totalStudents' => $totalStudents,
                        'studentsWithScores' => $studentsWithScores,
                        'averageScore' => $averageScore,
                        'passRate' => $passRate,
                        'totalExams' => $totalExams
                    ],
                    'topStudents' => $topStudents,
                    'weakStudents' => $weakStudents,
                    'scoreDistribution' => $scoreDistribution,
                    'studentDetails' => $studentDetails
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra khi lấy thống kê', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Lấy danh sách đề thi của giáo viên
     */
    public function getTeacherExams(Request $request)
    {
        try {
            $user = $request->user();

            if (!in_array($user->Role, ['giaovien', 'admin'])) {
                return response()->json(['success' => false, 'message' => 'Không có quyền truy cập'], 403);
            }

            // Get MaGV from GiaoVien table
            $giaoVien = \App\Models\GiaoVien::where('MaTK', $user->MaTK)->first();
            
            if (!$giaoVien) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Không tìm thấy thông tin giáo viên'
                ], 404);
            }

            $exams = DB::table('dethi')
                ->where('MaGV', $giaoVien->MaGV)
                ->orderBy('NgayTao', 'desc')
                ->get()
                ->map(function($exam) {
                    // Đếm số câu hỏi thực tế
                    $questionCount = DB::table('dethi_cauhoi')
                        ->where('MaDe', $exam->MaDe)
                        ->count();
                    
                    // Đếm số lần làm bài
                    $submissionCount = DB::table('bailam')
                        ->where('MaDe', $exam->MaDe)
                        ->count();

                    return [
                        'MaDe' => $exam->MaDe,
                        'TenDe' => $exam->TenDe,
                        'ChuDe' => $exam->ChuDe ?? 'Chưa phân loại',
                        'ThoiGianLamBai' => $exam->ThoiGianLamBai,
                        'SoLuongCauHoi' => $exam->SoLuongCauHoi,
                        'SoCauHoiThucTe' => $questionCount,
                        'MoTa' => $exam->MoTa,
                        'NgayTao' => $exam->NgayTao,
                        'TrangThai' => $exam->TrangThai,
                        'SoLuotLam' => $submissionCount
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $exams
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Xem chi tiết đề thi (bao gồm câu hỏi)
     */
    public function getExamDetail(Request $request, $maDe)
    {
        try {
            $user = $request->user();

            // Lấy thông tin đề thi
            $exam = DB::table('dethi')->where('MaDe', $maDe)->first();

            if (!$exam) {
                return response()->json(['success' => false, 'message' => 'Không tìm thấy đề thi'], 404);
            }

            // Kiểm tra quyền (chỉ giáo viên tạo đề hoặc admin)
            if ($user->Role !== 'admin') {
                // Lấy MaGV của user hiện tại
                $giaoVien = \App\Models\GiaoVien::where('MaTK', $user->MaTK)->first();
                if (!$giaoVien || $exam->MaGV != $giaoVien->MaGV) {
                    return response()->json(['success' => false, 'message' => 'Không có quyền xem đề thi này'], 403);
                }
            }

            // Lấy danh sách câu hỏi
            $questions = DB::table('dethi_cauhoi')
                ->join('cauhoi', 'dethi_cauhoi.MaCH', '=', 'cauhoi.MaCH')
                ->where('dethi_cauhoi.MaDe', $maDe)
                ->orderBy('dethi_cauhoi.ThuTu')
                ->select(
                    'dethi_cauhoi.ThuTu',
                    'cauhoi.MaCH',
                    'cauhoi.NoiDung',
                    'cauhoi.DapAnA',
                    'cauhoi.DapAnB',
                    'cauhoi.DapAnC',
                    'cauhoi.DapAnD',
                    'cauhoi.DapAn',
                    'cauhoi.DoKho'
                )
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'exam' => [
                        'MaDe' => $exam->MaDe,
                        'TenDe' => $exam->TenDe,
                        'ChuDe' => $exam->ChuDe ?? 'Chưa phân loại',
                        'ThoiGianLamBai' => $exam->ThoiGianLamBai,
                        'SoLuongCauHoi' => $exam->SoLuongCauHoi,
                        'MoTa' => $exam->MoTa,
                        'NgayTao' => $exam->NgayTao,
                        'TrangThai' => $exam->TrangThai
                    ],
                    'questions' => $questions
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Cập nhật thông tin đề thi
     */
    public function updateExam(Request $request, $maDe)
    {
        try {
            $user = $request->user();

            // Kiểm tra quyền giáo viên/admin
            if (!in_array($user->Role, ['giaovien', 'admin'])) {
                return response()->json(['success' => false, 'message' => 'Không có quyền sửa đề thi'], 403);
            }

            // Lấy đề thi
            $exam = DB::table('dethi')->where('MaDe', $maDe)->first();

            if (!$exam) {
                return response()->json(['success' => false, 'message' => 'Không tìm thấy đề thi'], 404);
            }

            // Kiểm tra quyền (chỉ giáo viên tạo đề hoặc admin)
            if ($user->Role !== 'admin') {
                // Lấy MaGV của user hiện tại
                $giaoVien = \App\Models\GiaoVien::where('MaTK', $user->MaTK)->first();
                if (!$giaoVien || $exam->MaGV != $giaoVien->MaGV) {
                    return response()->json(['success' => false, 'message' => 'Bạn không có quyền sửa đề thi này'], 403);
                }
            }

            // Validate dữ liệu
            $validator = Validator::make($request->all(), [
                'TenDe' => 'required|string|max:255',
                'ChuDe' => 'nullable|string|max:255',
                'ThoiGianLamBai' => 'required|integer|min:1',
                'MoTa' => 'nullable|string',
                'TrangThai' => 'nullable|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Cập nhật đề thi
            $updateData = [
                'TenDe' => $request->TenDe,
                'ChuDe' => $request->ChuDe ?? $exam->ChuDe,
                'ThoiGianLamBai' => $request->ThoiGianLamBai,
                'MoTa' => $request->MoTa ?? $exam->MoTa,
                'updated_at' => now()
            ];

            // Cập nhật trạng thái nếu được cung cấp
            if ($request->has('TrangThai')) {
                $updateData['TrangThai'] = $request->TrangThai;
            }

            DB::table('dethi')
                ->where('MaDe', $maDe)
                ->update($updateData);

            // Lấy thông tin đề thi sau khi cập nhật
            $updatedExam = DB::table('dethi')->where('MaDe', $maDe)->first();

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật đề thi thành công',
                'data' => $updatedExam
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật đề thi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Public helper: Lấy danh sách đề thi mẫu (không cần authentication)
     * Đây là phương thức được tham chiếu bởi middleware->except(['layDeThiMau'])
     */
    public function layDeThiMau(Request $request)
    {
        try {
            $exams = DeThi::where('TrangThai', true)
                ->with(['giaoVien' => function($q) { $q->select('MaGV','HoTen'); }])
                ->orderBy('NgayTao', 'desc')
                ->take(12)
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Lấy đề thi mẫu thành công',
                'data' => $exams
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi khi lấy đề thi mẫu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa đề thi
     */
    public function destroyExam(Request $request, $maDe)
    {
        try {
            $user = $request->user();

            // Kiểm tra quyền giáo viên/admin
            if (!in_array($user->Role, ['giaovien', 'admin'])) {
                return response()->json(['success' => false, 'message' => 'Không có quyền xóa đề thi'], 403);
            }

            // Lấy đề thi
            $exam = DB::table('dethi')->where('MaDe', $maDe)->first();

            if (!$exam) {
                return response()->json(['success' => false, 'message' => 'Không tìm thấy đề thi'], 404);
            }

            // Kiểm tra quyền (chỉ giáo viên tạo đề hoặc admin)
            if ($user->Role !== 'admin') {
                // Lấy MaGV của user hiện tại
                $giaoVien = \App\Models\GiaoVien::where('MaTK', $user->MaTK)->first();
                if (!$giaoVien || $exam->MaGV != $giaoVien->MaGV) {
                    return response()->json(['success' => false, 'message' => 'Bạn không có quyền xóa đề thi này'], 403);
                }
            }

            // Kiểm tra xem đã có học sinh làm bài chưa
            $submissionCount = DB::table('bailam')->where('MaDe', $maDe)->count();
            
            if ($submissionCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể xóa đề thi đã có học sinh làm bài',
                    'data' => ['submissionCount' => $submissionCount]
                ], 400);
            }

            DB::beginTransaction();

            // Xóa các câu hỏi liên quan trong dethi_cauhoi
            DB::table('dethi_cauhoi')->where('MaDe', $maDe)->delete();

            // Xóa đề thi
            DB::table('dethi')->where('MaDe', $maDe)->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Xóa đề thi thành công'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa đề thi',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
