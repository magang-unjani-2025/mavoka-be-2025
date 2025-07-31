<?php

namespace Database\Seeders;

use App\Models\LowonganMagang;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class LowonganMagangSeeder extends Seeder
{
    public function run(): void
    {
        LowonganMagang::insert([
            [
                'perusahaan_id' => 1,
                'judul_lowongan' => 'Magang Frontend Developer',
                'deskripsi' => 'Bergabunglah bersama tim IT kami untuk membangun aplikasi berbasis web.',
                'posisi' => 'Frontend Developer',
                'kuota' => 3,
                'lokasi_penempatan' => 'Jakarta Selatan',
                'persyaratan' => 'Menguasai HTML, CSS, JavaScript, dan React.',
                'benefit' => 'Uang transport, sertifikat, pengalaman kerja nyata.',
                'status' => 'buka',
                'deadline_lamaran' => Carbon::now()->addDays(30)->format('Y-m-d'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'perusahaan_id' => 1,
                'judul_lowongan' => 'Magang UI/UX Designer',
                'deskripsi' => 'Membantu mendesain antarmuka aplikasi mobile dan website.',
                'posisi' => 'UI/UX Designer',
                'kuota' => 2,
                'lokasi_penempatan' => 'Bandung',
                'persyaratan' => 'Memahami prinsip desain, bisa pakai Figma.',
                'benefit' => 'Sertifikat, mentoring, lingkungan kerja kreatif.',
                'status' => 'buka',
                'deadline_lamaran' => Carbon::now()->addDays(45)->format('Y-m-d'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
