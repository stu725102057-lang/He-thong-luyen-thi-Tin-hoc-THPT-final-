<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('DeThi', function (Blueprint $table) {
            $table->string('ChuDe', 255)->nullable()->after('TenDe');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('DeThi', function (Blueprint $table) {
            $table->dropColumn('ChuDe');
        });
    }
};
