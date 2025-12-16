<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\TaiKhoan;
use App\Models\HocSinh;
use App\Models\GiaoVien;
use App\Models\QuanTriVien;
use Carbon\Carbon;

class AuthController extends Controller
{
    /**
     * UC1: Đăng nhập
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // 1. VALIDATE DỮ LIỆU ĐẦU VÀO
        $validator = Validator::make($request->all(), [
            'TenDangNhap' => 'required|string',
            'MatKhau' => 'required|string',
        ], [
            'TenDangNhap.required' => 'Tên đăng nhập không được để trống',
            'MatKhau.required' => 'Mật khẩu không được để trống',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        // 2. TÌM TÀI KHOẢN TRONG DATABASE
        $taiKhoan = TaiKhoan::where('TenDangNhap', $request->TenDangNhap)->first();

        // Kiểm tra tài khoản có tồn tại không
        if (!$taiKhoan) {
            return response()->json([
                'success' => false,
                'message' => 'Tên đăng nhập hoặc mật khẩu không đúng'
            ], 401);
        }

        // 3. KIỂM TRA MẬT KHẨU
        if (!Hash::check($request->MatKhau, $taiKhoan->MatKhau)) {
            return response()->json([
                'success' => false,
                'message' => 'Tên đăng nhập hoặc mật khẩu không đúng'
            ], 401);
        }

        // 4. KIỂM TRA TRẠNG THÁI TÀI KHOẢN
        if (!$taiKhoan->TrangThai) {
            return response()->json([
                'success' => false,
                'message' => 'Tài khoản đã bị khóa. Vui lòng liên hệ quản trị viên'
            ], 403);
        }

        // 5. CẬP NHẬT LẦN ĐĂNG NHẬP CUỐI
        $taiKhoan->update([
            'LanDangNhapCuoi' => Carbon::now()
        ]);

        // 6. TẠO TOKEN (Sanctum)
        $token = $taiKhoan->createToken('auth_token')->plainTextToken;

        // 7. LẤY THÔNG TIN CHI TIẾT THEO ROLE
        $userDetail = $this->getUserDetailByRole($taiKhoan);

        // 8. TRẢ VỀ RESPONSE THÀNH CÔNG
        return response()->json([
            'success' => true,
            'message' => 'Đăng nhập thành công',
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
                'user' => [
                    'MaTK' => $taiKhoan->MaTK,
                    'TenDangNhap' => $taiKhoan->TenDangNhap,
                    'Email' => $taiKhoan->Email,
                    'Role' => $taiKhoan->Role,
                    'TrangThai' => $taiKhoan->TrangThai,
                    'LanDangNhapCuoi' => $taiKhoan->LanDangNhapCuoi,
                ],
                'detail' => $userDetail
            ]
        ], 200);
    }

    /**
     * Lấy thông tin chi tiết user theo Role
     * 
     * @param TaiKhoan $taiKhoan
     * @return array|null
     */
    private function getUserDetailByRole(TaiKhoan $taiKhoan)
    {
        switch ($taiKhoan->Role) {
            case 'admin':
                $quanTriVien = $taiKhoan->quanTriVien;
                return $quanTriVien ? [
                    'MaQTV' => $quanTriVien->MaQTV,
                ] : null;

            case 'giaovien':
                $giaoVien = $taiKhoan->giaoVien;
                return $giaoVien ? [
                    'MaGV' => $giaoVien->MaGV,
                    'HoTen' => $giaoVien->HoTen,
                    'SoDienThoai' => $giaoVien->SoDienThoai,
                    'ChuyenMon' => $giaoVien->ChuyenMon,
                ] : null;

            case 'hocsinh':
                $hocSinh = $taiKhoan->hocSinh;
                return $hocSinh ? [
                    'MaHS' => $hocSinh->MaHS,
                    'HoTen' => $hocSinh->HoTen,
                    'Lop' => $hocSinh->Lop,
                    'Truong' => $hocSinh->Truong,
                ] : null;

            default:
                return null;
        }
    }

    /**
     * UC2: Đăng xuất
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        // Xóa token hiện tại của user
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đăng xuất thành công'
        ], 200);
    }

    /**
     * Lấy thông tin user hiện tại
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request)
    {
        $taiKhoan = $request->user();
        $userDetail = $this->getUserDetailByRole($taiKhoan);

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'MaTK' => $taiKhoan->MaTK,
                    'TenDangNhap' => $taiKhoan->TenDangNhap,
                    'Email' => $taiKhoan->Email,
                    'Role' => $taiKhoan->Role,
                    'TrangThai' => $taiKhoan->TrangThai,
                    'LanDangNhapCuoi' => $taiKhoan->LanDangNhapCuoi,
                ],
                'detail' => $userDetail
            ]
        ], 200);
    }

    /**
     * UR-01.2: Đăng ký tài khoản (Self-registration for Students)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        // 1. VALIDATE DỮ LIỆU ĐẦU VÀO
        $validator = Validator::make($request->all(), [
            'TenDangNhap' => 'required|string|unique:TaiKhoan,TenDangNhap|min:3|max:50',
            'Email' => 'required|email|unique:TaiKhoan,Email',
            'MatKhau' => 'required|string|min:6|max:100',
            'HoTen' => 'required|string|max:100',
            'Lop' => 'nullable|string|max:50',
            'Truong' => 'nullable|string|max:200',
        ], [
            'TenDangNhap.required' => 'Tên đăng nhập không được để trống',
            'TenDangNhap.unique' => 'Tên đăng nhập đã tồn tại',
            'TenDangNhap.min' => 'Tên đăng nhập phải có ít nhất 3 ký tự',
            'Email.required' => 'Email không được để trống',
            'Email.email' => 'Email không đúng định dạng',
            'Email.unique' => 'Email đã được sử dụng',
            'MatKhau.required' => 'Mật khẩu không được để trống',
            'MatKhau.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
            'HoTen.required' => 'Họ tên không được để trống',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // 2. TẠO MÃ TÀI KHOẢN VÀ MÃ HỌC SINH TỰ ĐỘNG
            $maTK = $this->generateMaTK();
            $maHS = $this->generateMaHS();

            // 3. TẠO TÀI KHOẢN
            $taiKhoan = TaiKhoan::create([
                'MaTK' => $maTK,
                'TenDangNhap' => $request->TenDangNhap,
                'Email' => $request->Email,
                'MatKhau' => Hash::make($request->MatKhau), // Mã hóa mật khẩu (UR-05.3)
                'Role' => 'hocsinh', // Đăng ký mặc định là học sinh
                'TrangThai' => true, // Active
                'NgayTao' => Carbon::now(),
            ]);

            // 4. TẠO BẢN GHI HỌC SINH
            $hocSinh = HocSinh::create([
                'MaHS' => $maHS,
                'MaTK' => $maTK,
                'HoTen' => $request->HoTen,
                'Lop' => $request->Lop,
                'Truong' => $request->Truong,
                'NgayThamGia' => Carbon::now(),
            ]);

            DB::commit();

            // 5. TỰ ĐỘNG ĐĂNG NHẬP SAU KHI ĐĂNG KÝ
            $token = $taiKhoan->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Đăng ký tài khoản thành công!',
                'data' => [
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'user' => [
                        'MaTK' => $taiKhoan->MaTK,
                        'TenDangNhap' => $taiKhoan->TenDangNhap,
                        'Email' => $taiKhoan->Email,
                        'Role' => $taiKhoan->Role,
                    ],
                    'detail' => [
                        'MaHS' => $hocSinh->MaHS,
                        'HoTen' => $hocSinh->HoTen,
                        'Lop' => $hocSinh->Lop,
                        'Truong' => $hocSinh->Truong,
                    ]
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi đăng ký tài khoản',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * UR-01.3: Gửi email khôi phục mật khẩu
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgotPassword(Request $request)
    {
        // 1. VALIDATE
        $validator = Validator::make($request->all(), [
            'Email' => 'required|email|exists:TaiKhoan,Email',
        ], [
            'Email.required' => 'Email không được để trống',
            'Email.email' => 'Email không đúng định dạng',
            'Email.exists' => 'Email không tồn tại trong hệ thống',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // 2. TÌM TÀI KHOẢN
            $taiKhoan = TaiKhoan::where('Email', $request->Email)->first();

            // 3. TẠO RESET TOKEN (6 số ngẫu nhiên)
            $resetCode = random_int(100000, 999999);

            // 4. LƯU VÀO DATABASE (Bảng password_resets)
            DB::table('password_resets')->updateOrInsert(
                ['email' => $request->Email],
                [
                    'email' => $request->Email,
                    'token' => Hash::make($resetCode),
                    'created_at' => Carbon::now()
                ]
            );

            // 5. GỬI EMAIL (Tạm thời log ra console, cần config SMTP sau)
            // TODO: Cấu hình SMTP và gửi email thực tế
            \Log::info("Password Reset Code for {$request->Email}: {$resetCode}");

            return response()->json([
                'success' => true,
                'message' => 'Mã khôi phục mật khẩu đã được gửi đến email của bạn',
                // DEV ONLY: Trả về code để test (xóa khi production)
                'reset_code' => $resetCode,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi gửi email khôi phục',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * UR-01.3: Đặt lại mật khẩu bằng reset token
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(Request $request)
    {
        // 1. VALIDATE
        $validator = Validator::make($request->all(), [
            'Email' => 'required|email|exists:TaiKhoan,Email',
            'ResetCode' => 'required|numeric|digits:6',
            'MatKhauMoi' => 'required|string|min:6|max:100',
            'XacNhanMatKhau' => 'required|same:MatKhauMoi',
        ], [
            'Email.required' => 'Email không được để trống',
            'Email.exists' => 'Email không tồn tại',
            'ResetCode.required' => 'Mã khôi phục không được để trống',
            'ResetCode.digits' => 'Mã khôi phục phải là 6 chữ số',
            'MatKhauMoi.required' => 'Mật khẩu mới không được để trống',
            'MatKhauMoi.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
            'XacNhanMatKhau.required' => 'Xác nhận mật khẩu không được để trống',
            'XacNhanMatKhau.same' => 'Xác nhận mật khẩu không khớp',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // 2. KIỂM TRA RESET TOKEN TRONG DATABASE
            $resetRecord = DB::table('password_resets')
                ->where('email', $request->Email)
                ->first();

            if (!$resetRecord) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy yêu cầu khôi phục mật khẩu cho email này'
                ], 404);
            }

            // 3. KIỂM TRA TOKEN CÓ HỢP LỆ KHÔNG (Chỉ 15 phút)
            $tokenAge = Carbon::parse($resetRecord->created_at)->diffInMinutes(Carbon::now());
            if ($tokenAge > 15) {
                DB::table('password_resets')->where('email', $request->Email)->delete();
                return response()->json([
                    'success' => false,
                    'message' => 'Mã khôi phục đã hết hạn. Vui lòng yêu cầu mã mới'
                ], 400);
            }

            // 4. XÁC THỰC MÃ KHÔI PHỤC
            if (!Hash::check($request->ResetCode, $resetRecord->token)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mã khôi phục không đúng'
                ], 400);
            }

            // 5. CẬP NHẬT MẬT KHẨU MỚI
            $taiKhoan = TaiKhoan::where('Email', $request->Email)->first();
            $taiKhoan->update([
                'MatKhau' => Hash::make($request->MatKhauMoi)
            ]);

            // 6. XÓA RESET TOKEN (Chỉ dùng được 1 lần)
            DB::table('password_resets')->where('email', $request->Email)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Đặt lại mật khẩu thành công! Bạn có thể đăng nhập bằng mật khẩu mới'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi đặt lại mật khẩu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper: Tạo mã tài khoản tự động (TK001, TK002, ...)
     */
    private function generateMaTK()
    {
        $lastTK = TaiKhoan::where('MaTK', 'like', 'TK%')
            ->orderBy('MaTK', 'desc')
            ->first();

        if (!$lastTK) {
            return 'TK001';
        }

        $lastNumber = intval(substr($lastTK->MaTK, 2));
        $newNumber = $lastNumber + 1;

        return 'TK' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Helper: Tạo mã học sinh tự động (HS001, HS002, ...)
     */
    private function generateMaHS()
    {
        $lastHS = HocSinh::where('MaHS', 'like', 'HS%')
            ->orderBy('MaHS', 'desc')
            ->first();

        if (!$lastHS) {
            return 'HS001';
        }

        $lastNumber = intval(substr($lastHS->MaHS, 2));
        $newNumber = $lastNumber + 1;

        return 'HS' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }
}
