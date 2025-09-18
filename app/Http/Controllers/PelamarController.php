<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pelamar;
use App\Models\LowonganMagang;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\PemagangAktif;

class PelamarController extends Controller
{
    // Endpoint untuk siswa melamar ke lowongan magang
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'siswa_id' => 'required|exists:siswa,id',
            'lowongan_id' => 'required|exists:lowongan_magang,id',
            // Optional: jika lowongan terkait batch tertentu
            'batch_id' => 'sometimes|exists:batch,id',
            'cv' => 'required|file|mimetypes:application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document|max:2048',
            'transkrip' => 'required|file|mimetypes:application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document|max:2048',
        ], [
            'siswa_id.required' => 'ID siswa wajib diisi.',
            'siswa_id.exists' => 'Siswa tidak ditemukan.',
            'lowongan_id.required' => 'ID lowongan wajib diisi.',
            'lowongan_id.exists' => 'Lowongan magang tidak ditemukan.',
            'cv.required' => 'File CV wajib diupload.',
            'cv.file' => 'CV harus berupa file.',
            'cv.mimetypes' => 'CV harus berformat PDF/DOC/DOCX.',
            'cv.max' => 'Ukuran CV maksimal 2MB.',
            'transkrip.required' => 'File transkrip wajib diupload.',
            'transkrip.file' => 'Transkrip harus berupa file.',
            'transkrip.mimetypes' => 'Transkrip harus berformat PDF/DOC/DOCX.',
            'transkrip.max' => 'Ukuran transkrip maksimal 2MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Tolak pendaftaran jika kuota lowongan sudah 0 (semua slot terisi)
            $lowongan = LowonganMagang::find($request->lowongan_id);
            if (!$lowongan) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Lowongan tidak ditemukan.'
                ], 404);
            }

            // Jika lowongan dikaitkan dengan batch tertentu via batch_id request, cek status batch
            if ($request->filled('batch_id')) {
                $batch = \App\Models\Batch::find($request->batch_id);
                if (!$batch) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Batch tidak ditemukan.'
                    ], 404);
                }
                // Normalisasi status otomatis jika tanggal sudah lewat
                if (method_exists($batch, 'autoCompleteIfPast')) {
                    $batch->autoCompleteIfPast();
                }
                if (is_string($batch->status) && strtolower($batch->status) === 'selesai') {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Pendaftaran ditutup karena batch sudah selesai.'
                    ], 409);
                }
            }
            if ($lowongan->kuota <= 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Kuota lowongan sudah penuh.'
                ], 409);
            }
            // Cegah duplikasi lamaran (satu siswa ke lowongan sama hanya sekali kecuali sebelumnya ditolak)
            $existing = Pelamar::where('siswa_id', $request->siswa_id)
                ->where('lowongan_id', $request->lowongan_id)
                ->whereNotIn('status_lamaran', ['ditolak'])
                ->first();
            if ($existing) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Lamaran sudah ada dalam proses.'
                ], 409);
            }

            // Simpan file CV dan transkrip ke storage/app/public/pelamar
            $cvPath = $request->file('cv')->store('pelamar/cv', 'public');
            $transkripPath = $request->file('transkrip')->store('pelamar/transkrip', 'public');

            $pelamar = Pelamar::create([
                'siswa_id' => $request->siswa_id,
                'lowongan_id' => $request->lowongan_id,
                'cv' => $cvPath,
                'transkrip' => $transkripPath,
                'tanggal_lamaran' => now(),
                'status_lamaran' => 'lamar',
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Lamaran berhasil dikirim!',
                'data' => [
                    'id' => $pelamar->id,
                    'siswa_id' => $pelamar->siswa_id,
                    'lowongan_id' => $pelamar->lowongan_id,
                    'cv_url' => asset('storage/' . $pelamar->cv),
                    'transkrip_url' => asset('storage/' . $pelamar->transkrip),
                    'tanggal_lamaran' => $pelamar->tanggal_lamaran,
                    'status_lamaran' => $pelamar->status_lamaran,
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengirim lamaran, silakan coba lagi.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Endpoint perusahaan / admin untuk ubah status lamaran (wawancara, penawaran, ditolak)
    public function updateStatus(Request $request, $id)
    {
        // Terima alias 'status_lamaran' agar kompatibel dengan klien yang sudah terlanjur memakainya
        if (!$request->has('status') && $request->has('status_lamaran')) {
            $request->merge(['status' => $request->input('status_lamaran')]);
        }
        // Alias lama 'interview' -> 'wawancara'
        if ($request->has('status') && $request->input('status') === 'interview') {
            $request->merge(['status' => 'wawancara']);
        }
        // Fallback: query param ?status= / ?status_lamaran=
        if (!$request->has('status')) {
            if ($request->query('status')) {
                $request->merge(['status' => $request->query('status')]);
            } elseif ($request->query('status_lamaran')) {
                $request->merge(['status' => $request->query('status_lamaran')]);
            }
        }
        // Fallback: PUT multipart/form-data tidak ter-parse oleh PHP -> coba parse JSON jika Content-Type JSON
        if (!$request->has('status')) {
            $ct = $request->header('Content-Type');
            if ($ct && Str::contains($ct, 'application/json')) {
                $decoded = json_decode($request->getContent(), true);
                if (is_array($decoded)) {
                    if (isset($decoded['status'])) {
                        $request->merge(['status' => $decoded['status']]);
                    } elseif (isset($decoded['status_lamaran'])) {
                        $request->merge(['status' => $decoded['status_lamaran']]);
                    }
                }
            }
        }
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:wawancara,penawaran,diterima,ditolak'
        ], [
            'status.required' => 'Status wajib diisi.',
            'status.in' => 'Status tidak valid.'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $pelamar = Pelamar::find($id);
        if (!$pelamar) {
            return response()->json(['status' => 'error', 'message' => 'Lamaran tidak ditemukan'], 404);
        }

        $current = $pelamar->status_lamaran;
        $target = $request->status;

        $allowed = [
            'lamar' => ['wawancara','ditolak'],
            'wawancara' => ['penawaran','ditolak'],
            'penawaran' => ['diterima','ditolak'],
            'diterima' => [],
            'ditolak' => []
        ];

        if (!isset($allowed[$current]) || !in_array($target, $allowed[$current])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Transisi status tidak diperbolehkan dari '.$current.' ke '.$target
            ], 409);
        }

        // Jika menerima tawaran, pastikan siswa belum pernah diterima lamaran lain
        if ($target === 'diterima') {
            $alreadyAccepted = Pelamar::where('siswa_id', $pelamar->siswa_id)
                ->where('status_lamaran', 'diterima')
                ->where('id','!=',$pelamar->id)
                ->exists();
            if ($alreadyAccepted) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Siswa sudah diterima pada lamaran lain.'
                ], 409);
            }
        }

        // Transaksi jika butuh decrement kuota saat diterima
        if ($target === 'diterima') {
            DB::transaction(function() use ($pelamar, $target) {
                // Lock lowongan
                $lowongan = LowonganMagang::where('id', $pelamar->lowongan_id)->lockForUpdate()->first();
                if (!$lowongan) {
                    throw new \RuntimeException('Lowongan tidak ditemukan');
                }
                if ($lowongan->kuota <= 0) {
                    throw new \RuntimeException('Kuota lowongan sudah habis');
                }
                $pelamar->status_lamaran = $target;
                $pelamar->save();
                $lowongan->kuota = $lowongan->kuota - 1;
                if ($lowongan->kuota <= 0) {
                    $lowongan->status = 'tidak';
                }
                $lowongan->save();

                // Buat atau dapatkan pemagang aktif untuk pelamar ini
                $exists = PemagangAktif::where('pelamar_id', $pelamar->id)->first();
                if (!$exists) {
                    PemagangAktif::create([
                        'pelamar_id' => $pelamar->id,
                        'perusahaan_id' => $lowongan->perusahaan_id,
                        'lowongan_id' => $lowongan->id,
                        // Sekolah id: diambil dari relasi siswa -> sekolah melalui pelamar
                        'sekolah_id' => optional($pelamar->siswa)->sekolah_id ?? null,
                        'tanggal_mulai' => now()->toDateString(),
                        'status_magang' => 'aktif',
                    ]);
                } else {
                    if (!$exists->tanggal_mulai) {
                        $exists->tanggal_mulai = now()->toDateString();
                    }
                    $exists->status_magang = 'aktif';
                    $exists->save();
                }
            });
        } else {
            $pelamar->status_lamaran = $target;
            $pelamar->save();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Status lamaran diperbarui',
            'data' => [
                'id' => $pelamar->id,
                'status_lamaran' => $pelamar->status_lamaran
            ]
        ]);
    }

    // Endpoint siswa untuk merespon penawaran (terima / tolak)
    public function respondPenawaran(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'aksi' => 'required|in:terima,tolak'
        ], [
            'aksi.required' => 'Aksi wajib diisi.',
            'aksi.in' => 'Aksi tidak valid.'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $pelamar = Pelamar::find($id);
        if (!$pelamar) {
            return response()->json(['status' => 'error', 'message' => 'Lamaran tidak ditemukan'], 404);
        }
        if ($pelamar->status_lamaran !== 'penawaran') {
            return response()->json(['status' => 'error', 'message' => 'Lamaran tidak dalam status penawaran'], 409);
        }

        if ($request->aksi === 'terima') {
            // Pastikan belum ada lamaran lain yang diterima
            $alreadyAccepted = Pelamar::where('siswa_id', $pelamar->siswa_id)
                ->where('status_lamaran', 'diterima')
                ->where('id','!=',$pelamar->id)
                ->exists();
            if ($alreadyAccepted) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Siswa sudah diterima pada lamaran lain.'
                ], 409);
            }

            try {
                DB::transaction(function() use ($pelamar) {
                    // Lock lowongan dan kurangi kuota
                    $lowongan = LowonganMagang::where('id', $pelamar->lowongan_id)->lockForUpdate()->first();
                    if (!$lowongan) {
                        throw new \RuntimeException('Lowongan tidak ditemukan');
                    }
                    if ($lowongan->kuota <= 0) {
                        throw new \RuntimeException('Kuota lowongan sudah habis');
                    }

                    $pelamar->status_lamaran = 'diterima';
                    $pelamar->save();

                    $lowongan->kuota = $lowongan->kuota - 1;
                    if ($lowongan->kuota <= 0) {
                        $lowongan->status = 'tidak';
                    }
                    $lowongan->save();

                    // Buat atau aktifkan PemagangAktif untuk pelamar ini
                    $exists = PemagangAktif::where('pelamar_id', $pelamar->id)->first();
                    if (!$exists) {
                        PemagangAktif::create([
                            'pelamar_id' => $pelamar->id,
                            'perusahaan_id' => $lowongan->perusahaan_id,
                            'lowongan_id' => $lowongan->id,
                            'sekolah_id' => optional($pelamar->siswa)->sekolah_id ?? null,
                            'tanggal_mulai' => now()->toDateString(),
                            'status_magang' => 'aktif',
                        ]);
                    } else {
                        if (!$exists->tanggal_mulai) {
                            $exists->tanggal_mulai = now()->toDateString();
                        }
                        $exists->status_magang = 'aktif';
                        $exists->save();
                    }
                });
            } catch (\RuntimeException $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->getMessage()
                ], 409);
            }
        } else {
            // Tolak penawaran
            $pelamar->status_lamaran = 'ditolak';
            $pelamar->save();
        }

        return response()->json([
            'message' => 'Respon penawaran tersimpan',
            'data' => [
                'id' => $pelamar->id,
                'status_lamaran' => $pelamar->status_lamaran
            ]
        ]);
    }
}
