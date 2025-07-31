<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Sekolah;
use App\Models\Perusahaan;
use App\Models\LembagaPelatihan;

class VerifikasiController extends Controller
{
    public function verifikasi($tipe, $id)
    {
        $allowedRoles = ['siswa', 'sekolah', 'perusahaan', 'lpk'];

        if (!in_array($tipe, $allowedRoles)) {
            return response()->json(['message' => 'Tipe tidak valid'], 400);
        }

        $model = match ($tipe) {
            'siswa' => Siswa::class,
            'sekolah' => Sekolah::class,
            'perusahaan' => Perusahaan::class,
            'lpk' => LembagaPelatihan::class,
        };

        $akun = $model::find($id);

        if (!$akun) {
            return response()->json(['message' => 'Akun tidak ditemukan'], 404);
        }

        if ($akun->status_verifikasi === 'Terverifikasi') {
            return response()->json(['message' => 'Akun sudah diverifikasi oleh admin sebelumnya'], 400);
        }

        $akun->status_verifikasi = 'Terverifikasi';
        $akun->save();

        return response()->json([
            'message' => 'Akun berhasil diverifikasi oleh admin',
            'data' => $akun
        ]);
    }
}
