<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Tambah kolom baru string sementara
        Schema::table('pelamar', function (Blueprint $table) {
            $table->string('status_lamaran_new')->default('lamar')->after('tanggal_lamaran');
        });

        // Migrasi data lama enum ke nilai baru
        DB::table('pelamar')->select('id','status_lamaran')->orderBy('id')->chunk(100, function($rows){
            foreach ($rows as $row) {
                $mapped = match($row->status_lamaran){
                    'diproses','dikirim' => 'lamar',
                    'diterima' => 'diterima',
                    'ditolak' => 'ditolak',
                    default => 'lamar'
                };
                DB::table('pelamar')->where('id',$row->id)->update(['status_lamaran_new'=>$mapped]);
            }
        });

        Schema::table('pelamar', function (Blueprint $table) {
            $table->dropColumn('status_lamaran');
        });
        Schema::table('pelamar', function (Blueprint $table) {
            $table->renameColumn('status_lamaran_new','status_lamaran');
        });
    }

    public function down(): void
    {
        // Kembalikan ke enum sederhana (lamar/interview/penawaran/diterima/ditolak) => akan membuat enum baru
        Schema::table('pelamar', function (Blueprint $table) {
            $table->string('status_lamaran_old')->nullable();
        });

        DB::table('pelamar')->select('id','status_lamaran')->orderBy('id')->chunk(100, function($rows){
            foreach ($rows as $row) {
                $mapped = in_array($row->status_lamaran, ['lamar','interview','penawaran','diterima','ditolak']) ? $row->status_lamaran : 'lamar';
                DB::table('pelamar')->where('id',$row->id)->update(['status_lamaran_old'=>$mapped]);
            }
        });

        Schema::table('pelamar', function (Blueprint $table) {
            $table->dropColumn('status_lamaran');
        });
        Schema::table('pelamar', function (Blueprint $table) {
            $table->renameColumn('status_lamaran_old','status_lamaran');
        });
    }
};
