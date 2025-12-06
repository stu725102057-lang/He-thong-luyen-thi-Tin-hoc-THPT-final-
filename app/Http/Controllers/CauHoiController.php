<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\CauHoi;
use App\Models\NganHangCauHoi;

class CauHoiController extends Controller
{
    /**
     * Constructor - Kiểm tra quyền truy cập
     * Chỉ cho phép giaovien và admin thực hiện thêm/sửa/xóa
     */
    public function __construct()
    {
        // Middleware kiểm tra role cho các action cần bảo vệ
        $this->middleware('auth:sanctum');
        $this->middleware(function ($request, $next) {
            $user = $request->user();
            
            // Kiểm tra role cho các action thêm/sửa/xóa
            if (in_array($request->route()->getActionMethod(), ['store', 'update', 'destroy'])) {
                if (!in_array($user->Role, ['admin', 'giaovien'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Bạn không có quyền thực hiện thao tác này. Chỉ giáo viên và admin mới được phép.'
                    ], 403);
                }
            }
            
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     * Lấy danh sách câu hỏi (có phân trang)
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $maNH = $request->input('MaNH'); // Lọc theo ngân hàng câu hỏi
        $doKho = $request->input('DoKho'); // Lọc theo độ khó
        
        $query = CauHoi::with('nganHangCauHoi');
        
        // Lọc theo ngân hàng câu hỏi
        if ($maNH) {
            $query->where('MaNH', $maNH);
        }
        
        // Lọc theo độ khó
        if ($doKho) {
            $query->where('DoKho', $doKho);
        }
        
        $cauHoi = $query->orderBy('created_at', 'desc')->paginate($perPage);
        
        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách câu hỏi thành công',
            'data' => $cauHoi
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     * Thêm câu hỏi mới
     */
    public function store(Request $request)
    {
        // 1. VALIDATE DỮ LIỆU
        $validator = Validator::make($request->all(), [
            'MaCH' => 'required|string|max:10|unique:CauHoi,MaCH',
            'NoiDung' => 'required|string',
            'DapAnA' => 'required|string',
            'DapAnB' => 'required|string',
            'DapAnC' => 'required|string',
            'DapAnD' => 'required|string',
            'DapAn' => 'required|string|in:A,B,C,D',
            'DoKho' => 'required|in:De,TB,Kho',
            'MaNH' => 'required|string|exists:NganHangCauHoi,MaNH',
        ], [
            'MaCH.required' => 'Mã câu hỏi không được để trống',
            'MaCH.unique' => 'Mã câu hỏi đã tồn tại',
            'NoiDung.required' => 'Nội dung câu hỏi không được để trống',
            'DapAnA.required' => 'Đáp án A không được để trống',
            'DapAnB.required' => 'Đáp án B không được để trống',
            'DapAnC.required' => 'Đáp án C không được để trống',
            'DapAnD.required' => 'Đáp án D không được để trống',
            'DapAn.required' => 'Đáp án đúng không được để trống',
            'DapAn.in' => 'Đáp án đúng phải là A, B, C hoặc D',
            'DoKho.required' => 'Độ khó không được để trống',
            'DoKho.in' => 'Độ khó phải là De, TB hoặc Kho',
            'MaNH.required' => 'Mã ngân hàng câu hỏi không được để trống',
            'MaNH.exists' => 'Ngân hàng câu hỏi không tồn tại',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        // 2. TẠO CÂU HỎI MỚI
        try {
            $cauHoi = CauHoi::create([
                'MaCH' => $request->MaCH,
                'NoiDung' => $request->NoiDung,
                'DapAnA' => $request->DapAnA,
                'DapAnB' => $request->DapAnB,
                'DapAnC' => $request->DapAnC,
                'DapAnD' => $request->DapAnD,
                'DapAn' => $request->DapAn,
                'DoKho' => $request->DoKho,
                'MaNH' => $request->MaNH,
            ]);

            // Load relationship
            $cauHoi->load('nganHangCauHoi');

            return response()->json([
                'success' => true,
                'message' => 'Thêm câu hỏi thành công',
                'data' => $cauHoi
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi thêm câu hỏi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     * Xem chi tiết câu hỏi
     */
    public function show(string $id)
    {
        $cauHoi = CauHoi::with('nganHangCauHoi', 'deThi')->find($id);
        
        if (!$cauHoi) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy câu hỏi'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Lấy thông tin câu hỏi thành công',
            'data' => $cauHoi
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     * Sửa câu hỏi
     */
    public function update(Request $request, string $id)
    {
        // 1. TÌM CÂU HỎI
        $cauHoi = CauHoi::find($id);
        
        if (!$cauHoi) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy câu hỏi'
            ], 404);
        }

        // 2. VALIDATE DỮ LIỆU
        $validator = Validator::make($request->all(), [
            'NoiDung' => 'sometimes|required|string',
            'DapAnA' => 'sometimes|required|string',
            'DapAnB' => 'sometimes|required|string',
            'DapAnC' => 'sometimes|required|string',
            'DapAnD' => 'sometimes|required|string',
            'DapAn' => 'sometimes|required|string|in:A,B,C,D',
            'DoKho' => 'sometimes|required|in:De,TB,Kho',
            'MaNH' => 'sometimes|required|string|exists:NganHangCauHoi,MaNH',
        ], [
            'NoiDung.required' => 'Nội dung câu hỏi không được để trống',
            'DapAn.in' => 'Đáp án đúng phải là A, B, C hoặc D',
            'DoKho.in' => 'Độ khó phải là De, TB hoặc Kho',
            'MaNH.exists' => 'Ngân hàng câu hỏi không tồn tại',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        // 3. CẬP NHẬT CÂU HỎI
        try {
            $cauHoi->update($request->only([
                'NoiDung', 'DapAnA', 'DapAnB', 'DapAnC', 'DapAnD', 
                'DapAn', 'DoKho', 'MaNH'
            ]));

            // Load relationship
            $cauHoi->load('nganHangCauHoi');

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật câu hỏi thành công',
                'data' => $cauHoi
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật câu hỏi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     * Xóa câu hỏi
     */
    public function destroy(string $id)
    {
        $cauHoi = CauHoi::find($id);
        
        if (!$cauHoi) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy câu hỏi'
            ], 404);
        }

        try {
            $cauHoi->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Xóa câu hỏi thành công'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa câu hỏi. Có thể câu hỏi đang được sử dụng trong đề thi.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
