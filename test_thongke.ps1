# TEST THONG KE CA NHAN
$baseUrl = "http://127.0.0.1:8000/api"

Write-Host "================================" -ForegroundColor Cyan
Write-Host "TEST THONG KE CA NHAN HOC SINH" -ForegroundColor Cyan
Write-Host "================================" -ForegroundColor Cyan
Write-Host ""

# BUOC 1: DANG NHAP
Write-Host "[1/3] Dang nhap voi tai khoan hoc sinh..." -ForegroundColor Yellow

$loginData = @{
    TenDangNhap = "hs001"
    MatKhau = "123456"
} | ConvertTo-Json

try {
    $loginResponse = Invoke-RestMethod -Uri "$baseUrl/login" -Method POST -Body $loginData -ContentType "application/json"
    
    if ($loginResponse.success -eq $true) {
        $token = $loginResponse.token
        Write-Host "OK Dang nhap thanh cong!" -ForegroundColor Green
        Write-Host "  Token: $($token.Substring(0, 20))..." -ForegroundColor Gray
        Write-Host "  Vai tro: $($loginResponse.user.VaiTro)" -ForegroundColor Gray
        Write-Host "  Ten: $($loginResponse.user.HoTen)" -ForegroundColor Gray
        Write-Host ""
    } else {
        Write-Host "LOI: Dang nhap that bai!" -ForegroundColor Red
        exit
    }
} catch {
    Write-Host "LOI: Khong ket noi duoc API dang nhap!" -ForegroundColor Red
    Write-Host $_.Exception.Message -ForegroundColor Red
    exit
}

# BUOC 2: GOI API THONG KE
Write-Host "[2/3] Goi API thong ke ca nhan..." -ForegroundColor Yellow

$headers = @{
    "Authorization" = "Bearer $token"
    "Accept" = "application/json"
}

try {
    $response = Invoke-RestMethod -Uri "$baseUrl/thong-ke-ca-nhan" -Method GET -Headers $headers
    
    if ($response.success -eq $true) {
        Write-Host "OK Lay thong ke thanh cong!" -ForegroundColor Green
        Write-Host ""
        
        # BUOC 3: HIEN THI KET QUA
        Write-Host "[3/3] Hien thi ket qua:" -ForegroundColor Yellow
        Write-Host ""
        
        $data = $response.data
        
        # 1. THONG TIN CHUNG
        Write-Host "========== THONG TIN CHUNG ==========" -ForegroundColor Cyan
        Write-Host "Tong so bai lam: $($data.thongTinChung.tongSoBaiLam) bai"
        Write-Host "Diem trung binh: $($data.thongTinChung.diemTrungBinh)/10"
        Write-Host "Diem cao nhat: $($data.thongTinChung.diemCaoNhat)/10"
        Write-Host "Diem thap nhat: $($data.thongTinChung.diemThapNhat)/10"
        Write-Host "Ty le dung TB: $($data.thongTinChung.tiLeDungTrungBinh) phan tram"
        Write-Host "Tong cau da lam: $($data.thongTinChung.tongSoCauDaLam) cau"
        Write-Host "  - Cau dung: $($data.thongTinChung.tongSoCauDung)" -ForegroundColor Green
        Write-Host "  - Cau sai: $($data.thongTinChung.tongSoCauSai)" -ForegroundColor Red
        Write-Host "  - Khong lam: $($data.thongTinChung.tongSoCauKhongLam)" -ForegroundColor Yellow
        Write-Host ""
        
        # 2. LICH SU DIEM
        if ($data.lichSuDiem.Count -gt 0) {
            Write-Host "========== LICH SU DIEM ==========" -ForegroundColor Cyan
            $data.lichSuDiem | ForEach-Object {
                $color = if ($_.diem -ge 8) { "Green" } elseif ($_.diem -ge 6.5) { "Yellow" } else { "Red" }
                Write-Host "Lan $($_.lanThi) ($($_.ngayRutGon)): $($_.diem)/10 - $($_.tenDe)" -ForegroundColor $color
            }
            Write-Host ""
        }
        
        # 3. TY LE DUNG/SAI
        Write-Host "========== TY LE DUNG/SAI ==========" -ForegroundColor Cyan
        Write-Host "Dung: $($data.tyLeDungSai.dung) cau - $($data.tyLeDungSai.phanTram.dung) phan tram" -ForegroundColor Green
        Write-Host "Sai: $($data.tyLeDungSai.sai) cau - $($data.tyLeDungSai.phanTram.sai) phan tram" -ForegroundColor Red
        Write-Host "Khong lam: $($data.tyLeDungSai.khongLam) cau - $($data.tyLeDungSai.phanTram.khongLam) phan tram" -ForegroundColor Yellow
        Write-Host ""
        
        # 4. PHAN TICH CHUYEN DE
        if ($data.phanTichChuyenDe.Count -gt 0) {
            Write-Host "========== PHAN TICH CHUYEN DE ==========" -ForegroundColor Cyan
            $data.phanTichChuyenDe | ForEach-Object {
                $color = if ($_.tyLeDung -ge 70) { "Green" } elseif ($_.tyLeDung -ge 50) { "Yellow" } else { "Red" }
                Write-Host "$($_.tenChuyenDe): $($_.tyLeDung) phan tram [$($_.xepLoai)] - $($_.soCauDung)/$($_.tongSoCau)" -ForegroundColor $color
            }
            Write-Host ""
        }
        
        # 5. DIEM MANH/YEU
        Write-Host "========== DIEM MANH/YEU ==========" -ForegroundColor Cyan
        
        if ($data.diemManhYeu.diemManh.Count -gt 0) {
            Write-Host "DIEM MANH (>=70%):" -ForegroundColor Green
            $data.diemManhYeu.diemManh | ForEach-Object {
                Write-Host "  - $($_.tenChuyenDe): $($_.tyLeDung) phan tram" -ForegroundColor Green
            }
        }
        
        if ($data.diemManhYeu.diemYeu.Count -gt 0) {
            Write-Host "DIEM YEU (<50%):" -ForegroundColor Red
            $data.diemManhYeu.diemYeu | ForEach-Object {
                Write-Host "  - $($_.tenChuyenDe): $($_.tyLeDung) phan tram" -ForegroundColor Red
            }
        }
        
        Write-Host ""
        Write-Host "KHUYEN NGHI:" -ForegroundColor Cyan
        Write-Host $data.diemManhYeu.khuyenNghi -ForegroundColor White
        Write-Host ""
        
        # 6. BIEN DONG DIEM
        if ($data.bienDoDiem.Count -gt 0) {
            Write-Host "========== BIEN DONG DIEM ==========" -ForegroundColor Cyan
            $data.bienDoDiem | ForEach-Object {
                $color = if ($_.xuHuong -eq "Tang") { "Green" } elseif ($_.xuHuong -eq "Giam") { "Red" } else { "Yellow" }
                $sign = if ($_.chenhLech -gt 0) { "+" } else { "" }
                Write-Host "Lan $($_.lanThi): $($_.diemTruoc) -> $($_.diemHienTai) ($sign$($_.chenhLech)) - $($_.xuHuong)" -ForegroundColor $color
            }
            Write-Host ""
        }
        
        # LUU FILE JSON
        $response | ConvertTo-Json -Depth 10 | Out-File "TEST_THONG_KE_RESPONSE.json" -Encoding UTF8
        
        Write-Host "================================" -ForegroundColor Green
        Write-Host "TEST THANH CONG!" -ForegroundColor Green
        Write-Host "================================" -ForegroundColor Green
        Write-Host ""
        Write-Host "Da luu response vao: TEST_THONG_KE_RESPONSE.json" -ForegroundColor Cyan
        
    } else {
        Write-Host "LOI: Lay thong ke that bai!" -ForegroundColor Red
        Write-Host $response.message -ForegroundColor Red
    }
    
} catch {
    Write-Host "LOI: Khong goi duoc API thong ke!" -ForegroundColor Red
    Write-Host $_.Exception.Message -ForegroundColor Red
}
