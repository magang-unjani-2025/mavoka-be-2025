<?php   

namespace App\Http\Controllers;

use App\Models\Perusahaan;
use Illuminate\Http\Request;

class PerusahaanController extends Controller
{
    public function getById($id)
    {
        $perusahaan = Perusahaan::find($id);

        if (!$perusahaan) {
            return response()->json(['message' => 'Perusahaan tidak ditemukan'], 404);
        }

        return response()->json([
            'id' => $perusahaan->id,
            'nama_perusahaan' => $perusahaan->nama_perusahaan,
            'bidang_usaha' => $perusahaan->bidang_usaha,
            'deskripsi_usaha' => $perusahaan->deskripsi_usaha,
            'alamat' => $perusahaan->alamat,
            'kontak' => $perusahaan->kontak,
            'email' => $perusahaan->email,
            'web_perusahaan' => $perusahaan->web_perusahaan,
            'logo_perusahaan' => $perusahaan->logo_perusahaan,
            'divisi_penempatan' => $perusahaan->divisi_penempatan,
            'penanggung_jawab' => $perusahaan->penanggung_jawab,
        ]);
    }
}
