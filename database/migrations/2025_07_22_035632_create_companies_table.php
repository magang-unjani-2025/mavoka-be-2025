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
        Schema::create('perusahaan', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('status_verifikasi');
            $table->dateTime('tanggal_verifikasi')->nullable();
            $table->string('nama_perusahaan');
            $table->string('bidang_usaha');
            $table->string('deskripsi_usaha');
            $table->text('alamat');
            $table->string('kontak');
            $table->string('logo_perusahaan')->nullable();
            $table->string('divisi_penempatan');
            $table->string('mentor')->nullable();
            $table->string('otp')->nullable();
            $table->timestamp('otp_expired_at')->nullable();

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswa');
    }
};
