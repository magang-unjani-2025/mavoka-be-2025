<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pelatihan;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Throwable;

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
                'detail' => 'nullable|string',
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
                'detail' => 'sometimes|nullable|string',
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
}
