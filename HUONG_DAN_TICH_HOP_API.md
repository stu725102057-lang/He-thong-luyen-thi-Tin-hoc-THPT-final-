# 🚀 HƯỚNG DẪN TÍCH HỢP CODE ĐỂ ĐẠT 100%

## Ngày: 7/12/2025
## Mục tiêu: Hoàn thành 35% còn thiếu

---

## 📋 BƯỚC 1: TÍCH HỢP API CHO MODULE LÀM BÀI

### File: `app/Http/Controllers/DeThiController.php`

**Thêm vào cuối class (trước dấu `}` cuối cùng):**

```php
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
            'message' => 'Có lỗi xảy ra',
            'error' => $e->getMessage()
        ], 500);
    }
}

/**
 * UR-02.1: Lấy thông tin chi tiết đề thi
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
 * UR-02.2: Bắt đầu làm bài thi
 */
public function batDauLamBai(Request $request, $maDe)
{
    try {
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
        
        // Tạo mã bài làm
        $lastBaiLam = \App\Models\BaiLam::orderBy('MaBaiLam', 'desc')->first();
        if ($lastBaiLam && preg_match('/BL(\d+)/', $lastBaiLam->MaBaiLam, $matches)) {
            $newNumber = intval($matches[1]) + 1;
        } else {
            $newNumber = 1;
        }
        $maBaiLam = 'BL' . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
        
        // Lấy câu hỏi
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
        
        // Tạo bài làm
        $baiLam = \App\Models\BaiLam::create([
            'MaBaiLam' => $maBaiLam,
            'MaHS' => $hocSinh->MaHS,
            'MaDe' => $maDe,
            'ThoiGianBatDau' => \Carbon\Carbon::now(),
            'TrangThai' => 'dangLam'
        ]);
        
        // Format câu hỏi (ẩn đáp án đúng)
        $cauHoiFormatted = $cauHois->map(function ($cauHoi) {
            return [
                'MaCauHoi' => $cauHoi->MaCauHoi,
                'NoiDung' => $cauHoi->NoiDung,
                'DapAnA' => $cauHoi->DapAnA,
                'DapAnB' => $cauHoi->DapAnB,
                'DapAnC' => $cauHoi->DapAnC,
                'DapAnD' => $cauHoi->DapAnD,
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
```

---

## 📋 BƯỚC 2: THÊM ROUTES

### File: `routes/api.php`

**Thêm vào trong nhóm `Route::middleware('auth:sanctum')`** (sau dòng `Route::post('/tao-de-thi', ...)`)

```php
// ĐỀ THI ROUTES - Học sinh làm bài (UR-02.1 & UR-02.2)
Route::get('/de-thi', [DeThiController::class, 'layDanhSachDeThi']);
Route::get('/de-thi/{maDe}', [DeThiController::class, 'layChiTietDeThi']);
Route::post('/de-thi/{maDe}/bat-dau', [DeThiController::class, 'batDauLamBai']);
```

---

## 📋 BƯỚC 3: KIỂM TRA MODEL

### File: `app/Models/BaiLam.php`

**Đảm bảo model BaiLam có:**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaiLam extends Model
{
    use HasFactory;

    protected $table = 'BaiLam';
    protected $primaryKey = 'MaBaiLam';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'MaBaiLam',
        'MaHS',
        'MaDe',
        'ThoiGianBatDau',
        'ThoiGianKetThuc',
        'TrangThai'
    ];

    // Relationships
    public function hocSinh()
    {
        return $this->belongsTo(HocSinh::class, 'MaHS', 'MaHS');
    }

    public function deThi()
    {
        return $this->belongsTo(DeThi::class, 'MaDe', 'MaDe');
    }
}
```

---

## 🎯 TỔNG KẾT BƯỚC 1-3

Sau khi hoàn thành 3 bước trên, bạn đã có:
- ✅ API lấy danh sách đề thi
- ✅ API xem chi tiết đề thi
- ✅ API bắt đầu làm bài (tạo bài làm + lấy câu hỏi)

**Test bằng REST Client** hoặc Postman:
```http
GET http://localhost:8000/api/de-thi
Authorization: Bearer YOUR_TOKEN

GET http://localhost:8000/api/de-thi/DT001
Authorization: Bearer YOUR_TOKEN

POST http://localhost:8000/api/de-thi/DT001/bat-dau
Authorization: Bearer YOUR_TOKEN
```

---

**File tiếp theo:** `HUONG_DAN_TICH_HOP_FRONTEND.md` (sẽ tạo sau)

---

**Lưu ý:** 
- Copy code cẩn thận, chú ý indentation
- Sau khi thêm code, chạy: `php artisan route:clear` và `php artisan config:clear`
- Kiểm tra lỗi với: `php artisan route:list`
