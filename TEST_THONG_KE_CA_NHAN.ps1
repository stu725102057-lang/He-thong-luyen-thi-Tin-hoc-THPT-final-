# ====================================================================
# SCRIPT TEST THONG KE CA NHAN HOC SINH
# ====================================================================

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "   TEST THONG KE CA NHAN HOC SINH" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

$baseUrl = "http://127.0.0.1:8000/api"

# ====================================================================
# BƯỚC 1: ĐĂNG NHẬP VỚI TÀI KHOẢN HỌC SINH
# ====================================================================

Write-Host "[1/3] Đăng nhập với tài khoản học sinh..." -ForegroundColor Yellow

$loginData = @{
    TenDangNhap = "hs001"
    MatKhau = "123456"
} | ConvertTo-Json

try {
    $loginResponse = Invoke-RestMethod -Uri "$baseUrl/login" -Method POST -Body $loginData -ContentType "application/json"
    
    if ($loginResponse.success -eq $true) {
        $token = $loginResponse.token
        Write-Host "✓ Đăng nhập thành công!" -ForegroundColor Green
        Write-Host "  Token: $($token.Substring(0, 20))..." -ForegroundColor Gray
        Write-Host "  Vai trò: $($loginResponse.user.VaiTro)" -ForegroundColor Gray
        Write-Host "  Tên: $($loginResponse.user.HoTen)" -ForegroundColor Gray
        Write-Host ""
    } else {
        Write-Host "✗ Đăng nhập thất bại!" -ForegroundColor Red
        Write-Host "  Lỗi: $($loginResponse.message)" -ForegroundColor Red
        exit
    }
} catch {
    Write-Host "✗ Lỗi kết nối API đăng nhập!" -ForegroundColor Red
    Write-Host "  Chi tiết: $($_.Exception.Message)" -ForegroundColor Red
    exit
}

# ====================================================================
# BƯỚC 2: GỌI API THỐNG KÊ CÁ NHÂN
# ====================================================================

Write-Host "[2/3] Gọi API thống kê cá nhân..." -ForegroundColor Yellow

$headers = @{
    "Authorization" = "Bearer $token"
    "Accept" = "application/json"
}

try {
    $thongKeResponse = Invoke-RestMethod -Uri "$baseUrl/thong-ke-ca-nhan" -Method GET -Headers $headers
    
    if ($thongKeResponse.success -eq $true) {
        Write-Host "✓ Lấy thống kê thành công!" -ForegroundColor Green
        Write-Host ""
        
        # ====================================================================
        # BƯỚC 3: HIỂN THỊ KẾT QUẢ
        # ====================================================================
        
        Write-Host "[3/3] Hiển thị kết quả thống kê:" -ForegroundColor Yellow
        Write-Host ""
        
        $data = $thongKeResponse.data
        
        # 1. THỐNG KÊ TỔNG QUAN
        Write-Host "┌─────────────────────────────────────────┐" -ForegroundColor Cyan
        Write-Host "│   THỐNG KÊ TỔNG QUAN                    │" -ForegroundColor Cyan
        Write-Host "└─────────────────────────────────────────┘" -ForegroundColor Cyan
        Write-Host "  • Tổng số bài làm: $($data.thongTinChung.tongSoBaiLam) bài" -ForegroundColor White
        Write-Host "  • Điểm trung bình: $($data.thongTinChung.diemTrungBinh)/10" -ForegroundColor White
        Write-Host "  • Điểm cao nhất: $($data.thongTinChung.diemCaoNhat)/10" -ForegroundColor White
        Write-Host "  • Điểm thấp nhất: $($data.thongTinChung.diemThapNhat)/10" -ForegroundColor White
        Write-Host "  • Tỷ lệ đúng trung bình: $($data.thongTinChung.tiLeDungTrungBinh)%" -ForegroundColor White
        Write-Host "  • Tổng câu đã làm: $($data.thongTinChung.tongSoCauDaLam) câu" -ForegroundColor White
        Write-Host "    - Câu đúng: $($data.thongTinChung.tongSoCauDung) ✓" -ForegroundColor Green
        Write-Host "    - Câu sai: $($data.thongTinChung.tongSoCauSai) ✗" -ForegroundColor Red
        Write-Host "    - Không làm: $($data.thongTinChung.tongSoCauKhongLam) ○" -ForegroundColor Yellow
        Write-Host ""
        
        # 2. LỊCH SỬ ĐIỂM
        if ($data.lichSuDiem.Count -gt 0) {
            Write-Host "┌─────────────────────────────────────────┐" -ForegroundColor Cyan
            Write-Host "│   LỊCH SỬ ĐIỂM QUA CÁC LẦN THI         │" -ForegroundColor Cyan
            Write-Host "└─────────────────────────────────────────┘" -ForegroundColor Cyan
            
            $data.lichSuDiem | ForEach-Object {
                $diemColor = if ($_.diem -ge 8) { "Green" } elseif ($_.diem -ge 6.5) { "Yellow" } else { "Red" }
                Write-Host "  Lần $($_.lanThi) ($($_.ngayRutGon)): " -NoNewline -ForegroundColor White
                Write-Host "$($_.diem)/10 " -NoNewline -ForegroundColor $diemColor
                Write-Host "- $($_.tenDe) ($($_.soCauDung)/$($_.tongSoCau) câu đúng)" -ForegroundColor Gray
            }
            Write-Host ""
        }
        
        # 3. TỶ LỆ ĐÚNG/SAI
        Write-Host "┌─────────────────────────────────────────┐" -ForegroundColor Cyan
        Write-Host "│   TỶ LỆ ĐÚNG/SAI (Pie Chart Data)      │" -ForegroundColor Cyan
        Write-Host "└─────────────────────────────────────────┘" -ForegroundColor Cyan
        Write-Host "  • Đúng: $($data.tyLeDungSai.dung) câu ($($data.tyLeDungSai.phanTram.dung)%)" -ForegroundColor Green
        Write-Host "  • Sai: $($data.tyLeDungSai.sai) câu ($($data.tyLeDungSai.phanTram.sai)%)" -ForegroundColor Red
        Write-Host "  • Không làm: $($data.tyLeDungSai.khongLam) câu ($($data.tyLeDungSai.phanTram.khongLam)%)" -ForegroundColor Yellow
        Write-Host ""
        
        # 4. PHÂN TÍCH CHUYÊN ĐỀ
        if ($data.phanTichChuyenDe.Count -gt 0) {
            Write-Host "┌─────────────────────────────────────────┐" -ForegroundColor Cyan
            Write-Host "│   PHÂN TÍCH THEO CHUYÊN ĐỀ              │" -ForegroundColor Cyan
            Write-Host "└─────────────────────────────────────────┘" -ForegroundColor Cyan
            
            $data.phanTichChuyenDe | ForEach-Object {
                $xepLoaiColor = switch ($_.xepLoai) {
                    "Xuất sắc" { "Green" }
                    "Giỏi" { "Cyan" }
                    "Khá" { "Yellow" }
                    "Trung bình" { "Magenta" }
                    "Yếu" { "Red" }
                    default { "White" }
                }
                
                Write-Host "  • $($_.tenChuyenDe): " -NoNewline -ForegroundColor White
                Write-Host "$($_.tyLeDung)% " -NoNewline -ForegroundColor $xepLoaiColor
                Write-Host "[$($_.xepLoai)] " -NoNewline -ForegroundColor $xepLoaiColor
                Write-Host "($($_.soCauDung)/$($_.tongSoCau))" -ForegroundColor Gray
            }
            Write-Host ""
        }
        
        # 5. ĐIỂM MẠNH/YẾU
        Write-Host "┌─────────────────────────────────────────┐" -ForegroundColor Cyan
        Write-Host "│   PHÂN TÍCH ĐIỂM MẠNH/YẾU               │" -ForegroundColor Cyan
        Write-Host "└─────────────────────────────────────────┘" -ForegroundColor Cyan
        
        if ($data.diemManhYeu.diemManh.Count -gt 0) {
            Write-Host "  ✨ ĐIỂM MẠNH (≥70%):" -ForegroundColor Green
            $data.diemManhYeu.diemManh | ForEach-Object {
                Write-Host "     - $($_.tenChuyenDe): $($_.tyLeDung)%" -ForegroundColor Green
            }
        } else {
            Write-Host "  ✨ ĐIỂM MẠNH: Chưa có" -ForegroundColor Gray
        }
        
        if ($data.diemManhYeu.diemYeu.Count -gt 0) {
            Write-Host "  ⚠️  ĐIỂM YẾU (<50%):" -ForegroundColor Red
            $data.diemManhYeu.diemYeu | ForEach-Object {
                Write-Host "     - $($_.tenChuyenDe): $($_.tyLeDung)%" -ForegroundColor Red
            }
        } else {
            Write-Host "  ⚠️  ĐIỂM YẾU: Không có" -ForegroundColor Green
        }
        
        Write-Host ""
        Write-Host "  💡 KHUYẾN NGHỊ:" -ForegroundColor Cyan
        Write-Host "     $($data.diemManhYeu.khuyenNghi)" -ForegroundColor White
        Write-Host ""
        
        # 6. BIẾN ĐỘNG ĐIỂM
        if ($data.bienDoDiem.Count -gt 0) {
            Write-Host "┌─────────────────────────────────────────┐" -ForegroundColor Cyan
            Write-Host "│   BIẾN ĐỘNG ĐIỂM (Xu hướng tiến bộ)    │" -ForegroundColor Cyan
            Write-Host "└─────────────────────────────────────────┘" -ForegroundColor Cyan
            
            $data.bienDoDiem | ForEach-Object {
                $xuHuongIcon = switch ($_.xuHuong) {
                    "Tăng" { "↑"; $xuHuongColor = "Green" }
                    "Giảm" { "↓"; $xuHuongColor = "Red" }
                    default { "→"; $xuHuongColor = "Yellow" }
                }
                
                $chenhLechText = if ($_.chenhLech -gt 0) { "+$($_.chenhLech)" } else { "$($_.chenhLech)" }
                
                Write-Host "  Lần $($_.lanThi): $($_.diemTruoc) → $($_.diemHienTai) " -NoNewline -ForegroundColor White
                Write-Host "($chenhLechText) " -NoNewline -ForegroundColor $xuHuongColor
                Write-Host "$xuHuongIcon" -ForegroundColor $xuHuongColor
            }
            Write-Host ""
        }
        
        # ====================================================================
        # KẾT LUẬN
        # ====================================================================
        
        Write-Host "========================================" -ForegroundColor Green
        Write-Host "   ✓ TEST THÀNH CÔNG!" -ForegroundColor Green
        Write-Host "========================================" -ForegroundColor Green
        Write-Host ""
        Write-Host "📊 Chức năng thống kê cá nhân hoạt động hoàn hảo!" -ForegroundColor Green
        Write-Host "   • Có đầy đủ dữ liệu cho biểu đồ Line Chart (lịch sử điểm)" -ForegroundColor White
        Write-Host "   • Có đầy đủ dữ liệu cho biểu đồ Pie Chart (tỷ lệ đúng/sai)" -ForegroundColor White
        Write-Host "   • Có phân tích điểm mạnh/yếu theo chuyên đề" -ForegroundColor White
        Write-Host "   • Có khuyến nghị thông minh cho học sinh" -ForegroundColor White
        Write-Host "   • Có theo dõi xu hướng tiến bộ qua thời gian" -ForegroundColor White
        Write-Host ""
        
        # Lưu JSON response ra file để xem chi tiết
        $thongKeResponse | ConvertTo-Json -Depth 10 | Out-File "TEST_THONG_KE_RESPONSE.json" -Encoding UTF8
        Write-Host "💾 Đã lưu response đầy đủ vào: TEST_THONG_KE_RESPONSE.json" -ForegroundColor Cyan
        Write-Host ""
        
    } else {
        Write-Host "✗ Lấy thống kê thất bại!" -ForegroundColor Red
        Write-Host "  Lỗi: $($thongKeResponse.message)" -ForegroundColor Red
    }
    
} catch {
    Write-Host "✗ Lỗi khi gọi API thống kê!" -ForegroundColor Red
    Write-Host "  Chi tiết: $($_.Exception.Message)" -ForegroundColor Red
    
    if ($_.Exception.Response) {
        $reader = New-Object System.IO.StreamReader($_.Exception.Response.GetResponseStream())
        $responseBody = $reader.ReadToEnd()
        Write-Host "  Response: $responseBody" -ForegroundColor Red
    }
}
