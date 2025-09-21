<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Tambah kolom baru string sementara untuk status baru
        Schema::table('lowongan_magang', function (Blueprint $table) {
            $table->string('status_baru')->default('aktif')->after('benefit');
        });

        // Migrasi nilai lama buka/tutup -> aktif/tidak
        DB::table('lowongan_magang')->select('id','status','kuota')->orderBy('id')->chunk(100, function($rows){
            foreach ($rows as $row) {
                $mapped = match($row->status){
                    'buka' => ($row->kuota !== null && (int)$row->kuota > 0) ? 'aktif' : 'tidak',
                    'tutup' => 'tidak',
                    default => 'aktif'
                };
                DB::table('lowongan_magang')->where('id',$row->id)->update(['status_baru'=>$mapped]);
            }
        });

        // Hapus kolom enum lama
        Schema::table('lowongan_magang', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        // Rename kolom baru
        Schema::table('lowongan_magang', function (Blueprint $table) {
            $table->renameColumn('status_baru','status');
        });
    }

    public function down(): void
    {
        // Kembalikan ke enum buka/tutup (sederhana: string sementara lalu rename)
        Schema::table('lowongan_magang', function (Blueprint $table) {
            $table->enum('status_lama', ['buka','tutup'])->default('buka');
        });

        DB::table('lowongan_magang')->select('id','status','kuota')->orderBy('id')->chunk(100, function($rows){
            foreach ($rows as $row) {
                $mapped = $row->status === 'aktif' ? 'buka' : 'tutup';
                DB::table('lowongan_magang')->where('id',$row->id)->update(['status_lama'=>$mapped]);
            }
        });

        Schema::table('lowongan_magang', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        Schema::table('lowongan_magang', function (Blueprint $table) {
            $table->renameColumn('status_lama','status');
        });
    }
};
