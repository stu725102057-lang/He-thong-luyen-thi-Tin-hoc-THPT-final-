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
            $deThi = DeThi::where('MaDe', $maDe)->with(['giaoVien:MaGV,HoTen'])->first();

            if (!$deThi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy đề thi'
                ], 404);
            }

            $cauHoi = DB::table('ChiTietDeThi')
                ->join('CauHoi', 'ChiTietDeThi.MaCH', '=', 'CauHoi.MaCH')
                ->where('ChiTietDeThi.MaDe', $maDe)
                ->select('CauHoi.*', 'ChiTietDeThi.STT')
                ->orderBy('ChiTietDeThi.STT')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Lấy chi tiết đề thi thành công',
                'data' => [
                    'deThi' => $deThi,
                    'cauHoi' => $cauHoi
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra',
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
            $user = $request->user();

            $deThi = DeThi::where('MaDe', $maDe)->first();
            if (!$deThi) {
                return response()->json(['success' => false, 'message' => 'Không tìm thấy đề thi'], 404);
            }

            $existingBaiThi = DB::table('BaiThi')
                ->where('MaTK', $user->MaTK)
                ->where('MaDe', $maDe)
                ->whereIn('TrangThai', ['danglam', 'hoanthanh'])
                ->first();

            if ($existingBaiThi) {
                if ($existingBaiThi->TrangThai === 'hoanthanh') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Bạn đã hoàn thành đề thi này rồi',
                        'data' => ['MaBT' => $existingBaiThi->MaBT]
                    ], 400);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Tiếp tục làm bài thi',
                    'data' => ['MaBT' => $existingBaiThi->MaBT, 'ThoiGianBatDau' => $existingBaiThi->ThoiGianBatDau]
                ], 200);
            }

            $maBT = 'BT' . str_pad(DB::table('BaiThi')->count() + 1, 5, '0', STR_PAD_LEFT);

            DB::table('BaiThi')->insert([
                'MaBT' => $maBT,
                'MaTK' => $user->MaTK,
                'MaDe' => $maDe,
                'ThoiGianBatDau' => now(),
                'TrangThai' => 'danglam',
                'Diem' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Bắt đầu làm bài thi thành công',
                'data' => ['MaBT' => $maBT, 'ThoiGianBatDau' => now()]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra',
                'error' => $e->getMessage()
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

            $maDe = 'DE' . str_pad(DeThi::count() + 1, 3, '0', STR_PAD_LEFT);

            $deThi = DeThi::create([
                'MaDe' => $maDe,
                'TenDe' => $request->TenDe,
                'ChuDe' => $request->ChuDe,
                'ThoiGianLamBai' => $request->ThoiGianLamBai,
                'SoLuongCauHoi' => $request->SoLuongCauHoi,
                'MoTa' => $request->MoTa ?? '',
                'MaGV' => $user->MaTK,
                'TrangThai' => 1
            ]);

            DB::commit();

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

            DB::beginTransaction();

            $query = CauHoi::query();

            if ($request->MaNH) {
                $query->where('MaNH', $request->MaNH);
            }

            if ($request->DoKho) {
                $query->where('DoKho', $request->DoKho);
            }

            $availableQuestions = $query->get();

            if ($availableQuestions->count() < $request->SoLuongCauHoi) {
                return response()->json([
                    'success' => false,
                    'message' => "Không đủ câu hỏi. Có {$availableQuestions->count()} câu, cần {$request->SoLuongCauHoi} câu"
                ], 400);
            }

            $selectedQuestions = $availableQuestions->random($request->SoLuongCauHoi);

            $maDe = 'DE' . str_pad(DeThi::count() + 1, 3, '0', STR_PAD_LEFT);

            $deThi = DeThi::create([
                'MaDe' => $maDe,
                'TenDe' => $request->TenDe,
                'ChuDe' => $request->ChuDe,
                'ThoiGianLamBai' => $request->ThoiGianLamBai,
                'SoLuongCauHoi' => $request->SoLuongCauHoi,
                'MoTa' => $request->MoTa ?? '',
                'MaGV' => $user->MaTK,
                'TrangThai' => 1
            ]);

            foreach ($selectedQuestions as $index => $question) {
                DB::table('ChiTietDeThi')->insert([
                    'MaDe' => $maDe,
                    'MaCH' => $question->MaCH,
                    'STT' => $index + 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
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
            $validator = Validator::make($request->all(), [
                'TenDe' => 'required|string|max:255',
                'ChuDe' => 'required|string|max:255',
                'ThoiGianLamBai' => 'required|integer|min:1',
                'DanhSachCauHoi' => 'required|array|min:1',
                'DanhSachCauHoi.*' => 'string|exists:CauHoi,MaCH',
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

            $maDe = 'DE' . str_pad(DeThi::count() + 1, 3, '0', STR_PAD_LEFT);

            $deThi = DeThi::create([
                'MaDe' => $maDe,
                'TenDe' => $request->TenDe,
                'ChuDe' => $request->ChuDe,
                'ThoiGianLamBai' => $request->ThoiGianLamBai,
                'SoLuongCauHoi' => count($request->DanhSachCauHoi),
                'MoTa' => $request->MoTa ?? '',
                'MaGV' => $user->MaTK,
                'TrangThai' => 1
            ]);

            foreach ($request->DanhSachCauHoi as $index => $maCH) {
                DB::table('ChiTietDeThi')->insert([
                    'MaDe' => $maDe,
                    'MaCH' => $maCH,
                    'STT' => $index + 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
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
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tạo đề thi',
                'error' => $e->getMessage()
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

            $students = DB::table('TaiKhoan')->where('Role', 'hocsinh')->select('MaTK', 'TenTK', 'Email')->get();

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

            $studentScores = DB::table('BaiThi')
                ->join('TaiKhoan', 'BaiThi.MaTK', '=', 'TaiKhoan.MaTK')
                ->select('TaiKhoan.MaTK', 'TaiKhoan.TenTK', 'TaiKhoan.Email',
                    DB::raw('AVG(BaiThi.Diem) as avg_score'),
                    DB::raw('MAX(BaiThi.Diem) as max_score'),
                    DB::raw('MIN(BaiThi.Diem) as min_score'),
                    DB::raw('COUNT(*) as total_exams'))
                ->where('TaiKhoan.Role', 'hocsinh')
                ->where('BaiThi.TrangThai', 'hoanthanh')
                ->groupBy('TaiKhoan.MaTK', 'TaiKhoan.TenTK', 'TaiKhoan.Email')
                ->get();

            $totalStudents = $students->count();
            $studentsWithScores = $studentScores->count();
            $averageScore = $studentsWithScores > 0 ? round($studentScores->avg('avg_score'), 2) : 0;
            $passedStudents = $studentScores->filter(fn($s) => $s->avg_score >= 5)->count();
            $passRate = $studentsWithScores > 0 ? round(($passedStudents / $studentsWithScores) * 100, 2) : 0;
            $totalExams = DB::table('BaiThi')->where('TrangThai', 'hoanthanh')->count();

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
}
