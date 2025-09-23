<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Pada migration sebelumnya kolom status sudah menjadi string (rename dari status_baru)
        // Kita hanya memastikan normalisasi nilai agar konsisten: aktif | tidak | draft

        // 1. Tambah kolom sementara jika perlu untuk migrasi massal (tidak perlu di sini karena hanya normalisasi)
        // 2. Ubah nilai yang tidak dikenal menjadi 'draft'
        DB::table('lowongan_magang')->select('id','status')->orderBy('id')->chunk(200, function($rows){
            foreach ($rows as $row) {
                $val = strtolower(trim($row->status ?? ''));
                if (!in_array($val, ['aktif','tidak','draft'])) {
                    // Nilai lama bisa jadi 'buka','tutup','', null; map ulang
                    $mapped = match($val) {
                        'buka' => 'aktif',
                        'tutup' => 'tidak',
                        '' => 'draft',
                        null => 'draft',
                        default => 'draft'
                    };
                    DB::table('lowongan_magang')->where('id',$row->id)->update(['status'=>$mapped]);
                }
            }
        });

        // 3. Set default database level jika belum (gunakan raw alter karena kolom saat ini string biasa)
        // Catatan: kalau kolom sudah type enum di engine tertentu, perlu raw modify. Di sini asumsi string.
        if (Schema::hasTable('lowongan_magang')) {
            // MySQL saja: ALTER TABLE modify default (gunakan try-catch agar tidak error di sqlite/test)
            try {
                DB::statement("ALTER TABLE lowongan_magang MODIFY status VARCHAR(32) NOT NULL DEFAULT 'draft'");
            } catch (Exception $e) {
                // Abaikan jika DB driver tidak mendukung statement ini
            }
        }
    }

    public function down(): void
    {
        // Rollback default ke NULL (atau tanpa default)
        try {
            DB::statement("ALTER TABLE lowongan_magang MODIFY status VARCHAR(32) NOT NULL");
        } catch (Exception $e) {
            // ignore
        }
        // Tidak mengubah nilai 'draft' kembali karena bisa sudah digunakan secara valid.
    }
};
