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
        Schema::create('sekolah', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('status_verifikasi');
            $table->dateTime('tanggal_verifikasi')->nullable();
            $table->string('nama_sekolah');
            $table->string('web_sekolah');
            $table->string('npsn')->unique();
            $table->string('kontak')->nullable();
            $table->text('alamat')->nullable();
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
