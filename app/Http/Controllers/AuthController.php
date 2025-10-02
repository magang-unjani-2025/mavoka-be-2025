<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Siswa;
use App\Models\Sekolah;
use App\Models\Perusahaan;
use App\Models\LembagaPelatihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;


class AuthController extends Controller
{
    private function sendOtpEmail($user, $otp)
    {
        $nama = $user->nama_lengkap ?? $user->nama_sekolah ?? $user->nama_lembaga ?? $user->nama_perusahaan ?? $user->nama ?? 'Siswa';
        Mail::to($user->email)->send(new OtpMail($otp, $nama));
    }


    // Verifikasi OTP
    public function verifyOTP(Request $request, $role)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required'
        ]);

        $model = match ($role) {
            'siswa' => Siswa::class,
            'sekolah' => Sekolah::class,
            'perusahaan' => Perusahaan::class,
            'lpk' => LembagaPelatihan::class,
            default => null,
        };

        if (!$model) {
            return response()->json(['message' => 'Role tidak valid.'], 400);
        }

        $user = $model::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'Email tidak ditemukan.'], 404);
        }

        if ($user->otp !== $request->otp) {
            return response()->json(['message' => 'Kode OTP salah.'], 400);
        }

        if (now()->greaterThan($user->otp_expired_at)) {
            return response()->json(['message' => 'OTP sudah kadaluarsa.'], 400);
        }

        $user->tanggal_verifikasi = now();
        $user->otp = null;
        $user->otp_expired_at = null;
        $user->save();

        // Ambil nama sesuai field yang tersedia di masing-masing role
        $nama = $user->nama_perusahaan ?? $user->nama_sekolah ?? $user->nama_lembaga ?? 'Pengguna';

        return response()->json([
            'message' => 'Verifikasi berhasil.',
            'nama' => $nama,
        ]);
    }


    //Kirim ulang OTP
    public function resendOtp(Request $request, $role)
    {
        // Debugging: log incoming request details to compare frontend vs Postman
        \Log::info('OTP_DEBUG_incoming_request', [
            'route' => 'resendOtp',
            'role' => $role,
            'headers' => $request->headers->all(),
            'body' => $request->all(),
            'ip' => $request->ip(),
        ]);

        // Validasi input email
        $request->validate([
            'email' => 'required|email',
        ]);

        $model = match ($role) {
            'siswa' => Siswa::class,
            'sekolah' => Sekolah::class,
            'perusahaan' => Perusahaan::class,
            'lpk' => LembagaPelatihan::class,
            default => null,
        };

        if (!$model) {
            return response()->json(['message' => 'Role tidak valid.'], 400);
        }

        $user = $model::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'Email tidak ditemukan'], 404);
        }

        // Generate OTP baru
        $otp = random_int(100000, 999999);
        $user->otp = $otp;
        $user->otp_expired_at = now()->addMinutes(10); // Untuk testing
        $user->save();

        // Ambil nama dari field yang relevan
        $nama = $user->nama_lengkap ?? $user->nama_sekolah ?? $user->nama ?? 'Pengguna';

        // Kirim ulang email OTP (dengan nama) tanpa mematahkan proses saat gagal
        $emailStatus = 'sent';
        try {
            \Log::info('OTP_DEBUG_attempt_send', ['route' => 'resendOtp', 'role' => $role, 'to' => $user->email]);
            Mail::to($user->email)->send(new OtpMail($otp, $nama));
            \Log::info('OTP_DEBUG_sent_ok', ['route' => 'resendOtp', 'role' => $role, 'to' => $user->email]);
        } catch (\Throwable $e) {
            \Log::warning('Gagal mengirim email OTP (resend ' . $role . '): ' . $e->getMessage());
            \Log::warning('OTP_DEBUG_send_failed', ['route' => 'resendOtp', 'role' => $role, 'to' => $user->email, 'error' => $e->getMessage()]);
            $emailStatus = 'failed';
        }

        return response()->json([
            'message' => 'OTP baru berhasil dibuat' . ($emailStatus === 'sent' ? ' dan dikirim ke email.' : ', namun pengiriman email gagal.'),
            'email_status' => $emailStatus,
            'expires_at' => $user->otp_expired_at,
        ]);
    }
    // public function resendOtp(Request $request, $role)
    // {
    //     $model = match ($role) {
    //         'siswa' => Siswa::class,
    //         'sekolah' => Sekolah::class,
    //         'perusahaan' => Perusahaan::class,
    //         'lpk' => LembagaPelatihan::class,
    //         default => null,
    //     };
    //     $user = $model::where('email', $request->email)->first();

    //     if (!$user) {
    //         return response()->json(['message' => 'Email tidak ditemukan'], 404);
    //     }

    //     // Generate OTP baru
    //     $otp = random_int(100000, 999999);
    //     $user->otp = $otp;
    //     $user->otp_expired_at = now()->addMinutes(10); // Untuk testing
    //     $user->save();

    //     // Kirim ulang email OTP
    //     Mail::to($user->email)->send(new OtpMail($otp));

    //     return response()->json(['message' => 'OTP berhasil dikirim ulang']);
    // }



    // Register Sekolah
    public function registerSekolah(Request $request)
    {
        \Log::info('OTP_DEBUG_incoming_request', [
            'route' => 'registerSekolah',
            'headers' => $request->headers->all(),
            'body' => $request->all(),
            'ip' => $request->ip(),
        ]);

        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:sekolah',
            'email' => 'required|email|unique:sekolah',
            'password' => 'required|min:6',
            'nama_sekolah' => 'required',
            'npsn' => 'required|unique:sekolah|min:8',
            'web_sekolah' => 'required',
            'logo_sekolah' => 'sometimes|file|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $otp = random_int(100000, 999999);

        // Simpan logo jika dikirim
        $logoPath = null;
        if ($request->hasFile('logo_sekolah')) {
            $logoPath = $request->file('logo_sekolah')->store('sekolah/logo', 'public');
        }

        $sekolah = Sekolah::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'nama_sekolah' => $request->nama_sekolah,
            'web_sekolah' => $request->web_sekolah,
            'logo_sekolah' => $logoPath,
            'npsn' => $request->npsn,
            'kontak' => $request->kontak,
            'alamat' => $request->alamat,
            'status_verifikasi' => 'belum',
            'tanggal_verifikasi' => null,
            'otp' => $otp,
            'otp_expired_at' => now()->addMinutes(10),
        ]);

        // Kirim OTP via email (jangan patahkan proses jika gagal kirim)
        $emailStatus = 'sent';
        try {
            \Log::info('OTP_DEBUG_attempt_send', ['route' => 'registerSekolah', 'to' => $sekolah->email]);
            $this->sendOtpEmail($sekolah, $otp);
            \Log::info('OTP_DEBUG_sent_ok', ['route' => 'registerSekolah', 'to' => $sekolah->email]);
        } catch (\Throwable $e) {
            \Log::warning('Gagal mengirim email OTP (sekolah): ' . $e->getMessage());
            \Log::warning('OTP_DEBUG_send_failed', ['route' => 'registerSekolah', 'to' => $sekolah->email, 'error' => $e->getMessage()]);
            $emailStatus = 'failed';
        }

        return response()->json([
            'message' => 'Registrasi sekolah berhasil. Cek email untuk OTP.',
            'data' => $sekolah,
            'logo_url' => $sekolah->logo_sekolah ? asset('storage/' . $sekolah->logo_sekolah) : null,
            'email_status' => $emailStatus,
            'expires_at' => $sekolah->otp_expired_at,
        ]);
    }

    // Register Siswa
    public function registerSiswa(Request $request)
    {
        \Log::info('OTP_DEBUG_incoming_request', [
            'route' => 'registerSiswa',
            'headers' => $request->headers->all(),
            'body' => $request->all(),
            'ip' => $request->ip(),
        ]);

        // Support two flows:
        // - minimal registration (nisn + nama_sekolah + email) for schools that collect other data later
        // - full registration (including username/password/kelas/nama_jurusan/tahun_ajaran)
        $rules = [
            'nisn' => 'required|numeric|digits:10|unique:siswa',
            'nama_sekolah' => 'required|string',
            'email' => 'required|email|unique:siswa,email',
            // optional fields
            'kelas' => 'sometimes|nullable|integer',
            'nama_jurusan' => 'sometimes|nullable|string',
            'tahun_ajaran' => 'sometimes|nullable|integer',
            'username' => 'sometimes|nullable|unique:siswa,username',
            'password' => 'sometimes|nullable|min:6',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // 1. Cari sekolah dari nama
        $sekolah = Sekolah::where('nama_sekolah', $request->nama_sekolah)->first();
        if (!$sekolah) {
            return response()->json(['message' => 'Sekolah tidak ditemukan.'], 404);
        }

        // Build payload for creation; accept optional username/password and other fields
        $otp = random_int(100000, 999999);
        $otpExpiredAt = now()->addMinutes(10);

        $payload = [
            'nisn' => $request->nisn,
            'sekolah_id' => $sekolah->id,
            'kelas' => $request->filled('kelas') ? $request->kelas : null,
            'jurusan' => $request->filled('nama_jurusan') ? $request->nama_jurusan : null,
            'tahun_ajaran' => $request->filled('tahun_ajaran') ? $request->tahun_ajaran : null,
            'status_verifikasi' => 'belum',
            'tanggal_verifikasi' => null,
            'email' => $request->email,
            'otp' => $otp,
            'otp_expired_at' => $otpExpiredAt,
        ];

        if ($request->filled('username')) {
            $payload['username'] = $request->username;
        }
        if ($request->filled('password')) {
            $payload['password'] = Hash::make($request->password);
        }

        $siswa = Siswa::create($payload);

        // Send OTP email (don't fail the request if email sending fails)
        $emailStatus = 'sent';
        try {
            \Log::info('OTP_DEBUG_attempt_send', ['route' => 'registerSiswa', 'to' => $siswa->email]);
            $this->sendOtpEmail($siswa, $otp);
            \Log::info('OTP_DEBUG_sent_ok', ['route' => 'registerSiswa', 'to' => $siswa->email]);
        } catch (\Throwable $e) {
            \Log::warning('Gagal mengirim email OTP (registerSiswa): ' . $e->getMessage());
            \Log::warning('OTP_DEBUG_send_failed', ['route' => 'registerSiswa', 'to' => $siswa->email, 'error' => $e->getMessage()]);
            $emailStatus = 'failed';
        }

        return response()->json([
            'message' => 'Registrasi siswa berhasil. Cek email untuk OTP.',
            'data' => $siswa,
            'email_status' => $emailStatus,
            'expires_at' => $otpExpiredAt,
        ]);
    }


    // Lengkapi Data Siswa (Register Siswa)
    public function siswaLengkapiRegistrasi(Request $request)
    {
        \Log::info('OTP_DEBUG_incoming_request', [
            'route' => 'siswaLengkapiRegistrasi',
            'headers' => $request->headers->all(),
            'body' => $request->all(),
            'ip' => $request->ip(),
        ]);

        $validator = Validator::make($request->all(), [
            'nisn' => 'required|exists:siswa,nisn',
            'nama_sekolah' => 'required|string',
            'username' => 'required|unique:siswa,username',
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Ambil sekolah berdasarkan nama_sekolah
        $sekolah = Sekolah::where('nama_sekolah', $request->nama_sekolah)->first();

        if (!$sekolah) {
            return response()->json(['message' => 'Sekolah tidak ditemukan.'], 404);
        }

        $otp = random_int(100000, 999999);

        $siswa = Siswa::where('nisn', $request->nisn)->first();
        $siswa = Siswa::where('email', $request->email)->first();

        $siswa->update([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'sekolah_id' => $sekolah->id, // update sekolah_id
            'status_verifikasi' => 'sudah',
            'tanggal_verifikasi' => null,
            'otp' => $otp,
            'otp_expired_at' => now()->addMinutes(10),
        ]);

        // Kirim OTP, tapi jangan patahkan proses bila gagal
        $emailStatus = 'sent';
        try {
            \Log::info('OTP_DEBUG_attempt_send', ['route' => 'siswaLengkapiRegistrasi', 'to' => $siswa->email]);
            $this->sendOtpEmail($siswa, $otp);
            \Log::info('OTP_DEBUG_sent_ok', ['route' => 'siswaLengkapiRegistrasi', 'to' => $siswa->email]);
        } catch (\Throwable $e) {
            \Log::warning('Gagal mengirim email OTP (siswaLengkapiRegistrasi): ' . $e->getMessage());
            \Log::warning('OTP_DEBUG_send_failed', ['route' => 'siswaLengkapiRegistrasi', 'to' => $siswa->email, 'error' => $e->getMessage()]);
            $emailStatus = 'failed';
        }


        return response()->json([
            'message' => 'Data siswa berhasil diperbarui. Cek email untuk OTP.',
            'data' => $siswa,
            'email_status' => $emailStatus,
        ]);
    }



    // Register Perusahaan
    public function registerPerusahaan(Request $request)
    {
        \Log::info('OTP_DEBUG_incoming_request', [
            'route' => 'registerPerusahaan',
            'headers' => $request->headers->all(),
            'body' => $request->all(),
            'ip' => $request->ip(),
        ]);

        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:perusahaan',
            'email' => 'required|email|unique:perusahaan',
            'password' => 'required|min:6',
            'nama_perusahaan' => 'required',
            'bidang_usaha' => 'required',
            'web_perusahaan' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $otp = random_int(100000, 999999);
        $otpExpiredAt = now()->addMinutes(10);

        $perusahaan = Perusahaan::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'nama_perusahaan' => $request->nama_perusahaan,
            'bidang_usaha' => $request->bidang_usaha,
            'web_perusahaan' => $request->web_perusahaan,
            'deskripsi_usaha' => $request->deskripsi_usaha,
            'alamat' => $request->alamat,
            'kontak' => $request->kontak,
            'divisi_penempatan' => $request->divisi_penempatan,
            'penanggung_jawab' => $request->penanggung_jawab ?? null,
            'logo_perusahaan' => $request->logo_perusahaan ?? null,
            'status_verifikasi' => 'belum',
            'tanggal_verifikasi' => null,
            'otp' => $otp,
            'otp_expired_at' => $otpExpiredAt,
        ]);

        // Kirim OTP, tapi bila gagal jangan patahkan proses
        $emailStatus = 'sent';
        try {
            \Log::info('OTP_DEBUG_attempt_send', ['route' => 'registerPerusahaan', 'to' => $perusahaan->email]);
            $this->sendOtpEmail($perusahaan, $otp);
            \Log::info('OTP_DEBUG_sent_ok', ['route' => 'registerPerusahaan', 'to' => $perusahaan->email]);
        } catch (\Throwable $e) {
            \Log::warning('Gagal mengirim email OTP (perusahaan): ' . $e->getMessage());
            \Log::warning('OTP_DEBUG_send_failed', ['route' => 'registerPerusahaan', 'to' => $perusahaan->email, 'error' => $e->getMessage()]);
            $emailStatus = 'failed';
        }


        return response()->json([
            'message' => 'Registrasi perusahaan berhasil. Silakan cek email untuk OTP.',
            'data' => $perusahaan,
            'email_status' => $emailStatus,
            'expires_at' => $otpExpiredAt,
        ]);
    }


    // Register Lembaga Pelatihan
    public function registerLembagaPelatihan(Request $request)
    {
        \Log::info('OTP_DEBUG_incoming_request', [
            'route' => 'registerLembagaPelatihan',
            'headers' => $request->headers->all(),
            'body' => $request->all(),
            'ip' => $request->ip(),
        ]);

        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:lembaga_pelatihan',
            'email' => 'required|email|unique:lembaga_pelatihan',
            'password' => 'required|min:6',
            'nama_lembaga' => 'required',
            'bidang_pelatihan' => 'required',
            'web_lembaga' => 'required',
            // Optional fields that user can fill in
            'deskripsi_lembaga' => 'sometimes|string|nullable',
            'alamat' => 'sometimes|string|nullable',
            'kontak' => 'sometimes|string|nullable',
            'status_akreditasi' => 'sometimes|string|nullable',
            // File uploads
            'logo_lembaga' => 'sometimes|file|image|mimes:jpeg,png,jpg,gif|max:2048',
            'dokumen_akreditasi' => 'sometimes|file|mimes:pdf,jpeg,png,jpg,gif|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $otp = random_int(100000, 999999);

        // Handle file uploads if provided
        $logoPath = null;
        if ($request->hasFile('logo_lembaga')) {
            $logoPath = $request->file('logo_lembaga')->store('lpk/logo', 'public');
        }
        $dokumenAkreditasiPath = null;
        if ($request->hasFile('dokumen_akreditasi')) {
            $dokumenAkreditasiPath = $request->file('dokumen_akreditasi')->store('lpk/dokumen', 'public');
        }

        $lembaga = LembagaPelatihan::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'nama_lembaga' => $request->nama_lembaga,
            'bidang_pelatihan' => $request->bidang_pelatihan,
            'deskripsi_lembaga' => $request->deskripsi_lembaga,
            'web_lembaga' => $request->web_lembaga,
            'alamat' => $request->alamat,
            'kontak' => $request->kontak,
            'status_akreditasi' => $request->status_akreditasi,
            'logo_lembaga' => $logoPath,
            'dokumen_akreditasi' => $dokumenAkreditasiPath,
            'status_verifikasi' => 'belum',
            'tanggal_verifikasi' => null,
            'otp' => $otp,
            'otp_expired_at' => now()->addMinutes(10),
        ]);

        $nama = $lembaga->nama_lembaga ?? 'Pengguna';

        // Kirim OTP, tapi bila gagal jangan patahkan proses
        $emailStatus = 'sent';
        try {
            \Log::info('OTP_DEBUG_attempt_send', ['route' => 'registerLembagaPelatihan', 'to' => $lembaga->email]);
            $this->sendOtpEmail($lembaga, $otp);
            \Log::info('OTP_DEBUG_sent_ok', ['route' => 'registerLembagaPelatihan', 'to' => $lembaga->email]);
        } catch (\Throwable $e) {
            \Log::warning('Gagal mengirim email OTP (lembaga): ' . $e->getMessage());
            \Log::warning('OTP_DEBUG_send_failed', ['route' => 'registerLembagaPelatihan', 'to' => $lembaga->email, 'error' => $e->getMessage()]);
            $emailStatus = 'failed';
        }

        return response()->json([
            'message' => 'Registrasi lembaga pelatihan berhasil. Cek email untuk OTP.',
            'data' => $lembaga,
            'email_status' => $emailStatus,
            'logo_url' => $lembaga->logo_lembaga ? asset('storage/' . $lembaga->logo_lembaga) : null,
            'dokumen_akreditasi_url' => $lembaga->dokumen_akreditasi ? asset('storage/' . $lembaga->dokumen_akreditasi) : null,
            'expires_at' => $lembaga->otp_expired_at,
        ]);
    }


    // Edit Akun
    public function updateAccount(Request $request, $role, $id)
    {
        // Validasi role -> model
        $model = match ($role) {
            'siswa' => Siswa::class,
            'sekolah' => Sekolah::class,
            'perusahaan' => Perusahaan::class,
            'lpk' => LembagaPelatihan::class,
            default => null,
        };
        if (!$model) {
            return response()->json(['message' => 'Role tidak valid.'], 400);
        }

        // DETEKSI GUARD YANG AKTIF (karena route sekarang multi guard)
        $guards = ['siswa', 'sekolah', 'perusahaan', 'lpk'];
        $activeGuard = null;
        $authUser = null;
        foreach ($guards as $g) {
            if (auth($g)->check()) {
                $activeGuard = $g;
                $authUser = auth($g)->user();
                break;
            }
        }
        if (!$authUser) {
            return response()->json(['message' => 'Unauthenticated (token tidak dikenali).'], 401);
        }

        // Pastikan token guard cocok dengan role path
        if ($activeGuard !== $role) {
            return response()->json([
                'message' => 'Token tidak sesuai dengan role path.',
                'detail' => [
                    'expected_role' => $role,
                    'token_role' => $activeGuard,
                ]
            ], 403);
        }

        if ($authUser->id != $id) {
            return response()->json(['message' => 'Akses ditolak (id tidak cocok).'], 403);
        }

        $user = $model::find($id);
        if (!$user) {
            return response()->json(['message' => 'Data tidak ditemukan.'], 404);
        }

        // Field yang boleh diupdate per role
        $allowedFields = match ($role) {
            // extend sekolah allowed fields so frontend names can be persisted
            'sekolah' => ['username', 'password', 'alamat', 'kontak', 'logo_sekolah', 'nama_sekolah', 'npsn', 'email', 'web_sekolah'],
            'siswa' => ['username', 'password', 'tanggal_lahir', 'alamat', 'kontak', 'jenis_kelamin', 'foto_profil'],
            'perusahaan' => ['username', 'password', 'deskripsi_usaha', 'alamat', 'kontak', 'logo_perusahaan', 'penanggung_jawab', 'tanda_tangan'],
            'lpk' => ['username', 'password', 'deskripsi_lembaga', 'alamat', 'kontak', 'logo_lembaga', 'status_akreditasi', 'dokumen_akreditasi'],
        };

        // Validasi minimal (opsional): username unik jika dikirim
        $rules = [];
        if ($request->filled('username')) {
            $rules['username'] = 'unique:' . $user->getTable() . ',username,' . $user->id;
        }
        if ($request->has('password')) {
            // Boleh kosong? Jika user tidak ingin ubah password kirimkan field kosong akan diabaikan nanti
            if (trim($request->password) !== '') {
                $rules['password'] = 'min:6';
            }
        }
        if (!empty($rules)) {
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json(['message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
            }
        }

        // Ambil payload (prioritas: parsed request -> json() -> raw body decode)
        $incoming = $request->all(); // inisialisasi awal
        if (empty($incoming)) {
            $jsonPayload = $request->json()->all();
            if (!empty($jsonPayload)) {
                $incoming = $jsonPayload;
            } else {
                // Coba manual decode raw body
                $raw = $request->getContent();
                if ($raw) {
                    $decoded = json_decode($raw, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $incoming = $decoded;
                    }
                }
            }
        }

        // Normalize alternative frontend field names into backend column names
        // So we accept payloads like { phone, address, website, profile_pic, profilePic }
        $aliases = [
            'phone' => 'kontak',
            'telp' => 'kontak',
            'kontak' => 'kontak',
            'address' => 'alamat',
            'alamat' => 'alamat',
            'website' => 'web_sekolah',
            'web' => 'web_sekolah',
            'web_sekolah' => 'web_sekolah',
            'profile_pic' => 'logo_sekolah',
            'profilePic' => 'logo_sekolah',
            'foto' => 'logo_sekolah',
            'nama_sekolah' => 'nama_sekolah',
            'npsn' => 'npsn',
            'email' => 'email',
        ];

        // Apply aliases only for sekolah role — avoid unexpected mapping for other roles
        if ($role === 'sekolah') {
            foreach ($aliases as $from => $to) {
                if (array_key_exists($from, $incoming) && !array_key_exists($to, $incoming)) {
                    $incoming[$to] = $incoming[$from];
                }
            }
        }

        // Tentukan field yang benar-benar dikirim: gunakan array_key_exists agar nilai null tetap dianggap hadir
        $presentData = [];
        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $incoming)) {
                $presentData[$field] = $incoming[$field];
            }
        }

        // Hindari menimpa file field dengan blob:/data: string (frontend preview) bila tidak ada upload file baru
        foreach (['tanda_tangan','logo_perusahaan','logo_sekolah','logo_lembaga','foto_profil'] as $ff) {
            if (array_key_exists($ff, $presentData) && is_string($presentData[$ff])) {
                $val = trim($presentData[$ff]);
                if (str_starts_with($val, 'blob:') || str_starts_with($val, 'data:')) {
                    unset($presentData[$ff]); // jangan overwrite
                }
            }
        }

        // Normalisasi: ubah string kosong menjadi null (agar bisa benar-benar kosongkan kolom)
        foreach ($presentData as $k => $v) {
            if (is_string($v) && trim($v) === '') {
                $presentData[$k] = null;
            }
        }

        // Handle file upload jika ada
        $fileFields = [
            'logo_sekolah' => 'sekolah/logo',
            'foto_profil' => 'siswa/foto',
            'logo_perusahaan' => 'perusahaan/logo',
            'logo_lembaga' => 'lpk/logo',
            'dokumen_akreditasi' => 'lpk/dokumen',
            'tanda_tangan' => 'perusahaan/tanda_tangan',
        ];
        foreach ($fileFields as $input => $folder) {
            if ($request->hasFile($input)) {
                $path = $request->file($input)->store($folder, 'public');
                $presentData[$input] = $path;
            }
        }

        if (array_key_exists('password', $presentData)) {
            if ($presentData['password'] === null || trim((string)$presentData['password']) === '') {
                unset($presentData['password']); // jangan ubah
            } else {
                $presentData['password'] = Hash::make($presentData['password']);
            }
        }

        // Force update semua field yang dikirim (tanpa diff) karena sebelumnya ada kasus field tidak terdeteksi
        $dirtyBeforeSave = [];
        if (!empty($presentData)) {
            $user->fill($presentData);
            $dirtyBeforeSave = $user->getDirty();
            if (!empty($dirtyBeforeSave)) {
                $user->save();
            }
        }

        $user->refresh();

        // Normalisasi path absolute Windows/public/storage menjadi relative (contoh:
        // C:\Project\mavoka\mavoka-be-2025\public\storage\perusahaan\logo\file.png -> perusahaan/logo/file.png)
        foreach (['logo_sekolah','foto_profil','logo_perusahaan','logo_lembaga','dokumen_akreditasi','tanda_tangan'] as $fNorm) {
            $val = $user->{$fNorm} ?? null;
            if (is_string($val) && preg_match('/storage[\\\\\/](.+)$/i', $val, $m)) {
                // Jika path mengandung 'storage/' di tengah (absolute path), ambil bagian setelahnya
                $relative = str_replace(['\\'], '/', $m[1]);
                // Hilangkan kemungkinan awalan seperti 'public/' bila tersisa
                $relative = preg_replace('#^public/#i', '', $relative);
                $user->{$fNorm} = $relative; // set ke relative bersih
            } elseif (is_string($val)) {
                // Ubah backslash Windows umum menjadi slash
                if (str_contains($val, '\\')) {
                    $user->{$fNorm} = str_replace('\\', '/', $val);
                }
            }
        }

        // Tambah URL file jika ada (post-refresh) & kumpulkan path untuk response ringkas
        $fileMeta = [];
        foreach (['logo_sekolah','foto_profil','logo_perusahaan','logo_lembaga','dokumen_akreditasi','tanda_tangan'] as $f) {
            if ($user->{$f} ?? null) {
                $publicUrl = asset('storage/' . $user->{$f});
                $user->{$f . '_url'} = $publicUrl;
                $fileMeta[$f] = [
                    'path' => $user->{$f}, // relative path inside storage
                    'url' => $publicUrl,
                ];
            }
        }

        // Logging debug (aktifkan dengan ?debug=1)
        if ($request->query('debug') == 1) {
            \Log::info('UPDATE_ACCOUNT_DEBUG', [
                'role' => $role,
                'user_id' => $id,
                'active_guard' => $activeGuard,
                'request_all' => $request->all(),
                'raw_content' => $request->getContent(),
                'incoming_after_merge' => $incoming,
                'allowed_fields' => $allowedFields,
                'present_data' => $presentData,
                'dirty_before_save' => $dirtyBeforeSave,
            ]);
        }

        // Response ringkas khusus untuk upload tanda_tangan/logo agar FE dapat langsung mengambil path/url tanpa menggali objek nested
        $quick = [];
        foreach (['logo_perusahaan','tanda_tangan','logo_sekolah','logo_lembaga','foto_profil'] as $qf) {
            if (isset($fileMeta[$qf])) {
                $quick[$qf] = $fileMeta[$qf];
            }
        }

        return response()->json([
            'message' => empty($dirtyBeforeSave) ? 'Tidak ada perubahan data (nilai sama atau tidak ada field dikirim).' : 'Akun berhasil diperbarui',
            'role' => $role,
            'guard' => $activeGuard,
            'updated_fields' => array_keys($dirtyBeforeSave),
            'sent_fields' => array_keys($presentData),
            'files' => $quick,
            // Backward compatible: tetap kirim data user (dengan *_url ditambahkan)
            'data' => $user,
        ]);
    }


    // Tampil Semua Akun
    public function getAllAccounts($role)
    {
        $model = match ($role) {
            'siswa' => Siswa::class,
            'sekolah' => Sekolah::class,
            'perusahaan' => Perusahaan::class,
            'lpk' => LembagaPelatihan::class,
            default => null,
        };

        if (!$model) {
            return response()->json(['message' => 'Role tidak valid.'], 400);
        }

        if ($role === 'siswa' && !Auth::guard('admin')->check()) {
            return response()->json(['message' => 'Unauthorized. Admin token required.'], 401);
        }

        $query = $model::query();

        if ($role === 'siswa') {
            $query->with(['sekolah']);
        }

        return response()->json([
            'data' => $query->get()
        ]);
    }


    // Delete Akun
    public function deleteAccount(Request $request, $role, $id)
    {
        $model = match ($role) {
            'siswa' => Siswa::class,
            'sekolah' => Sekolah::class,
            'perusahaan' => Perusahaan::class,
            'lpk' => LembagaPelatihan::class,
            default => null,
        };

        if (!$model) {
            return response()->json(['message' => 'Role tidak valid.'], 400);
        }

        $user = $model::find($id);

        if (!$user) {
            return response()->json(['message' => 'Data tidak ditemukan.'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'Akun berhasil dihapus.']);
    }

    //Lupa Password
    public function forgotPassword(Request $request, $role)
    {
        \Log::info('OTP_DEBUG_incoming_request', [
            'route' => 'forgotPassword',
            'role' => $role,
            'headers' => $request->headers->all(),
            'body' => $request->all(),
            'ip' => $request->ip(),
        ]);

        $request->validate([
            'email' => 'required|email'
        ]);

        $model = match ($role) {
            'siswa' => Siswa::class,
            'sekolah' => Sekolah::class,
            'perusahaan' => Perusahaan::class,
            'lpk' => LembagaPelatihan::class,
            default => null,
        };

        if (!$model) {
            return response()->json(['message' => 'Role tidak valid.'], 400);
        }

        $user = $model::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'Email tidak ditemukan.'], 404);
        }

        // Buat OTP baru
        $otp = random_int(100000, 999999);
        $user->otp = $otp;
        $user->otp_expired_at = now()->addMinutes(10);
        $user->save();

        // Kirim email tanpa mematahkan proses saat gagal
        $emailStatus = 'sent';
        try {
            $this->sendOtpEmail($user, $otp);
        } catch (\Throwable $e) {
            \Log::warning('Gagal mengirim email OTP (forgotPassword ' . $role . '): ' . $e->getMessage());
            $emailStatus = 'failed';
        }

        return response()->json([
            'message' => $emailStatus === 'sent' ? 'OTP untuk reset password telah dikirim.' : 'OTP dibuat, namun pengiriman email gagal.',
            'email_status' => $emailStatus,
            'expires_at' => $user->otp_expired_at,
        ]);
    }

    //Ganti Password
    public function resetPassword(Request $request, $role)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        $model = match ($role) {
            'siswa' => Siswa::class,
            'sekolah' => Sekolah::class,
            'perusahaan' => Perusahaan::class,
            'lpk' => LembagaPelatihan::class,
            default => null,
        };

        if (!$model) {
            return response()->json(['message' => 'Role tidak valid.'], 400);
        }

        $user = $model::where('email', $request->email)->first();

        if (!$user || $user->otp !== $request->otp) {
            return response()->json(['message' => 'OTP salah atau email tidak ditemukan.'], 400);
        }

        if (now()->greaterThan($user->otp_expired_at)) {
            return response()->json(['message' => 'OTP sudah kadaluarsa.'], 400);
        }

        $user->password = Hash::make($request->new_password);
        $user->otp = null;
        $user->otp_expired_at = null;
        $user->save();

        return response()->json(['message' => 'Password berhasil direset.']);
    }

    // Verify current password for authenticated user
    public function verifyPassword(Request $request, $role)
    {
        $request->validate([
            'current_password' => 'required'
        ]);

        $model = match ($role) {
            'siswa' => Siswa::class,
            'sekolah' => Sekolah::class,
            'perusahaan' => Perusahaan::class,
            'lpk' => LembagaPelatihan::class,
            default => null,
        };

        if (!$model) {
            return response()->json(['message' => 'Role tidak valid.'], 400);
        }

        // Get active authenticated user via guards
        $guards = ['siswa', 'sekolah', 'perusahaan', 'lpk'];
        $authUser = null;
        foreach ($guards as $g) {
            if (auth($g)->check()) {
                $authUser = auth($g)->user();
                break;
            }
        }
        if (!$authUser) {
            return response()->json(['message' => 'Unauthenticated (token tidak dikenali).'], 401);
        }

        if (!$request->filled('current_password')) {
            return response()->json(['message' => 'Kata sandi saat ini diperlukan.'], 422);
        }

        if (!
            \Illuminate\Support\Facades\Hash::check($request->current_password, $authUser->password)
        ) {
            return response()->json(['message' => 'Kata sandi lama tidak cocok.'], 400);
        }

        return response()->json(['message' => 'Password valid.']);
    }
}
