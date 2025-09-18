<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LowonganMagang;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
            'tugas_tanggung_jawab' => 'nullable|string',
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
        try {
            $perusahaan = JWTAuth::parseToken()->authenticate();
            $lowongan = LowonganMagang::where('perusahaan_id', $perusahaan->id)->findOrFail($id);

            // Ambil hanya field yang diizinkan
            $payload = $request->only([
                'judul_lowongan','deskripsi','posisi','kuota','lokasi_penempatan','persyaratan','benefit','tugas_tanggung_jawab','status','deadline_lamaran','periode_awal','periode_akhir'
            ]);

            if (empty(array_filter($payload, fn($v) => $v !== null && $v !== ''))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data yang dikirim untuk diperbarui'
                ], 422);
            }

            // Validation: only validate fields that are present (sometimes)
            $rules = [
                'judul_lowongan' => 'sometimes|string',
                'deskripsi' => 'sometimes|string',
                'posisi' => 'sometimes|string',
                'kuota' => 'sometimes|integer|min:0',
                'lokasi_penempatan' => 'sometimes|string',
                'persyaratan' => 'sometimes|string',
                'benefit' => 'sometimes|string',
                'tugas_tanggung_jawab' => 'sometimes|string',
                'status' => 'sometimes|in:aktif,tidak',
                'deadline_lamaran' => 'sometimes|date',
                'periode_awal' => 'sometimes|date',
                'periode_akhir' => 'sometimes|date',
            ];

            $validated = $request->validate($rules);

            // Jika periode_awal / periode_akhir keduanya ada atau salah satunya berubah -> validasi konsistensi urutan
            $periode_awal = $validated['periode_awal'] ?? $lowongan->periode_awal;
            $periode_akhir = $validated['periode_akhir'] ?? $lowongan->periode_akhir;
            if ($periode_awal && $periode_akhir && strtotime($periode_akhir) < strtotime($periode_awal)) {
                return response()->json([
                    'success' => false,
                    'message' => 'periode_akhir harus setelah atau sama dengan periode_awal'
                ], 422);
            }

            // Logika kuota & status otomatis
            if (array_key_exists('kuota', $validated)) {
                if ((int)$validated['kuota'] <= 0) {
                    $validated['status'] = 'tidak';
                } else {
                    // Hanya set aktif jika status tidak dikirim eksplisit
                    if (!array_key_exists('status', $validated)) {
                        $validated['status'] = 'aktif';
                    }
                }
            }

            // Hindari update jika tidak ada perubahan nyata
            $dirtyInput = collect($validated)->filter(function($value, $key) use ($lowongan) {
                return $lowongan->{$key} != $value; // loose comparison cukup di sini
            })->all();

            if (empty($dirtyInput)) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tidak ada perubahan (data sama)',
                    'data' => $lowongan
                ]);
            }

            $lowongan->update($dirtyInput);

            return response()->json([
                'success' => true,
                'message' => 'Lowongan berhasil diperbarui',
                'data' => $lowongan->fresh('perusahaan')
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lowongan tidak ditemukan untuk perusahaan Anda'
            ], 404);
        }
    }

    public function destroy($id)
    {
        $perusahaan = JWTAuth::parseToken()->authenticate();
        $lowongan = LowonganMagang::where('perusahaan_id', $perusahaan->id)->findOrFail($id);

        $lowongan->delete();

        return response()->json(['message' => 'Lowongan berhasil dihapus']);
    }
}
