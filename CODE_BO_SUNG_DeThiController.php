<?php
// FILE NÀY CHỨA CODE BỔ SUNG CHO DeThiController.php
// Copy các methods này vào cuối class DeThiController (trước dấu } cuối cùng)

/**
 * UR-02.1: Lấy danh sách tất cả đề thi có sẵn cho học sinh
 * Yêu cầu đăng nhập
 */
public function layDanhSachDeThi(Request $request)
{
    try {
        // Lấy danh sách đề thi đang hoạt động (TrangThai = true)
        $query = DeThi::where('TrangThai', true);

        // Lọc theo tên đề (nếu có)
        if ($request->has('search') && !empty($request->search)) {
            $query->where('TenDe', 'like', '%' . $request->search . '%');
        }

        // Sắp xếp theo ngày tạo mới nhất
        $query->orderBy('NgayTao', 'desc');

        // Lấy thông tin giáo viên tạo đề
        $query->with(['giaoVien:MaGV,HoTen']);

        // Phân trang hoặc lấy tất cả
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
 * Hiển thị thông tin để học sinh xem trước khi bắt đầu làm
 */
public function layChiTietDeThi(Request $request, $maDe)
{
    try {
        $deThi = DeThi::with(['giaoVien:MaGV,HoTen'])
                      ->where('MaDe', $maDe)
                      ->first();

        if (!$deThi) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đề thi'
            ], 404);
        }

        // Kiểm tra xem học sinh đã làm đề này chưa
        $user = $request->user();
        $daLam = false;
        $lanLamGanNhat = null;

        if ($user->Role === 'hocsinh') {
            $hocSinh = \App\Models\HocSinh::where('MaTK', $user->MaTK)->first();
            if ($hocSinh) {
                $ketQuaGanNhat = \App\Models\KetQua::where('MaHS', $hocSinh->MaHS)
                                                    ->where('MaDe', $maDe)
                                                    ->latest('NgayLamBai')
                                                    ->first();
                if ($ketQuaGanNhat) {
                    $daLam = true;
                    $lanLamGanNhat = [
                        'NgayLamBai' => $ketQuaGanNhat->NgayLamBai,
                        'Diem' => $ketQuaGanNhat->Diem,
                        'SoCauDung' => $ketQuaGanNhat->SoCauDung
                    ];
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Lấy thông tin đề thi thành công',
            'data' => [
                'MaDe' => $deThi->MaDe,
                'TenDe' => $deThi->TenDe,
                'MoTa' => $deThi->MoTa,
                'ThoiGianLamBai' => $deThi->ThoiGianLamBai,
                'SoLuongCauHoi' => $deThi->SoLuongCauHoi,
                'NgayTao' => $deThi->NgayTao,
                'GiaoVien' => $deThi->giaoVien ? $deThi->giaoVien->HoTen : 'N/A',
                'DaLam' => $daLam,
                'LanLamGanNhat' => $lanLamGanNhat
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
 * UR-02.1 & UR-02.2: Bắt đầu làm bài thi
 * Tạo bản ghi bài làm mới và trả về câu hỏi
 */
public function batDauLamBai(Request $request, $maDe)
{
    try {
        // 1. Kiểm tra đề thi có tồn tại không
        $deThi = DeThi::where('MaDe', $maDe)->first();
        
        if (!$deThi) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đề thi'
            ], 404);
        }

        if (!$deThi->TrangThai) {
            return response()->json([
                'success' => false,
                'message' => 'Đề thi này đã bị vô hiệu hóa'
            ], 403);
        }

        // 2. Lấy thông tin học sinh
        $user = $request->user();
        
        if ($user->Role !== 'hocsinh') {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ học sinh mới có thể làm bài thi'
            ], 403);
        }

        $hocSinh = \App\Models\HocSinh::where('MaTK', $user->MaTK)->first();
        
        if (!$hocSinh) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin học sinh'
            ], 404);
        }

        // 3. Tạo mã bài làm tự động
        $lastBaiLam = \App\Models\BaiLam::orderBy('MaBaiLam', 'desc')->first();
        if ($lastBaiLam && preg_match('/BL(\d+)/', $lastBaiLam->MaBaiLam, $matches)) {
            $newNumber = intval($matches[1]) + 1;
        } else {
            $newNumber = 1;
        }
        $maBaiLam = 'BL' . str_pad($newNumber, 5, '0', STR_PAD_LEFT);

        // 4. Lấy danh sách câu hỏi của đề thi
        $cauHois = DB::table('ChiTietDeThi')
                     ->join('CauHoi', 'ChiTietDeThi.MaCauHoi', '=', 'CauHoi.MaCauHoi')
                     ->where('ChiTietDeThi.MaDe', $maDe)
                     ->select('CauHoi.*')
                     ->get();

        if ($cauHois->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Đề thi chưa có câu hỏi nào'
            ], 400);
        }

        // 5. Tạo bản ghi bài làm mới
        $baiLam = \App\Models\BaiLam::create([
            'MaBaiLam' => $maBaiLam,
            'MaHS' => $hocSinh->MaHS,
            'MaDe' => $maDe,
            'ThoiGianBatDau' => \Carbon\Carbon::now(),
            'TrangThai' => 'dangLam' // Trạng thái: dangLam, daHoanThanh
        ]);

        // 6. Format câu hỏi (ẩn đáp án đúng)
        $cauHoiFormatted = $cauHois->map(function ($cauHoi) {
            return [
                'MaCauHoi' => $cauHoi->MaCauHoi,
                'NoiDung' => $cauHoi->NoiDung,
                'DapAnA' => $cauHoi->DapAnA,
                'DapAnB' => $cauHoi->DapAnB,
                'DapAnC' => $cauHoi->DapAnC,
                'DapAnD' => $cauHoi->DapAnD,
                // KHÔNG GỬI DapAnDung cho học sinh
                'DoKho' => $cauHoi->DoKho
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Bắt đầu làm bài thành công',
            'data' => [
                'MaBaiLam' => $maBaiLam,
                'DeThi' => [
                    'MaDe' => $deThi->MaDe,
                    'TenDe' => $deThi->TenDe,
                    'ThoiGianLamBai' => $deThi->ThoiGianLamBai,
                    'SoLuongCauHoi' => $deThi->SoLuongCauHoi
                ],
                'ThoiGianBatDau' => $baiLam->ThoiGianBatDau,
                'ThoiGianKetThuc' => \Carbon\Carbon::parse($baiLam->ThoiGianBatDau)->addMinutes($deThi->ThoiGianLamBai),
                'CauHoi' => $cauHoiFormatted
            ]
        ], 201);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Có lỗi xảy ra khi bắt đầu làm bài',
            'error' => $e->getMessage()
        ], 500);
    }
}
