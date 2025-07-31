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
            'status' => 'required|in:buka,tutup',
            'deadline_lamaran' => 'required|date',
        ]);

        $lowongan = LowonganMagang::create([
            'perusahaan_id' => $perusahaan->id,
            ...$validated
        ]);

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

        $lowongan->update($request->only([
            'judul_lowongan',
            'deskripsi',
            'posisi',
            'kuota',
            'lokasi_penempatan',
            'persyaratan',
            'benefit',
            'status',
            'deadline_lamaran',
        ]));

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
