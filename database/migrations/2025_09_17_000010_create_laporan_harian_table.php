<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('laporan_harian', function (Blueprint $table) {
            $table->id('laporan_harian_id');
            $table->foreignId('perusahaan_id')->constrained('perusahaan')->onDelete('cascade');
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->unsignedBigInteger('magang_id'); // placeholder jika tabel magang belum ada
            $table->date('tanggal_laporan');
            $table->string('dokumentasi_foto')->nullable();
            $table->text('deskripsi')->nullable();
            $table->text('output')->nullable();
            $table->text('hambatan')->nullable();
            $table->text('solusi')->nullable();
            $table->text('evaluasi_perusahaan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_harian');
    }
};
