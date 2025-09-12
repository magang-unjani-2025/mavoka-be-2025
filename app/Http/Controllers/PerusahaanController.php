<?php

namespace App\Http\Controllers;

use App\Models\Perusahaan;
use App\Models\Pelamar;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Request;

class PerusahaanController extends Controller
{
    // Daftar pelamar untuk semua lowongan milik perusahaan yang login
    public function listPelamar(Request $request)
    {
        $perusahaan = JWTAuth::parseToken()->authenticate();

        $status = $request->query('status'); // lamar|wawancara|penawaran|ditolak|diterima
        $q = $request->query('q'); // pencarian by nama siswa / judul lowongan
        $allowed = ['lamar','wawancara','penawaran','ditolak','diterima'];
        if ($status && !in_array($status, $allowed, true)) {
            return response()->json(['message' => 'Status tidak valid'], 422);
        }

        $pelamarQuery = Pelamar::with(['siswa','lowongan'])
            ->whereHas('lowongan', function($lw) use ($perusahaan){
                $lw->where('perusahaan_id', $perusahaan->id);
            });

        if ($status) {
            $pelamarQuery->where('status_lamaran', $status);
        }
        if ($q) {
            $pelamarQuery->where(function($sub) use ($q){
                $sub->whereHas('siswa', function($s) use ($q){
                    $s->where('nama_lengkap','ILIKE','%'.$q.'%')
                      ->orWhere('nisn','ILIKE','%'.$q.'%');
                })->orWhereHas('lowongan', function($l) use ($q){
                    $l->where('judul_lowongan','ILIKE','%'.$q.'%');
                });
            });
        }

        // Pagination sederhana
        $perPage = (int)($request->query('per_page', 15));
        $pelamar = $pelamarQuery->orderByDesc('tanggal_lamaran')->paginate($perPage);

        $data = $pelamar->getCollection()->map(function($p){
            return [
                'id' => $p->id,
                'status_lamaran' => $p->status_lamaran,
                'tanggal_lamaran' => $p->tanggal_lamaran,
                'siswa' => $p->siswa ? [
                    'id' => $p->siswa->id,
                    'nama_lengkap' => $p->siswa->nama_lengkap,
                    'nisn' => $p->siswa->nisn,
                    'kelas' => $p->siswa->kelas,
                ] : null,
                'lowongan' => $p->lowongan ? [
                    'id' => $p->lowongan->id,
                    'judul_lowongan' => $p->lowongan->judul_lowongan,
                    'posisi' => $p->lowongan->posisi,
                ] : null,
                'cv_url' => $p->cv ? asset('storage/'.$p->cv) : null,
                'transkrip_url' => $p->transkrip ? asset('storage/'.$p->transkrip) : null,
            ];
        });

        return response()->json([
            'status_filter' => $status,
            'search' => $q,
            'pagination' => [
                'current_page' => $pelamar->currentPage(),
                'per_page' => $pelamar->perPage(),
                'total' => $pelamar->total(),
                'last_page' => $pelamar->lastPage(),
            ],
            'data' => $data
        ]);
    }
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
