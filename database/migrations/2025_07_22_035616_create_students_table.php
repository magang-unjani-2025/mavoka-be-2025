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
        Schema::create('siswa', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('status_verifikasi')->nullable();
            $table->dateTime('tanggal_verifikasi')->nullable();
            $table->string('nama_lengkap');
            $table->string('nisn')->unique();
            $table->integer('kelas');
            $table->foreignId('jurusan_id')->nullable()->constrained('jurusan')->onDelete('set null');
            $table->integer('tahun_ajaran');
            $table->date('tanggal_lahir');
            $table->string('jenis_kelamin');
            $table->text('alamat');
            $table->string('kontak');
            $table->string('status_siswa');
            $table->string('foto_profil')->nullable();
            $table->foreignId('sekolah_id')->nullable()->constrained('sekolah')->onDelete('set null');
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
