<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pemagang_aktif', function (Blueprint $table) {
            $table->id('magang_id');
            $table->foreignId('pelamar_id')->constrained('pelamar')->onDelete('cascade');
            $table->foreignId('perusahaan_id')->constrained('perusahaan')->onDelete('cascade');
            $table->foreignId('lowongan_id')->constrained('lowongan_magang')->onDelete('cascade');
            $table->foreignId('sekolah_id')->constrained('sekolah')->onDelete('cascade');
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->enum('status_magang', ['aktif','tidak'])->default('aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemagang_aktif');
    }
};
