<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Hapus FK dan kolom jurusan_id di tabel siswa jika ada
        if (Schema::hasColumn('siswa', 'jurusan_id')) {
            Schema::table('siswa', function (Blueprint $table) {
                try {
                    $table->dropForeign(['jurusan_id']);
                } catch (\Throwable $e) {
                    // abaikan jika constraint sudah tidak ada
                }
                $table->dropColumn('jurusan_id');
            });
        }

        // Hapus tabel jurusan jika ada
        Schema::dropIfExists('jurusan');
    }

    public function down(): void
    {
        // Kembalikan tabel jurusan secara minimal
        if (!Schema::hasTable('jurusan')) {
            Schema::create('jurusan', function (Blueprint $table) {
                $table->id();
                $table->foreignId('sekolah_id')->constrained('sekolah')->onDelete('cascade');
                $table->string('nama_jurusan');
                $table->timestamps();
                $table->unique(['sekolah_id', 'nama_jurusan']);
            });
        }

        // Tambahkan kembali kolom jurusan_id pada siswa (nullable)
        if (!Schema::hasColumn('siswa', 'jurusan_id')) {
            Schema::table('siswa', function (Blueprint $table) {
                $table->foreignId('jurusan_id')->nullable()->constrained('jurusan')->onDelete('set null');
            });
        }
    }
};
