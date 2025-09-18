<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('siswa', 'jurusan')) {
            Schema::table('siswa', function (Blueprint $table) {
                $table->string('jurusan')->nullable()->after('kelas');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('siswa', 'jurusan')) {
            Schema::table('siswa', function (Blueprint $table) {
                $table->dropColumn('jurusan');
            });
        }
    }
};
