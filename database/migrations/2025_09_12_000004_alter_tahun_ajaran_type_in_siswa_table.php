<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // PostgreSQL: ubah integer -> string (varchar) dengan cast
        // Simpan nilai integer lama tetap sebagai string yang sama (misal 2024)
        DB::statement('ALTER TABLE siswa ALTER COLUMN tahun_ajaran TYPE varchar(20) USING tahun_ajaran::varchar');
    }

    public function down(): void
    {
        // Down: coba konversi balik ke integer (ambil angka pertama sebelum slash jika ada)
        // Nilai seperti 2024/2025 akan diambil bagian sebelum '/'
        DB::statement("UPDATE siswa SET tahun_ajaran = split_part(tahun_ajaran, '/', 1) WHERE tahun_ajaran IS NOT NULL AND tahun_ajaran ~ '^[0-9]{4}/[0-9]{4}$'");
        DB::statement("ALTER TABLE siswa ALTER COLUMN tahun_ajaran TYPE integer USING NULLIF(tahun_ajaran,'')::integer");
    }
};
