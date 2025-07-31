<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\LembagaPelatihan;

class LembagaSeeder extends Seeder
{
    public function run(): void
    {
        LembagaPelatihan::create([
            'username' => 'lpkbangun',
            'email' => 'admin@lpkbangun.com',
            'password' => Hash::make('lpkbangun'),
            'status_verifikasi' => 'Terverifikasi',
            'tanggal_verifikasi' => now(),
            'nama_lembaga' => 'LPK Bangun Karier',
            'bidang_pelatihan' => 'Desain Grafis',
            'deskripsi_lembaga' => 'Lembaga pelatihan profesional di bidang desain digital dan kreatif.',
            'alamat' => 'Jl. Pelatihan No.23, Yogyakarta',
            'kontak' => '081234567890',
            'logo_lembaga' => null,
            'status_akreditasi' => 'A',
            'otp' => Str::random(6),
            'otp_expired_at' => now()->addMinutes(10),
        ]);
    }
}
