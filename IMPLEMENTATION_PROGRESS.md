# 🚀 Tổng Hợp Triển Khai Các Chức Năng Còn Thiếu

## ✅ ĐÃ HOÀN THÀNH (trong lần này)

### 1. ✅ Module 1: Authentication Frontend (UR-01.2, UR-01.3)
**Files đã sửa**:
- `resources/views/app.blade.php`: Thêm 3 screens (Register, Forgot Password, Reset Password)
- Added JavaScript functions: `register()`, `forgotPassword()`, `resetPassword()`

**Features**:
- ✅ Register form (6 fields)
- ✅ Forgot password form
- ✅ Reset password form
- ✅ Auto-login after register
- ✅ Session storage for email
- ✅ Links between login/register/forgot screens

---

### 2. ✅ Module 3: Export Questions (UR-03.2)
**Files đã sửa**:
- `app/Http/Controllers/CauHoiController.php`: Added `export()`, `exportCSV()`, `exportExcel()`
- `routes/api.php`: Added `GET /api/cau-hoi/export`

**Features**:
- ✅ Export to JSON (default)
- ✅ Export to CSV (UTF-8 BOM)
- ✅ Export to Excel (XLSX format)
- ✅ Filter by subject & difficulty
- ✅ Download as file

**API**:
```
GET /api/cau-hoi/export?format=csv&MaNH=TIN&DoKho=De
GET /api/cau-hoi/export?format=json
GET /api/cau-hoi/export?format=excel
```

---

## 📋 CÒN THIẾU (Cần implement tiếp)

### 3. ⏳ Module 2: Exam Selection UI (UR-02.1)
**Cần làm**:
- [ ] API: `GET /api/de-thi/available` - List exams for students
- [ ] Frontend: Exam list screen với filter
- [ ] Frontend: "Bắt đầu làm bài" button
- [ ] Frontend: Exam taking interface (timer, questions, submit)

**Ước tính**: 6 giờ

---

### 4. ⏳ Module 2: Detailed Result Modal (UR-02.4)
**Cần làm**:
- [ ] Frontend: Modal component
- [ ] Show all questions với đáp án
- [ ] Highlight correct (green) / wrong (red)
- [ ] Show explanation (if available)

**Ước tính**: 4 giờ

---

### 5. ⏳ Module 3: Random Exam Generation (UR-03.4)
**Cần làm**:
- [ ] API: `POST /api/tao-de-thi/random`
- [ ] Controller: `DeThiController@taoDeThiNgauNhien()`
- [ ] Logic: Select random questions by criteria
- [ ] Frontend: Toggle manual/random mode

**Ước tính**: 8 giờ

**API Example**:
```json
POST /api/tao-de-thi/random
{
  "TenDe": "Đề thi ngẫu nhiên",
  "MaNH": "TIN",
  "SoCauHoi": 40,
  "DoKho": "TB",
  "ThoiGianLamBai": 90
}
```

---

### 6. ⏳ Module 4: Backup & Restore (UR-04.4, UR-04.5)
**Cần làm**:
- [ ] Backend: `UserController@backupDatabase()`
- [ ] Backend: `UserController@restoreDatabase()`
- [ ] Shell command: mysqldump
- [ ] Frontend: Upload backup file
- [ ] Storage: Save to storage/backups/

**Ước tính**: 6 giờ

**Implementation**:
```php
public function backupDatabase() {
    $filename = 'backup-' . date('Y-m-d-His') . '.sql';
    $path = storage_path('backups/' . $filename);
    
    $command = sprintf(
        'mysqldump -u%s -p%s %s > %s',
        env('DB_USERNAME'),
        env('DB_PASSWORD'),
        env('DB_DATABASE'),
        $path
    );
    
    exec($command);
    
    return response()->download($path);
}
```

---

### 7. ⏳ Module 5: Cheating Detection Frontend (UR-05.1)
**Cần làm**:
- [ ] JavaScript: Detect tab switch
- [ ] JavaScript: Detect window blur
- [ ] JavaScript: Count violations
- [ ] Frontend: Warning modal
- [ ] API call: `POST /api/ghi-nhan-gian-lan`
- [ ] Auto-submit after 5 violations

**Ước tính**: 4 giờ

**Implementation**:
```javascript
// Detect tab switch
document.addEventListener('visibilitychange', () => {
    if (document.hidden && app.isExamInProgress) {
        app.cheatingCount++;
        app.recordCheating('TAB_SWITCH');
        
        if (app.cheatingCount >= 3) {
            app.showCheatingWarning();
        }
        
        if (app.cheatingCount >= 5) {
            app.autoSubmitExam();
        }
    }
});

// Detect window blur
window.addEventListener('blur', () => {
    if (app.isExamInProgress) {
        app.cheatingCount++;
        app.recordCheating('WINDOW_BLUR');
    }
});
```

---

### 8. ⏳ Module 5: Auto-save Timer (UR-05.2)
**Cần làm**:
- [ ] JavaScript: setInterval every 60 seconds
- [ ] API call: `POST /api/luu-nhap`
- [ ] Show "Đã lưu tự động" indicator
- [ ] Restore from draft on reload

**Ước tính**: 3 giờ

**Implementation**:
```javascript
startAutoSave() {
    this.autoSaveInterval = setInterval(() => {
        this.saveDraft();
    }, 60000); // Every 1 minute
}

async saveDraft() {
    const answers = this.getCurrentAnswers();
    
    await this.apiCall('/luu-nhap', {
        method: 'POST',
        body: JSON.stringify({
            MaDe: this.currentExam.MaDe,
            CauTraLoi: answers
        })
    });
    
    this.showAutoSaveIndicator();
}
```

---

### 9. ⏳ Security: Rate Limiting (Non-functional requirement)
**Cần làm**:
- [ ] Add throttle middleware to routes
- [ ] Login: 5 attempts per minute
- [ ] Register: 3 attempts per minute
- [ ] Forgot password: 3 attempts per 15 minutes
- [ ] API calls: 60 per minute

**Ước tính**: 2 giờ

**Implementation**:
```php
// routes/api.php
Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1'); // 5 attempts per 1 minute

Route::post('/register', [AuthController::class, 'register'])
    ->middleware('throttle:3,1');

Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])
    ->middleware('throttle:3,15');
```

---

### 10. ⏳ Admin: Dashboard & Monitoring (UR-04.3)
**Cần làm**:
- [ ] API: `GET /api/admin/dashboard`
- [ ] Statistics: Online users, Today's exams, Active exams
- [ ] Frontend: Dashboard với charts
- [ ] Real-time updates (optional: WebSockets)

**Ước tính**: 8 giờ

---

## 📊 Tổng Hợp

| # | Feature | Status | Priority | Estimate |
|---|---------|--------|----------|----------|
| 1 | Authentication Frontend | ✅ DONE | HIGH | 0h |
| 2 | Export Questions | ✅ DONE | MEDIUM | 0h |
| 3 | Exam Selection UI | ⏳ TODO | HIGH | 6h |
| 4 | Detailed Result Modal | ⏳ TODO | HIGH | 4h |
| 5 | Random Exam Generation | ⏳ TODO | HIGH | 8h |
| 6 | Backup & Restore | ⏳ TODO | MEDIUM | 6h |
| 7 | Cheating Detection | ⏳ TODO | HIGH | 4h |
| 8 | Auto-save Timer | ⏳ TODO | HIGH | 3h |
| 9 | Rate Limiting | ⏳ TODO | HIGH | 2h |
| 10 | Admin Dashboard | ⏳ TODO | LOW | 8h |

**Tổng còn lại**: ~41 giờ

---

## 🎯 Roadmap Đề Xuất

### Phase 1: Core Exam Features (18h)
1. Exam Selection UI (6h)
2. Detailed Result Modal (4h)
3. Random Exam Generation (8h)

**Kết quả**: Học sinh có thể chọn đề, làm bài, xem chi tiết

---

### Phase 2: Security & Safety (9h)
4. Cheating Detection (4h)
5. Auto-save Timer (3h)
6. Rate Limiting (2h)

**Kết quả**: Hệ thống an toàn, chống gian lận

---

### Phase 3: Admin Tools (14h)
7. Backup & Restore (6h)
8. Admin Dashboard (8h)

**Kết quả**: Admin có đầy đủ công cụ quản trị

---

## 🚀 Quick Implementation Scripts

### Script 1: Random Exam Generation
```php
// DeThiController.php
public function taoDeThiNgauNhien(Request $request) {
    $validated = $request->validate([
        'TenDe' => 'required|string',
        'MaNH' => 'required|string',
        'SoCauHoi' => 'required|integer|min:1',
        'DoKho' => 'nullable|string',
        'ThoiGianLamBai' => 'required|integer|min:1'
    ]);
    
    // Get random questions
    $query = CauHoi::where('MaNH', $validated['MaNH']);
    
    if (isset($validated['DoKho'])) {
        $query->where('DoKho', $validated['DoKho']);
    }
    
    $randomQuestions = $query->inRandomOrder()
        ->limit($validated['SoCauHoi'])
        ->get();
    
    if ($randomQuestions->count() < $validated['SoCauHoi']) {
        return response()->json([
            'success' => false,
            'message' => 'Không đủ câu hỏi trong ngân hàng'
        ], 400);
    }
    
    // Create exam
    DB::beginTransaction();
    
    $maDe = $this->generateMaDe();
    
    $deThi = DeThi::create([
        'MaDe' => $maDe,
        'TenDe' => $validated['TenDe'],
        'ThoiGianLamBai' => $validated['ThoiGianLamBai'],
        'MaGV' => $request->user()->giaoVien->MaGV,
        'NgayTao' => now(),
        'TrangThai' => 'Chua_Xuat_Ban'
    ]);
    
    // Add questions to exam
    foreach ($randomQuestions as $index => $cauHoi) {
        DB::table('ChiTietDeThi')->insert([
            'MaDe' => $maDe,
            'MaCH' => $cauHoi->MaCH,
            'ThuTu' => $index + 1
        ]);
    }
    
    DB::commit();
    
    return response()->json([
        'success' => true,
        'message' => 'Tạo đề thi ngẫu nhiên thành công',
        'data' => $deThi
    ], 201);
}
```

---

### Script 2: Rate Limiting Setup
```php
// app/Providers/RouteServiceProvider.php
protected function configureRateLimiting()
{
    RateLimiter::for('api', function (Request $request) {
        return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
    });
    
    RateLimiter::for('login', function (Request $request) {
        return Limit::perMinute(5)->by($request->ip());
    });
}
```

---

### Script 3: Cheating Detection
```javascript
// app.blade.php
const app = {
    cheatingCount: 0,
    cheatingWarningShown: false,
    isExamInProgress: false,
    
    startExamMonitoring() {
        this.isExamInProgress = true;
        this.cheatingCount = 0;
        
        document.addEventListener('visibilitychange', this.handleVisibilityChange);
        window.addEventListener('blur', this.handleWindowBlur);
    },
    
    stopExamMonitoring() {
        this.isExamInProgress = false;
        document.removeEventListener('visibilitychange', this.handleVisibilityChange);
        window.removeEventListener('blur', this.handleWindowBlur);
    },
    
    handleVisibilityChange() {
        if (document.hidden && app.isExamInProgress) {
            app.recordCheating('TAB_SWITCH');
        }
    },
    
    handleWindowBlur() {
        if (app.isExamInProgress) {
            app.recordCheating('WINDOW_BLUR');
        }
    },
    
    async recordCheating(type) {
        this.cheatingCount++;
        
        await this.apiCall('/ghi-nhan-gian-lan', {
            method: 'POST',
            body: JSON.stringify({
                MaBaiLam: this.currentExam.MaBaiLam,
                LoaiGianLan: type,
                ThoiDiem: new Date().toISOString()
            })
        });
        
        if (this.cheatingCount === 3 && !this.cheatingWarningShown) {
            this.showCheatingWarning();
            this.cheatingWarningShown = true;
        }
        
        if (this.cheatingCount >= 5) {
            this.showAlert('Bạn đã vi phạm quá nhiều lần. Bài thi sẽ tự động nộp!', 'danger');
            await this.autoSubmitExam();
        }
    },
    
    showCheatingWarning() {
        this.showAlert(
            'CẢNH BÁO: Hệ thống phát hiện bạn chuyển tab/cửa sổ. Nếu vi phạm thêm 2 lần nữa, bài thi sẽ tự động nộp!',
            'warning'
        );
    }
};
```

---

## 📝 Next Steps

1. **Run migration**: ✅ Done (password_resets table created)
2. **Test Authentication**: Test register/forgot/reset với REST Client
3. **Test Export**: Test export CSV/JSON/Excel
4. **Implement Phase 1**: Exam Selection + Result Modal + Random Exam (18h)
5. **Implement Phase 2**: Cheating Detection + Auto-save + Rate Limiting (9h)
6. **Implement Phase 3**: Backup + Dashboard (14h)

**Total remaining**: ~41 giờ công việc

---

## ✅ Checklist Triển Khai

### Đã xong
- [x] UR-01.2: Register API + Frontend
- [x] UR-01.3: Forgot/Reset Password API + Frontend
- [x] UR-03.2: Export Questions (CSV/JSON/Excel)
- [x] Migration: password_resets table
- [x] Test file: test-authentication.http
- [x] Documentation: AUTHENTICATION_COMPLETE.md

### Đang làm
- [-] Frontend UI for authentication (3 screens added, need testing)
- [-] Export button in Question Management screen

### Chưa làm
- [ ] UR-02.1: Exam Selection UI
- [ ] UR-02.4: Detailed Result Modal
- [ ] UR-03.4: Random Exam Generation
- [ ] UR-04.4/4.5: Backup & Restore
- [ ] UR-05.1: Cheating Detection Frontend
- [ ] UR-05.2: Auto-save Timer
- [ ] Rate Limiting
- [ ] Admin Dashboard

---

**Last Updated**: December 7, 2025  
**Version**: 2.5.0  
**Progress**: 70% → 75% (Authentication + Export complete)  
**Next**: Exam Selection UI + Result Modal + Random Exam (High Priority)
