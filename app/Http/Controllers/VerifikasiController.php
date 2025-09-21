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

}
