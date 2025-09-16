<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Pelatihan;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Throwable;

class BatchController extends Controller
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

    // 🟢 Public: list batch by pelatihan
    public function listByPelatihan($pelatihanId)
    {
        try {
            $batches = Batch::where('pelatihan_id', $pelatihanId)->orderByDesc('mulai')->get();
            return $this->success($batches);
        } catch (Throwable $e) {
            return $this->error($e);
        }
    }

    // 🟢 Public: detail batch
    public function detail($id)
    {
        try {
            $batch = Batch::with('pelatihan.lembaga')->findOrFail($id);
            return $this->success($batch);
        } catch (ModelNotFoundException $e) {
            return $this->error($e, 404, 'Batch tidak ditemukan');
        } catch (Throwable $e) {
            return $this->error($e);
        }
    }

    // 🔒 LPK: list batch milik pelatihan lembaga login
    public function index($pelatihanId)
    {
        try {
            $lembaga = JWTAuth::parseToken()->authenticate();
            $pelatihan = Pelatihan::where('lembaga_id', $lembaga->id)->findOrFail($pelatihanId);
            $batches = $pelatihan->batches()->orderByDesc('mulai')->get();
            return $this->success($batches);
        } catch (ModelNotFoundException $e) {
            return $this->error($e, 404, 'Pelatihan tidak ditemukan atau bukan milik Anda');
        } catch (Throwable $e) {
            return $this->error($e);
        }
    }

    // 🔒 LPK: buat batch untuk pelatihan miliknya
    public function store(Request $request, $pelatihanId)
    {
        try {
            $lembaga = JWTAuth::parseToken()->authenticate();
            $pelatihan = Pelatihan::where('lembaga_id', $lembaga->id)->findOrFail($pelatihanId);

            $validated = $request->validate([
                'nama_batch' => 'required|string',
                'mulai' => 'nullable|date',
                'selesai' => 'nullable|date|after_or_equal:mulai',
                'status' => 'nullable|string|max:50',
            ]);

            $data = array_merge($validated, [
                'pelatihan_id' => $pelatihan->id,
            ]);

            $batch = Batch::create($data);
            return $this->success($batch, 'Batch berhasil dibuat', 201);
        } catch (ModelNotFoundException $e) {
            return $this->error($e, 404, 'Pelatihan tidak ditemukan atau bukan milik Anda');
        } catch (Throwable $e) {
            return $this->error($e);
        }
    }

    // 🔒 LPK: update batch miliknya
    public function update(Request $request, $pelatihanId, $id)
    {
        try {
            $lembaga = JWTAuth::parseToken()->authenticate();
            $pelatihan = Pelatihan::where('lembaga_id', $lembaga->id)->findOrFail($pelatihanId);
            $batch = $pelatihan->batches()->findOrFail($id);

            $validated = $request->validate([
                'nama_batch' => 'sometimes|string',
                'mulai' => 'sometimes|nullable|date',
                'selesai' => 'sometimes|nullable|date|after_or_equal:mulai',
                'status' => 'sometimes|nullable|string|max:50',
            ]);

            $data = collect($validated)
                ->filter(fn($v, $k) => $request->exists($k))
                ->all();

            if (empty($data)) {
                return $this->error(new \Exception('Tidak ada field yang dikirim'), 422, 'Tidak ada field yang dikirim');
            }

            $batch->update($data);
            return $this->success($batch, 'Batch berhasil diperbarui');
        } catch (ModelNotFoundException $e) {
            return $this->error($e, 404, 'Batch/Pelatihan tidak ditemukan atau bukan milik Anda');
        } catch (Throwable $e) {
            return $this->error($e);
        }
    }

    // 🔒 LPK: hapus batch miliknya
    public function destroy($pelatihanId, $id)
    {
        try {
            $lembaga = JWTAuth::parseToken()->authenticate();
            $pelatihan = Pelatihan::where('lembaga_id', $lembaga->id)->findOrFail($pelatihanId);
            $batch = $pelatihan->batches()->findOrFail($id);
            $batch->delete();

            return $this->success(null, 'Batch berhasil dihapus');
        } catch (ModelNotFoundException $e) {
            return $this->error($e, 404, 'Batch/Pelatihan tidak ditemukan atau bukan milik Anda');
        } catch (Throwable $e) {
            return $this->error($e);
        }
    }
}
