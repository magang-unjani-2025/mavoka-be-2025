<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class SiswaAuthController extends Controller
{
    public function showRegisterForm()
    {
        return view('auth.siswa-register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:siswa',
            'email' => 'required|email|unique:siswa',
            'password' => 'required|min:6|confirmed',
            'nama_lengkap' => 'required',
        ]);

        Siswa::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'nama_lengkap' => $request->nama_lengkap,
            'status_verifikasi' => 'belum', // default
            'nisn' => '0000000000',
            'kelas' => 0,
            'jurusan' => '-',
            'tahun_ajaran' => 0,
            'tanggal_lahir' => now(),
            'jenis_kelamin' => '-',
            'alamat' => '-',
            'kontak' => '-',
            'status_siswa' => 'aktif',
        ]);

        return redirect('/login-siswa')->with('success', 'Pendaftaran berhasil!');
    }

    public function showLoginForm()
    {
        return view('auth.siswa-login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $siswa = Siswa::where('username', $request->username)->first();

        if ($siswa && Hash::check($request->password, $siswa->password)) {
            session(['siswa_id' => $siswa->id]);
            return redirect('/dashboard-siswa'); // arahkan ke halaman siswa
        }

        return back()->withErrors(['username' => 'Username atau password salah.']);
    }

    public function logout(Request $request)
    {
        $request->session()->forget('siswa_id');
        return redirect('/login-siswa');
    }
}
