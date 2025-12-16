# ⚠️ VẤN ĐỀ PHÁT HIỆN: API LƯU NHÁP CHƯA HOÀN THIỆN

## 🔍 Phát hiện

File: `app/Http/Controllers/BaiThiController.php`  
Method: `luuBaiLam()` (line 237-260)

**Hiện trạng:**
```php
public function luuBaiLam(Request $request) 
{
     // Validate cơ bản
     $validator = Validator::make($request->all(), [
        'MaDe' => 'required|string',
        'MaHS' => 'required|string',
        'CauTraLoi' => 'required|array',
    ]);

    if ($validator->fails()) return response()->json(['success' => false], 422);

    try {
        // Tìm bài làm đang làm dở (nếu có) hoặc tạo mới
        // Logic ở đây: Update field DSCauTraLoi, không tính điểm, trạng thái vẫn là "Đang làm"
        
        // Code demo (Bạn cần tùy chỉnh logic update DB của bạn ở đây)
        // ⚠️ CHƯA CÓ LOGIC THỰC SỰ
        
        return response()->json([
            'success' => true,
            'message' => 'Đã lưu nháp'
        ]);
    } catch (\Exception $e) {
        return response()->json(['success' => false], 500);
    }
}
```

**Vấn đề:**
- Chỉ trả về success=true mà không lưu gì vào database
- Frontend gọi API auto-save mỗi 60s nhưng không có tác dụng
- Nếu tắt trình duyệt, mất hết câu trả lời

---

## ✅ GIẢI PHÁP: CODE HOÀN CHỈNH

### Option 1: Lưu vào BaiLam hiện có (Nếu đã bắt đầu làm bài)

```php
/**
 * [MỚI] API Lưu nháp bài làm (UR-05.2)
 * Dùng cho tính năng tự động lưu mỗi 1 phút
 */
public function luuBaiLam(Request $request) 
{
    \Log::info('=== LƯU NHÁP BÀI LÀM START ===');
    \Log::info('Request data:', $request->all());
    
    // 1. VALIDATE DỮ LIỆU ĐẦU VÀO
    $validator = Validator::make($request->all(), [
        'MaBaiLam' => 'required|string|exists:BaiLam,MaBaiLam',
        'CauTraLoi' => 'required|array',
    ], [
        'MaBaiLam.required' => 'Mã bài làm không được để trống',
        'MaBaiLam.exists' => 'Bài làm không tồn tại',
        'CauTraLoi.required' => 'Danh sách câu trả lời không được để trống',
        'CauTraLoi.array' => 'Danh sách câu trả lời phải là mảng',
    ]);

    if ($validator->fails()) {
        \Log::error('Validation failed:', $validator->errors()->toArray());
        return response()->json([
            'success' => false,
            'message' => 'Dữ liệu không hợp lệ',
            'errors' => $validator->errors()
        ], 422);
    }

    try {
        $user = $request->user();
        $maBaiLam = $request->MaBaiLam;
        $cauTraLoi = $request->CauTraLoi;

        // 2. TÌM BÀI LÀM ĐANG LÀM DỞ
        $baiLam = BaiLam::where('MaBaiLam', $maBaiLam)
            ->where('TrangThai', 'DangLam') // Chỉ lưu nếu đang làm
            ->first();

        if (!$baiLam) {
            \Log::error('BaiLam not found or already submitted:', ['MaBaiLam' => $maBaiLam]);
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy bài làm hoặc bài làm đã nộp'
            ], 404);
        }

        // 3. KIỂM TRA QUYỀN
        $hocSinh = \App\Models\HocSinh::where('MaTK', $user->MaTK)->first();
        
        if (!$hocSinh || $baiLam->MaHS !== $hocSinh->MaHS) {
            \Log::warning('User trying to save for different student');
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền lưu bài làm này'
            ], 403);
        }

        // 4. CẬP NHẬT DSCauTraLoi (JSON)
        // Format: [{"MaCH": "CH00000001", "TraLoi": "A"}, ...]
        $dsCauTraLoiMoi = [];
        foreach ($cauTraLoi as $maCH => $dapAn) {
            if (!empty($dapAn)) { // Chỉ lưu câu đã chọn
                $dsCauTraLoiMoi[] = [
                    'MaCH' => $maCH,
                    'TraLoi' => $dapAn
                ];
            }
        }

        // 5. LƯU VÀO DATABASE
        $baiLam->DSCauTraLoi = json_encode($dsCauTraLoiMoi);
        $baiLam->updated_at = now(); // Đánh dấu thời gian lưu gần nhất
        $baiLam->save();

        \Log::info('BaiLam saved successfully:', [
            'MaBaiLam' => $maBaiLam,
            'SoCauDaLam' => count($dsCauTraLoiMoi)
        ]);

        // 6. TRẢ VỀ KẾT QUẢ
        return response()->json([
            'success' => true,
            'message' => 'Đã lưu nháp thành công',
            'data' => [
                'MaBaiLam' => $maBaiLam,
                'SoCauDaLam' => count($dsCauTraLoiMoi),
                'ThoiGianLuu' => $baiLam->updated_at->toDateTimeString()
            ]
        ], 200);

    } catch (\Exception $e) {
        \Log::error('=== LƯU NHÁP ERROR ===');
        \Log::error('Error message: ' . $e->getMessage());
        \Log::error('Stack trace: ' . $e->getTraceAsString());
        
        return response()->json([
            'success' => false,
            'message' => 'Có lỗi xảy ra khi lưu nháp: ' . $e->getMessage()
        ], 500);
    }
}
```

---

### Option 2: Tạo mới BaiLam nếu chưa có (Backup plan)

Nếu frontend không gửi `MaBaiLam`, bạn cần tìm hoặc tạo mới:

```php
public function luuBaiLam(Request $request) 
{
    // ... (validation tương tự nhưng không require MaBaiLam)
    
    $baiLam = BaiLam::firstOrCreate(
        [
            'MaHS' => $hocSinh->MaHS,
            'MaDe' => $request->MaDe,
            'TrangThai' => 'DangLam'
        ],
        [
            'MaBaiLam' => $this->generateMaBaiLam(),
            'ThoiGianBatDau' => now(),
            'DSCauTraLoi' => json_encode([]),
        ]
    );
    
    // Sau đó update như Option 1
    $baiLam->DSCauTraLoi = json_encode($dsCauTraLoiMoi);
    $baiLam->save();
    
    // ...
}
```

---

## 🔧 HƯỚNG DẪN THAY THẾ CODE

### Bước 1: Mở file BaiThiController.php

```
File: app/Http/Controllers/BaiThiController.php
Line: 237-260
```

### Bước 2: Xóa method cũ (line 237-260)

Xóa đoạn code:
```php
public function luuBaiLam(Request $request) 
{
     // Validate cơ bản
     $validator = Validator::make($request->all(), [
        'MaDe' => 'required|string',
        'MaHS' => 'required|string',
        'CauTraLoi' => 'required|array',
    ]);
    // ... (code cũ)
}
```

### Bước 3: Thay bằng code mới (Option 1)

Copy toàn bộ code từ phần "Option 1" ở trên.

### Bước 4: Kiểm tra Frontend

Mở `resources/views/app.blade.php`, tìm dòng 6795:

```javascript
const data = {
    MaBaiLam: this.examData.MaBaiLam,  // ⚠️ Đảm bảo có field này
    CauTraLoi: this.answers
};

const response = await this.apiCall('/luu-nhap', {
    method: 'POST',
    body: JSON.stringify(data)
});
```

**Kiểm tra:**
- `this.examData.MaBaiLam` phải có giá trị (được set khi bắt đầu làm bài)
- `this.answers` là object dạng: `{"CH00000001": "A", "CH00000002": "B", ...}`

Nếu format khác, cần điều chỉnh backend cho phù hợp.

---

## 🧪 TEST CASE

### Test 1: Auto-save hoạt động

1. Đăng nhập học sinh
2. Bắt đầu làm bài thi
3. Chọn đáp án cho 1-2 câu
4. **Đợi 60 giây**
5. Mở **Network tab (F12)** → Xem request `POST /api/luu-nhap`
6. Response phải có `success: true`
7. Vào phpMyAdmin → Bảng `BaiLam` → Xem cột `DSCauTraLoi` đã có dữ liệu JSON

### Test 2: Khôi phục sau khi refresh

1. Làm bài, đợi auto-save
2. **F5 (Refresh trang)**
3. Vào lại bài thi
4. Đáp án đã chọn phải vẫn còn (load từ database)

### Test 3: Không lưu sau khi nộp bài

1. Nộp bài (TrangThai → 'DaNop')
2. Auto-save vẫn chạy → Nhưng phải return lỗi 404 (vì bài làm không còn trạng thái 'DangLam')

---

## 📊 ĐÁNH GIÁ TÁC ĐỘNG

### Trước khi sửa:
- ❌ Auto-save không hoạt động
- ❌ Refresh trang → Mất dữ liệu
- ❌ Không đạt yêu cầu báo cáo: "Hệ thống phải tự động lưu bài làm tạm thời mỗi 1 phút"

### Sau khi sửa:
- ✅ Auto-save mỗi 60s
- ✅ Dữ liệu được lưu vào database
- ✅ Đạt 100% yêu cầu báo cáo

---

## 🚨 LƯU Ý QUAN TRỌNG

1. **Backup code cũ trước khi sửa:**
   ```bash
   cp app/Http/Controllers/BaiThiController.php app/Http/Controllers/BaiThiController.php.backup
   ```

2. **Test kỹ trước khi deploy:**
   - Test trên localhost trước
   - Kiểm tra log: `storage/logs/laravel.log`
   - Monitor Network tab khi auto-save

3. **Frontend phải gửi đúng format:**
   - Nếu backend nhận `MaBaiLam`, frontend phải gửi
   - Format `CauTraLoi` phải khớp với logic backend

---

## 📞 PROMPT CHO AI

Để AI tự động sửa:

```
@BaiThiController.php

Hãy thay thế method luuBaiLam() (line 237-260) bằng code hoàn chỉnh theo yêu cầu:

1. Nhận input: MaBaiLam, CauTraLoi (array)
2. Validate đầu vào
3. Tìm BaiLam với TrangThai = 'DangLam'
4. Kiểm tra quyền (chỉ học sinh chủ bài làm mới được lưu)
5. Cập nhật DSCauTraLoi (JSON) vào database
6. Trả về success: true

Tham khảo logic từ method nopBai() để đảm bảo nhất quán.
```

---

**Tác giả:** GitHub Copilot  
**Ngày:** 14/12/2025  
**Mức độ ưu tiên:** 🔴 CAO (Ảnh hưởng trực tiếp đến trải nghiệm người dùng)
