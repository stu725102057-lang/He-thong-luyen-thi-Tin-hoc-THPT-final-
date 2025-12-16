<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\DeThi;
use App\Models\CauHoi;
use Carbon\Carbon;

/**
 * UR-03.4: Random Exam Generation
 * COMPLETE IMPLEMENTATION - Add to DeThiController.php
 */
class DeThiControllerExtension
{
    /**
     * UR-03.4: Tạo đề thi ngẫu nhiên
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function taoDeThiNgauNhien(Request $request)
    {
        // 1. VALIDATE INPUT
        $validator = Validator::make($request->all(), [
            'TenDe' => 'required|string|max:255',
            'MaNH' => 'required|string|exists:NganHangCauHoi,MaNH',
            'SoCauHoi' => 'required|integer|min:1|max:100',
            'DoKho' => 'nullable|string|in:De,TB,Kho',
            'ThoiGianLamBai' => 'required|integer|min:1',
            'MoTa' => 'nullable|string'
        ], [
            'TenDe.required' => 'Tên đề không được để trống',
            'MaNH.required' => 'Mã ngân hàng câu hỏi không được để trống',
            'MaNH.exists' => 'Ngân hàng câu hỏi không tồn tại',
            'SoCauHoi.required' => 'Số câu hỏi không được để trống',
            'SoCauHoi.min' => 'Số câu hỏi phải lớn hơn 0',
            'SoCauHoi.max' => 'Số câu hỏi không được vượt quá 100',
            'ThoiGianLamBai.required' => 'Thời gian làm bài không được để trống',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        // 2. CHECK USER IS TEACHER
        $user = $request->user();
        if ($user->Role !== 'giaovien') {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ giáo viên mới được phép tạo đề thi'
            ], 403);
        }

        try {
            // 3. GET RANDOM QUESTIONS
            $query = CauHoi::where('MaNH', $request->MaNH);
            
            if ($request->has('DoKho') && !empty($request->DoKho)) {
                $query->where('DoKho', $request->DoKho);
            }
            
            $availableQuestions = $query->count();
            
            if ($availableQuestions < $request->SoCauHoi) {
                return response()->json([
                    'success' => false,
                    'message' => "Không đủ câu hỏi trong ngân hàng. Có {$availableQuestions} câu, cần {$request->SoCauHoi} câu"
                ], 400);
            }
            
            $randomQuestions = $query->inRandomOrder()
                ->limit($request->SoCauHoi)
                ->get();

            // 4. CREATE EXAM WITH TRANSACTION
            DB::beginTransaction();
            
            // Generate exam code
            $maDe = $this->generateMaDe();
            
            // Create DeThi record
            $deThi = DeThi::create([
                'MaDe' => $maDe,
                'TenDe' => $request->TenDe,
                'ThoiGianLamBai' => $request->ThoiGianLamBai,
                'MoTa' => $request->MoTa,
                'MaGV' => $user->giaoVien->MaGV,
                'NgayTao' => Carbon::now(),
                'TrangThai' => 'ChuaXuatBan'
            ]);
            
            // Add questions to exam (ChiTietDeThi table)
            foreach ($randomQuestions as $index => $cauHoi) {
                DB::table('ChiTietDeThi')->insert([
                    'MaDe' => $maDe,
                    'MaCH' => $cauHoi->MaCH,
                    'ThuTu' => $index + 1,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }
            
            DB::commit();

            // 5. RETURN SUCCESS
            return response()->json([
                'success' => true,
                'message' => 'Tạo đề thi ngẫu nhiên thành công',
                'data' => [
                    'MaDe' => $deThi->MaDe,
                    'TenDe' => $deThi->TenDe,
                    'SoCauHoi' => $randomQuestions->count(),
                    'ThoiGianLamBai' => $deThi->ThoiGianLamBai,
                    'NgayTao' => $deThi->NgayTao,
                    'CauHoi' => $randomQuestions->map(function($ch) {
                        return [
                            'MaCH' => $ch->MaCH,
                            'NoiDung' => $ch->NoiDung,
                            'DoKho' => $ch->DoKho
                        ];
                    })
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tạo đề thi ngẫu nhiên',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper: Generate unique exam code
     */
    private function generateMaDe()
    {
        $lastDe = DeThi::where('MaDe', 'like', 'DT%')
            ->orderBy('MaDe', 'desc')
            ->first();

        if (!$lastDe) {
            return 'DT001';
        }

        $lastNumber = intval(substr($lastDe->MaDe, 2));
        $newNumber = $lastNumber + 1;

        return 'DT' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * UR-02.1: Get available exams for students
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAvailableExams(Request $request)
    {
        try {
            $query = DeThi::with('giaoVien')
                ->where('TrangThai', 'DaXuatBan'); // Only published exams
            
            // Filter by subject if provided
            if ($request->has('MaMon')) {
                $query->where('MaMon', $request->MaMon);
            }
            
            $exams = $query->orderBy('NgayTao', 'desc')
                ->paginate(12);
            
            return response()->json([
                'success' => true,
                'message' => 'Lấy danh sách đề thi thành công',
                'data' => $exams
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
     * UR-02.1: Get exam with questions for student to take
     * 
     * @param string $maDe
     * @return \Illuminate\Http\JsonResponse
     */
    public function startExam(Request $request, $maDe)
    {
        try {
            $user = $request->user();
            
            if ($user->Role !== 'hocsinh') {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ học sinh mới được phép làm bài thi'
                ], 403);
            }
            
            $deThi = DeThi::with(['cauHoi' => function($query) {
                $query->orderBy('pivot.ThuTu');
            }])->find($maDe);
            
            if (!$deThi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy đề thi'
                ], 404);
            }
            
            if ($deThi->TrangThai !== 'DaXuatBan') {
                return response()->json([
                    'success' => false,
                    'message' => 'Đề thi chưa được xuất bản'
                ], 400);
            }
            
            // Format questions (hide correct answer)
            $questions = $deThi->cauHoi->map(function($ch) {
                return [
                    'MaCH' => $ch->MaCH,
                    'NoiDung' => $ch->NoiDung,
                    'DapAn1' => $ch->DapAn1,
                    'DapAn2' => $ch->DapAn2,
                    'DapAn3' => $ch->DapAn3,
                    'DapAn4' => $ch->DapAn4,
                    // Do NOT return DapAn (correct answer)
                ];
            });
            
            return response()->json([
                'success' => true,
                'message' => 'Lấy đề thi thành công',
                'data' => [
                    'MaDe' => $deThi->MaDe,
                    'TenDe' => $deThi->TenDe,
                    'ThoiGianLamBai' => $deThi->ThoiGianLamBai,
                    'SoCauHoi' => $questions->count(),
                    'CauHoi' => $questions
                ]
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy đề thi',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
