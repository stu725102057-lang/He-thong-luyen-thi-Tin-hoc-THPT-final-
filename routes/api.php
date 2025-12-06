<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CauHoiController;
use App\Http\Controllers\BaiThiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// ============================================
// PUBLIC ROUTES - Không cần authentication
// ============================================
Route::post('/login', [AuthController::class, 'login']);

// ============================================
// PROTECTED ROUTES - Yêu cầu authentication
// ============================================
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', function (Request $request) {
        return $request->user();
    } 
);
Route::post('/nop-bai', [BaiThiController::class, 'nopBai']);
Route::post('/luu-nhap', [\App\Http\Controllers\BaiThiController::class, 'luuBaiLam']);
    
    // ============================================
    // CÂU HỎI ROUTES - CRUD
    // ============================================
    // Chỉ admin và giaovien mới được thêm/sửa/xóa (kiểm tra trong controller)
    Route::apiResource('cau-hoi', CauHoiController::class);
    
    // ============================================
    // BÀI THI ROUTES - Nộp bài và xem kết quả
    // ============================================
    Route::post('/baithi/nop', [BaiThiController::class, 'nopBai']);
    Route::get('/baithi/{maBaiLam}/ketqua', [BaiThiController::class, 'getKetQua']);
    
    // Các route bảo mật khác sẽ viết ở đây
});
