<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Siswa;
use App\Models\Sekolah;
use App\Models\Perusahaan;
use App\Models\LembagaPelatihan;
use App\Models\Jurusan;
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

        // Kirim ulang email OTP (dengan nama)
        Mail::to($user->email)->send(new OtpMail($otp, $nama));

        return response()->json(['message' => 'OTP berhasil dikirim ulang']);
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
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:sekolah',
            'email' => 'required|email|unique:sekolah',
            'password' => 'required|min:6',
            'nama_sekolah' => 'required',
            'npsn' => 'required|unique:sekolah|min:8',
            'web_sekolah' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $otp = random_int(100000, 999999);

        $sekolah = Sekolah::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'nama_sekolah' => $request->nama_sekolah,
            'web_sekolah' => $request->web_sekolah,
            'npsn' => $request->npsn,
            'kontak' => $request->kontak,
            'alamat' => $request->alamat,
            'status_verifikasi' => 'belum',
            'tanggal_verifikasi' => now(),
            'otp' => $otp,
            'otp_expired_at' => now()->addMinutes(10),
        ]);

        // $this->sendOtpEmail($request->email, $otp);
        $this->sendOtpEmail($sekolah, $otp);

        return response()->json([
            'message' => 'Registrasi sekolah berhasil. Cek email untuk OTP.',
            'data' => $sekolah
        ]);
    }

    // Register Siswa
    public function registerSiswa(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nisn' => 'required|numeric|digits:10|unique:siswa',
            'nama_sekolah' => 'required|string',
            'kelas' => 'required|integer',
            'nama_jurusan' => 'required|string',
            'tahun_ajaran' => 'required|integer',
            'email' => 'required|email|unique:siswa,email',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // 1. Cari sekolah dari nama
        $sekolah = Sekolah::where('nama_sekolah', $request->nama_sekolah)->first();
        if (!$sekolah) {
            return response()->json(['message' => 'Sekolah tidak ditemukan.'], 404);
        }

        // 2. Cari jurusan dari nama jurusan + sekolah_id
        $jurusan = Jurusan::where('sekolah_id', $sekolah->id)
            ->where('nama_jurusan', $request->nama_jurusan)
            ->first();
        if (!$jurusan) {
            return response()->json(['message' => 'Jurusan tidak ditemukan.'], 404);
        }

        // 3. Simpan siswa
        $siswa = Siswa::create([
            'nisn' => $request->nisn,
            'sekolah_id' => $sekolah->id,
            'kelas' => $request->kelas,
            'jurusan_id' => $jurusan->id,
            'tahun_ajaran' => $request->tahun_ajaran,
            'status_verifikasi' => 'sudah',
            'tanggal_verifikasi' => now(),
            'email' => $request->email,
        ]);

        return response()->json([
            'message' => 'Registrasi siswa berhasil.',
            'data' => $siswa
        ]);
    }


    // Lengkapi Data Siswa (Register Siswa)
    public function siswaLengkapiRegistrasi(Request $request)
    {
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

        // $this->sendOtpEmail($request->email, $otp);
        $this->sendOtpEmail($siswa, $otp);


        return response()->json([
            'message' => 'Data siswa berhasil diperbarui. Cek email untuk OTP.',
            'data' => $siswa
        ]);
    }



    // Register Perusahaan
    public function registerPerusahaan(Request $request)
    {
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
            'tanggal_verifikasi' => now(),
            'otp' => $otp,
            'otp_expired_at' => $otpExpiredAt,
        ]);

        // $this->sendOtpEmail($request->email, $otp);
        $this->sendOtpEmail($perusahaan, $otp);


        return response()->json([
            'message' => 'Registrasi perusahaan berhasil. Silakan cek email untuk OTP.',
            'data' => $perusahaan
        ]);
    }


    // Register Lembaga Pelatihan
    public function registerLembagaPelatihan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:lembaga_pelatihan',
            'email' => 'required|email|unique:lembaga_pelatihan',
            'password' => 'required|min:6',
            'nama_lembaga' => 'required',
            'bidang_pelatihan' => 'required',
            'web_lembaga' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $otp = random_int(100000, 999999);

        $lembaga = LembagaPelatihan::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'nama_lembaga' => $request->nama_lembaga,
            'bidang_pelatihan' => $request->bidang_pelatihan,
            'deskripsi_lembaga' => $request->deskripsi_lembaga,
            'alamat' => $request->alamat,
            'kontak' => $request->kontak,
            'status_akreditasi' => $request->status_akreditasi,
            'status_verifikasi' => 'belum',
            'tanggal_verifikasi' => now(),
            'otp' => $otp,
            'otp_expired_at' => now()->addMinutes(10),
        ]);

        $nama = $lembaga->nama_lembaga ?? 'Pengguna';

        $this->sendOtpEmail($lembaga, $otp);

        return response()->json([
            'message' => 'Registrasi lembaga pelatihan berhasil. Cek email untuk OTP.',
            'data' => $lembaga
        ]);
    }


    // Edit Akun
    public function updateAccount(Request $request, $role, $id)
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

        $authUser = auth($role)->user();
        if (!$authUser) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if ($authUser->id != $id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $user = $model::find($id);
        if (!$user) {
            return response()->json(['message' => 'Data tidak ditemukan.'], 404);
        }

        $allowedFields = match ($role) {
            'sekolah' => ['username', 'password', 'alamat', 'kontak'],
            'siswa' => ['username', 'password', 'tanggal_lahir', 'alamat', 'kontak', 'jenis_kelamin', 'foto_profil'],
            'perusahaan' => ['username', 'password', 'deskripsi_usaha', 'alamat', 'kontak', 'logo_perusahaan', 'penanggung_jawab'],
            'lpk' => ['username', 'password', 'deskripsi_lembaga', 'alamat', 'kontak', 'logo_lembaga', 'status_akreditasi', 'dokumen_akreditasi'],
        };

        $data = $request->only($allowedFields);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return response()->json(['message' => 'Akun berhasil diperbarui', 'data' => $user]);
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
            $query->with(['sekolah', 'jurusan']);
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

        // Kirim email
        $this->sendOtpEmail($user, $otp);

        return response()->json(['message' => 'OTP untuk reset password telah dikirim.']);
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
}
