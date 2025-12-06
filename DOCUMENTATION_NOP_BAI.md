# 📝 DOCUMENTATION - HÀM nopBai()

## 🎯 Mục đích
Xử lý việc nộp bài thi trắc nghiệm và chấm điểm tự động theo thang điểm 10.

---

## 📋 LOGIC NGHIỆP VỤ

### 1️⃣ **VALIDATE DỮ LIỆU ĐẦU VÀO**

#### Các trường bắt buộc:
```php
[
    'MaDe' => 'required|string|exists:DeThi,MaDe',        // Mã đề thi
    'MaHS' => 'required|string|exists:HocSinh,MaHS',      // Mã học sinh
    'CauTraLoi' => 'required|array',                       // Mảng câu trả lời
    'CauTraLoi.*.MaCH' => 'required|string',              // Mã câu hỏi
    'CauTraLoi.*.DapAnChon' => 'required|string|in:A,B,C,D' // Đáp án chọn
]
```

#### Request mẫu:
```json
{
  "MaDe": "DT001",
  "MaHS": "HS001",
  "CauTraLoi": [
    {
      "MaCH": "CH001",
      "DapAnChon": "A"
    },
    {
      "MaCH": "CH002",
      "DapAnChon": "B"
    }
  ],
  "ThoiGianBatDau": "2025-12-06T20:00:00" // Optional
}
```

---

### 2️⃣ **KIỂM TRA ĐỀ THI**

```php
$deThi = DeThi::with('cauHoi')->find($maDe);

if (!$deThi) {
    return response()->json([
        'success' => false,
        'message' => 'Không tìm thấy đề thi'
    ], 404);
}
```

**Quan hệ:** Sử dụng relationship `cauHoi()` để lấy danh sách câu hỏi từ bảng trung gian `DETHI_CAUHOI`.

---

### 3️⃣ **TÍNH ĐIỂM - THUẬT TOÁN CHẤM**

#### Bước 1: Lấy đáp án đúng
```php
$dapAnDung = [];
foreach ($deThi->cauHoi as $cauHoi) {
    $dapAnDung[$cauHoi->MaCH] = $cauHoi->DapAn;
}

// Kết quả: 
// ['CH001' => 'A', 'CH002' => 'B', 'CH003' => 'B', ...]
```

#### Bước 2: So sánh từng câu
```php
$soCauDung = 0;
$soCauSai = 0;

foreach ($cauTraLoi as $traLoi) {
    $maCH = $traLoi['MaCH'];
    $dapAnChon = $traLoi['DapAnChon'];
    
    // So sánh với đáp án đúng
    if (isset($dapAnDung[$maCH]) && $dapAnDung[$maCH] === $dapAnChon) {
        $soCauDung++;  // ✅ Đúng
    } else {
        $soCauSai++;   // ❌ Sai
    }
}
```

#### Bước 3: Tính số câu không làm
```php
$tongSoCau = count($dapAnDung);
$soCauKhongLam = $tongSoCau - count($cauTraLoi);
```

#### Bước 4: Tính điểm (Thang 10)
```php
$diem = $tongSoCau > 0 
    ? round(($soCauDung / $tongSoCau) * 10, 2) 
    : 0;

// Ví dụ: 4/5 câu đúng = (4/5) * 10 = 8.00 điểm
```

---

### 4️⃣ **LƯU DỮ LIỆU**

#### A. Lưu vào bảng `BaiLam`

```php
$baiLam = BaiLam::create([
    'MaBaiLam' => $maBaiLam,                    // Sinh tự động (BL + random)
    'DSCauTraLoi' => json_encode($chiTietCauTraLoi), // JSON chi tiết
    'Diem' => $diem,                             // Điểm đã tính
    'ThoiGianBatDau' => $thoiGianBatDau,        // Thời gian bắt đầu
    'ThoiGianNop' => Carbon::now(),              // Thời gian nộp
    'TrangThai' => 'ChamDiem',                   // Trạng thái
    'MaHS' => $maHS,                             // Học sinh
    'MaDe' => $maDe,                             // Đề thi
]);
```

**Chi tiết DSCauTraLoi (JSON):**
```json
[
  {
    "MaCH": "CH001",
    "DapAnChon": "A",
    "DapAnDung": "A",
    "KetQua": "Dung"
  },
  {
    "MaCH": "CH002",
    "DapAnChon": "C",
    "DapAnDung": "B",
    "KetQua": "Sai"
  }
]
```

#### B. Lưu vào bảng `KetQua`

```php
$ketQua = KetQua::create([
    'MaKQ' => $maKQ,                          // Sinh tự động (KQ + random)
    'Diem' => $diem,                          // Điểm
    'SoCauDung' => $soCauDung,                // Số câu đúng
    'SoCauSai' => $soCauSai,                  // Số câu sai
    'SoCauKhongLam' => $soCauKhongLam,        // Số câu không làm
    'ThoiGianHoanThanh' => Carbon::now(),     // Thời gian hoàn thành
    'MaHS' => $maHS,                          // Học sinh
    'MaDe' => $maDe,                          // Đề thi
    'MaBaiLam' => $maBaiLam,                  // FK đến BaiLam
]);
```

---

### 5️⃣ **TRẢ VỀ KẾT QUẢ**

```json
{
  "success": true,
  "message": "Nộp bài thành công",
  "data": {
    "MaBaiLam": "BL00012345",
    "MaKQ": "KQ00012345",
    "Diem": 8.0,
    "SoCauDung": 4,
    "SoCauSai": 1,
    "SoCauKhongLam": 0,
    "TongSoCau": 5,
    "ThoiGianNop": "2025-12-06 21:30:45",
    "TenDe": "Kiểm tra Tin học đại cương",
    "HocSinh": {
      "MaHS": "HS001",
      "HoTen": "Trần Thị Bình"
    },
    "ChiTiet": [
      {
        "MaCH": "CH001",
        "DapAnChon": "A",
        "DapAnDung": "A",
        "KetQua": "Dung"
      },
      ...
    ]
  }
}
```

---

## 🔒 PHÂN QUYỀN

### Kiểm tra Role:
```php
if ($user->Role !== 'hocsinh') {
    return response()->json([
        'success' => false,
        'message' => 'Chỉ học sinh mới được phép nộp bài thi'
    ], 403);
}
```

### Kiểm tra quyền sở hữu:
```php
if ($user->hocSinh && $user->hocSinh->MaHS !== $maHS) {
    return response()->json([
        'success' => false,
        'message' => 'Bạn không có quyền nộp bài cho học sinh khác'
    ], 403);
}
```

---

## 💾 DATABASE TRANSACTION

Sử dụng transaction để đảm bảo tính toàn vẹn dữ liệu:

```php
try {
    DB::beginTransaction();
    
    // Lưu BaiLam
    // Lưu KetQua
    
    DB::commit();
    
    return response()->json([...]);
    
} catch (\Exception $e) {
    DB::rollBack();
    
    return response()->json([
        'success' => false,
        'message' => 'Có lỗi xảy ra khi nộp bài',
        'error' => $e->getMessage()
    ], 500);
}
```

---

## 🧮 CÔNG THỨC TÍNH ĐIỂM

### Thang điểm 10:
```
Điểm = (Số câu đúng / Tổng số câu) × 10
```

### Ví dụ:

| Đúng | Sai | Tổng | Công thức | Điểm |
|------|-----|------|-----------|------|
| 5 | 0 | 5 | (5/5) × 10 | 10.0 |
| 4 | 1 | 5 | (4/5) × 10 | 8.0 |
| 3 | 2 | 5 | (3/5) × 10 | 6.0 |
| 2 | 3 | 5 | (2/5) × 10 | 4.0 |
| 0 | 5 | 5 | (0/5) × 10 | 0.0 |

---

## 🔧 HELPER FUNCTIONS

### 1. Sinh mã BaiLam
```php
private function generateMaBaiLam()
{
    do {
        $ma = 'BL' . str_pad(rand(1, 99999999), 8, '0', STR_PAD_LEFT);
    } while (BaiLam::where('MaBaiLam', $ma)->exists());
    
    return $ma; // Ví dụ: BL00012345
}
```

### 2. Sinh mã KetQua
```php
private function generateMaKetQua()
{
    do {
        $ma = 'KQ' . str_pad(rand(1, 99999999), 8, '0', STR_PAD_LEFT);
    } while (KetQua::where('MaKQ', $ma)->exists());
    
    return $ma; // Ví dụ: KQ00012345
}
```

---

## 📊 QUAN HỆ DATABASE

```
DeThi (1) ----< DETHI_CAUHOI >---- (n) CauHoi
  |                                     |
  |                                     | DapAn
  |                                     |
  v                                     v
BaiLam (n) ---> (1) KetQua        [So sánh đáp án]
  |                 |
  | MaHS            | MaHS
  v                 v
HocSinh ---------> (chung)
```

---

## ✅ CHECKLIST VALIDATION

- [x] MaDe phải tồn tại trong bảng DeThi
- [x] MaHS phải tồn tại trong bảng HocSinh
- [x] CauTraLoi phải là mảng không rỗng
- [x] Mỗi MaCH phải tồn tại trong bảng CauHoi
- [x] DapAnChon phải là A, B, C hoặc D
- [x] User phải có Role = 'hocsinh'
- [x] User chỉ được nộp bài cho chính mình

---

## 🎓 VÍ DỤ THỰC TẾ

### Request:
```http
POST /api/baithi/nop
Authorization: Bearer 1|abc123xyz
Content-Type: application/json

{
  "MaDe": "DT001",
  "MaHS": "HS001",
  "CauTraLoi": [
    {"MaCH": "CH001", "DapAnChon": "A"},
    {"MaCH": "CH002", "DapAnChon": "B"},
    {"MaCH": "CH003", "DapAnChon": "A"},  // Sai (Đáp án đúng: B)
    {"MaCH": "CH004", "DapAnChon": "C"},
    {"MaCH": "CH005", "DapAnChon": "C"}
  ]
}
```

### Response:
```json
{
  "success": true,
  "message": "Nộp bài thành công",
  "data": {
    "Diem": 8.0,
    "SoCauDung": 4,
    "SoCauSai": 1,
    "SoCauKhongLam": 0,
    "TongSoCau": 5
  }
}
```

---

## 🚨 ERROR HANDLING

| Lỗi | HTTP Code | Message |
|-----|-----------|---------|
| Thiếu MaDe | 422 | Mã đề thi không được để trống |
| Thiếu MaHS | 422 | Mã học sinh không được để trống |
| Đề không tồn tại | 404 | Không tìm thấy đề thi |
| Không phải học sinh | 403 | Chỉ học sinh mới được phép nộp bài |
| Nộp bài cho người khác | 403 | Bạn không có quyền nộp bài cho học sinh khác |
| Lỗi server | 500 | Có lỗi xảy ra khi nộp bài |

---

**✨ Hàm nopBai() đã được implement đầy đủ và sẵn sàng sử dụng!**
