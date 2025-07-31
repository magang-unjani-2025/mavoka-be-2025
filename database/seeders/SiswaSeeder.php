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
        Siswa::create([
            'username' => 'siswa1',
            'email' => 'siswa1@example.com',
            'password' => Hash::make('siswa1'),
            'nama_lengkap' => 'Budi Santoso',
            'nisn' => '99887766',
            'kelas' => 12,
            'jurusan_id' => 1,
            'tahun_ajaran' => 2024,
            'tanggal_lahir' => '2007-05-21',
            'jenis_kelamin' => 'Laki-laki',
            'alamat' => 'Jl. Kenangan No. 5',
            'kontak' => '081234567891',
            'status_siswa' => 'aktif',
            'sekolah_id' => 1,
            'status_verifikasi' => 'Terverifikasi',
            'tanggal_verifikasi' => now(),
            'otp' => 654321,
            'otp_expired_at' => now()->addMinutes(10),
        ]);
    }
}
