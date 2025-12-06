<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Set default engine and charset
        DB::statement('SET default_storage_engine=InnoDB');
        
        // 1. Bảng TaiKhoan
        Schema::create('TaiKhoan', function (Blueprint $table) {
            $table->char('MaTK', 10)->primary();
            $table->string('TenDangNhap', 50)->unique();
            $table->string('MatKhau', 255);
            $table->string('Email', 100)->unique();
            $table->enum('Role', ['admin', 'giaovien', 'hocsinh']);
            $table->boolean('TrangThai')->default(1);
            $table->dateTime('LanDangNhapCuoi')->nullable();
            $table->timestamps();
            
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->engine = 'InnoDB';
        });

        // 2. Bảng QuanTriVien (Kế thừa từ TaiKhoan)
        Schema::create('QuanTriVien', function (Blueprint $table) {
            $table->char('MaQTV', 10)->primary();
            $table->char('MaTK', 10);
            $table->timestamps();
            
            $table->foreign('MaTK')
                  ->references('MaTK')
                  ->on('TaiKhoan')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->engine = 'InnoDB';
        });

        // 3. Bảng GiaoVien (Kế thừa từ TaiKhoan)
        Schema::create('GiaoVien', function (Blueprint $table) {
            $table->char('MaGV', 10)->primary();
            $table->char('MaTK', 10);
            $table->string('HoTen', 100)->nullable();
            $table->string('SoDienThoai', 15)->nullable();
            $table->string('ChuyenMon', 100)->nullable();
            $table->timestamps();
            
            $table->foreign('MaTK')
                  ->references('MaTK')
                  ->on('TaiKhoan')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->engine = 'InnoDB';
        });

        // 4. Bảng HocSinh (Kế thừa từ TaiKhoan)
        Schema::create('HocSinh', function (Blueprint $table) {
            $table->char('MaHS', 10)->primary();
            $table->char('MaTK', 10);
            $table->string('HoTen', 100);
            $table->string('Lop', 20)->nullable();
            $table->string('Truong', 100)->nullable();
            $table->timestamps();
            
            $table->foreign('MaTK')
                  ->references('MaTK')
                  ->on('TaiKhoan')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->engine = 'InnoDB';
        });

        // 5. Bảng NganHangCauHoi
        Schema::create('NganHangCauHoi', function (Blueprint $table) {
            $table->char('MaNH', 10)->primary();
            $table->string('TenNH', 200);
            $table->text('MoTa')->nullable();
            $table->timestamps();
            
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->engine = 'InnoDB';
        });

        // 6. Bảng CauHoi
        Schema::create('CauHoi', function (Blueprint $table) {
            $table->char('MaCH', 10)->primary();
            $table->text('NoiDung');
            $table->string('DapAn', 1); // A, B, C, D
            $table->text('DapAnA')->nullable();
            $table->text('DapAnB')->nullable();
            $table->text('DapAnC')->nullable();
            $table->text('DapAnD')->nullable();
            $table->enum('DoKho', ['De', 'TB', 'Kho'])->default('TB');
            $table->char('MaNH', 10);
            $table->timestamps();
            
            $table->foreign('MaNH')
                  ->references('MaNH')
                  ->on('NganHangCauHoi')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->engine = 'InnoDB';
        });

        // 7. Bảng DeThi
        Schema::create('DeThi', function (Blueprint $table) {
            $table->char('MaDe', 10)->primary();
            $table->string('TenDe', 200);
            $table->integer('ThoiGianLamBai'); // Phút
            $table->dateTime('NgayTao');
            $table->integer('SoLuongCauHoi');
            $table->char('MaGV', 10);
            $table->text('MoTa')->nullable();
            $table->boolean('TrangThai')->default(1);
            $table->timestamps();
            
            $table->foreign('MaGV')
                  ->references('MaGV')
                  ->on('GiaoVien')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->engine = 'InnoDB';
        });

        // 8. Bảng DETHI_CAUHOI (Bảng trung gian n-n)
        Schema::create('DETHI_CAUHOI', function (Blueprint $table) {
            $table->char('MaDe', 10);
            $table->char('MaCH', 10);
            $table->integer('ThuTu')->default(1); // Thứ tự câu hỏi trong đề
            $table->timestamps();
            
            $table->primary(['MaDe', 'MaCH']);
            
            $table->foreign('MaDe')
                  ->references('MaDe')
                  ->on('DeThi')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            
            $table->foreign('MaCH')
                  ->references('MaCH')
                  ->on('CauHoi')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->engine = 'InnoDB';
        });

        // 9. Bảng BaiLam
        Schema::create('BaiLam', function (Blueprint $table) {
            $table->char('MaBaiLam', 10)->primary();
            $table->json('DSCauTraLoi')->nullable(); // JSON: [{MaCH: "", TraLoi: "A"}, ...]
            $table->float('Diem', 8, 2)->nullable();
            $table->dateTime('ThoiGianBatDau');
            $table->dateTime('ThoiGianNop')->nullable();
            $table->enum('TrangThai', ['DangLam', 'DaNop', 'ChamDiem'])->default('DangLam');
            $table->char('MaHS', 10);
            $table->char('MaDe', 10);
            $table->timestamps();
            
            $table->foreign('MaHS')
                  ->references('MaHS')
                  ->on('HocSinh')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            
            $table->foreign('MaDe')
                  ->references('MaDe')
                  ->on('DeThi')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->engine = 'InnoDB';
        });

        // 10. Bảng KetQua
        Schema::create('KetQua', function (Blueprint $table) {
            $table->char('MaKQ', 10)->primary();
            $table->float('Diem', 8, 2);
            $table->integer('SoCauDung')->default(0);
            $table->integer('SoCauSai')->default(0);
            $table->integer('SoCauKhongLam')->default(0);
            $table->dateTime('ThoiGianHoanThanh');
            $table->char('MaHS', 10);
            $table->char('MaDe', 10);
            $table->char('MaBaiLam', 10)->nullable();
            $table->timestamps();
            
            $table->foreign('MaHS')
                  ->references('MaHS')
                  ->on('HocSinh')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            
            $table->foreign('MaDe')
                  ->references('MaDe')
                  ->on('DeThi')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            
            $table->foreign('MaBaiLam')
                  ->references('MaBaiLam')
                  ->on('BaiLam')
                  ->onDelete('set null')
                  ->onUpdate('cascade');
            
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->engine = 'InnoDB';
        });

        // 11. Bảng Loi (Log hệ thống)
        Schema::create('Loi', function (Blueprint $table) {
            $table->id('MaLoi');
            $table->enum('LoaiLoi', ['Error', 'Warning', 'Info'])->default('Info');
            $table->text('NoiDung');
            $table->string('NguyenNhan', 255)->nullable();
            $table->dateTime('ThoiGian');
            $table->char('MaTK', 10)->nullable();
            $table->timestamps();
            
            $table->foreign('MaTK')
                  ->references('MaTK')
                  ->on('TaiKhoan')
                  ->onDelete('set null')
                  ->onUpdate('cascade');
            
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->engine = 'InnoDB';
        });

        // 12. Bảng SaoLuu (Backup)
        Schema::create('SaoLuu', function (Blueprint $table) {
            $table->id('MaSaoLuu');
            $table->string('TenFile', 255);
            $table->string('DuongDan', 500);
            $table->dateTime('ThoiGianSaoLuu');
            $table->bigInteger('KichThuoc')->nullable(); // KB
            $table->enum('TrangThai', ['ThanhCong', 'ThatBai'])->default('ThanhCong');
            $table->char('MaQTV', 10)->nullable();
            $table->timestamps();
            
            $table->foreign('MaQTV')
                  ->references('MaQTV')
                  ->on('QuanTriVien')
                  ->onDelete('set null')
                  ->onUpdate('cascade');
            
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->engine = 'InnoDB';
        });

        // 13. Bảng ThoiGian (Thời gian làm bài)
        Schema::create('ThoiGian', function (Blueprint $table) {
            $table->id('MaThoiGian');
            $table->dateTime('ThoiGianBatDau');
            $table->dateTime('ThoiGianKetThuc')->nullable();
            $table->integer('TongThoiGian')->nullable(); // Phút
            $table->char('MaBaiLam', 10);
            $table->timestamps();
            
            $table->foreign('MaBaiLam')
                  ->references('MaBaiLam')
                  ->on('BaiLam')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->engine = 'InnoDB';
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ThoiGian');
        Schema::dropIfExists('SaoLuu');
        Schema::dropIfExists('Loi');
        Schema::dropIfExists('KetQua');
        Schema::dropIfExists('BaiLam');
        Schema::dropIfExists('DETHI_CAUHOI');
        Schema::dropIfExists('DeThi');
        Schema::dropIfExists('CauHoi');
        Schema::dropIfExists('NganHangCauHoi');
        Schema::dropIfExists('HocSinh');
        Schema::dropIfExists('GiaoVien');
        Schema::dropIfExists('QuanTriVien');
        Schema::dropIfExists('TaiKhoan');
    }
};
