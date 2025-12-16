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
            
            // Kiểm tra role cho các action thêm/sửa/xóa/import
            // Lưu ý: importJson cũng cần quyền giáo viên
            if (in_array($request->route()->getActionMethod(), ['store', 'update', 'destroy', 'importJson'])) {
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
     * Lấy danh sách câu hỏi (UR-03.1)
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 15);
            
            // Hỗ trợ cả MaMon (theo thói quen cũ) và MaNH (chuẩn database)
            $maNH = $request->input('MaNH') ?? $request->input('MaMon');
            
            // Hỗ trợ cả MucDo và DoKho
            $doKho = $request->input('DoKho') ?? $request->input('MucDo');
            
            // Map MucDo values: De, TrungBinh, Kho -> De, TB, Kho
            if ($doKho === 'TrungBinh') {
                $doKho = 'TB';
            }
            
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
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy danh sách câu hỏi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * [MỚI] UR-03.2: Nhập hàng loạt câu hỏi (Import JSON)
     */
    public function importJson(Request $request)
    {
        $data = $request->json()->all(); // Nhận mảng JSON từ body
        
        if (!is_array($data)) {
            return response()->json(['success' => false, 'message' => 'Dữ liệu phải là danh sách (mảng)'], 400);
        }

        $count = 0;
        $errors = [];

        foreach ($data as $index => $item) {
            try {
                // TẠO MÃ 10 KÝ TỰ: CH (2) + Random (5) + Index (3) = 10 ký tự
                // Ví dụ: CH12345001 - Đảm bảo không quá 10 ký tự database
                $maCH = 'CH' . rand(10000, 99999) . str_pad($index % 1000, 3, '0', STR_PAD_LEFT);

                // Map dữ liệu (Linh hoạt xử lý MucDo và DapAn)
                // Chuyển "TrungBinh" -> "TB"
                $mucDo = isset($item['MucDo']) && $item['MucDo'] == 'TrungBinh' ? 'TB' : ($item['MucDo'] ?? 'TB');
                // Chấp nhận cả DapAnDung hoặc DapAn
                $dapAnDung = $item['DapAnDung'] ?? $item['DapAn'] ?? 'A';
                // Chấp nhận cả MaNH hoặc MaMon
                $maNH = $item['MaNH'] ?? $item['MaMon'] ?? null;

                if (!$maNH) {
                    throw new \Exception("Thiếu thông tin Mã ngân hàng (MaNH)");
                }

                CauHoi::create([
                    'MaCH' => $maCH,
                    'NoiDung' => $item['NoiDung'],
                    'DapAnA' => $item['DapAnA'],
                    'DapAnB' => $item['DapAnB'],
                    'DapAnC' => $item['DapAnC'],
                    'DapAnD' => $item['DapAnD'],
                    'DapAn'  => $dapAnDung,
                    'DoKho'  => $mucDo,
                    'MaNH'   => $maNH
                ]);
                $count++;
            } catch (\Exception $e) {
                $errors[] = "Lỗi tại dòng " . ($index + 1) . ": " . $e->getMessage();
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Đã nhập thành công $count câu hỏi.",
            'errors' => $errors // Trả về lỗi nếu có câu nào bị sai
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * Thêm câu hỏi mới (UR-03.1)
     */
    public function store(Request $request)
    {
        // 1. Validate theo đúng Báo cáo
        $validator = Validator::make($request->all(), [
            'NoiDung' => 'required|string',
            'DapAnA' => 'required|string',
            'DapAnB' => 'required|string',
            'DapAnC' => 'required|string',
            'DapAnD' => 'required|string',
            'DapAnDung' => 'required|in:A,B,C,D',
            'MucDo' => 'required|in:De,TrungBinh,Kho,TB', // Chấp nhận cả TB
            'MaNH' => 'required|exists:NganHangCauHoi,MaNH'
        ], [
            'MaNH.exists' => 'Mã ngân hàng câu hỏi không tồn tại (Hãy tạo NH01 trong DB trước)',
            'MaNH.required' => 'Vui lòng chọn chủ đề (Ngân hàng câu hỏi)'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // 2. Tạo câu hỏi mới
            $cauHoi = new CauHoi();
            
            // Sinh mã ngẫu nhiên 8 số -> Tổng 10 ký tự (CH + 8 số)
            $cauHoi->MaCH = 'CH' . rand(10000000, 99999999);
            
            $cauHoi->NoiDung = $request->NoiDung;
            $cauHoi->DapAnA = $request->DapAnA; 
            $cauHoi->DapAnB = $request->DapAnB;
            $cauHoi->DapAnC = $request->DapAnC;
            $cauHoi->DapAnD = $request->DapAnD;
            $cauHoi->DapAn = $request->DapAnDung;
            
            // Xử lý MucDo -> DoKho (TrungBinh -> TB)
            $cauHoi->DoKho = ($request->MucDo === 'TrungBinh') ? 'TB' : $request->MucDo;
            
            $cauHoi->MaNH = $request->MaNH;
            
            $cauHoi->save();

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
     * Sửa câu hỏi (UR-03.1)
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
            'DapAnDung' => 'sometimes|required|string|in:A,B,C,D',
            'MucDo' => 'sometimes|required|in:De,TrungBinh,Kho,TB',
            'MaNH' => 'sometimes|string|exists:NganHangCauHoi,MaNH',
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
            if ($request->has('NoiDung')) $cauHoi->NoiDung = $request->NoiDung;
            if ($request->has('DapAnA')) $cauHoi->DapAnA = $request->DapAnA;
            if ($request->has('DapAnB')) $cauHoi->DapAnB = $request->DapAnB;
            if ($request->has('DapAnC')) $cauHoi->DapAnC = $request->DapAnC;
            if ($request->has('DapAnD')) $cauHoi->DapAnD = $request->DapAnD;
            
            // Xử lý DapAnDung -> DapAn
            if ($request->has('DapAnDung')) {
                $cauHoi->DapAn = $request->DapAnDung;
            } elseif ($request->has('DapAn')) {
                $cauHoi->DapAn = $request->DapAn;
            }
            
            // Xử lý MucDo -> DoKho
            if ($request->has('MucDo')) {
                // Nếu gửi lên là "TrungBinh" thì đổi thành "TB" để khớp Database
                $cauHoi->DoKho = ($request->MucDo === 'TrungBinh') ? 'TB' : $request->MucDo;
            }
            
            // Xử lý MaNH
            if ($request->has('MaNH')) {
                $cauHoi->MaNH = $request->MaNH;
            }
            
            $cauHoi->save();

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
     * Xóa câu hỏi (UR-03.1)
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
            // Có thể kiểm tra ràng buộc trước khi xóa
            // ...
            
            $cauHoi->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Xóa câu hỏi thành công'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa câu hỏi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * UR-03.2: Export câu hỏi ra file (CSV, JSON, Excel format)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function export(Request $request)
    {
        try {
            $format = $request->input('format', 'json'); // json, csv, excel
            $maNH = $request->input('MaNH') ?? $request->input('MaMon');
            $doKho = $request->input('DoKho') ?? $request->input('MucDo');
            
            // Map độ khó
            if ($doKho === 'TrungBinh') {
                $doKho = 'TB';
            }
            
            // Query câu hỏi
            $query = CauHoi::with('nganHangCauHoi');
            
            if ($maNH) {
                $query->where('MaNH', $maNH);
            }
            
            if ($doKho) {
                $query->where('DoKho', $doKho);
            }
            
            $cauHoi = $query->orderBy('created_at', 'desc')->get();
            
            if ($cauHoi->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không có câu hỏi nào để xuất'
                ], 404);
            }
            
            // Format data
            $exportData = $cauHoi->map(function($ch) {
                return [
                    'MaCH' => $ch->MaCH,
                    'NoiDung' => $ch->NoiDung,
                    'DapAn1' => $ch->DapAn1,
                    'DapAn2' => $ch->DapAn2,
                    'DapAn3' => $ch->DapAn3,
                    'DapAn4' => $ch->DapAn4,
                    'DapAn' => $ch->DapAn,
                    'MaNH' => $ch->MaNH,
                    'DoKho' => $ch->DoKho,
                ];
            });
            
            // Export theo format
            switch (strtolower($format)) {
                case 'csv':
                    return $this->exportCSV($exportData);
                    
                case 'excel':
                case 'xlsx':
                    return $this->exportExcel($exportData);
                    
                case 'json':
                default:
                    return response()->json([
                        'success' => true,
                        'message' => 'Xuất dữ liệu thành công',
                        'data' => $exportData,
                        'count' => $exportData->count()
                    ], 200);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xuất câu hỏi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export to CSV format
     */
    private function exportCSV($data)
    {
        $filename = 'cau-hoi-' . date('Y-m-d-His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];
        
        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header row
            fputcsv($file, ['MaCH', 'NoiDung', 'DapAn1', 'DapAn2', 'DapAn3', 'DapAn4', 'DapAn', 'MaNH', 'DoKho']);
            
            // Data rows
            foreach ($data as $row) {
                fputcsv($file, [
                    $row['MaCH'],
                    $row['NoiDung'],
                    $row['DapAn1'],
                    $row['DapAn2'],
                    $row['DapAn3'],
                    $row['DapAn4'],
                    $row['DapAn'],
                    $row['MaNH'],
                    $row['DoKho']
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export to Excel format (using CSV with .xlsx extension)
     * For proper Excel, consider using maatwebsite/excel package
     */
    private function exportExcel($data)
    {
        // Simple Excel export (actually CSV with .xlsx name)
        // For production, use: composer require maatwebsite/excel
        
        $filename = 'cau-hoi-' . date('Y-m-d-His') . '.xlsx';
        
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];
        
        // For now, return as CSV (can upgrade to real Excel later)
        return $this->exportCSV($data);
    }
}
