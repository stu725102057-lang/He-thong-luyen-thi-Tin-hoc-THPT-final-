<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NganHangCauHoi;
use App\Models\CauHoi;
use Illuminate\Support\Str;

class CauHoiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo ngân hàng câu hỏi
        $nganHang = NganHangCauHoi::firstOrCreate(
            ['MaNH' => 'NH001'],
            [
                'TenNH' => 'Ngân hàng câu hỏi Tin học THPT',
                'MoTa' => 'Ngân hàng câu hỏi trắc nghiệm môn Tin học THPT Quốc gia'
            ]
        );

        // Danh sách câu hỏi mẫu theo chuyên đề
        $cauHois = [
            // Chuyên đề: Hệ điều hành
            [
                'NoiDung' => 'Hệ điều hành Windows là loại phần mềm gì?',
                'DapAn' => 'A',
                'DapAnA' => 'Phần mềm hệ thống',
                'DapAnB' => 'Phần mềm ứng dụng',
                'DapAnC' => 'Phần mềm tiện ích',
                'DapAnD' => 'Phần mềm máy chủ',
                'DoKho' => 'De',
                'ChuyenDe' => 'Hệ điều hành'
            ],
            [
                'NoiDung' => 'Chức năng chính của hệ điều hành là gì?',
                'DapAn' => 'B',
                'DapAnA' => 'Tạo văn bản',
                'DapAnB' => 'Quản lý tài nguyên máy tính',
                'DapAnC' => 'Duyệt web',
                'DapAnD' => 'Chỉnh sửa ảnh',
                'DoKho' => 'TB',
                'ChuyenDe' => 'Hệ điều hành'
            ],
            
            // Chuyên đề: Mạng máy tính
            [
                'NoiDung' => 'Giao thức HTTP hoạt động ở tầng nào trong mô hình TCP/IP?',
                'DapAn' => 'D',
                'DapAnA' => 'Tầng Vật lý',
                'DapAnB' => 'Tầng Liên kết dữ liệu',
                'DapAnC' => 'Tầng Mạng',
                'DapAnD' => 'Tầng Ứng dụng',
                'DoKho' => 'TB',
                'ChuyenDe' => 'Mạng máy tính'
            ],
            [
                'NoiDung' => 'Địa chỉ IP là gì?',
                'DapAn' => 'A',
                'DapAnA' => 'Địa chỉ logic xác định thiết bị trên mạng',
                'DapAnB' => 'Địa chỉ vật lý của thiết bị',
                'DapAnC' => 'Tên miền của website',
                'DapAnD' => 'Mã định danh của phần mềm',
                'DoKho' => 'De',
                'ChuyenDe' => 'Mạng máy tính'
            ],
            
            // Chuyên đề: Cơ sở dữ liệu
            [
                'NoiDung' => 'SQL là viết tắt của gì?',
                'DapAn' => 'C',
                'DapAnA' => 'Simple Query Language',
                'DapAnB' => 'Standard Quality Language',
                'DapAnC' => 'Structured Query Language',
                'DapAnD' => 'System Query Language',
                'DoKho' => 'De',
                'ChuyenDe' => 'Cơ sở dữ liệu'
            ],
            [
                'NoiDung' => 'Khóa chính (Primary Key) trong cơ sở dữ liệu có đặc điểm gì?',
                'DapAn' => 'B',
                'DapAnA' => 'Có thể trùng lặp',
                'DapAnB' => 'Duy nhất và không null',
                'DapAnC' => 'Có thể null',
                'DapAnD' => 'Không bắt buộc',
                'DoKho' => 'TB',
                'ChuyenDe' => 'Cơ sở dữ liệu'
            ],
            [
                'NoiDung' => 'Lệnh nào dùng để lấy dữ liệu từ bảng trong SQL?',
                'DapAn' => 'A',
                'DapAnA' => 'SELECT',
                'DapAnB' => 'INSERT',
                'DapAnC' => 'UPDATE',
                'DapAnD' => 'DELETE',
                'DoKho' => 'De',
                'ChuyenDe' => 'Cơ sở dữ liệu'
            ],
            
            // Chuyên đề: Lập trình
            [
                'NoiDung' => 'Biến trong lập trình là gì?',
                'DapAn' => 'B',
                'DapAnA' => 'Một hàm',
                'DapAnB' => 'Vùng nhớ lưu trữ dữ liệu',
                'DapAnC' => 'Một lệnh',
                'DapAnD' => 'Một file',
                'DoKho' => 'De',
                'ChuyenDe' => 'Lập trình'
            ],
            [
                'NoiDung' => 'Vòng lặp for trong lập trình dùng để làm gì?',
                'DapAn' => 'C',
                'DapAnA' => 'Kiểm tra điều kiện',
                'DapAnB' => 'Khai báo biến',
                'DapAnC' => 'Lặp lại một đoạn code nhiều lần',
                'DapAnD' => 'Tạo hàm mới',
                'DoKho' => 'TB',
                'ChuyenDe' => 'Lập trình'
            ],
            [
                'NoiDung' => 'Hàm (Function) trong lập trình có tác dụng gì?',
                'DapAn' => 'A',
                'DapAnA' => 'Tái sử dụng code',
                'DapAnB' => 'Lưu trữ dữ liệu',
                'DapAnC' => 'Tạo giao diện',
                'DapAnD' => 'Kết nối database',
                'DoKho' => 'TB',
                'ChuyenDe' => 'Lập trình'
            ],
            
            // Chuyên đề: An toàn thông tin
            [
                'NoiDung' => 'Virus máy tính là gì?',
                'DapAn' => 'D',
                'DapAnA' => 'Phần mềm diệt virus',
                'DapAnB' => 'Phần cứng máy tính',
                'DapAnC' => 'Hệ điều hành',
                'DapAnD' => 'Phần mềm độc hại',
                'DoKho' => 'De',
                'ChuyenDe' => 'An toàn thông tin'
            ],
            [
                'NoiDung' => 'Mã hóa dữ liệu có mục đích gì?',
                'DapAn' => 'B',
                'DapAnA' => 'Tăng tốc độ xử lý',
                'DapAnB' => 'Bảo mật thông tin',
                'DapAnC' => 'Giảm dung lượng file',
                'DapAnD' => 'Tăng hiệu suất mạng',
                'DoKho' => 'TB',
                'ChuyenDe' => 'An toàn thông tin'
            ],
            
            // Chuyên đề: Tin học văn phòng
            [
                'NoiDung' => 'Microsoft Excel là phần mềm gì?',
                'DapAn' => 'C',
                'DapAnA' => 'Soạn thảo văn bản',
                'DapAnB' => 'Tạo slide thuyết trình',
                'DapAnC' => 'Bảng tính',
                'DapAnD' => 'Quản lý email',
                'DoKho' => 'De',
                'ChuyenDe' => 'Tin học văn phòng'
            ],
            [
                'NoiDung' => 'Phím tắt Ctrl+C trong Windows có chức năng gì?',
                'DapAn' => 'A',
                'DapAnA' => 'Sao chép',
                'DapAnB' => 'Dán',
                'DapAnC' => 'Cắt',
                'DapAnD' => 'Lưu',
                'DoKho' => 'De',
                'ChuyenDe' => 'Tin học văn phòng'
            ],
            
            // Chuyên đề: Thuật toán
            [
                'NoiDung' => 'Độ phức tạp của thuật toán sắp xếp nổi bọt (Bubble Sort) là gì?',
                'DapAn' => 'B',
                'DapAnA' => 'O(n)',
                'DapAnB' => 'O(n²)',
                'DapAnC' => 'O(log n)',
                'DapAnD' => 'O(n log n)',
                'DoKho' => 'Kho',
                'ChuyenDe' => 'Thuật toán'
            ],
            [
                'NoiDung' => 'Thuật toán tìm kiếm nhị phân (Binary Search) yêu cầu dữ liệu phải như thế nào?',
                'DapAn' => 'C',
                'DapAnA' => 'Ngẫu nhiên',
                'DapAnB' => 'Giảm dần',
                'DapAnC' => 'Đã được sắp xếp',
                'DapAnD' => 'Trùng lặp',
                'DoKho' => 'TB',
                'ChuyenDe' => 'Thuật toán'
            ],
        ];

        // Thêm câu hỏi vào database
        foreach ($cauHois as $index => $cau) {
            $maCH = 'CH' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);
            
            CauHoi::updateOrCreate(
                ['MaCH' => $maCH],
                array_merge($cau, [
                    'MaNH' => $nganHang->MaNH
                ])
            );
        }

        $this->command->info('Đã tạo ' . count($cauHois) . ' câu hỏi mẫu với chuyên đề!');
    }
}
