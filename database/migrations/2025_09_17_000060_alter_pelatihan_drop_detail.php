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
            if (Schema::hasColumn('pelatihan', 'detail')) {
                $table->dropColumn('detail');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pelatihan', function (Blueprint $table) {
            if (!Schema::hasColumn('pelatihan', 'detail')) {
                $table->longText('detail')->nullable()->after('capaian_pembelajaran');
            }
        });
    }
};
