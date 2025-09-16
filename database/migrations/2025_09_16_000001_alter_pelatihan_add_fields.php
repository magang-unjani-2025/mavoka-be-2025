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
        Schema::table('pelatihan', function (Blueprint $table) {
            $table->longText('capaian_pembelajaran')->nullable()->after('deskripsi');
            $table->longText('detail')->nullable()->after('capaian_pembelajaran');
            $table->json('history_batch')->nullable()->after('detail');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pelatihan', function (Blueprint $table) {
            $table->dropColumn(['capaian_pembelajaran', 'detail', 'history_batch']);
        });
    }
};
