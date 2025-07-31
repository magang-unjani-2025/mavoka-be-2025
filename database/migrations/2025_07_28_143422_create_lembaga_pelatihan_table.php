<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('lembaga_pelatihan', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('status_verifikasi');
            $table->dateTime('tanggal_verifikasi')->nullable();
            $table->string('nama_lembaga');
            $table->string('bidang_pelatihan');
            $table->string('deskripsi_lembaga');
            $table->string('alamat');
            $table->string('kontak');
            $table->string('logo_lembaga')->nullable();
            $table->string('status_akreditasi');
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
        Schema::dropIfExists('lembaga_pelatihan');
    }
};
