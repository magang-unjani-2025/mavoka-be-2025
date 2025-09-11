<?php

namespace App\Http\Controllers;

use App\Models\Sekolah;
use App\Models\Jurusan;
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

    public function getJurusanBySekolah($sekolah_id)
    {
        $jurusan = Jurusan::where('sekolah_id', $sekolah_id)->get();

        if ($jurusan->isEmpty()) {
            return response()->json(['message' => 'Tidak ada jurusan yang terdaftar untuk sekolah ini.'], 404);
        }

        return response()->json($jurusan);
    }

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
    public function getLamaranSiswaBySekolah($sekolah_id)
    {
        // Ambil semua siswa di sekolah ini beserta lamaran dan statusnya
        $siswa = Siswa::where('sekolah_id', $sekolah_id)
            ->with(['lamaran.lowongan', 'jurusan']) // relasi lamaran, lowongan, dan jurusan
            ->get();

        // Format data: siswa, lamaran, status_lamaran
        $result = $siswa->map(function ($s) {
            return [
                'id' => $s->id,
                'nama_lengkap' => $s->nama_lengkap,
                'nisn' => $s->nisn,
                'kelas' => $s->kelas,
                'jurusan' => $s->jurusan ? $s->jurusan->nama_jurusan : null,
                'lamaran' => $s->lamaran->map(function ($l) {
                    return [
                        'lowongan_id' => $l->lowongan_id,
                        'nama_lowongan' => $l->lowongan ? $l->lowongan->judul_lowongan : null,
                        'status_lamaran' => $l->status_lamaran,
                        'tanggal_lamaran' => $l->tanggal_lamaran,
                    ];
                })
            ];
        });

        return response()->json(['data' => $result]);
    }

    // Upload satu siswa
    public function uploadSiswaSingle(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'nullable|unique:siswa',
            'email' => 'required|email|unique:siswa',
            'password' => 'nullable|min:6',
            'nama_lengkap' => 'required',
            'nisn' => 'required|unique:siswa',
            'kelas' => 'required',
            'nama_jurusan' => 'required|string',
            'tahun_ajaran' => 'required',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable',
            'alamat' => 'nullable',
            'kontak' => 'nullable',
            'sekolah_id' => 'required|exists:sekolah,id',
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

        // Cari jurusan_id berdasarkan nama_jurusan dan sekolah_id
        $jurusan_id = null;
        if (isset($data['nama_jurusan']) && isset($data['sekolah_id'])) {
            $jurusan = Jurusan::where('nama_jurusan', $data['nama_jurusan'])
                ->where('sekolah_id', $data['sekolah_id'])
                ->first();
            if ($jurusan) {
                $jurusan_id = $jurusan->id;
            }
        }
        $data['jurusan_id'] = $jurusan_id;
        unset($data['nama_jurusan']);

        // Normalisasi kelas ke integer bila mungkin
        if (isset($data['kelas']) && $data['kelas'] !== null && $data['kelas'] !== '') {
            if (is_numeric($data['kelas'])) {
                $data['kelas'] = (int) $data['kelas'];
            }
        }

        // Normalisasi tahun_ajaran: ambil tahun pertama dari format seperti "2024/2025"
        if (isset($data['tahun_ajaran']) && $data['tahun_ajaran'] !== null && trim((string)$data['tahun_ajaran']) !== '') {
            $taRaw = trim((string) $data['tahun_ajaran']);
            if (preg_match('/(19\d{2}|20\d{2})/', $taRaw, $m)) {
                $data['tahun_ajaran'] = (int) $m[1];
            } elseif (is_numeric($taRaw)) {
                $data['tahun_ajaran'] = (int) $taRaw;
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
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:xlsx,csv',
            'sekolah_id' => 'required|exists:sekolah,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $file = $request->file('file');
        $sekolah_id = $request->input('sekolah_id');
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

                // Cari jurusan_id berdasarkan nama_jurusan dan sekolah_id
                $jurusan_id = null;
                if (!is_null($map['namajurusan']) && isset($row[$map['namajurusan']])) {
                    $nama_jurusan = $row[$map['namajurusan']];
                    $jurusan = Jurusan::where('nama_jurusan', $nama_jurusan)
                        ->where('sekolah_id', $sekolah_id)
                        ->first();
                    if ($jurusan) {
                        $jurusan_id = $jurusan->id;
                    }
                }

                $data = [
                    'username' => !is_null($map['username']) ? ($row[$map['username']] ?? null) : null,
                    'email' => !is_null($map['email']) ? ($row[$map['email']] ?? null) : null,
                    'password' => (!is_null($map['password']) && isset($row[$map['password']]) && trim((string)$row[$map['password']]) !== '') ? Hash::make($row[$map['password']]) : null,
                    'nama_lengkap' => !is_null($map['namalengkap']) ? ($row[$map['namalengkap']] ?? null) : null,
                    'nisn' => !is_null($map['nisn']) ? ($row[$map['nisn']] ?? null) : null,
                    'kelas' => !is_null($map['kelas']) ? ($row[$map['kelas']] ?? null) : null,
                    'jurusan_id' => $jurusan_id,
                    'tahun_ajaran' => !is_null($map['tahunajaran']) ? ($row[$map['tahunajaran']] ?? null) : null,
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

                // Normalisasi tahun_ajaran: ambil tahun pertama dari format seperti "2024/2025"
                if (!is_null($data['tahun_ajaran'])) {
                    $taRaw = trim((string) $data['tahun_ajaran']);
                    if ($taRaw !== '') {
                        if (preg_match('/(19\\d{2}|20\\d{2})/', $taRaw, $m)) {
                            $data['tahun_ajaran'] = (int) $m[1];
                        } elseif (is_numeric($taRaw)) {
                            $data['tahun_ajaran'] = (int) $taRaw;
                        } else {
                            $data['tahun_ajaran'] = null; // biar divalidasi gagal dan masuk daftar failed
                        }
                    }
                }

                $rowValidator = Validator::make($data, [
                    'username' => 'nullable|unique:siswa',
                    'email' => 'required|email|unique:siswa',
                    'password' => 'nullable',
                    'nama_lengkap' => 'required',
                    'nisn' => 'required|unique:siswa',
                    'kelas' => 'required',
                    'jurusan_id' => 'required|exists:jurusan,id',
                    'tahun_ajaran' => 'required|integer',
                    'tanggal_lahir' => 'nullable|date',
                    'jenis_kelamin' => 'nullable',
                    'alamat' => 'nullable',
                    'kontak' => 'nullable',
                    'sekolah_id' => 'required|exists:sekolah,id',
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
