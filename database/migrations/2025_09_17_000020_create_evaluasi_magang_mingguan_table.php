<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('evaluasi_magang_mingguan', function (Blueprint $table) {
            $table->id('evaluasi_id');
            $table->foreignId('perusahaan_id')->constrained('perusahaan')->onDelete('cascade');
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->unsignedBigInteger('magang_id'); // placeholder jika tabel magang belum ada
            // Aspek (deskripsi)
            $table->text('aspek_teknis')->nullable();
            $table->text('aspek_komunikasi')->nullable();
            $table->text('aspek_kerjasama')->nullable();
            $table->text('aspek_disiplin')->nullable();
            $table->text('aspek_inisiatif')->nullable();
            // Nilai per aspek (0-100)
            $table->unsignedTinyInteger('nilai_aspek_teknis')->nullable();
            $table->unsignedTinyInteger('nilai_aspek_komunikasi')->nullable();
            $table->unsignedTinyInteger('nilai_aspek_kerjasama')->nullable();
            $table->unsignedTinyInteger('nilai_aspek_disiplin')->nullable();
            $table->unsignedTinyInteger('nilai_aspek_inisiatif')->nullable();
            $table->decimal('nilai_rata_rata', 5, 2)->nullable();
            $table->timestamp('upload_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluasi_magang_mingguan');
    }
};
