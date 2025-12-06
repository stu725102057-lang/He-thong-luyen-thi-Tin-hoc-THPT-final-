<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\TaiKhoan;
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
}
