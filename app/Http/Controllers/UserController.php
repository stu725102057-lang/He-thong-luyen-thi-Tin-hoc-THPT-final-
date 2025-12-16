<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\TaiKhoan;
use App\Models\HocSinh;
use App\Models\GiaoVien;
use App\Models\QuanTriVien;

class UserController extends Controller
{
    /**
     * Constructor - Admin-only access
     * Chỉ cho phép admin truy cập User Management (UR-04.1)
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware(function ($request, $next) {
            $user = $request->user();
            
            // Chỉ admin mới được phép quản lý users
            if ($user->Role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ quản trị viên mới có quyền quản lý người dùng'
                ], 403);
            }
            
            return $next($request);
        });
    }

    /**
     * Display a listing of users.
     * Lấy danh sách người dùng (UR-04.1)
     * Hỗ trợ lọc theo Role
     */
    public function index(Request $request)
    {
        try {
            $query = TaiKhoan::query();

            // Lọc theo Role nếu có
            if ($request->has('Role') && !empty($request->Role)) {
                $role = $request->Role;
                
                // Validate Role value
                if (!in_array($role, ['admin', 'giaovien', 'hocsinh'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Role không hợp lệ. Chỉ chấp nhận: admin, giaovien, hocsinh'
                    ], 400);
                }
                
                $query->where('Role', $role);
            }

            // Lấy danh sách users với relationships
            $users = $query->with([
                'hocSinh' => function($q) {
                    $q->select('MaHS', 'MaTK', 'HoTen', 'Lop', 'Truong');
                },
                'giaoVien' => function($q) {
                    $q->select('MaGV', 'MaTK', 'HoTen', 'SoDienThoai', 'ChuyenMon');
                },
                'quanTriVien' => function($q) {
                    $q->select('MaQTV', 'MaTK');
                }
            ])->orderBy('created_at', 'desc')->get();

            // Format response để dễ đọc
            $formattedUsers = $users->map(function($user) {
                $userData = [
                    'MaTK' => $user->MaTK,
                    'TenDangNhap' => $user->TenDangNhap,
                    'Email' => $user->Email,
                    'Role' => $user->Role,
                    'TrangThai' => $user->TrangThai,
                    'LanDangNhapCuoi' => $user->LanDangNhapCuoi,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ];

                // Thêm thông tin chi tiết theo role
                if ($user->Role === 'hocsinh' && $user->hocSinh) {
                    $userData['ThongTinHocSinh'] = $user->hocSinh;
                } elseif ($user->Role === 'giaovien' && $user->giaoVien) {
                    $userData['ThongTinGiaoVien'] = $user->giaoVien;
                } elseif ($user->Role === 'admin' && $user->quanTriVien) {
                    $userData['ThongTinQuanTriVien'] = $user->quanTriVien;
                }

                return $userData;
            });

            return response()->json([
                'success' => true,
                'message' => 'Lấy danh sách người dùng thành công',
                'data' => $formattedUsers,
                'total' => $formattedUsers->count()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy danh sách người dùng',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created user in storage.
     * Tạo người dùng mới (UR-04.1)
     * Hash password (UR-05.3)
     */
    public function store(Request $request)
    {
        // 1. VALIDATE DỮ LIỆU
        $validator = Validator::make($request->all(), [
            'TenDangNhap' => 'required|string|max:50|unique:TaiKhoan,TenDangNhap',
            'Email' => 'required|email|max:100|unique:TaiKhoan,Email',
            'MatKhau' => 'required|string|min:6',
            'Role' => 'required|in:admin,giaovien,hocsinh',
            // Optional fields cho HocSinh
            'HoTen' => 'required_if:Role,hocsinh,giaovien|string|max:100',
            'Lop' => 'sometimes|string|max:20',
            'Truong' => 'sometimes|string|max:100',
            // Optional fields cho GiaoVien
            'SoDienThoai' => 'sometimes|string|max:15',
            'ChuyenMon' => 'sometimes|string|max:100',
        ], [
            'TenDangNhap.required' => 'Tên đăng nhập không được để trống',
            'TenDangNhap.unique' => 'Tên đăng nhập đã tồn tại',
            'Email.required' => 'Email không được để trống',
            'Email.email' => 'Email không đúng định dạng',
            'Email.unique' => 'Email đã được sử dụng',
            'MatKhau.required' => 'Mật khẩu không được để trống',
            'MatKhau.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
            'Role.required' => 'Role không được để trống',
            'Role.in' => 'Role phải là admin, giaovien hoặc hocsinh',
            'HoTen.required_if' => 'Họ tên không được để trống đối với học sinh và giáo viên',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        // 2. TẠO NGƯỜI DÙNG (Sử dụng transaction để đảm bảo tính toàn vẹn dữ liệu)
        DB::beginTransaction();
        try {
            // 2.1. Tạo MaTK tự động
            $lastTaiKhoan = TaiKhoan::orderBy('MaTK', 'desc')->first();
            if ($lastTaiKhoan) {
                $lastNumber = (int)substr($lastTaiKhoan->MaTK, 2);
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }
            $newMaTK = 'TK' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);

            // 2.2. Tạo TaiKhoan với mật khẩu đã hash (UR-05.3)
            $taiKhoan = TaiKhoan::create([
                'MaTK' => $newMaTK,
                'TenDangNhap' => $request->TenDangNhap,
                'MatKhau' => Hash::make($request->MatKhau), // Hash password
                'Email' => $request->Email,
                'Role' => $request->Role,
                'TrangThai' => true, // Mặc định active
            ]);

            // 2.3. Tạo record trong bảng tương ứng theo Role
            $roleData = null;

            if ($request->Role === 'hocsinh') {
                // Tạo MaHS tự động
                $lastHS = HocSinh::orderBy('MaHS', 'desc')->first();
                if ($lastHS) {
                    $lastNumber = (int)substr($lastHS->MaHS, 2);
                    $newNumber = $lastNumber + 1;
                } else {
                    $newNumber = 1;
                }
                $newMaHS = 'HS' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);

                // Tạo record HocSinh
                $roleData = HocSinh::create([
                    'MaHS' => $newMaHS,
                    'MaTK' => $newMaTK,
                    'HoTen' => $request->HoTen,
                    'Lop' => $request->Lop,
                    'Truong' => $request->Truong,
                ]);

            } elseif ($request->Role === 'giaovien') {
                // Tạo MaGV tự động
                $lastGV = GiaoVien::orderBy('MaGV', 'desc')->first();
                if ($lastGV) {
                    $lastNumber = (int)substr($lastGV->MaGV, 2);
                    $newNumber = $lastNumber + 1;
                } else {
                    $newNumber = 1;
                }
                $newMaGV = 'GV' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);

                // Tạo record GiaoVien
                $roleData = GiaoVien::create([
                    'MaGV' => $newMaGV,
                    'MaTK' => $newMaTK,
                    'HoTen' => $request->HoTen,
                    'SoDienThoai' => $request->SoDienThoai,
                    'ChuyenMon' => $request->ChuyenMon,
                ]);

            } elseif ($request->Role === 'admin') {
                // Tạo MaQTV tự động
                $lastQTV = QuanTriVien::orderBy('MaQTV', 'desc')->first();
                if ($lastQTV) {
                    $lastNumber = (int)substr($lastQTV->MaQTV, 3);
                    $newNumber = $lastNumber + 1;
                } else {
                    $newNumber = 1;
                }
                $newMaQTV = 'QTV' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);

                // Tạo record QuanTriVien
                $roleData = QuanTriVien::create([
                    'MaQTV' => $newMaQTV,
                    'MaTK' => $newMaTK,
                ]);
            }

            DB::commit();

            // 3. TRẢ VỀ RESPONSE
            $taiKhoan->load('hocSinh', 'giaoVien', 'quanTriVien');

            return response()->json([
                'success' => true,
                'message' => 'Tạo người dùng thành công',
                'data' => [
                    'TaiKhoan' => [
                        'MaTK' => $taiKhoan->MaTK,
                        'TenDangNhap' => $taiKhoan->TenDangNhap,
                        'Email' => $taiKhoan->Email,
                        'Role' => $taiKhoan->Role,
                        'TrangThai' => $taiKhoan->TrangThai,
                        'created_at' => $taiKhoan->created_at,
                    ],
                    'RoleData' => $roleData
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tạo người dùng',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified user in storage.
     * Cập nhật thông tin người dùng (UR-04.1)
     * Sử dụng DB::transaction để đảm bảo tính toàn vẹn dữ liệu
     */
    public function update(Request $request, string $id)
    {
        // 1. TÌM NGƯỜI DÙNG
        $taiKhoan = TaiKhoan::find($id);
        
        if (!$taiKhoan) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy người dùng'
            ], 404);
        }

        // 2. VALIDATE DỮ LIỆU
        $validator = Validator::make($request->all(), [
            'Email' => 'sometimes|email|max:100|unique:TaiKhoan,Email,' . $id . ',MaTK',
            'Role' => 'sometimes|in:admin,giaovien,hocsinh',
            'TrangThai' => 'sometimes|boolean',
            'MatKhau' => 'sometimes|string|min:6',
            'HoTen' => 'sometimes|string|max:100',
            'Lop' => 'sometimes|string|max:20',
            'Truong' => 'sometimes|string|max:100',
            'SoDienThoai' => 'sometimes|string|max:15',
            'ChuyenMon' => 'sometimes|string|max:100',
        ], [
            'Email.email' => 'Email không đúng định dạng',
            'Email.unique' => 'Email đã được sử dụng',
            'Role.in' => 'Role phải là admin, giaovien hoặc hocsinh',
            'MatKhau.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        // 3. CẬP NHẬT NGƯỜI DÙNG VỚI TRANSACTION
        DB::beginTransaction();
        try {
            $updateData = [];
            
            if ($request->has('Email')) {
                $updateData['Email'] = $request->Email;
            }
            
            if ($request->has('TrangThai')) {
                $updateData['TrangThai'] = $request->TrangThai;
            }

            // Hash password nếu cập nhật
            if ($request->has('MatKhau')) {
                $updateData['MatKhau'] = Hash::make($request->MatKhau);
            }

            // Cập nhật Role (lưu ý: thay đổi role phức tạp, cần xử lý relationships)
            if ($request->has('Role') && $request->Role !== $taiKhoan->Role) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể thay đổi Role của người dùng. Vui lòng tạo tài khoản mới với Role mong muốn.'
                ], 400);
            }

            // Cập nhật TaiKhoan
            $taiKhoan->update($updateData);

            // CẬP NHẬT BẢNG LIÊN QUAN (HocSinh hoặc GiaoVien) nếu có HoTen
            if ($request->has('HoTen') || $request->has('Lop') || $request->has('Truong') || 
                $request->has('SoDienThoai') || $request->has('ChuyenMon')) {
                
                if ($taiKhoan->Role === 'hocsinh') {
                    // Cập nhật HocSinh
                    $hocSinh = HocSinh::where('MaTK', $taiKhoan->MaTK)->first();
                    if ($hocSinh) {
                        $hocSinhUpdate = [];
                        if ($request->has('HoTen')) $hocSinhUpdate['HoTen'] = $request->HoTen;
                        if ($request->has('Lop')) $hocSinhUpdate['Lop'] = $request->Lop;
                        if ($request->has('Truong')) $hocSinhUpdate['Truong'] = $request->Truong;
                        
                        if (!empty($hocSinhUpdate)) {
                            $hocSinh->update($hocSinhUpdate);
                        }
                    }
                } elseif ($taiKhoan->Role === 'giaovien') {
                    // Cập nhật GiaoVien
                    $giaoVien = GiaoVien::where('MaTK', $taiKhoan->MaTK)->first();
                    if ($giaoVien) {
                        $giaoVienUpdate = [];
                        if ($request->has('HoTen')) $giaoVienUpdate['HoTen'] = $request->HoTen;
                        if ($request->has('SoDienThoai')) $giaoVienUpdate['SoDienThoai'] = $request->SoDienThoai;
                        if ($request->has('ChuyenMon')) $giaoVienUpdate['ChuyenMon'] = $request->ChuyenMon;
                        
                        if (!empty($giaoVienUpdate)) {
                            $giaoVien->update($giaoVienUpdate);
                        }
                    }
                }
            }

            DB::commit();

            // Load lại relationships
            $taiKhoan->load('hocSinh', 'giaoVien');

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật người dùng thành công',
                'data' => [
                    'MaTK' => $taiKhoan->MaTK,
                    'TenDangNhap' => $taiKhoan->TenDangNhap,
                    'Email' => $taiKhoan->Email,
                    'Role' => $taiKhoan->Role,
                    'TrangThai' => $taiKhoan->TrangThai,
                    'HocSinh' => $taiKhoan->hocSinh,
                    'GiaoVien' => $taiKhoan->giaoVien,
                    'updated_at' => $taiKhoan->updated_at,
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật người dùng',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle user account status (Lock/Unlock).
     * Khóa/Mở khóa tài khoản người dùng (UR-04.1)
     */
    public function toggleStatus(string $id)
    {
        try {
            // 1. TÌM NGƯỜI DÙNG
            $taiKhoan = TaiKhoan::find($id);
            
            if (!$taiKhoan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy người dùng'
                ], 404);
            }

            // 2. KHÔNG CHO PHÉP KHÓA TÀI KHOẢN ADMIN (để tránh khóa chính mình)
            if ($taiKhoan->Role === 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể khóa tài khoản quản trị viên'
                ], 400);
            }

            // 3. ĐẢO TRẠNG THÁI
            $oldStatus = $taiKhoan->TrangThai;
            $taiKhoan->TrangThai = !$oldStatus;
            $taiKhoan->save();

            $statusText = $taiKhoan->TrangThai ? 'mở khóa' : 'khóa';

            return response()->json([
                'success' => true,
                'message' => "Đã {$statusText} tài khoản thành công",
                'data' => [
                    'MaTK' => $taiKhoan->MaTK,
                    'TenDangNhap' => $taiKhoan->TenDangNhap,
                    'Email' => $taiKhoan->Email,
                    'Role' => $taiKhoan->Role,
                    'TrangThai' => $taiKhoan->TrangThai,
                    'StatusText' => $taiKhoan->TrangThai ? 'Hoạt động' : 'Đã khóa',
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi thay đổi trạng thái tài khoản',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * UR-04.3: Xóa tài khoản người dùng
     * Xóa vĩnh viễn tài khoản khỏi hệ thống
     */
    public function destroy(string $id)
    {
        try {
            // 1. TÌM NGƯỜI DÙNG
            $taiKhoan = TaiKhoan::find($id);
            
            if (!$taiKhoan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy người dùng'
                ], 404);
            }

            // 2. KHÔNG CHO PHÉP XÓA TÀI KHOẢN ADMIN
            if ($taiKhoan->Role === 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể xóa tài khoản quản trị viên'
                ], 400);
            }

            // 3. KIỂM TRA RÀNG BUỘC DỮ LIỆU
            // Nếu là học sinh và có bài thi, không cho xóa
            if ($taiKhoan->Role === 'hocsinh') {
                $soBaiThi = \App\Models\BaiLam::where('MaHS', $id)->count();
                if ($soBaiThi > 0) {
                    return response()->json([
                        'success' => false,
                        'message' => "Không thể xóa học sinh đã có {$soBaiThi} bài thi trong hệ thống"
                    ], 400);
                }
            }

            // 4. LƯU TÊN ĐỂ HIỂN THỊ THÔNG BÁO
            $tenDangNhap = $taiKhoan->TenDangNhap;
            $email = $taiKhoan->Email;

            // 5. XÓA TÀI KHOẢN
            $taiKhoan->delete();

            return response()->json([
                'success' => true,
                'message' => "Đã xóa tài khoản '{$tenDangNhap}' thành công",
                'data' => [
                    'TenDangNhap' => $tenDangNhap,
                    'Email' => $email
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa tài khoản',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * UR-04.4: Sao lưu database
     * Tạo file backup MySQL
     */
    public function backupDatabase(Request $request)
    {
        try {
            $user = $request->user();
            if ($user->Role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ quản trị viên mới có quyền sao lưu dữ liệu'
                ], 403);
            }

            // Tạo tên file backup
            $filename = 'backup_' . date('Y-m-d_His') . '.sql';
            $backupPath = storage_path('app/backups');
            
            // Tạo thư mục backups nếu chưa có
            if (!file_exists($backupPath)) {
                mkdir($backupPath, 0755, true);
            }

            $fullPath = $backupPath . '/' . $filename;

            // Lấy thông tin database từ config
            $dbHost = config('database.connections.mysql.host');
            $dbName = config('database.connections.mysql.database');
            $dbUser = config('database.connections.mysql.username');
            $dbPass = config('database.connections.mysql.password');

            // Command mysqldump
            $command = sprintf(
                'mysqldump --user=%s --password=%s --host=%s %s > %s 2>&1',
                escapeshellarg($dbUser),
                escapeshellarg($dbPass),
                escapeshellarg($dbHost),
                escapeshellarg($dbName),
                escapeshellarg($fullPath)
            );

            // Thực thi command
            exec($command, $output, $returnVar);

            if ($returnVar === 0 && file_exists($fullPath)) {
                $fileSize = filesize($fullPath);
                
                // Lưu thông tin backup vào database
                DB::table('backup_history')->insert([
                    'filename' => $filename,
                    'file_path' => $fullPath,
                    'file_size' => $fileSize,
                    'created_by' => $user->MaTK,
                    'created_at' => now(),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Sao lưu dữ liệu thành công',
                    'data' => [
                        'filename' => $filename,
                        'size' => round($fileSize / 1024 / 1024, 2) . ' MB',
                        'path' => $fullPath,
                        'created_at' => now()->format('Y-m-d H:i:s'),
                    ]
                ], 201);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể tạo file backup',
                    'error' => implode("\n", $output)
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi sao lưu dữ liệu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * UR-04.5: Khôi phục database từ file backup
     */
    public function restoreDatabase(Request $request)
    {
        try {
            $user = $request->user();
            if ($user->Role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ quản trị viên mới có quyền khôi phục dữ liệu'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'backup_file' => 'required|file|mimes:sql|max:102400', // Max 100MB
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'File không hợp lệ',
                    'errors' => $validator->errors()
                ], 422);
            }

            $file = $request->file('backup_file');
            $tempPath = $file->path();

            // Lấy thông tin database
            $dbHost = config('database.connections.mysql.host');
            $dbName = config('database.connections.mysql.database');
            $dbUser = config('database.connections.mysql.username');
            $dbPass = config('database.connections.mysql.password');

            // Command mysql import
            $command = sprintf(
                'mysql --user=%s --password=%s --host=%s %s < %s 2>&1',
                escapeshellarg($dbUser),
                escapeshellarg($dbPass),
                escapeshellarg($dbHost),
                escapeshellarg($dbName),
                escapeshellarg($tempPath)
            );

            exec($command, $output, $returnVar);

            if ($returnVar === 0) {
                return response()->json([
                    'success' => true,
                    'message' => 'Khôi phục dữ liệu thành công',
                    'data' => [
                        'filename' => $file->getClientOriginalName(),
                        'restored_at' => now()->format('Y-m-d H:i:s'),
                    ]
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể khôi phục dữ liệu',
                    'error' => implode("\n", $output)
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi khôi phục dữ liệu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy danh sách backup đã tạo
     */
    public function listBackups(Request $request)
    {
        try {
            $user = $request->user();
            if ($user->Role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ quản trị viên mới có quyền xem danh sách backup'
                ], 403);
            }

            $backups = DB::table('backup_history')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($backup) {
                    return [
                        'id' => $backup->id,
                        'filename' => $backup->filename,
                        'size' => round($backup->file_size / 1024 / 1024, 2) . ' MB',
                        'created_by' => $backup->created_by,
                        'created_at' => $backup->created_at,
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'Lấy danh sách backup thành công',
                'data' => $backups,
                'total' => $backups->count()
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
     * Download backup file (UR-04.4 - Enhancement)
     */
    public function downloadBackup(Request $request, $filename)
    {
        $user = $request->user();
        
        // Only admin can download backups
        if ($user->Role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ admin mới có quyền download backup'
            ], 403);
        }
        
        $filePath = storage_path('app/backups/' . $filename);
        
        // Validate filename to prevent directory traversal
        if (!file_exists($filePath) || strpos($filename, '..') !== false) {
            return response()->json([
                'success' => false,
                'message' => 'File backup không tồn tại'
            ], 404);
        }
        
        return response()->download($filePath);
    }

    /**
     * SYSTEM MONITORING: Lấy metrics giám sát hệ thống
     */
    public function getSystemMonitor(Request $request)
    {
        try {
            $user = $request->user();
            if ($user->Role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ quản trị viên mới có quyền giám sát hệ thống'
                ], 403);
            }

            // 1. THỐNG KÊ NGƯỜI DÙNG
            $totalUsers = TaiKhoan::count();
            $activeUsers = TaiKhoan::where('TrangThai', true)->count();
            $totalStudents = TaiKhoan::where('Role', 'hocsinh')->count();
            $totalTeachers = TaiKhoan::where('Role', 'giaovien')->count();
            $totalAdmins = TaiKhoan::where('Role', 'admin')->count();

            // 2. THỐNG KÊ BÀI THI
            $totalExams = \App\Models\DeThi::count();
            $totalSubmissions = \App\Models\BaiLam::count();
            $todaySubmissions = \App\Models\BaiLam::whereDate('ThoiGianNop', today())->count();
            $avgScore = \App\Models\BaiLam::whereNotNull('Diem')->avg('Diem');

            // 3. THỐNG KÊ CÂU HỎI
            $totalQuestions = \App\Models\CauHoi::count();
            $easyQuestions = \App\Models\CauHoi::where('DoKho', 'De')->count();
            $mediumQuestions = \App\Models\CauHoi::where('DoKho', 'TB')->count();
            $hardQuestions = \App\Models\CauHoi::where('DoKho', 'Kho')->count();

            // 4. THỐNG KÊ NGÂN HÀNG CÂU HỎI
            $totalQuestionBanks = \App\Models\NganHangCauHoi::count();

            // 5. NGƯỜI DÙNG ONLINE (giả lập - dựa vào personal_access_tokens của Sanctum)
            $onlineUsers = \DB::table('personal_access_tokens')
                ->where('last_used_at', '>=', now()->subMinutes(15))
                ->distinct('tokenable_id')
                ->count();

            // 6. HOẠT ĐỘNG GẦN ĐÂY (10 bài làm gần nhất)
            $recentActivities = \App\Models\BaiLam::join('hocsinh', 'bailam.MaHS', '=', 'hocsinh.MaHS')
                ->join('taikhoan', 'hocsinh.MaTK', '=', 'taikhoan.MaTK')
                ->join('dethi', 'bailam.MaDe', '=', 'dethi.MaDe')
                ->select(
                    'bailam.MaBaiLam',
                    'taikhoan.TenDangNhap',
                    'dethi.TenDe',
                    'bailam.Diem',
                    'bailam.ThoiGianNop'
                )
                ->whereNotNull('bailam.ThoiGianNop')
                ->orderBy('bailam.ThoiGianNop', 'desc')
                ->limit(10)
                ->get()
                ->map(function($bailam) {
                    return [
                        'MaBaiLam' => $bailam->MaBaiLam,
                        'TenDangNhap' => $bailam->TenDangNhap ?? 'N/A',
                        'TenDe' => $bailam->TenDe ?? 'N/A',
                        'Diem' => $bailam->Diem,
                        'ThoiGianNop' => $bailam->ThoiGianNop,
                        'ThoiGianNopFormatted' => $bailam->ThoiGianNop ? 
                            \Carbon\Carbon::parse($bailam->ThoiGianNop)->diffForHumans() : 'N/A'
                    ];
                });

            // 7. SYSTEM INFO (PHP version, Laravel version, Database)
            $systemInfo = [
                'php_version' => phpversion(),
                'laravel_version' => app()->version(),
                'database' => config('database.default'),
                'server_time' => now()->format('Y-m-d H:i:s'),
                'uptime' => $this->getServerUptime()
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'users' => [
                        'total' => $totalUsers,
                        'active' => $activeUsers,
                        'online' => $onlineUsers,
                        'students' => $totalStudents,
                        'teachers' => $totalTeachers,
                        'admins' => $totalAdmins
                    ],
                    'exams' => [
                        'total' => $totalExams,
                        'total_submissions' => $totalSubmissions,
                        'today_submissions' => $todaySubmissions,
                        'avg_score' => round($avgScore ?? 0, 2)
                    ],
                    'questions' => [
                        'total' => $totalQuestions,
                        'easy' => $easyQuestions,
                        'medium' => $mediumQuestions,
                        'hard' => $hardQuestions,
                        'banks' => $totalQuestionBanks
                    ],
                    'system' => $systemInfo,
                    'recent_activities' => $recentActivities
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy thông tin giám sát',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy danh sách hoạt động gần đây chi tiết
     */
    public function getRecentActivities(Request $request)
    {
        try {
            $user = $request->user();
            if ($user->Role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ quản trị viên mới có quyền xem hoạt động'
                ], 403);
            }

            $limit = $request->input('limit', 20);

            $activities = \App\Models\BaiLam::with(['taiKhoan', 'deThi'])
                ->orderBy('ThoiGianNop', 'desc')
                ->limit($limit)
                ->get()
                ->map(function($bailam) {
                    return [
                        'id' => $bailam->MaBaiLam,
                        'user' => $bailam->taiKhoan->TenDangNhap ?? 'Unknown',
                        'exam' => $bailam->deThi->TenDe ?? 'Unknown',
                        'score' => $bailam->Diem,
                        'time' => $bailam->ThoiGianNop,
                        'time_ago' => $bailam->ThoiGianNop ? 
                            \Carbon\Carbon::parse($bailam->ThoiGianNop)->diffForHumans() : 'N/A',
                        'status' => $bailam->Diem >= 5 ? 'pass' : 'fail'
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $activities
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
     * Helper: Lấy thời gian server uptime (giả lập)
     */
    private function getServerUptime()
    {
        // Giả lập uptime dựa vào thời gian tạo file storage
        $storagePath = storage_path('app');
        if (file_exists($storagePath)) {
            $created = filectime($storagePath);
            $uptime = time() - $created;
            $days = floor($uptime / 86400);
            $hours = floor(($uptime % 86400) / 3600);
            return "{$days} ngày {$hours} giờ";
        }
        return 'N/A';
    }
}


