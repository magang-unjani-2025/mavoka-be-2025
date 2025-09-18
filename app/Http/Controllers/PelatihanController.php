<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pelatihan;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Throwable;
use Maatwebsite\Excel\Facades\Excel;
use App\Helpers\GenericImport;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PelatihanController extends Controller
{
    private function success($data = null, string $message = null, int $code = 200)
    {
        $payload = [
            'success' => true,
        ];
        if ($message !== null) $payload['message'] = $message;
        if ($data !== null) $payload['data'] = $data;
        return response()->json($payload, $code);
    }

    private function error(Throwable $e, int $code = 500, string $customMessage = null)
    {
        $message = $customMessage ?? $e->getMessage();
        // Jangan expose pesan internal pada production
        if (!config('app.debug')) {
            if ($e instanceof ModelNotFoundException) {
                $message = 'Data tidak ditemukan';
                $code = 404;
            } elseif ($code >= 500) {
                $message = 'Terjadi kesalahan pada server';
            }
        }

        $error = [
            'success' => false,
            'message' => $message,
        ];
        if (config('app.debug')) {
            $error['exception'] = [
                'type' => class_basename($e),
                'trace' => collect($e->getTrace())->take(3),
            ];
        }
        return response()->json($error, $code);
    }

    // 🟢 Public: list all pelatihan dengan lembaga
    public function listAll()
    {
        try {
            $data = Pelatihan::with(['lembaga', 'batches'])->get();
            return $this->success($data);
        } catch (Throwable $e) {
            return $this->error($e);
        }
    }

    // 🟢 Public: detail satu pelatihan
    public function detail($id)
    {
        try {
            $pelatihan = Pelatihan::with(['lembaga', 'batches'])->findOrFail($id);
            return $this->success($pelatihan);
        } catch (ModelNotFoundException $e) {
            return $this->error($e, 404, 'Pelatihan tidak ditemukan');
        } catch (Throwable $e) {
            return $this->error($e);
        }
    }

    // 🔒 LPK: buat pelatihan
    public function store(Request $request)
    {
        try {
            $lembaga = JWTAuth::parseToken()->authenticate();

            $validated = $request->validate([
                'nama_pelatihan' => 'required|string',
                'deskripsi' => 'required|string',
                'kategori' => 'nullable|string',
                'capaian_pembelajaran' => 'nullable|string',
                'history_batch' => 'nullable|array',
            ]);

            $data = array_merge($validated, [
                'lembaga_id' => $lembaga->id,
            ]);

            $pelatihan = Pelatihan::create($data);
            $pelatihan->load(['lembaga', 'batches']);
            return $this->success($pelatihan, 'Pelatihan berhasil dibuat', 201);
        } catch (Throwable $e) {
            return $this->error($e);
        }
    }

    // 🔒 LPK: list pelatihan milik lembaga login
    public function index()
    {
        try {
            $lembaga = JWTAuth::parseToken()->authenticate();
            $pelatihan = Pelatihan::with('batches')
                ->where('lembaga_id', $lembaga->id)
                ->get();
            return $this->success($pelatihan);
        } catch (Throwable $e) {
            return $this->error($e);
        }
    }

    // 🔒 LPK: update pelatihan miliknya
    public function update(Request $request, $id)
    {
        try {
            $lembaga = JWTAuth::parseToken()->authenticate();
            $pelatihan = Pelatihan::where('lembaga_id', $lembaga->id)->findOrFail($id);

            $validated = $request->validate([
                'nama_pelatihan' => 'sometimes|string',
                'deskripsi' => 'sometimes|string',
                'kategori' => 'sometimes|nullable|string',
                'capaian_pembelajaran' => 'sometimes|nullable|string',
                'history_batch' => 'sometimes|nullable|array',
            ]);

            // Filter hanya field yang benar-benar dikirim (sometimes) dan tidak semua null
            $data = collect($validated)
                ->filter(fn($v, $k) => $request->exists($k)) // allow null assignments when explicitly present
                ->all();

            if (empty($data)) {
                return $this->error(new \Exception('Tidak ada field yang dikirim'), 422, 'Tidak ada field yang dikirim');
            }

            $pelatihan->update($data);
            $pelatihan->load(['lembaga', 'batches']);
            return $this->success($pelatihan, 'Pelatihan berhasil diperbarui');
        } catch (ModelNotFoundException $e) {
            return $this->error($e, 404, 'Pelatihan tidak ditemukan');
        } catch (Throwable $e) {
            return $this->error($e);
        }
    }

    // 🔒 LPK: hapus pelatihan miliknya
    public function destroy($id)
    {
        try {
            $lembaga = JWTAuth::parseToken()->authenticate();
            $pelatihan = Pelatihan::where('lembaga_id', $lembaga->id)->findOrFail($id);
            $pelatihan->delete();

            return $this->success(null, 'Pelatihan berhasil dihapus');
        } catch (ModelNotFoundException $e) {
            return $this->error($e, 404, 'Pelatihan tidak ditemukan');
        } catch (Throwable $e) {
            return $this->error($e);
        }
    }

    // 🔒 LPK: upload bulk via Excel/CSV
    public function uploadBulk(Request $request)
    {
        try {
            $lembaga = JWTAuth::parseToken()->authenticate();

            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls,csv,txt'
            ]);

            $sheets = Excel::toArray(new GenericImport, $request->file('file'));
            $rows = $sheets[0] ?? [];
            if (empty($rows)) {
                return $this->error(new \Exception('File kosong'), 422, 'File kosong atau tidak terbaca');
            }

            $headerIndex = null;
            foreach ($rows as $i => $r) {
                if (collect($r)->filter(fn($v) => trim((string)$v) !== '')->isNotEmpty()) { $headerIndex = $i; break; }
            }
            if ($headerIndex === null) {
                return $this->error(new \Exception('Header tidak ditemukan'), 422, 'Header tidak ditemukan');
            }

            $headersRaw = $rows[$headerIndex];
            $dataRows = array_slice($rows, $headerIndex + 1);

            $normalize = function ($name) {
                $name = trim((string)$name);
                $name = preg_replace('/\xEF\xBB\xBF/', '', $name);
                $name = strtolower($name);
                $name = preg_replace('/[^a-z0-9]+/i', '_', $name);
                return trim($name, '_');
            };

            $columnMap = [
                'nama_pelatihan' => ['nama_pelatihan','nama','judul'],
                'deskripsi' => ['deskripsi','deskripsi_pelatihan'],
                'kategori' => ['kategori','kategori_pelatihan'],
                'capaian_pembelajaran' => ['capaian_pembelajaran','capaian','outcome','learning_outcomes'],
                'history_batch' => ['history_batch','riwayat_batch'],
            ];

            $resolved = [];
            foreach ($headersRaw as $idx => $h) {
                $hNorm = $normalize($h);
                foreach ($columnMap as $field => $aliases) {
                    if (in_array($hNorm, array_map($normalize, $aliases), true)) {
                        $resolved[$idx] = $field; break;
                    }
                }
                if (!isset($resolved[$idx]) && isset($columnMap[$hNorm])) { $resolved[$idx] = $hNorm; }
            }

            $created = 0; $failed = 0; $errors = []; $inserted = [];

            foreach ($dataRows as $rowIndex => $row) {
                if (collect($row)->filter(fn($v) => trim((string)$v) !== '')->isEmpty()) continue;

                $payload = [];
                foreach ($row as $i => $val) {
                    if (isset($resolved[$i])) { $payload[$resolved[$i]] = is_string($val) ? trim($val) : $val; }
                }

                // history_batch bisa dikirim sebagai JSON atau string dipisah koma
                if (!empty($payload['history_batch'])) {
                    if (is_string($payload['history_batch'])) {
                        $json = json_decode($payload['history_batch'], true);
                        $payload['history_batch'] = json_last_error() === JSON_ERROR_NONE ? $json : array_map('trim', explode(',', $payload['history_batch']));
                    }
                }

                $validator = Validator::make($payload, [
                    'nama_pelatihan' => 'required|string',
                    'deskripsi' => 'required|string',
                    'kategori' => 'nullable|string',
                    'capaian_pembelajaran' => 'nullable|string',
                    'history_batch' => 'nullable|array',
                ]);

                if ($validator->fails()) {
                    $failed++;
                    $errors[] = [
                        'row' => $headerIndex + 2 + $rowIndex,
                        'messages' => $validator->errors()->all(),
                    ];
                    continue;
                }

                $data = array_merge($validator->validated(), [ 'lembaga_id' => $lembaga->id ]);
                $pelatihan = Pelatihan::create($data);
                $inserted[] = $pelatihan->id; $created++;
            }

            return $this->success([
                'created' => $created,
                'failed' => $failed,
                'errors' => $errors,
                'inserted_ids' => $inserted,
            ], 'Proses upload selesai');
        } catch (Throwable $e) {
            return $this->error($e);
        }
    }
}
