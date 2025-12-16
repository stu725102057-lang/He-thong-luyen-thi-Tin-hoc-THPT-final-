# 🎯 HƯỚNG DẪN TÍCH HỢP HOÀN CHỈNH MODULE LÀM BÀI

## 📌 TỔNG QUAN

Module làm bài gồm 3 màn hình chính:
1. **Chọn đề thi** - Danh sách đề thi có sẵn
2. **Làm bài** - Giao diện làm bài với timer và auto-save
3. **Kết quả** - Hiển thị điểm số và chi tiết

---

## 📁 FILES ĐÃ TẠO

```
✅ FRONTEND_CHON_DE_THI_COMPLETE.html    - Màn hình chọn đề thi
✅ FRONTEND_LAM_BAI_COMPLETE.html         - Màn hình làm bài với timer
✅ FRONTEND_KET_QUA_COMPLETE.html         - Màn hình kết quả
✅ CODE_BO_SUNG_DeThiController.php       - 3 API methods
✅ HUONG_DAN_TICH_HOP_API.md              - Hướng dẫn tích hợp API
```

---

## 🚀 BƯỚC 1: TÍCH HỢP BACKEND API (5 phút)

### 1.1. Thêm methods vào DeThiController.php

**File:** `app/Http/Controllers/DeThiController.php`

Mở file và **thêm 3 methods sau** vào cuối class (trước dấu `}` cuối cùng):

```php
/**
 * Lấy danh sách đề thi (cho học sinh)
 */
public function layDanhSachDeThi(Request $request)
{
    try {
        $user = $request->user();
        
        // Lấy danh sách đề thi đang hoạt động
        $query = DeThi::where('TrangThai', 'hoatdong');
        
        // Tìm kiếm (nếu có)
        if ($request->has('search')) {
            $keyword = $request->search;
            $query->where(function($q) use ($keyword) {
                $q->where('TenDe', 'like', "%{$keyword}%")
                  ->orWhere('MaDe', 'like', "%{$keyword}%");
            });
        }
        
        // Phân trang
        $danhSach = $query->orderBy('created_at', 'desc')->paginate(12);
        
        // Kiểm tra từng đề xem học sinh đã làm chưa
        $danhSach->getCollection()->transform(function($deThi) use ($user) {
            $baiLam = BaiLam::where('MaDe', $deThi->MaDe)
                           ->where('MaHS', $user->MaNguoiDung)
                           ->first();
            
            $deThi->da_lam = $baiLam ? true : false;
            if ($baiLam) {
                $deThi->diem_cu = $baiLam->Diem;
                $deThi->thoi_gian_lam = $baiLam->ThoiGianNop;
            }
            
            return $deThi;
        });
        
        return response()->json($danhSach);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Lỗi khi lấy danh sách đề thi: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Lấy chi tiết đề thi
 */
public function layChiTietDeThi(Request $request, $maDe)
{
    try {
        $user = $request->user();
        
        $deThi = DeThi::where('MaDe', $maDe)->first();
        
        if (!$deThi) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đề thi'
            ], 404);
        }
        
        // Kiểm tra xem học sinh đã làm chưa
        $baiLam = BaiLam::where('MaDe', $maDe)
                       ->where('MaHS', $user->MaNguoiDung)
                       ->first();
        
        $deThi->da_lam = $baiLam ? true : false;
        if ($baiLam) {
            $deThi->diem_cu = $baiLam->Diem;
            $deThi->thoi_gian_lam = $baiLam->ThoiGianNop;
        }
        
        return response()->json($deThi);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Lỗi: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Bắt đầu làm bài thi
 */
public function batDauLamBai(Request $request, $maDe)
{
    DB::beginTransaction();
    
    try {
        $user = $request->user();
        
        // Kiểm tra đề thi tồn tại
        $deThi = DeThi::where('MaDe', $maDe)->first();
        if (!$deThi) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đề thi'
            ], 404);
        }
        
        // Kiểm tra đã làm chưa
        $baiLamCu = BaiLam::where('MaDe', $maDe)
                         ->where('MaHS', $user->MaNguoiDung)
                         ->first();
        
        if ($baiLamCu) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn đã làm bài thi này rồi'
            ], 400);
        }
        
        // Tạo mã bài làm tự động
        $lastBaiLam = BaiLam::orderBy('MaBaiLam', 'desc')->first();
        if ($lastBaiLam) {
            $number = intval(substr($lastBaiLam->MaBaiLam, 2)) + 1;
        } else {
            $number = 1;
        }
        $maBaiLam = 'BL' . str_pad($number, 5, '0', STR_PAD_LEFT);
        
        // Tạo bài làm mới
        $baiLam = new BaiLam();
        $baiLam->MaBaiLam = $maBaiLam;
        $baiLam->MaDe = $maDe;
        $baiLam->MaHS = $user->MaNguoiDung;
        $baiLam->ThoiGianBatDau = now();
        $baiLam->TrangThai = 'dangLam';
        $baiLam->save();
        
        // Lấy danh sách câu hỏi (không trả về đáp án đúng)
        $cauHois = DB::table('chitietdethi')
                    ->join('cauhoi', 'chitietdethi.MaCauHoi', '=', 'cauhoi.MaCauHoi')
                    ->where('chitietdethi.MaDe', $maDe)
                    ->select(
                        'cauhoi.MaCauHoi',
                        'cauhoi.NoiDung',
                        'cauhoi.DapAnA',
                        'cauhoi.DapAnB',
                        'cauhoi.DapAnC',
                        'cauhoi.DapAnD'
                        // Không lấy DapAnDung
                    )
                    ->orderBy('chitietdethi.STT')
                    ->get();
        
        DB::commit();
        
        return response()->json([
            'success' => true,
            'message' => 'Bắt đầu làm bài thành công',
            'MaBaiLam' => $maBaiLam,
            'MaDe' => $maDe,
            'TenDe' => $deThi->TenDe,
            'ThoiGianLamBai' => $deThi->ThoiGianLamBai,
            'ThoiGianBatDau' => $baiLam->ThoiGianBatDau,
            'DanhSachCauHoi' => $cauHois
        ]);
        
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Lỗi: ' . $e->getMessage()
        ], 500);
    }
}
```

**Copy 3 methods trên từ file `CODE_BO_SUNG_DeThiController.php`**

---

### 1.2. Thêm routes vào api.php

**File:** `routes/api.php`

Thêm 3 dòng sau vào trong group `auth:sanctum`:

```php
Route::middleware('auth:sanctum')->group(function () {
    // ... existing routes ...
    
    // Module Làm bài thi
    Route::get('/de-thi', [DeThiController::class, 'layDanhSachDeThi']);
    Route::get('/de-thi/{maDe}', [DeThiController::class, 'layChiTietDeThi']);
    Route::post('/de-thi/{maDe}/bat-dau', [DeThiController::class, 'batDauLamBai']);
});
```

---

### 1.3. Clear cache

```bash
php artisan route:clear
php artisan config:clear
php artisan cache:clear
```

---

## 🎨 BƯỚC 2: TÍCH HỢP FRONTEND (10 phút)

### 2.1. Thêm HTML vào app.blade.php

**File:** `resources/views/app.blade.php`

Mở file và tìm phần `<div id="app">`. Thêm **3 màn hình mới**:

```html
<div id="app">
    <!-- Existing screens: home, login, register, questions, exams... -->
    
    <!-- ========== THÊM 3 MÀN HÌNH MỚI ========== -->
    
    <!-- 1. Màn hình chọn đề thi -->
    @include('partials.screen-chon-de-thi')
    
    <!-- 2. Màn hình làm bài -->
    @include('partials.screen-lam-bai')
    
    <!-- 3. Màn hình kết quả -->
    @include('partials.screen-ket-qua')
</div>
```

**HOẶC** nếu không dùng partials, copy trực tiếp nội dung từ 3 files HTML:

```html
<div id="app">
    <!-- Existing screens -->
    
    <!-- Copy toàn bộ nội dung từ FRONTEND_CHON_DE_THI_COMPLETE.html -->
    <!-- Copy toàn bộ nội dung từ FRONTEND_LAM_BAI_COMPLETE.html -->
    <!-- Copy toàn bộ nội dung từ FRONTEND_KET_QUA_COMPLETE.html -->
</div>
```

---

### 2.2. Thêm menu "Làm bài" vào sidebar

**Tìm phần menu dành cho học sinh** và thêm:

```html
<!-- Menu cho học sinh -->
<li class="nav-item" v-if="user.VaiTro === 'hocsinh'">
    <a class="nav-link" href="#" onclick="khoiTaoManHinhChonDeThi(); return false;">
        <i class="fas fa-edit"></i>
        Làm bài thi
    </a>
</li>
```

---

### 2.3. Thêm helper functions (nếu chưa có)

**Trong phần `<script>` của app.blade.php**, thêm các helper functions:

```javascript
// Helper: Show screen
function showScreen(screenId) {
    document.querySelectorAll('.screen').forEach(s => s.style.display = 'none');
    const screen = document.getElementById(screenId);
    if (screen) screen.style.display = 'block';
}

// Helper: Toast notifications
function showSuccessToast(title, message) {
    alert(`✅ ${title}\n${message}`);
}

function showErrorToast(title, message) {
    alert(`❌ ${title}\n${message}`);
}

function showWarningToast(title, message) {
    alert(`⚠️ ${title}\n${message}`);
}

function showInfoToast(title, message) {
    alert(`ℹ️ ${title}\n${message}`);
}

function showLoadingToast(message) {
    console.log('Loading:', message);
}
```

**LƯU Ý:** Nếu bạn đã có thư viện toast (như Toastify, SweetAlert2), thay thế `alert()` bằng toast library.

---

## 🧪 BƯỚC 3: TEST (10 phút)

### 3.1. Test Backend API

**Sử dụng REST Client hoặc Postman:**

#### Test 1: Lấy danh sách đề thi
```http
GET http://localhost:8000/api/de-thi
Authorization: Bearer {your_token}
```

**Expected Response:**
```json
{
  "current_page": 1,
  "data": [
    {
      "MaDe": "DT001",
      "TenDe": "Đề thi thử THPT 2024",
      "SoCau": 20,
      "ThoiGianLamBai": 30,
      "da_lam": false
    }
  ],
  "last_page": 1
}
```

#### Test 2: Xem chi tiết đề
```http
GET http://localhost:8000/api/de-thi/DT001
Authorization: Bearer {your_token}
```

#### Test 3: Bắt đầu làm bài
```http
POST http://localhost:8000/api/de-thi/DT001/bat-dau
Authorization: Bearer {your_token}
```

**Expected Response:**
```json
{
  "success": true,
  "message": "Bắt đầu làm bài thành công",
  "MaBaiLam": "BL00001",
  "MaDe": "DT001",
  "TenDe": "Đề thi thử THPT 2024",
  "ThoiGianLamBai": 30,
  "DanhSachCauHoi": [...]
}
```

---

### 3.2. Test Frontend

1. **Đăng nhập** với tài khoản học sinh
2. Click menu **"Làm bài thi"**
3. Xem danh sách đề thi
4. Click **"Xem chi tiết"** một đề thi
5. Click **"Bắt đầu làm bài"**
6. Kiểm tra:
   - ✅ Timer đếm ngược hoạt động
   - ✅ Chuyển câu hỏi hoạt động
   - ✅ Chọn đáp án hoạt động
   - ✅ Navigator hiển thị đúng
7. Chờ 60 giây → Kiểm tra auto-save
8. Chuyển tab → Kiểm tra cảnh báo gian lận
9. Click **"Nộp bài"** → Xác nhận
10. Xem kết quả với điểm số
11. Click **"Xem chi tiết bài làm"**

---

## 📋 CHECKLIST HOÀN THÀNH

### Backend
- [ ] Đã thêm 3 methods vào DeThiController.php
- [ ] Đã thêm 3 routes vào api.php
- [ ] Đã clear cache (route:clear, config:clear)
- [ ] Đã test 3 API endpoints với Postman/REST Client

### Frontend
- [ ] Đã thêm 3 màn hình vào app.blade.php
- [ ] Đã thêm menu "Làm bài" vào sidebar
- [ ] Đã thêm helper functions (showScreen, toast)
- [ ] Đã test giao diện chọn đề
- [ ] Đã test giao diện làm bài
- [ ] Đã test giao diện kết quả

### Features
- [ ] Timer đếm ngược hoạt động
- [ ] Auto-save mỗi 60 giây hoạt động
- [ ] Phát hiện gian lận (chuyển tab) hoạt động
- [ ] Navigator câu hỏi hoạt động
- [ ] Nộp bài thành công
- [ ] Hiển thị kết quả đúng
- [ ] Xem chi tiết bài làm hoạt động

---

## 🔧 TROUBLESHOOTING

### Lỗi 1: "Class BaiLam not found"

**Nguyên nhân:** Chưa import model BaiLam

**Giải pháp:** Thêm vào đầu file DeThiController.php:
```php
use App\Models\BaiLam;
```

---

### Lỗi 2: "Route [layDanhSachDeThi] not defined"

**Nguyên nhân:** Chưa clear route cache

**Giải pháp:**
```bash
php artisan route:clear
php artisan cache:clear
```

---

### Lỗi 3: JavaScript "khoiTaoManHinhChonDeThi is not defined"

**Nguyên nhân:** Chưa thêm JavaScript code

**Giải pháp:** Copy toàn bộ phần `<script>` từ 3 files HTML vào app.blade.php

---

### Lỗi 4: Timer không đếm ngược

**Nguyên nhân:** Thời gian server và client không đồng bộ

**Giải pháp:** Sử dụng timestamp từ server:
```javascript
const startTime = new Date(examData.ThoiGianBatDau);
const now = new Date();
```

---

### Lỗi 5: API trả về 401 Unauthorized

**Nguyên nhân:** Token hết hạn hoặc chưa gửi token

**Giải pháp:** Kiểm tra localStorage có token không:
```javascript
const token = localStorage.getItem('token');
if (!token) {
    showScreen('screen-login');
    return;
}
```

---

## 🎯 BƯỚC TIẾP THEO

Sau khi hoàn thành module làm bài, tiếp tục với:

1. ✅ **Module xem lại bài chi tiết** (modal popup)
2. ✅ **Module thống kê cá nhân** (biểu đồ điểm số)
3. ✅ **Module backup/restore** (admin)
4. ✅ **Module sinh đề ngẫu nhiên** (giáo viên)
5. ✅ **Module tạo đề thủ công** (giáo viên)

---

## 📞 HỖ TRỢ

Nếu gặp vấn đề trong quá trình tích hợp, kiểm tra:

1. **Console log:** F12 → Console (xem lỗi JavaScript)
2. **Network tab:** F12 → Network (xem API request/response)
3. **Laravel log:** `storage/logs/laravel.log`
4. **Route list:** `php artisan route:list | grep de-thi`

---

**Thời gian ước tính:** 25 phút (5 phút backend + 10 phút frontend + 10 phút test)

**Kết quả:** Module làm bài hoàn chỉnh với đầy đủ tính năng ✅
