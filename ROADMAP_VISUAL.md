# 🗺️ ROADMAP HOÀN THÀNH HỆ THỐNG - VISUAL GUIDE

```
┌─────────────────────────────────────────────────────────────────────┐
│  HỆ THỐNG LUYỆN THI THPT QUỐC GIA MÔN TIN HỌC                       │
│  Tiến độ hiện tại: ████████████████░░░░░░░░ 77%                      │
└─────────────────────────────────────────────────────────────────────┘
```

## 📊 TÌNH TRẠNG TỪNG MODULE

```
┌─────────────────────┬─────────────────────┬──────────┐
│ MODULE              │ PROGRESS BAR        │ TRẠNG THÁI│
├─────────────────────┼─────────────────────┼──────────┤
│ Backend APIs        │ ████████████████░░  │   88%    │
│ Frontend UI         │ ██████████░░░░░░░░  │   53%    │
│ Database Schema     │ ████████████████████│  100%    │
│ Authentication      │ ████████████████████│  100%    │
│ Security Features   │ ███████████░░░░░░░░ │   57%    │
│ Testing & QA        │ ░░░░░░░░░░░░░░░░░░░ │    0%    │
└─────────────────────┴─────────────────────┴──────────┘
```

---

## 🎯 SPRINT 1: CRITICAL FEATURES (Tuần 1)
**Mục tiêu:** Hoàn thành workflow học sinh làm bài cơ bản

```
NGÀY 1-2 (4-6 giờ)
┌─────────────────────────────────────────────────────────────┐
│ ✅ Task 1: Hoàn thiện màn hình LÀM BÀI THI                   │
│    ├─ Display questions from API                            │
│    ├─ Radio buttons (A/B/C/D)                               │
│    ├─ Countdown timer                                       │
│    ├─ Submit button                                         │
│    └─ Navigation (Câu trước/Câu sau)                        │
│    Time: 2-3 giờ                                            │
├─────────────────────────────────────────────────────────────┤
│ ✅ Task 2: Implement AUTO-SAVE                              │
│    ├─ setInterval 60s                                       │
│    ├─ POST /api/luu-nhap                                    │
│    ├─ Show "Đang lưu..." indicator                          │
│    └─ Handle network errors                                 │
│    Time: 1 giờ                                              │
├─────────────────────────────────────────────────────────────┤
│ ✅ Task 3: Implement CHEATING DETECTION                     │
│    ├─ visibilitychange listener                            │
│    ├─ window blur listener                                 │
│    ├─ Context menu prevention                              │
│    └─ Warning modals                                        │
│    Time: 1 giờ                                              │
└─────────────────────────────────────────────────────────────┘

NGÀY 3 (2-3 giờ)
┌─────────────────────────────────────────────────────────────┐
│ ✅ Task 4: Connect chonDeThiScreen JavaScript               │
│    ├─ Add functions to app object                          │
│    ├─ loadDanhSachDeThi()                                   │
│    ├─ displayDanhSachDeThi()                                │
│    ├─ showConfirmStartModal()                               │
│    └─ confirmStartExam()                                    │
│    Time: 30 phút                                            │
├─────────────────────────────────────────────────────────────┤
│ ✅ Task 5: Tạo màn hình KẾT QUẢ THI                         │
│    ├─ ketQuaScreen layout                                   │
│    ├─ Display score + stats                                │
│    ├─ Modal xem chi tiết                                    │
│    └─ Connect API /baithi/{id}/ketqua                       │
│    Time: 2 giờ                                              │
└─────────────────────────────────────────────────────────────┘

DELIVERABLE SPRINT 1:
✅ Học sinh có thể: Đăng nhập → Chọn đề → Làm bài → Nộp → Xem kết quả
✅ Auto-save hoạt động
✅ Cheating detection hoạt động
Progress: 77% → 85%
```

---

## 📈 SPRINT 2: CHARTS & STATISTICS (Tuần 2)
**Mục tiêu:** Thêm thống kê và biểu đồ

```
NGÀY 4-5 (3-4 giờ)
┌─────────────────────────────────────────────────────────────┐
│ ✅ Task 6: Integrate Chart.js                               │
│    ├─ Add Chart.js CDN                                      │
│    ├─ Create thongKeScreen                                  │
│    ├─ Line chart: Điểm theo thời gian                       │
│    ├─ Bar chart: Phân tích chuyên đề                        │
│    └─ Pie chart: Điểm mạnh/yếu                              │
│    Time: 3-4 giờ                                            │
└─────────────────────────────────────────────────────────────┘

NGÀY 6 (2-3 giờ)
┌─────────────────────────────────────────────────────────────┐
│ ✅ Task 7: Dashboard Admin                                  │
│    ├─ Stats cards (users, exams, submissions)              │
│    ├─ Recent activities table                              │
│    ├─ Quick action buttons                                 │
│    └─ System health indicators                             │
│    Time: 2-3 giờ                                            │
└─────────────────────────────────────────────────────────────┘

NGÀY 7 (1 giờ)
┌─────────────────────────────────────────────────────────────┐
│ ✅ Task 8: Hoàn thiện BACKUP/RESTORE UI                     │
│    ├─ Connect backup button                                │
│    ├─ Upload restore file                                  │
│    ├─ Display backup history table                         │
│    └─ Progress indicators                                   │
│    Time: 1 giờ                                              │
└─────────────────────────────────────────────────────────────┘

DELIVERABLE SPRINT 2:
✅ Thống kê đầy đủ với biểu đồ
✅ Dashboard admin hoạt động
✅ Backup/Restore UI hoàn chỉnh
Progress: 85% → 95%
```

---

## 🔒 SPRINT 3: SECURITY & POLISH (Tuần 3)
**Mục tiêu:** Bảo mật và hoàn thiện

```
NGÀY 8-9 (4 giờ)
┌─────────────────────────────────────────────────────────────┐
│ ✅ Task 9: Security Hardening                               │
│    ├─ Add rate limiting (throttle)                         │
│    ├─ Login attempts limit                                 │
│    ├─ API rate limits                                       │
│    └─ CSRF protection check                                │
│    Time: 2 giờ                                              │
├─────────────────────────────────────────────────────────────┤
│ ✅ Task 10: Export Reports                                  │
│    ├─ Install PhpSpreadsheet                               │
│    ├─ Export thống kê ra Excel                             │
│    ├─ Install DomPDF                                       │
│    └─ Export kết quả ra PDF                                │
│    Time: 2-3 giờ                                            │
└─────────────────────────────────────────────────────────────┘

NGÀY 10-11 (4-6 giờ)
┌─────────────────────────────────────────────────────────────┐
│ ✅ Task 11: Testing & Bug Fixes                             │
│    ├─ Test all user flows                                  │
│    ├─ Test on different browsers                           │
│    ├─ Test responsive (mobile/tablet)                      │
│    ├─ Fix bugs discovered                                  │
│    └─ Polish UI/UX                                         │
│    Time: 4-6 giờ                                            │
└─────────────────────────────────────────────────────────────┘

DELIVERABLE SPRINT 3:
✅ Hệ thống bảo mật tốt
✅ Export reports hoạt động
✅ Đã test kỹ trên nhiều môi trường
Progress: 95% → 100% 🎉
```

---

## 🎨 WORKFLOW DIAGRAM

```
┌─────────────────────────────────────────────────────────────────┐
│                     USER WORKFLOW                               │
└─────────────────────────────────────────────────────────────────┘

HỌC SINH:
┌──────┐    ┌──────┐    ┌──────┐    ┌──────┐    ┌──────┐
│Login │ -> │Chọn  │ -> │Làm   │ -> │Nộp   │ -> │Xem   │
│      │    │Đề    │    │Bài   │    │Bài   │    │KQ    │
└──────┘    └──────┘    └──────┘    └──────┘    └──────┘
   ✅          ⚠️90%       ❌60%        ✅          ❌50%

GIÁO VIÊN:
┌──────┐    ┌──────┐    ┌──────┐    ┌──────┐    ┌──────┐
│Login │ -> │Quản  │ -> │Tạo   │ -> │Xem   │ -> │Export│
│      │    │lý CH │    │Đề    │    │TK    │    │BC    │
└──────┘    └──────┘    └──────┘    └──────┘    └──────┘
   ✅          ✅          ⚠️70%       ❌50%        ❌

ADMIN:
┌──────┐    ┌──────┐    ┌──────┐    ┌──────┐    ┌──────┐
│Login │ -> │Quản  │ -> │Backup│ -> │Restore│-> │Giám  │
│      │    │lý User│    │      │    │       │   │sát   │
└──────┘    └──────┘    └──────┘    └──────┘    └──────┘
   ✅          ✅          ⚠️90%       ⚠️90%       ❌0%
```

---

## 📋 DAILY CHECKLIST

### NGÀY 1: Màn hình Làm bài
```
Morning (2h):
[ ] Đọc lại batDauLamBai API response structure
[ ] Design lambaithiScreen layout (wireframe)
[ ] Code HTML structure

Afternoon (1h):
[ ] Implement displayQuestions() function
[ ] Add radio buttons với event handlers
[ ] Test hiển thị câu hỏi

Evening (30min):
[ ] Add countdown timer với auto-submit
[ ] Test timer functionality
```

### NGÀY 2: Auto-save & Cheating
```
Morning (1h):
[ ] Implement saveProgress() function
[ ] Test POST /api/luu-nhap
[ ] Add save indicator UI

Afternoon (1h):
[ ] Add visibilitychange listener
[ ] Add blur listener
[ ] Implement logCheatingAttempt()

Evening (30min):
[ ] Test cheating detection
[ ] Add warning modals
```

### NGÀY 3: Chọn đề & Kết quả
```
Morning (30min):
[ ] Add chonDeThiScreen functions to app object
[ ] Test loadDanhSachDeThi()
[ ] Test confirmStartExam()

Afternoon (2h):
[ ] Design ketQuaScreen layout
[ ] Implement displayKetQua() function
[ ] Add xemChiTietModal

Evening (30min):
[ ] Test complete flow: Chọn → Làm → Nộp → Xem KQ
```

---

## 🎯 MILESTONES

```
Milestone 1: MVP (Minimum Viable Product)
├─ Đăng nhập/Đăng ký ✅
├─ Quản lý câu hỏi ✅
├─ Tạo đề thi ⚠️
├─ Học sinh làm bài ❌ <- ĐANG Ở ĐÂY
├─ Nộp bài & chấm điểm ⚠️
└─ Xem kết quả ❌
Target: Tuần 1
Progress: 85%

Milestone 2: Full Features
├─ Auto-save ❌
├─ Cheating detection ❌
├─ Thống kê với charts ❌
├─ Dashboard admin ❌
└─ Backup/Restore UI ⚠️
Target: Tuần 2
Progress: 95%

Milestone 3: Production Ready
├─ Security hardening ❌
├─ Export reports ❌
├─ Performance testing ❌
├─ Bug fixes ❌
└─ Documentation ⚠️
Target: Tuần 3
Progress: 100% 🎉
```

---

## 📈 PROGRESS TRACKING

```
Week 1 (CRITICAL):
Sun  Mon  Tue  Wed  Thu  Fri  Sat
 █    █    █    █    ░    ░    ░
Day1 Day2 Day3 Day4  -    -    -
77%  80%  83%  85%   -    -    -

Week 2 (HIGH):
Sun  Mon  Tue  Wed  Thu  Fri  Sat
 ░    ░    ░    ░    ░    ░    ░
Day5 Day6 Day7  -    -    -    -
87%  90%  92%   -    -    -    -

Week 3 (POLISH):
Sun  Mon  Tue  Wed  Thu  Fri  Sat
 ░    ░    ░    ░    ░    ░    ░
Day8 Day9 D10  D11   -    -    -
94%  96%  98% 100%   -    -    - 🎉
```

---

## 🚀 QUICK START DEVELOPMENT

### Setup môi trường
```bash
# 1. Start server
php artisan serve

# 2. Mở browser
http://127.0.0.1:8000

# 3. Mở code editor
resources/views/app.blade.php
```

### Làm Task 1: Màn hình làm bài
```javascript
// 1. Tìm dòng: <div id="lambaithiScreen" class="screen">
// 2. Replace placeholder bằng:

<div id="lambaithiScreen" class="screen">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar: Danh sách câu hỏi -->
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h6>Danh sách câu</h6>
                    </div>
                    <div class="card-body" id="questionNav">
                        <!-- Will be populated by JS -->
                    </div>
                </div>
            </div>
            
            <!-- Main: Câu hỏi hiện tại -->
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between">
                            <h5 id="examTitle">Đề thi</h5>
                            <div id="timer" class="timer"></div>
                        </div>
                    </div>
                    <div class="card-body" id="questionContent">
                        <!-- Current question -->
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-secondary" onclick="app.prevQuestion()">
                            Câu trước
                        </button>
                        <button class="btn btn-secondary" onclick="app.nextQuestion()">
                            Câu sau
                        </button>
                        <button class="btn btn-danger float-end" onclick="app.submitExam()">
                            Nộp bài
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

// 3. Thêm JavaScript functions vào app object
// 4. Test!
```

---

## 📚 RESOURCES

### Documentation
- Laravel Docs: https://laravel.com/docs
- Bootstrap 5: https://getbootstrap.com/docs/5.3
- Chart.js: https://www.chartjs.org/docs

### Testing Tools
- Browser DevTools (F12)
- Postman (test APIs)
- Chrome Lighthouse (performance)

### Deploy
- Local: XAMPP/Laragon
- Cloud: DigitalOcean, AWS, Heroku

---

**🎯 BẮT ĐẦU NGAY:** Task 1 - Màn hình làm bài (2-3 giờ)  
**📊 Tiến độ hiện tại:** 77% → Mục tiêu Sprint 1: 85%  
**⏰ Thời gian còn lại:** 21-26 giờ ≈ 3 tuần  
**🎉 Completion Date:** ~28/12/2025 (dự kiến)
