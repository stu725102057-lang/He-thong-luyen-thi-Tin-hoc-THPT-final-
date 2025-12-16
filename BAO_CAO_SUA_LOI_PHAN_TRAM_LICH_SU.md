# ================================================================
# BAO CAO SUA LOI TY LE PHAN TRAM LICH SU LAM BAI
# ================================================================

## NGAY: 14/12/2025 - 19:30

## LOI PHAT HIEN:
```
Trong "Lịch sử làm bài", cột "Kết quả" hiển thị:
- 9 đúng / 1 sai
- (0%)  <-- SAI! Phải là 90%
```

## NGUYEN NHAN:
1. Frontend tinh ty le: `tiLeDung = (item.SoCauDung / item.TongSoCau) * 100`
2. API backend `layLichSuThi()` CHI tra ve data tu bang `KetQua`
3. Bang `KetQua` KHONG co cot `TongSoCau` 
4. => `item.TongSoCau` = undefined
5. => `undefined` chia cho bat ky so nao = NaN
6. => `Math.round(NaN)` = 0
7. => Hien thi "(0%)"

## GIAI PHAP:

### Backend: Them TongSoCau vao response
```php
// Trong BaiThiController.php - method layLichSuThi()

// CU (THIEU TongSoCau):
$lichSu = KetQua::with('deThi')
    ->where('MaHS', $hocSinh->MaHS)
    ->orderBy('created_at', 'desc')
    ->get();

return response()->json([
    'success' => true,
    'data' => $lichSu
]);

// MOI (CO TongSoCau):
$lichSu = KetQua::with('deThi')
    ->where('MaHS', $hocSinh->MaHS)
    ->orderBy('created_at', 'desc')
    ->get();

// Them TongSoCau = SoCauDung + SoCauSai + SoCauKhongLam
$lichSu = $lichSu->map(function($item) {
    $item->TongSoCau = $item->SoCauDung + $item->SoCauSai + $item->SoCauKhongLam;
    return $item;
});

return response()->json([
    'success' => true,
    'data' => $lichSu
]);
```

## KET QUA SAU KHI SUA:

### API Response:
```json
{
  "success": true,
  "data": [
    {
      "MaBaiLam": "BL94546299",
      "Diem": 9.0,
      "SoCauDung": 9,
      "SoCauSai": 1,
      "SoCauKhongLam": 0,
      "TongSoCau": 10,      // <- THEM MOI
      "de_thi": {
        "TenDe": "Đề thi thử Tin học THPT Quốc gia 2025"
      }
    }
  ]
}
```

### Frontend Calculation:
```javascript
const tiLeDung = item.TongSoCau > 0 
    ? Math.round((item.SoCauDung / item.TongSoCau) * 100) 
    : 0;

// Voi du lieu tren:
// tiLeDung = Math.round((9 / 10) * 100) = Math.round(90) = 90
```

### Hien thi UI:
```
9 đúng / 1 sai
(90%)          // <- DUNG ROI!
```

## FILE DA SUA:
- app/Http/Controllers/BaiThiController.php
  + Method: layLichSuThi()
  + Dong: ~492-502
  + Them: map() de tinh TongSoCau

## TEST RESULTS:

### Before Fix:
```
Mã bài làm    | Điểm | Kết quả
BL94546299    | 9.00 | 9 đúng / 1 sai (0%)    <- SAI
BL33098415    | 5.00 | 5 đúng / 5 sai (0%)    <- SAI
```

### After Fix:
```
Mã bài làm    | Điểm | Kết quả
BL94546299    | 9.00 | 9 đúng / 1 sai (90%)   <- DUNG
BL33098415    | 5.00 | 5 đúng / 5 sai (50%)   <- DUNG
BL61424604    | 2.00 | 2 đúng / 3 sai (40%)   <- DUNG
```

## VALIDATION:
Đã test API với curl:
```powershell
curl http://127.0.0.1:8000/api/lich-su-thi
Response: TongSoCau = 10 ✓
```

## HUONG DAN TEST:
1. Reload trang (Ctrl+F5)
2. Đăng nhập: hocsinh / 123456
3. Click "Lịch sử làm bài"
4. Kiểm tra cột "Kết quả"
5. Xác nhận phần trăm hiển thị đúng

## EXPECTED OUTPUT:
```
BL94546299 | 9.00/10 | 9 đúng / 1 sai  (90%)
BL33098415 | 5.00/10 | 5 đúng / 5 sai  (50%)
BL61424604 | 2.00/10 | 2 đúng / 3 sai  (40%)
BL78004879 | 3.00/10 | 3 đúng / 6 sai  (33%)
BL001      | 7.00/10 | 7 đúng / 3 sai  (70%)
```

## NOTES:
- Bang KetQua KHONG co cot TongSoCau trong database
- Phai tinh = SoCauDung + SoCauSai + SoCauKhongLam
- Frontend da co logic tinh % DUNG, chi can backend tra ve TongSoCau
- Su dung map() de them field vao collection, KHONG thay doi database

## TONG KET:
✅ API giờ trả về TongSoCau
✅ Frontend tính % chính xác
✅ Hiển thị đúng: "9 đúng / 1 sai (90%)"
✅ Không cần sửa database schema
✅ Không cần sửa frontend code
