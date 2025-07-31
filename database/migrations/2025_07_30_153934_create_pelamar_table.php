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
        Schema::create('pelamar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->foreignId('lowongan_id')->constrained('lowongan_magang')->onDelete('cascade');
            $table->dateTime('tanggal_lamaran');
            $table->enum('status_lamaran', ['diproses', 'diterima', 'ditolak'])->default('dikirim');
            $table->string('cv');
            $table->string('transkrip');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelamar');
    }
};
