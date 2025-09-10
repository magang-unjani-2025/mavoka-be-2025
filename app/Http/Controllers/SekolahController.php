<?php

namespace App\Http\Controllers;

use App\Models\Sekolah;
use App\Models\Jurusan;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

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
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required',
            'alamat' => 'required',
            'kontak' => 'required',
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

        // Set status_verifikasi dan tanggal_verifikasi jika ada
        if (!isset($data['status_verifikasi']))
            $data['status_verifikasi'] = 0;
        if (!isset($data['tanggal_verifikasi']))
            $data['tanggal_verifikasi'] = null;

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
        $rows = Excel::toArray(null, $file);
        $rows = $rows[0]; // Ambil sheet pertama

        // Ambil header dan mapping index ke nama kolom
        $header = array_map(function ($h) {
            return strtolower(str_replace([' ', '_'], '', $h));
        }, $rows[0]);

        // Mapping nama kolom ke index
        $map = [
            'username' => null,
            'email' => null,
            'password' => null,
            'namalengkap' => null,
            'nisn' => null,
            'kelas' => null,
            'namajurusan' => null,
            'tahunajaran' => null,
            'tanggallahir' => null,
            'jeniskelamin' => null,
            'alamat' => null,
            'kontak' => null,
            'statusverifikasi' => null,
            'tanggalverifikasi' => null,
        ];
        foreach ($map as $key => $v) {
            $idx = array_search($key, $header);
            if ($idx !== false)
                $map[$key] = $idx;
        }

        DB::beginTransaction();
        try {
            foreach ($rows as $i => $row) {
                // Lewati header
                if ($i === 0)
                    continue;

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
                if (isset($map['namajurusan']) && isset($row[$map['namajurusan']])) {
                    $nama_jurusan = $row[$map['namajurusan']];
                    $jurusan = Jurusan::where('nama_jurusan', $nama_jurusan)
                        ->where('sekolah_id', $sekolah_id)
                        ->first();
                    if ($jurusan) {
                        $jurusan_id = $jurusan->id;
                    }
                }

                $data = [
                    'username' => isset($map['username']) ? $row[$map['username']] ?? null : null,
                    'email' => isset($map['email']) ? $row[$map['email']] ?? null : null,
                    'password' => isset($map['password']) && isset($row[$map['password']]) ? Hash::make($row[$map['password']]) : null,
                    'nama_lengkap' => isset($map['namalengkap']) ? $row[$map['namalengkap']] ?? null : null,
                    'nisn' => isset($map['nisn']) ? $row[$map['nisn']] ?? null : null,
                    'kelas' => isset($map['kelas']) ? $row[$map['kelas']] ?? null : null,
                    'jurusan_id' => $jurusan_id,
                    'tahun_ajaran' => isset($map['tahunajaran']) ? $row[$map['tahunajaran']] ?? null : null,
                    'tanggal_lahir' => isset($map['tanggallahir']) ? $row[$map['tanggallahir']] ?? null : null,
                    'jenis_kelamin' => isset($map['jeniskelamin']) ? $row[$map['jeniskelamin']] ?? null : null,
                    'alamat' => isset($map['alamat']) ? $row[$map['alamat']] ?? null : null,
                    'kontak' => isset($map['kontak']) ? $row[$map['kontak']] ?? null : null,
                    'status_verifikasi' => $statusVerifikasi,
                    'tanggal_verifikasi' => isset($map['tanggalverifikasi']) ? $row[$map['tanggalverifikasi']] ?? null : null,
                    'sekolah_id' => $sekolah_id,
                ];

                $rowValidator = Validator::make($data, [
                    'username' => 'nullable|unique:siswa',
                    'email' => 'required|email|unique:siswa',
                    'password' => 'nullable',
                    'nama_lengkap' => 'required',
                    'nisn' => 'required|unique:siswa',
                    'kelas' => 'required',
                    'jurusan_id' => 'required|exists:jurusan,id',
                    'tahun_ajaran' => 'required',
                    'tanggal_lahir' => 'required|date',
                    'jenis_kelamin' => 'required',
                    'alamat' => 'required',
                    'kontak' => 'required',
                    'sekolah_id' => 'required|exists:sekolah,id',
                    'status_verifikasi' => 'nullable',
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
