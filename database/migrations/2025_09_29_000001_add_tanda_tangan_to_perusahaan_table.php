<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('perusahaan', function (Blueprint $table) {
            // tanda_tangan menyimpan path file tanda tangan (nullable)
            $table->string('tanda_tangan')->nullable()->after('penanggung_jawab');
        });
    }

    public function down()
    {
        Schema::table('perusahaan', function (Blueprint $table) {
            $table->dropColumn('tanda_tangan');
        });
    }
};
