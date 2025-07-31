<?php

// database/seeders/SiswaSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Siswa;
use Illuminate\Support\Facades\Hash;

class SiswaSeeder extends Seeder
{
    public function run(): void
    {
        Siswa::insert([
            [
                'nisn' => '99887766',
                'sekolah_id'=>1,
                'kelas' => 12,
                'jurusan_id' => 1,
                'tahun_ajaran' => 2024,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nisn' => '1010101010',
                'sekolah_id' => 1,
                'kelas' => 12,
                'jurusan_id' => 1,
                'tahun_ajaran' => 2025,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}

