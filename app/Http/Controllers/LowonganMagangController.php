<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LowonganMagang;
use Tymon\JWTAuth\Facades\JWTAuth;

class LowonganMagangController extends Controller
{
    // 🟢 Semua orang bisa akses
    public function listAll()
    {
        $lowongans = LowonganMagang::with('perusahaan')->get();
        return response()->json($lowongans);
    }

    public function detail($id)
    {
        $lowongan = LowonganMagang::with('perusahaan')->findOrFail($id);
        return response()->json($lowongan);
    }

    // 🔒 Hanya untuk perusahaan login
    public function store(Request $request)
    {
        $perusahaan = JWTAuth::parseToken()->authenticate();

        $validated = $request->validate([
            'judul_lowongan' => 'required|string',
            'deskripsi' => 'required|string',
            'posisi' => 'required|string',
            'kuota' => 'required|integer',
            'lokasi_penempatan' => 'required|string',
            'persyaratan' => 'required|string',
            'benefit' => 'required|string',
            'status' => 'nullable|in:aktif,tidak',
            'deadline_lamaran' => 'required|date',
            'periode_awal' => 'required|date',
            'periode_akhir' => 'required|date|after_or_equal:periode_awal',
        ]);

        $data = array_merge($validated, [
            'perusahaan_id' => $perusahaan->id,
            'status' => ($validated['status'] ?? 'aktif')
        ]);
        $lowongan = LowonganMagang::create($data);

        return response()->json(['message' => 'Lowongan berhasil dibuat', 'data' => $lowongan], 201);
    }

    public function index()
    {
        $perusahaan = JWTAuth::parseToken()->authenticate();
        $lowongans = LowonganMagang::where('perusahaan_id', $perusahaan->id)->get();

        return response()->json($lowongans);
    }

    public function update(Request $request, $id)
    {
        $perusahaan = JWTAuth::parseToken()->authenticate();
        $lowongan = LowonganMagang::where('perusahaan_id', $perusahaan->id)->findOrFail($id);

        $fields = $request->only([
            'judul_lowongan','deskripsi','posisi','kuota','lokasi_penempatan','persyaratan','benefit','status','deadline_lamaran','periode_awal','periode_akhir'
        ]);
        // Jika kuota diupdate ke 0 atau sudah 0 -> status otomatis 'tidak'
        if (array_key_exists('kuota', $fields)) {
            if ((int)$fields['kuota'] <= 0) {
                $fields['status'] = 'tidak';
            } elseif (!isset($fields['status'])) {
                // Jika kuota > 0 dan status tidak diset manual, tetap aktif
                $fields['status'] = 'aktif';
            }
        }
        $lowongan->update($fields);

        return response()->json(['message' => 'Lowongan berhasil diperbarui', 'data' => $lowongan]);
    }

    public function destroy($id)
    {
        $perusahaan = JWTAuth::parseToken()->authenticate();
        $lowongan = LowonganMagang::where('perusahaan_id', $perusahaan->id)->findOrFail($id);

        $lowongan->delete();

        return response()->json(['message' => 'Lowongan berhasil dihapus']);
    }
}
