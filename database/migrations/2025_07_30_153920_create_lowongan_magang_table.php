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
        Schema::create('lowongan_magang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perusahaan_id')->constrained('perusahaan')->onDelete('cascade');
            $table->string('judul_lowongan');
            $table->text('deskripsi');
            $table->string('posisi');
            $table->integer('kuota');
            $table->string('lokasi_penempatan');
            $table->text('persyaratan');
            $table->text('benefit');
            $table->enum('status', ['buka', 'tutup']);
            $table->date('deadline_lamaran');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lowongan_magang');
    }
};
