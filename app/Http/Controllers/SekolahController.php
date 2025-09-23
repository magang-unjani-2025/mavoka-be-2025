<?php

namespace App\Http\Controllers;

use App\Models\Sekolah;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Helpers\GenericImport;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class SekolahController extends Controller
{
    public function getAllSekolah()
    {
        return response()->json(Sekolah::all());
    }

    // 🟢 Public: detail sekolah (tanpa tabel jurusan; jurusan per siswa kini string)
    public function detail($id)
    {
        $sekolah = Sekolah::find($id);

        if (!$sekolah) {
            return response()->json(['message' => 'Sekolah tidak ditemukan'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $sekolah->id,
                'nama_sekolah' => $sekolah->nama_sekolah,
                'web_sekolah' => $sekolah->web_sekolah,
                'email' => $sekolah->email,
                'npsn' => $sekolah->npsn,
                'logo_sekolah' => $sekolah->logo_sekolah,
                'logo_url' => $sekolah->logo_url,
                'kontak' => $sekolah->kontak,
                'alamat' => $sekolah->alamat,
                'jurusan' => is_array($sekolah->jurusan) ? $sekolah->jurusan : (empty($sekolah->jurusan) ? [] : [$sekolah->jurusan]),
            ]
        ]);
    }

    // Baru: ambil daftar jurusan (array) langsung dari kolom json sekolah
    public function getJurusanBySekolah($sekolah_id)
    {
        $sekolah = Sekolah::find($sekolah_id);
        if (!$sekolah) {
            return response()->json(['message' => 'Sekolah tidak ditemukan'], 404);
        }
        $jurusan = $sekolah->jurusan;
        if (!is_array($jurusan)) {
            $jurusan = empty($jurusan) ? [] : [$jurusan];
        }
        return response()->json([
            'sekolah_id' => $sekolah->id,
            'nama_sekolah' => $sekolah->nama_sekolah,
            'jurusan' => $jurusan,
            'count' => count($jurusan),
        ]);
    }

    // getJurusanBySekolah dihapus, karena tabel jurusan ditiadakan.

    // Upload logo sekolah
    public function uploadLogoSekolah(Request $request, $sekolah_id)
    {
        $sekolah = Sekolah::find($sekolah_id);
        if (!$sekolah) {
            return response()->json(['message' => 'Sekolah tidak ditemukan'], 404);
        }

        $validator = Validator::make(array_merge($request->all(), [
            'logo' => $request->file('logo')
        ]), [
            'logo' => 'required|file|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'logo.required' => 'Logo wajib diupload.',
            'logo.image' => 'File harus berupa gambar.',
            'logo.mimes' => 'Format logo harus JPEG, PNG, JPG, atau GIF.',
            'logo.max' => 'Ukuran logo maksimal 2MB.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $path = $request->file('logo')->store('sekolah/logo', 'public');
        $sekolah->logo_sekolah = $path;
        $sekolah->save();

        return response()->json([
            'message' => 'Logo sekolah berhasil diupload',
            'logo_url' => asset('storage/' . $path)
        ], 200);
    }

    // Endpoint: Melihat status lamaran siswa berdasarkan sekolah
    public function getLamaranSiswaBySekolah(Request $request, $sekolah_id)
    {
        $status = $request->query('status'); // lamar|wawancara|penawaran|ditolak|diterima|belum
        $allowedStatus = ['lamar','wawancara','penawaran','ditolak','diterima','belum'];
        if ($status && !in_array($status, $allowedStatus, true)) {
            return response()->json(['message' => 'Status filter tidak valid'], 422);
        }

    $query = Siswa::where('sekolah_id', $sekolah_id)->with(['lamaran.lowongan']);

        // Jika filter 'belum', ambil siswa tanpa lamaran
        if ($status === 'belum') {
            $query->whereDoesntHave('lamaran');
        } elseif ($status) {
            // Filter siswa yang punya minimal satu lamaran dengan status tertentu
            $query->whereHas('lamaran', function($q) use ($status) {
                $q->where('status_lamaran', $status);
            });
        }

        $siswa = $query->get();

        $result = $siswa->map(function ($s) use ($status) {
            $lamaran = $s->lamaran;
            if ($status && $status !== 'belum') {
                // Filter lamaran di representasi jika status spesifik diminta
                $lamaran = $lamaran->where('status_lamaran', $status)->values();
            }
            return [
                'id' => $s->id,
                'nama_lengkap' => $s->nama_lengkap,
                'nisn' => $s->nisn,
                'kelas' => $s->kelas,
                'jurusan' => $s->jurusan,
                'jumlah_lamaran' => $s->lamaran->count(),
                'lamaran' => $lamaran->map(function ($l) {
                    return [
                        'lowongan_id' => $l->lowongan_id,
                        'nama_lowongan' => $l->lowongan ? $l->lowongan->judul_lowongan : null,
                        'status_lamaran' => $l->status_lamaran,
                        'tanggal_lamaran' => $l->tanggal_lamaran,
                    ];
                })
            ];
        });

        return response()->json([
            'filter_status' => $status,
            'total_siswa' => $result->count(),
            'data' => $result
        ]);
    }

    // Upload satu siswa
    public function uploadSiswaSingle(Request $request)
    {
        // Ambil sekolah dari token (guard: sekolah)
        $sekolah = auth('sekolah')->user();
        if (!$sekolah) {
            return response()->json(['message' => 'Unauthorized: token sekolah tidak valid atau tidak ada'], 401);
        }

        $validator = Validator::make($request->all(), [
            'username' => 'nullable|unique:siswa',
            'email' => 'required|email|unique:siswa',
            'password' => 'nullable|min:6',
            'nama_lengkap' => 'required',
            'nisn' => 'required|unique:siswa',
            'kelas' => 'required',
            'nama_jurusan' => 'required|string',
            'tahun_ajaran' => 'required|string',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable',
            'alamat' => 'nullable',
            'kontak' => 'nullable',
            'status_verifikasi' => 'nullable|in:sudah,belum',
            'tanggal_verifikasi' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->all();
        if (isset($data['password']) && $data['password']) {
            $data['password'] = Hash::make($data['password']);
        } else {
            $data['password'] = null;
        }

        // Perubahan skema: kolom jurusan sekarang string pada tabel siswa.
        // Mapping jurusan_id dihapus; gunakan field 'jurusan' langsung.
        // Simpan nama jurusan ke kolom string; terima nama_jurusan atau jurusan
        if (isset($data['nama_jurusan']) && trim((string)$data['nama_jurusan']) !== '') {
            $data['jurusan'] = trim((string)$data['nama_jurusan']);
        } elseif (isset($data['jurusan'])) {
            $data['jurusan'] = trim((string)$data['jurusan']);
        }
        // $data['jurusan_id'] dihapus - gunakan $data['jurusan'] bila tersedia.
        unset($data['nama_jurusan']);

        // Set sekolah_id dari token
        $data['sekolah_id'] = $sekolah->id;

        // Normalisasi kelas ke integer bila mungkin
        if (isset($data['kelas']) && $data['kelas'] !== null && $data['kelas'] !== '') {
            if (is_numeric($data['kelas'])) {
                $data['kelas'] = (int) $data['kelas'];
            }
        }

        // Set default status_verifikasi dan tanggal_verifikasi
        if (!isset($data['status_verifikasi']) || $data['status_verifikasi'] === null || $data['status_verifikasi'] === '') {
            $data['status_verifikasi'] = 'belum';
        }
        if (!isset($data['tanggal_verifikasi'])) {
            $data['tanggal_verifikasi'] = null;
        }

        $siswa = Siswa::create($data);
        return response()->json(['message' => 'Siswa berhasil ditambahkan', 'siswa' => $siswa], 201);
    }

    // Upload banyak siswa via file Excel/CSV
    public function uploadSiswaBulk(Request $request)
    {
        // Ambil sekolah dari token (guard: sekolah)
        $sekolah = auth('sekolah')->user();
        if (!$sekolah) {
            return response()->json(['message' => 'Unauthorized: token sekolah tidak valid atau tidak ada'], 401);
        }

        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:xlsx,csv',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $file = $request->file('file');
        $sekolah_id = $sekolah->id;
        $imported = [];
        $failed = [];

        // Read file using Laravel Excel
        $rows = Excel::toArray(new GenericImport, $file);
        $rows = $rows[0]; // Ambil sheet pertama

        // Normalizer untuk header
        $normalize = function ($v) {
            return strtolower(str_replace([' ', '_'], '', trim((string) $v)));
        };

        // Temukan baris header yang benar (file bisa punya judul di baris atas)
        $headerRowIndex = null;
        $maxScan = min(count($rows), 50);
        for ($i = 0; $i < $maxScan; $i++) {
            $norm = array_map($normalize, $rows[$i] ?? []);
            if (in_array('email', $norm, true) && (in_array('namalengkap', $norm, true) || in_array('nisn', $norm, true))) {
                $headerRowIndex = $i;
                break;
            }
        }

        if ($headerRowIndex === null) {
            return response()->json([
                'message' => 'Header tidak ditemukan. Pastikan ada baris header dengan kolom: Email, Nama Lengkap, NISN, Kelas, Jurusan, Tahun Ajaran, Tanggal Lahir, Jenis Kelamin, Alamat, Kontak.'
            ], 422);
        }

        // Ambil header ter-normalisasi dari baris header yang ditemukan
        $header = array_map($normalize, $rows[$headerRowIndex]);

        // Helper mencari index kolom berdasarkan kandidat alias
        $findIndex = function (array $candidates) use ($header) {
            foreach ($candidates as $c) {
                $idx = array_search($c, $header, true);
                if ($idx !== false) return $idx;
            }
            return null;
        };

        // Mapping nama kolom ke index (dengan alias seperlunya)
        $map = [
            'username' => $findIndex(['username']),
            'email' => $findIndex(['email']),
            'password' => $findIndex(['password', 'katasandi']),
            'namalengkap' => $findIndex(['namalengkap', 'nama', 'namasiswa']),
            'nisn' => $findIndex(['nisn']),
            'kelas' => $findIndex(['kelas']),
            'namajurusan' => $findIndex(['namajurusan', 'jurusan']),
            'tahunajaran' => $findIndex(['tahunajaran', 'tahunakademik']),
            'tanggallahir' => $findIndex(['tanggallahir', 'tgllahir', 'tanggalahir', 'tanggallahir']),
            'jeniskelamin' => $findIndex(['jeniskelamin', 'kelamin', 'gender']),
            'alamat' => $findIndex(['alamat', 'alamatdomisili']),
            'kontak' => $findIndex(['kontak', 'telepon', 'nohp', 'nohandphone', 'telp', 'notelp', 'nohpwa']),
            'statusverifikasi' => $findIndex(['statusverifikasi', 'statusverif']),
            'tanggalverifikasi' => $findIndex(['tanggalverifikasi']),
        ];

        DB::beginTransaction();
        try {
            foreach ($rows as $i => $row) {
                // Lewati baris sebelum/sama dengan header dan baris kosong
                if ($i <= $headerRowIndex) continue;
                $isEmptyRow = count(array_filter($row, function ($v) { return !is_null($v) && trim((string)$v) !== ''; })) === 0;
                if ($isEmptyRow) continue;

                $statusVerifikasi = isset($map['statusverifikasi']) ? $row[$map['statusverifikasi']] ?? null : null;
                // Terima 'sudah' dan 'belum' dari file, default 'belum' jika kosong/invalid
                if ($statusVerifikasi === null || $statusVerifikasi === '') {
                    $statusVerifikasi = 'belum';
                } elseif (strtolower(trim($statusVerifikasi)) === 'sudah') {
                    $statusVerifikasi = 'sudah';
                } elseif (strtolower(trim($statusVerifikasi)) === 'belum') {
                    $statusVerifikasi = 'belum';
                } else {
                    $statusVerifikasi = 'belum';
                }

                // Perubahan skema: tidak lagi menggunakan jurusan_id. Jurusan disimpan sebagai string.

                $data = [
                    'username' => !is_null($map['username']) ? ($row[$map['username']] ?? null) : null,
                    'email' => !is_null($map['email']) ? ($row[$map['email']] ?? null) : null,
                    'password' => (!is_null($map['password']) && isset($row[$map['password']]) && trim((string)$row[$map['password']]) !== '') ? Hash::make($row[$map['password']]) : null,
                    'nama_lengkap' => !is_null($map['namalengkap']) ? ($row[$map['namalengkap']] ?? null) : null,
                    'nisn' => !is_null($map['nisn']) ? ($row[$map['nisn']] ?? null) : null,
                    'kelas' => !is_null($map['kelas']) ? ($row[$map['kelas']] ?? null) : null,
                    // jurusan sebagai string langsung dari file
                    'jurusan' => !is_null($map['namajurusan']) ? (isset($row[$map['namajurusan']]) ? trim((string)$row[$map['namajurusan']]) : null) : null,
                    'tahun_ajaran' => !is_null($map['tahunajaran']) ? (isset($row[$map['tahunajaran']]) ? trim((string)$row[$map['tahunajaran']]) : null) : null,
                    // Handle kemungkinan tanggal Excel numeric -> konversi ke Y-m-d
                    'tanggal_lahir' => !is_null($map['tanggallahir']) ? (function($val){
                        if (is_null($val) || trim((string)$val) === '') return null;
                        if (is_numeric($val)) {
                            try { return \Carbon\Carbon::instance(ExcelDate::excelToDateTimeObject((float)$val))->format('Y-m-d'); } catch (\Throwable $e) { return $val; }
                        }
                        // Normalisasi beberapa format umum dd/mm/yyyy
                        $v = trim((string)$val);
                        $v = str_replace(['\\', '.'], ['/', '/'], $v);
                        return $v;
                    })($row[$map['tanggallahir']] ?? null) : null,
                    'jenis_kelamin' => !is_null($map['jeniskelamin']) ? ($row[$map['jeniskelamin']] ?? null) : null,
                    'alamat' => !is_null($map['alamat']) ? ($row[$map['alamat']] ?? null) : null,
                    'kontak' => !is_null($map['kontak']) ? ($row[$map['kontak']] ?? null) : null,
                    'status_verifikasi' => $statusVerifikasi,
                    'tanggal_verifikasi' => !is_null($map['tanggalverifikasi']) ? ($row[$map['tanggalverifikasi']] ?? null) : null,
                    'sekolah_id' => $sekolah_id,
                ];

                // Tidak normalisasi: kosong = gagal, selain itu disimpan apa adanya
                if (!is_null($data['tahun_ajaran'])) {
                    $taRaw = trim((string)$data['tahun_ajaran']);
                    if ($taRaw === '') {
                        $data['tahun_ajaran'] = null; // memicu gagal validasi
                    } else {
                        $data['tahun_ajaran'] = $taRaw;
                    }
                }

                $rowValidator = Validator::make($data, [
                    'username' => 'nullable|unique:siswa',
                    'email' => 'required|email|unique:siswa',
                    'password' => 'nullable',
                    'nama_lengkap' => 'required',
                    'nisn' => 'required|unique:siswa',
                    'kelas' => 'required',
                    'jurusan' => 'required|string|max:100',
                    'tahun_ajaran' => 'required|string',
                    'tanggal_lahir' => 'nullable|date',
                    'jenis_kelamin' => 'nullable',
                    'alamat' => 'nullable',
                    'kontak' => 'nullable',
                    'status_verifikasi' => 'nullable|in:sudah,belum',
                    'tanggal_verifikasi' => 'nullable|date',
                ]);

                if ($rowValidator->fails()) {
                    $failed[] = [
                        'row' => $i + 1,
                        'errors' => $rowValidator->errors()->all(),
                    ];
                    continue;
                }

                $imported[] = Siswa::create($data);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal import data', 'error' => $e->getMessage()], 500);
        }

        return response()->json([
            'message' => 'Import selesai',
            'berhasil' => count($imported),
            'gagal' => $failed,
        ]);
    }

}
