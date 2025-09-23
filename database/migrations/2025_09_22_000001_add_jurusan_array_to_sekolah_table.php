<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sekolah', function (Blueprint $table) {
            // JSON kolom untuk menampung list jurusan (array string)
            if (!Schema::hasColumn('sekolah','jurusan')) {
                $table->json('jurusan')->nullable()->after('npsn');
            } else {
                // Jika sudah ada tapi mungkin tipe lama (string), biarkan - adapt di model (cast json/decode)
            }
        });
    }

    public function down(): void
    {
        Schema::table('sekolah', function (Blueprint $table) {
            if (Schema::hasColumn('sekolah','jurusan')) {
                $table->dropColumn('jurusan');
            }
        });
    }
};
