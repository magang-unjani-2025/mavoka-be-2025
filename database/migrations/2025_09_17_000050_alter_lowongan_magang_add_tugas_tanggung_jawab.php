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
        Schema::table('lowongan_magang', function (Blueprint $table) {
            // Menambahkan kolom tugas_tanggung_jawab untuk menjelaskan tugas & tanggung jawab posisi
            if (!Schema::hasColumn('lowongan_magang', 'tugas_tanggung_jawab')) {
                $table->longText('tugas_tanggung_jawab')->nullable()->after('benefit');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lowongan_magang', function (Blueprint $table) {
            if (Schema::hasColumn('lowongan_magang', 'tugas_tanggung_jawab')) {
                $table->dropColumn('tugas_tanggung_jawab');
            }
        });
    }
};
