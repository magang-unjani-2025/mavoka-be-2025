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
            $table->string('username')->nullable()->unique();
            $table->string('email')->nullable()->unique();
            $table->string('password')->nullable();
            $table->string('status_verifikasi')->nullable();
            $table->timestamp('tanggal_verifikasi')->nullable();
            $table->string('nama_lengkap')->nullable();
            $table->string('nisn')->unique();
            $table->foreignId('sekolah_id')->constrained('sekolah')->onDelete('set null');
            $table->integer('kelas')->nullable();
            $table->foreignId('jurusan_id')->nullable()->constrained('jurusan')->onDelete('set null');
            $table->integer('tahun_ajaran')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('jenis_kelamin')->nullable();
            $table->text('alamat')->nullable();
            $table->string('kontak')->nullable();
            $table->string('foto_profil')->nullable();
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
