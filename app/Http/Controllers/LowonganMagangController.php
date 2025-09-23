<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LowonganMagang;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Maatwebsite\Excel\Facades\Excel;
use App\Helpers\GenericImport;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class LowonganMagangController extends Controller
{
    // 🟢 Semua orang bisa akses
    public function listAll()
    {
        $lowongans = LowonganMagang::with('perusahaan')->get();
        $data = $lowongans->map(function($l){
            $p = $l->perusahaan;
            return [
                'id' => $l->id,
                'judul_lowongan' => $l->judul_lowongan,
                'posisi' => $l->posisi,
                'kuota' => $l->kuota,
                'lokasi_penempatan' => $l->lokasi_penempatan,
                'deadline_lamaran' => $l->deadline_lamaran,
                'status' => $l->status,
                'perusahaan' => $p ? [
                    'id' => $p->id,
                    'nama_perusahaan' => $p->nama_perusahaan,
                    'logo_perusahaan' => $p->logo_perusahaan, // original value (relative maybe)
                    'logo_url' => $p->logo_perusahaan ? asset($p->logo_perusahaan) : null,
                    'alamat' => $p->alamat,
                ] : null,
            ];
        });
        return response()->json(['data' => $data]);
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

    // 🔒 Perusahaan upload bulk via Excel/CSV
    public function uploadBulk(Request $request)
    {
        $perusahaan = JWTAuth::parseToken()->authenticate();

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,txt'
        ]);

        $sheets = Excel::toArray(new GenericImport, $request->file('file'));
        $rows = $sheets[0] ?? [];
        if (empty($rows)) {
            return response()->json(['success' => false, 'message' => 'File kosong atau tidak terbaca'], 422);
        }

        // Temukan header pertama yang tidak kosong
        $headerIndex = null;
        foreach ($rows as $i => $r) {
            if (collect($r)->filter(fn($v) => trim((string)$v) !== '')->isNotEmpty()) {
                $headerIndex = $i;
                break;
            }
        }
        if ($headerIndex === null) {
            return response()->json(['success' => false, 'message' => 'Header tidak ditemukan'], 422);
        }

        $headersRaw = $rows[$headerIndex];
        $dataRows = array_slice($rows, $headerIndex + 1);

        // Normalisasi nama kolom menjadi snake_case tanpa karakter spesial
        $normalize = function ($name) {
            $name = trim((string)$name);
            $name = preg_replace('/\xEF\xBB\xBF/', '', $name); // remove BOM
            $name = strtolower($name);
            $name = preg_replace('/[^a-z0-9]+/i', '_', $name);
            $name = trim($name, '_');
            return $name;
        };

        // Peta alias kolom -> field model (dinamis, dukung berbagai variasi header)
        $columnMap = [
            'judul_lowongan' => ['judul_lowongan','judul','judul_lowongan_magang','judul_pekerjaan','title'],
            'deskripsi' => ['deskripsi','deskripsi_lowongan','deskripsi_pekerjaan','description'],
            'posisi' => ['posisi','jabatan','role','position'],
            'kuota' => ['kuota','quota','jumlah','jumlah_kebutuhan'],
            'lokasi_penempatan' => ['lokasi_penempatan','lokasi','penempatan','kota','kota_penempatan','lokasi_kerja','location'],
            'persyaratan' => ['persyaratan','requirement','requirements','kualifikasi','syarat','qualifications'],
            'benefit' => ['benefit','manfaat','benefits','fasilitas'],
            'tugas_tanggung_jawab' => ['tugas_tanggung_jawab','tugas','tanggung_jawab','job_desc','job_description'],
            'status' => ['status','aktif_nonaktif'],
            'deadline_lamaran' => ['deadline_lamaran','deadline','batas_lamaran','batas_akhir','batas_akhir_lamaran','tgl_deadline','tanggal_deadline'],
            'periode_awal' => ['periode_awal','mulai','tanggal_mulai','start_date','tgl_mulai','awal'],
            'periode_akhir' => ['periode_akhir','akhir','tanggal_selesai','end_date','tgl_selesai','selesai'],
            // kolom gabungan periode
            'periode' => ['periode','rentang_waktu','periode_magang','durasi'],
        ];

        // Buat lookup dari header index -> field model
        $resolved = [];
        foreach ($headersRaw as $idx => $h) {
            $hNorm = $normalize($h);
            foreach ($columnMap as $field => $aliases) {
                if (in_array($hNorm, array_map($normalize, $aliases), true)) {
                    $resolved[$idx] = $field;
                    break;
                }
            }
            // Jika kolom header sudah sama dengan field
            if (!isset($resolved[$idx]) && isset($columnMap[$hNorm])) {
                $resolved[$idx] = $hNorm;
            }
        }

        $required = ['judul_lowongan','deskripsi','posisi','kuota','lokasi_penempatan','persyaratan','benefit','deadline_lamaran','periode_awal','periode_akhir'];

        $created = 0; $failed = 0; $errors = []; $inserted = [];

    foreach ($dataRows as $rowIndex => $row) {
            // Lewati baris kosong total
            if (collect($row)->filter(fn($v) => trim((string)$v) !== '')->isEmpty()) {
                continue;
            }

            $payload = [];
            foreach ($row as $i => $val) {
                if (isset($resolved[$i])) {
                    $payload[$resolved[$i]] = is_string($val) ? trim($val) : $val;
                }
            }

            // Fungsi bantu parse tanggal fleksibel (mendukung Excel serial, berbagai format, dan rentang)
            $parseDate = function ($value) {
                if ($value === null || $value === '') return null;
                if (is_numeric($value)) {
                    try { return Carbon::instance(ExcelDate::excelToDateTimeObject((float)$value))->toDateString(); }
                    catch (\Throwable $e) { /* fallback di bawah */ }
                }
                $str = trim((string)$value);
                // coba format umum
                try { return Carbon::parse($str)->toDateString(); } catch (\Throwable $e) {}
                // coba format manual
                foreach (['d/m/Y','d-m-Y','d.m.Y','Y/m/d','Y-m-d','m/d/Y','m-d-Y','d M Y','M d, Y'] as $fmt) {
                    try { return Carbon::createFromFormat($fmt, $str)->toDateString(); } catch (\Throwable $e) {}
                }
                return null;
            };

            // Normalisasi tipe sederhana
            if (isset($payload['kuota']) && $payload['kuota'] !== '') $payload['kuota'] = (int) $payload['kuota'];

            // Jika ada kolom periode gabungan, coba pecah menjadi awal/akhir (contoh: "2025-10-01 s/d 2026-01-01")
            if (!isset($payload['periode_awal']) && !isset($payload['periode_akhir']) && isset($payload['periode'])) {
                $sepPattern = '/\s*(s\/d|sd|to|sampai|-)\s*/i';
                $parts = preg_split($sepPattern, (string)$payload['periode']);
                if ($parts && count($parts) >= 2) {
                    $payload['periode_awal'] = $parts[0] ?? null;
                    $payload['periode_akhir'] = $parts[1] ?? null;
                }
            }

            // Parse fields tanggal
            foreach (['deadline_lamaran','periode_awal','periode_akhir'] as $df) {
                if (array_key_exists($df, $payload)) {
                    $payload[$df] = $parseDate($payload[$df]);
                }
            }
            // Jika hanya awal ada, set akhir = awal (fallback ringan)
            if (!empty($payload['periode_awal']) && empty($payload['periode_akhir'])) {
                $payload['periode_akhir'] = $payload['periode_awal'];
            }

            // Normalisasi status (terima berbagai nilai truthy)
            $normalizeStatus = function ($val, $kuota = null) {
                $s = strtolower(trim((string)($val ?? '')));
                $aktifTokens = ['aktif','active','ya','yes','y','1','true','open'];
                $nonaktifTokens = ['tidak','nonaktif','non-active','no','n','0','false','closed','tutup'];
                if (in_array($s, $aktifTokens, true)) return 'aktif';
                if (in_array($s, $nonaktifTokens, true)) return 'tidak';
                return ($kuota !== null && (int)$kuota <= 0) ? 'tidak' : 'aktif';
            };

            $payload['status'] = $normalizeStatus($payload['status'] ?? null, $payload['kuota'] ?? null);
            if (!isset($payload['kuota']) || $payload['kuota'] === null || $payload['kuota'] === '') {
                $payload['kuota'] = 1; // default ringan
            }

            // Validasi baris
            $validator = Validator::make($payload, [
                'judul_lowongan' => 'required|string',
                'deskripsi' => 'required|string',
                'posisi' => 'required|string',
                'kuota' => 'required|integer',
                'lokasi_penempatan' => 'required|string',
                'persyaratan' => 'sometimes|nullable|string',
                'benefit' => 'sometimes|nullable|string',
                'tugas_tanggung_jawab' => 'sometimes|nullable|string',
                'status' => 'required|in:aktif,tidak',
                'deadline_lamaran' => 'sometimes|nullable|date',
                'periode_awal' => 'sometimes|nullable|date',
                'periode_akhir' => 'sometimes|nullable|date|after_or_equal:periode_awal',
            ]);

            if ($validator->fails()) {
                $failed++;
                $errors[] = [
                    'row' => $headerIndex + 2 + $rowIndex, // baris sebenarnya di file
                    'messages' => $validator->errors()->all(),
                ];
                continue;
            }

            $data = array_merge($validator->validated(), [
                'perusahaan_id' => $perusahaan->id,
            ]);

            $lowongan = LowonganMagang::create($data);
            $inserted[] = $lowongan->id;
            $created++;
        }

        return response()->json([
            'success' => true,
            'message' => 'Proses upload selesai',
            'created' => $created,
            'failed' => $failed,
            'errors' => $errors,
            'inserted_ids' => $inserted,
        ]);
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
