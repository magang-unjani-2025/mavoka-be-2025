<?php

namespace App\Http\Controllers;

use App\Models\LaporanHarian;
use App\Models\EvaluasiMagangMingguan;
use App\Models\PemagangAktif;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;

class LaporanMagangController extends Controller
{
    // Siswa: buat laporan harian
    public function createLaporanHarian(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // perusahaan_id & magang_id tidak perlu dikirim, diambil dari PemagangAktif siswa yang login
            'tanggal_laporan' => 'required|date',
            'dokumentasi_foto' => 'sometimes|file|image|mimes:jpeg,png,jpg|max:4096',
            'deskripsi' => 'nullable|string',
            'output' => 'nullable|string',
            'hambatan' => 'nullable|string',
            'solusi' => 'nullable|string',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $siswa = JWTAuth::parseToken()->authenticate();

        // Cari pemagang aktif berdasarkan siswa dari token
        $pemagang = PemagangAktif::with('pelamar')
            ->whereHas('pelamar', function ($q) use ($siswa) {
                $q->where('siswa_id', $siswa->id);
            })
            ->where('status_magang', 'aktif')
            ->first();
        if (!$pemagang) {
            return response()->json(['message' => 'Anda tidak terdaftar sebagai pemagang aktif'], 403);
        }

        $fotoPath = null;
        if ($request->hasFile('dokumentasi_foto')) {
            $fotoPath = $request->file('dokumentasi_foto')->store('laporan/dokumentasi', 'public');
        }

        // Cegah duplikat laporan pada tanggal yang sama untuk siswa & magang yang sama
        $exists = LaporanHarian::where('siswa_id', $siswa->id)
            ->where('magang_id', $pemagang->magang_id)
            ->whereDate('tanggal_laporan', $request->tanggal_laporan)
            ->exists();
        if ($exists) {
            return response()->json(['message' => 'Laporan untuk tanggal ini sudah dibuat'], 409);
        }

        $laporan = LaporanHarian::create([
            // Ambil perusahaan dari data PemagangAktif
            'perusahaan_id' => $pemagang->perusahaan_id,
            'siswa_id' => $siswa->id,
            'magang_id' => $pemagang->magang_id,
            'tanggal_laporan' => $request->tanggal_laporan,
            'dokumentasi_foto' => $fotoPath,
            'deskripsi' => $request->deskripsi,
            'output' => $request->output,
            'hambatan' => $request->hambatan,
            'solusi' => $request->solusi,
        ]);

        return response()->json([
            'message' => 'Laporan harian berhasil dibuat',
            'data' => $laporan
        ], 201);
    }

    // Perusahaan: evaluasi / komentar laporan harian siswa
    public function evaluasiLaporanHarian(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'evaluasi_perusahaan' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $laporan = LaporanHarian::find($id);
        if (!$laporan) {
            return response()->json(['message' => 'Laporan tidak ditemukan'], 404);
        }

        // TODO: validasi perusahaan yang login sesuai laporan->perusahaan_id jika sudah ada multi-guard perusahaan
        $laporan->evaluasi_perusahaan = $request->evaluasi_perusahaan;
        $laporan->save();

        return response()->json(['message' => 'Evaluasi disimpan', 'data' => $laporan]);
    }

    // Perusahaan: input penilaian mingguan
    public function createEvaluasiMingguan(Request $request, $magangId)
    {
        $validator = Validator::make($request->all(), [
            // perusahaan_id, siswa_id, magang_id tidak perlu dikirim
            'aspek_teknis' => 'nullable|string',
            'aspek_komunikasi' => 'nullable|string',
            'aspek_kerjasama' => 'nullable|string',
            'aspek_disiplin' => 'nullable|string',
            'aspek_inisiatif' => 'nullable|string',
            'nilai_aspek_teknis' => 'nullable|integer|min:0|max:100',
            'nilai_aspek_komunikasi' => 'nullable|integer|min:0|max:100',
            'nilai_aspek_kerjasama' => 'nullable|integer|min:0|max:100',
            'nilai_aspek_disiplin' => 'nullable|integer|min:0|max:100',
            'nilai_aspek_inisiatif' => 'nullable|integer|min:0|max:100',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Ambil perusahaan dari token dan validasi kepemilikan terhadap magangId
        $perusahaan = Auth::guard('perusahaan')->user();
        if (!$perusahaan) {
            return response()->json(['message' => 'Unauthorized perusahaan'], 401);
        }

        $pemagang = PemagangAktif::with(['pelamar'])
            ->where('magang_id', $magangId)
            ->first();
        if (!$pemagang) {
            return response()->json(['message' => 'Data magang tidak ditemukan'], 404);
        }
        if ($pemagang->perusahaan_id != $perusahaan->id) {
            return response()->json(['message' => 'Anda tidak berhak menilai magang ini'], 403);
        }
        if (isset($pemagang->status_magang) && $pemagang->status_magang !== 'aktif') {
            return response()->json(['message' => 'Status magang tidak aktif'], 422);
        }

        $nilai = collect([
            $request->nilai_aspek_teknis,
            $request->nilai_aspek_komunikasi,
            $request->nilai_aspek_kerjasama,
            $request->nilai_aspek_disiplin,
            $request->nilai_aspek_inisiatif,
        ])->filter(fn($v) => $v !== null);
        $rata = $nilai->isNotEmpty() ? round($nilai->avg(), 2) : null;

        $evaluasi = EvaluasiMagangMingguan::create([
            'perusahaan_id' => $pemagang->perusahaan_id,
            // siswa_id diambil dari relasi pelamar
            'siswa_id' => optional($pemagang->pelamar)->siswa_id,
            'magang_id' => $pemagang->magang_id,
            'aspek_teknis' => $request->aspek_teknis,
            'aspek_komunikasi' => $request->aspek_komunikasi,
            'aspek_kerjasama' => $request->aspek_kerjasama,
            'aspek_disiplin' => $request->aspek_disiplin,
            'aspek_inisiatif' => $request->aspek_inisiatif,
            'nilai_aspek_teknis' => $request->nilai_aspek_teknis,
            'nilai_aspek_komunikasi' => $request->nilai_aspek_komunikasi,
            'nilai_aspek_kerjasama' => $request->nilai_aspek_kerjasama,
            'nilai_aspek_disiplin' => $request->nilai_aspek_disiplin,
            'nilai_aspek_inisiatif' => $request->nilai_aspek_inisiatif,
            'nilai_rata_rata' => $rata,
            'upload_at' => now(),
        ]);

        return response()->json([
            'message' => 'Evaluasi mingguan tersimpan',
            'data' => $evaluasi
        ], 201);
    }

    // Siswa/perusahaan: list laporan harian per siswa
    public function listLaporanSiswa(Request $request, $siswaId)
    {
        $items = LaporanHarian::where('siswa_id', $siswaId)
            ->orderByDesc('tanggal_laporan')
            ->get();
        return response()->json(['data' => $items]);
    }

    // Siswa/perusahaan: list evaluasi mingguan per siswa
    public function listEvaluasiSiswa(Request $request, $siswaId)
    {
        $items = EvaluasiMagangMingguan::where('siswa_id', $siswaId)
            ->orderByDesc('upload_at')
            ->get();
        return response()->json(['data' => $items]);
    }

    // Sekolah: lihat evaluasi perusahaan & laporan harian untuk semua siswa sekolah
    public function sekolahEvaluasiMagang(Request $request, $siswaId)
    {
        // Ambil sekolah dari guard 'sekolah' (middleware sudah memastikan token valid)
        $sekolah = Auth::guard('sekolah')->user();
        if (!$sekolah) {
            return response()->json(['message' => 'Unauthorized sekolah'], 401);
        }

        $validator = Validator::make($request->all(), [
            'perusahaan_id' => 'sometimes|integer|exists:perusahaan,id',
            'from' => 'sometimes|date',
            'to' => 'sometimes|date|after_or_equal:from',
            'with_laporan_harian' => 'sometimes|boolean',
            'with_evaluasi_mingguan' => 'sometimes|boolean',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Pastikan siswa ini milik sekolah yg login
        $targetSiswa = \App\Models\Siswa::where('id', $siswaId)->where('sekolah_id', $sekolah->id)->first();
        if (!$targetSiswa) {
            return response()->json(['message' => 'Siswa tidak ditemukan di sekolah ini'], 404);
        }

        $withDaily = $request->boolean('with_laporan_harian', true);
        $withWeekly = $request->boolean('with_evaluasi_mingguan', true);

        // Query base: siswa dalam sekolah ini
        $siswaFilter = function($q) use ($sekolah, $siswaId) {
            $q->where('sekolah_id', $sekolah->id)->where('id', $siswaId);
        };

        $daily = [];
        if ($withDaily) {
            // Cukup filter langsung berdasarkan siswa_id karena sudah diverifikasi milik sekolah ini
            $dailyQuery = LaporanHarian::query()
                ->where('siswa_id', $siswaId);
            if ($request->filled('perusahaan_id')) {
                $dailyQuery->where('perusahaan_id', $request->perusahaan_id);
            }
            if ($request->filled('from')) {
                $dailyQuery->whereDate('tanggal_laporan', '>=', $request->from);
            }
            if ($request->filled('to')) {
                $dailyQuery->whereDate('tanggal_laporan', '<=', $request->to);
            }
            $daily = $dailyQuery->orderByDesc('tanggal_laporan')->get();
        }

        $weekly = [];
        if ($withWeekly) {
            $weeklyQuery = EvaluasiMagangMingguan::query()
                ->where('siswa_id', $siswaId);
            if ($request->filled('perusahaan_id')) {
                $weeklyQuery->where('perusahaan_id', $request->perusahaan_id);
            }
            if ($request->filled('from')) {
                $weeklyQuery->whereDate('upload_at', '>=', $request->from);
            }
            if ($request->filled('to')) {
                $weeklyQuery->whereDate('upload_at', '<=', $request->to);
            }
            $weekly = $weeklyQuery->orderByDesc('upload_at')->get();
        }

        return response()->json([
            'sekolah_id' => $sekolah->id,
            'siswa_id' => (int)$siswaId,
            'filters' => [
                'perusahaan_id' => $request->perusahaan_id,
                'from' => $request->from,
                'to' => $request->to,
                'with_laporan_harian' => $withDaily,
                'with_evaluasi_mingguan' => $withWeekly,
            ],
            'laporan_harian' => $daily,
            'evaluasi_mingguan' => $weekly,
        ]);
    }
}
