<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CauHoiController;
use App\Http\Controllers\BaiThiController;
use App\Http\Controllers\DeThiController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BackupController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ============================================
// PUBLIC ROUTES - Không cần authentication (Ai cũng truy cập được)
// ============================================
Route::post('/login', [AuthController::class, 'login']);

// UR-01.2: Đăng ký tài khoản (Self-registration)
Route::post('/register', [AuthController::class, 'register']);

// UR-01.3: Khôi phục mật khẩu
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// UR-01.4: Khách xem danh sách đề thi mẫu (MỚI THÊM)
// Route này nằm NGOÀI nhóm middleware auth:sanctum nên không cần Token
Route::get('/de-thi-mau', [DeThiController::class, 'layDeThiMau']);


// ============================================
// PROTECTED ROUTES - Yêu cầu authentication (Phải có Token)
// ============================================
Route::middleware('auth:sanctum')->group(function () {
    
    // Lấy thông tin user hiện tại với detail (MaHS, MaGV)
    Route::get('/me', [AuthController::class, 'me']);
    
    // ============================================
    // CÂU HỎI ROUTES - CRUD (UR-03.1 & UR-03.2)
    // ============================================
    // UR-03.2: Nhập câu hỏi hàng loạt
    Route::post('/cau-hoi/import', [CauHoiController::class, 'importJson']); 
    
    // UR-03.2: Xuất câu hỏi (Export) - NEW
    Route::get('/cau-hoi/export', [CauHoiController::class, 'export']);
    
    // Quản lý ngân hàng câu hỏi (Thêm/Sửa/Xóa/Xem)
    Route::get('/cau-hoi', [CauHoiController::class, 'index']);   
    Route::post('/cau-hoi', [CauHoiController::class, 'store']);   
    Route::put('/cau-hoi/{id}', [CauHoiController::class, 'update']); 
    Route::delete('/cau-hoi/{id}', [CauHoiController::class, 'destroy']); 
    
    // ============================================
    // BÀI THI ROUTES - Nộp bài và xem kết quả (UR-02)
    // ============================================
    Route::post('/baithi/nop', [BaiThiController::class, 'nopBai']); 
    Route::get('/baithi/{maBaiLam}/ketqua', [BaiThiController::class, 'getKetQua']);
    Route::post('/luu-nhap', [BaiThiController::class, 'luuBaiLam']); // Tự động lưu (UR-05.2)
    Route::get('/lich-su-thi', [BaiThiController::class, 'layLichSuThi']); // Xem lịch sử thi (UR-02.5)
    
    // ============================================
    // THỐNG KÊ ROUTES (UR-03)
    // ============================================
    // QUAN TRỌNG: Các route cụ thể phải đặt TRƯỚC route với parameter {maDe}
    // để tránh Laravel match nhầm "ca-nhan" và "lop-hoc" thành {maDe}
    
    // UR-03.1: Thống kê cá nhân học sinh
    Route::get('/thong-ke/ca-nhan', [BaiThiController::class, 'thongKeCanhan']);
    
    // UR-03.5: Thống kê lớp học (Dashboard giáo viên)
    Route::get('/thong-ke/lop-hoc', [DeThiController::class, 'getClassStatistics']);
    
    // UR-03.5: Thống kê theo đề thi (với parameter {maDe})
    Route::get('/thong-ke/{maDe}', [DeThiController::class, 'thongKeKetQua']);
    
    // ============================================
    // ĐỀ THI ROUTES - Quản lý đề thi (Giáo viên) (UR-03.3 & UR-03.4)
    // ============================================
    Route::post('/tao-de-thi', [DeThiController::class, 'taoDeThi']);
    Route::post('/tao-de-thi-ngau-nhien', [DeThiController::class, 'taoDeThiNgauNhien']); // UR-03.4
    Route::post('/de-thi/manual', [DeThiController::class, 'createManualExam']); // UR-03.3: Tạo đề thủ công
    Route::get('/de-thi/teacher', [DeThiController::class, 'getTeacherExams']); // Danh sách đề thi của GV
    Route::get('/de-thi/{maDe}/detail', [DeThiController::class, 'getExamDetail']); // Chi tiết đề thi
    Route::put('/de-thi/{maDe}', [DeThiController::class, 'updateExam']); // Sửa đề thi
    Route::delete('/de-thi/{maDe}', [DeThiController::class, 'destroyExam']); // Xóa đề thi
    
    // ============================================
    // ĐỀ THI ROUTES - Học sinh làm bài (UR-02.1)
    // ============================================
    Route::get('/de-thi', [DeThiController::class, 'layDanhSachDeThi']); // Danh sách đề thi
    Route::get('/de-thi/{maDe}', [DeThiController::class, 'layChiTietDeThi']); // Chi tiết đề thi
    Route::post('/de-thi/{maDe}/bat-dau', [DeThiController::class, 'batDauLamBai']); // Bắt đầu làm bài
    
    // ============================================
    // BÀI LÀM ROUTES - Nộp bài và xem kết quả (UR-02.2 & UR-02.3)
    // ============================================
    Route::post('/bai-lam/nop-bai', [BaiThiController::class, 'nopBai']); // Nộp bài
    Route::post('/bai-lam/luu-nhap', [BaiThiController::class, 'luuBaiLam']); // Lưu nháp (auto-save)
    Route::get('/bai-lam/{maBaiLam}/chi-tiet', [BaiThiController::class, 'chiTietBaiLam']); // Xem chi tiết bài làm
    Route::get('/bai-lam/{maBaiLam}/ket-qua', [BaiThiController::class, 'xemKetQua']); // Xem kết quả sau khi nộp
    
    // ============================================
    // CHEATING DETECTION - Ghi nhận gian lận (UR-05.1)
    // ============================================
    Route::post('/ghi-nhan-gian-lan', [BaiThiController::class, 'ghiNhanGianLan']);
    
    // ============================================
    // ADMIN ROUTES - Quản lý người dùng (UR-04)
    // ============================================
    // Chỉ Admin mới được truy cập
    Route::get('/users', [UserController::class, 'index']);          // Xem danh sách
    Route::post('/users', [UserController::class, 'store']);         // Tạo tài khoản mới
    Route::put('/users/{id}', [UserController::class, 'update']);    // Sửa thông tin
    Route::patch('/users/{id}/toggle', [UserController::class, 'toggleStatus']); // Khóa/Mở khóa
    Route::delete('/users/{id}', [UserController::class, 'destroy']); // Xóa tài khoản
    
    // ============================================
    // BACKUP & RESTORE (UR-04.4 & UR-04.5)
    // ============================================
    Route::post('/backup', [BackupController::class, 'createBackup']); // Sao lưu dữ liệu
    Route::post('/restore', [BackupController::class, 'restoreBackup']); // Khôi phục dữ liệu
    Route::get('/backups', [BackupController::class, 'listBackups']); // Danh sách backup
    Route::delete('/backups/{maSaoLuu}', [BackupController::class, 'deleteBackup']); // Xóa backup
    
    // ============================================
    // SYSTEM MONITORING (Giám sát hệ thống)
    // ============================================
    Route::get('/system/monitor', [UserController::class, 'getSystemMonitor']); // Lấy metrics giám sát
    Route::get('/system/activities', [UserController::class, 'getRecentActivities']); // Hoạt động gần đây
});

// ============================================
// DOWNLOAD BACKUP - Use standard Bearer token
// ============================================
Route::middleware('auth:sanctum')->get('/backups/{maSaoLuu}/download', [BackupController::class, 'downloadBackup']);