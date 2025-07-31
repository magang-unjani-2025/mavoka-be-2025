<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jurusan;

class JurusanSeeder extends Seeder
{
    public function run(): void
    {
        Jurusan::create([
            'sekolah_id' => 1,
            'nama_jurusan' => 'Rekayasa Perangkat Lunak',
        ]);
    }
}
