<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Perusahaan;

class PerusahaanSeeder extends Seeder
{
    public function run(): void
    {
        Perusahaan::create([
            'username' => 'techcorp',
            'email' => 'info@techcorp.com',
            'password' => Hash::make('techcorp'),
            'status_verifikasi' => 'Terverifikasi',
            'tanggal_verifikasi' => now(),
            'nama_perusahaan' => 'TechCorp Indonesia',
            'bidang_usaha' => 'Teknologi Informasi',
            'deskripsi_usaha' => 'Perusahaan IT yang fokus pada pengembangan perangkat lunak dan solusi cloud.',
            'alamat' => 'Jl. Teknologi No.88, Jakarta',
            'kontak' => '021-12345678',
            'logo_perusahaan' => null,
            'penanggung_jawab' => 'Dian Prasetyo',
            'web_perusahaan' => 'https://pmb.unjaya.ac.id/',
            'otp' => Str::random(6),
            'otp_expired_at' => now()->addMinutes(10),
        ]);
    }
}
