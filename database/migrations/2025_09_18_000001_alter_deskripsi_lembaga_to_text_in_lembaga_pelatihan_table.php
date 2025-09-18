<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE `lembaga_pelatihan` MODIFY `deskripsi_lembaga` TEXT NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE lembaga_pelatihan ALTER COLUMN deskripsi_lembaga TYPE TEXT');
        } else {
            // Fallback (requires doctrine/dbal for change())
            Schema::table('lembaga_pelatihan', function (Blueprint $table) {
                $table->text('deskripsi_lembaga')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE `lembaga_pelatihan` MODIFY `deskripsi_lembaga` VARCHAR(255) NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE lembaga_pelatihan ALTER COLUMN deskripsi_lembaga TYPE VARCHAR(255)');
        } else {
            // Fallback (requires doctrine/dbal for change())
            Schema::table('lembaga_pelatihan', function (Blueprint $table) {
                $table->string('deskripsi_lembaga', 255)->nullable()->change();
            });
        }
    }
};
