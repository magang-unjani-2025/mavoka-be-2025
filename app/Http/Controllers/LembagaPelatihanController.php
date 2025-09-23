<?php

namespace App\Http\Controllers;

use App\Models\LembagaPelatihan;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Throwable;

class LembagaPelatihanController extends Controller
{
    private function success($data = null, string $message = null, int $code = 200)
    {
        $payload = ['success' => true];
        if ($message !== null)
            $payload['message'] = $message;
        if ($data !== null)
            $payload['data'] = $data;
        return response()->json($payload, $code);
    }

    private function error(Throwable $e, int $code = 500, string $customMessage = null)
    {
        $message = $customMessage ?? $e->getMessage();
        if (!config('app.debug')) {
            if ($e instanceof ModelNotFoundException) {
                $message = 'Lembaga Pelatihan tidak ditemukan';
                $code = 404;
            } elseif ($code >= 500) {
                $message = 'Terjadi kesalahan pada server';
            }
        }
        $error = ['success' => false, 'message' => $message];
        if (config('app.debug')) {
            $error['exception'] = [
                'type' => class_basename($e),
            ];
        }
        return response()->json($error, $code);
    }

    // 🟢 Public: detail satu LPK + daftar ringkas pelatihan
    public function detail($id)
    {
        try {
            $lembaga = LembagaPelatihan::with([
                'pelatihan' => function ($q) {
                    $q->select('id', 'lembaga_id', 'nama_pelatihan', 'kategori');
                }
            ])->findOrFail($id);

            $data = [
                'id' => $lembaga->id,
                'nama_lembaga' => $lembaga->nama_lembaga,
                'web_lembaga' => $lembaga->web_lembaga,
                'bidang_pelatihan' => $lembaga->bidang_pelatihan,
                'deskripsi_lembaga' => $lembaga->deskripsi_lembaga,
                'alamat' => $lembaga->alamat,
                'kontak' => $lembaga->kontak,
                'logo_lembaga' => $lembaga->logo_lembaga,
                'logo_url' => $lembaga->logo_lembaga ? asset($lembaga->logo_lembaga) : null,
                'status_akreditasi' => $lembaga->status_akreditasi,
                'jumlah_pelatihan' => $lembaga->pelatihan->count(),
                'pelatihan' => $lembaga->pelatihan->map(function ($p) {
                    return [
                        'id' => $p->id,
                        'nama_pelatihan' => $p->nama_pelatihan,
                        'kategori' => $p->kategori,
                    ];
                })
            ];

            return $this->success($data);
        } catch (ModelNotFoundException $e) {
            return $this->error($e, 404, 'Lembaga Pelatihan tidak ditemukan');
        } catch (Throwable $e) {
            return $this->error($e);
        }
    }

    // 🟢 Public: list semua LPK ringkas
    public function index()
    {
        try {
            $list = LembagaPelatihan::query()
                ->select('id','nama_lembaga','bidang_pelatihan','status_akreditasi','logo_lembaga')
                ->orderBy('nama_lembaga')
                ->get()
                ->map(function($l){
                    return [
                        'id' => $l->id,
                        'nama_lembaga' => $l->nama_lembaga,
                        'bidang_pelatihan' => $l->bidang_pelatihan,
                        'status_akreditasi' => $l->status_akreditasi,
                        'logo_lembaga' => $l->logo_lembaga,
                        'logo_url' => $l->logo_url,
                    ];
                });
            return $this->success($list);
        } catch (Throwable $e) {
            return $this->error($e);
        }
    }
}
