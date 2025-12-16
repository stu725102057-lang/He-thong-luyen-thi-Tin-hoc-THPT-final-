<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tạo bảng backup_history để lưu lịch sử sao lưu
     */
    public function up(): void
    {
        Schema::create('backup_history', function (Blueprint $table) {
            $table->id();
            $table->string('filename', 255);
            $table->string('file_path', 500);
            $table->bigInteger('file_size')->comment('Kích thước file (bytes)');
            $table->string('created_by', 10)->comment('Mã tài khoản admin tạo backup');
            $table->timestamp('created_at')->useCurrent();
            
            $table->index('created_by');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backup_history');
    }
};
