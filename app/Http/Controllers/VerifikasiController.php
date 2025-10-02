<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\Sekolah;
use App\Models\Perusahaan;
use App\Models\LembagaPelatihan;
use Illuminate\Support\Facades\Mail;
use App\Mail\AccountVerifiedMail;

class VerifikasiController extends Controller
{
    public function verifikasiAkun(Request $request, $role, $id)
    {
        $model = match ($role) {
            'sekolah' => Sekolah::class,
            'siswa' => Siswa::class,
            'perusahaan' => Perusahaan::class,
            'lpk' => LembagaPelatihan::class,
            default => null,
        };

        if (!$model) {
            return response()->json(['message' => 'Role tidak valid'], 400);
        }

        $akun = $model::find($id);

        if (!$akun) {
            return response()->json(['message' => 'Akun tidak ditemukan'], 404);
        }

        if (!$akun->tanggal_verifikasi) {
            return response()->json([
                'message' => 'Pengguna belum melakukan verifikasi OTP.'
            ], 403);
        }

        $akun->status_verifikasi = 'Terverifikasi';
        $akun->save();

        $nama = $akun->nama_perusahaan
            ?? $akun->nama_sekolah
            ?? $akun->nama_lengkap
            ?? $akun->nama_lembaga
            ?? $akun->nama
            ?? 'Siswa';

        if ($akun->email && filter_var($akun->email, FILTER_VALIDATE_EMAIL)) {
            // Kirim email konfirmasi, tapi jangan patahkan proses bila gagal (misal SMTP down)
            $emailStatus = 'sent';
            try {
                Mail::to($akun->email)->send(new AccountVerifiedMail($nama));
            } catch (\Throwable $e) {
                \Log::warning('Gagal mengirim email verifikasi akun (' . $role . '): ' . $e->getMessage());
                $emailStatus = 'failed';
            }
        } else {
            $emailStatus = 'skipped';
        }

        return response()->json([
            'message' => $emailStatus === 'sent'
                ? 'Akun berhasil diverifikasi dan email telah dikirim.'
                : ($emailStatus === 'failed'
                    ? 'Akun berhasil diverifikasi, namun pengiriman email gagal.'
                    : 'Akun berhasil diverifikasi.'),
            'email_status' => $emailStatus,
        ]);
    }

    /**
     * Return a merged list of accounts for admin verification table.
     * Normalizes fields so frontend can consume consistently.
     */
    public function index(Request $request)
    {
        // Fetch from each account table

        $sekolah = Sekolah::select([
            'id', 'username', 'email', 'web_sekolah as link', 'nama_sekolah as name', 'status_verifikasi', 'tanggal_verifikasi', 'created_at'
        ])
        ->get()->map(function ($it) {
            $status = $it->status_verifikasi;
            $label = $status
                ? (strtolower($status) === 'belum' ? 'Belum' : 'Sudah')
                : ($it->tanggal_verifikasi ? 'Sudah' : 'Belum');

            return [
                'id' => $it->id,
                'username' => $it->username ?? $it->nama_sekolah,
                'email' => $it->email,
                'link' => $it->link ?? null,
                'role' => 'Sekolah',
                'label' => $label,
                'tanggal' => $it->tanggal_verifikasi ?? $it->created_at,
            ];
        })->toArray();


        $perusahaan = Perusahaan::select([
            'id', 'username', 'email', 'web_perusahaan as link', 'nama_perusahaan as name', 'status_verifikasi', 'tanggal_verifikasi', 'created_at'
        ])
        ->get()->map(function ($it) {
            $status = $it->status_verifikasi;
            $label = $status
                ? (strtolower($status) === 'belum' ? 'Belum' : 'Sudah')
                : ($it->tanggal_verifikasi ? 'Sudah' : 'Belum');

            return [
                'id' => $it->id,
                'username' => $it->username ?? $it->nama_perusahaan,
                'email' => $it->email,
                'link' => $it->link ?? null,
                'role' => 'Perusahaan',
                'label' => $label,
                'tanggal' => $it->tanggal_verifikasi ?? $it->created_at,
            ];
        })->toArray();


        $lpk = LembagaPelatihan::select([
            'id', 'username', 'email', 'web_lembaga as link', 'nama_lembaga as name', 'status_verifikasi', 'tanggal_verifikasi', 'created_at'
        ])
        ->get()->map(function ($it) {
            $status = $it->status_verifikasi;
            $label = $status
                ? (strtolower($status) === 'belum' ? 'Belum' : 'Sudah')
                : ($it->tanggal_verifikasi ? 'Sudah' : 'Belum');

            return [
                'id' => $it->id,
                'username' => $it->username ?? $it->nama_lembaga,
                'email' => $it->email,
                'link' => $it->link ?? null,
                'role' => 'Lembaga Pelatihan',
                'label' => $label,
                'tanggal' => $it->tanggal_verifikasi ?? $it->created_at,
            ];
        })->toArray();

        // Include siswa (students) in the admin verifikasi listing as well
        $siswa = Siswa::select([
            'id', 'username', 'email', 'status_verifikasi', 'tanggal_verifikasi', 'created_at'
        ])
        ->get()->map(function ($it) {
            $status = $it->status_verifikasi;
            $label = $status
                ? (strtolower($status) === 'belum' ? 'Belum' : 'Sudah')
                : ($it->tanggal_verifikasi ? 'Sudah' : 'Belum');

            return [
                'id' => $it->id,
                'username' => $it->username ?? $it->nama ?? 'Siswa',
                'email' => $it->email,
                'link' => null,
                'role' => 'Siswa',
                'label' => $label,
                'tanggal' => $it->tanggal_verifikasi ?? $it->created_at,
            ];
        })->toArray();

    $merged = array_merge($sekolah, $perusahaan, $lpk, $siswa);

        // Sort so 'Belum' first and then by tanggal desc
        usort($merged, function ($a, $b) {
            $la = $a['label'] ?? '';
            $lb = $b['label'] ?? '';
            if ($la === $lb) {
                $ta = isset($a['tanggal']) ? strtotime((string)$a['tanggal']) : 0;
                $tb = isset($b['tanggal']) ? strtotime((string)$b['tanggal']) : 0;
                // newer first
                return $tb <=> $ta;
            }
            if ($la === 'Belum') return -1;
            if ($lb === 'Belum') return 1;
            return 0;
        });

        return response()->json(['data' => $merged]);
    }

}
