<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sekolah;
use Illuminate\Support\Facades\Hash;

class SekolahSeeder extends Seeder
{
    public function run(): void
    {
        Sekolah::create([
            'username' => 'sekolah1',
            'email' => 'sekolah1@example.com',
            'password' => Hash::make('sekolah1'),
            'nama_sekolah' => 'SMK Negeri 1',
            'web_sekolah' => 'https://smkn1.sch.id',
            'npsn' => '12345678',
            'kontak' => '081234567890',
            'alamat' => 'Jl. Pendidikan No. 1',
            'status_verifikasi' => 'Terverifikasi',
            'tanggal_verifikasi' => now(),
            'otp' => 123456,
            'otp_expired_at' => now()->addMinutes(10),
        ]);
    }
}
