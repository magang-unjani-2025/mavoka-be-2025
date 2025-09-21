<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Perusahaan;
use App\Models\Sekolah;
use App\Models\LembagaPelatihan;

class UserController extends Controller
{
    public function getById($role, $id)
    {
        $model = match ($role) {
            'perusahaan' => Perusahaan::class,
            'sekolah' => Sekolah::class,
            'lpk' => LembagaPelatihan::class,
            default => null,
        };

        if (!$model) {
            return response()->json(['message' => 'Role tidak valid'], 400);
        }

        $akun = $model::find($id);

        if (!$akun) {
            return response()->json(['message' => ucfirst($role).' tidak ditemukan'], 404);
        }

        // Mapping field per role
        $data = match ($role) {
            'perusahaan' => [
                'id' => $akun->id,
                'nama_perusahaan' => $akun->nama_perusahaan,
                'bidang_usaha' => $akun->bidang_usaha,
                'deskripsi_usaha' => $akun->deskripsi_usaha,
                'alamat' => $akun->alamat,
                'kontak' => $akun->kontak,
                'email' => $akun->email,
                'web_perusahaan' => $akun->web_perusahaan,
                'logo_perusahaan' => $akun->logo_perusahaan,
                'divisi_penempatan' => $akun->divisi_penempatan,
                'penanggung_jawab' => $akun->penanggung_jawab,
            ],
            'sekolah' => [
                'id' => $akun->id,
                'nama_sekolah' => $akun->nama_sekolah,
                'web_sekolah' => $akun->web_sekolah,
                'npsn' => $akun->npsn,
                'jurusan' => $akun->jurusan,
                'kontak' => $akun->kontak,
                'alamat' => $akun->alamat,
                'email' => $akun->email,
            ],
            'lpk' => [
                'id' => $akun->id,
                'nama_lembaga' => $akun->nama_lembaga,
                'web_lembaga' => $akun->web_lembaga,
                'bidang_pelatihan' => $akun->bidang_pelatihan,
                'deskripsi_lembaga' => $akun->deskripsi_lembaga,
                'alamat' => $akun->alamat,
                'kontak' => $akun->kontak,
                'email' => $akun->email,
                'logo_lembaga' => $akun->logo_lembaga,
                'status_akreditasi' => $akun->status_akreditasi,
                'dokumen_akreditasi' => $akun->dokumen_akreditasi,
            ],
        };

        return response()->json($data);
    }
}
