<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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

    // Get all siswa
    public function getAll()
    {
        return response()->json(Siswa::all());
    }

    // Get siswa by id
    public function getById($id)
    {
        $siswa = Siswa::find($id);
        if (!$siswa) {
            return response()->json(['message' => 'Siswa tidak ditemukan'], 404);
        }
        return response()->json($siswa);
    }

    // Update data siswa
    public function update(Request $request, $id)
    {
        $siswa = Siswa::find($id);
        if (!$siswa) {
            return response()->json(['message' => 'Siswa tidak ditemukan'], 404);
        }
        $data = $request->only(array_keys($request->all()));
        $validator = Validator::make($data, [
            'username' => 'sometimes|required|unique:siswa,username,' . $id,
            'email' => 'sometimes|required|email|unique:siswa,email,' . $id,
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        if (isset($data['status_verifikasi'])) {
            $status = strtolower(trim($data['status_verifikasi']));
            if ($status === 'sudah' || $status === '1' || $status === 'true') {
                $data['status_verifikasi'] = 'sudah';
            } elseif ($status === 'belum' || $status === '0' || $status === 'false') {
                $data['status_verifikasi'] = 'belum';
            } else {
                $data['status_verifikasi'] = 'belum';
            }
        }
        $siswa->update($data);
        $siswa->refresh();
        return response()->json(['message' => 'Siswa berhasil diupdate', 'siswa' => $siswa]);
    }

    // Delete data siswa
    public function delete($id)
    {
        $siswa = Siswa::find($id);
        if (!$siswa) {
            return response()->json(['message' => 'Siswa tidak ditemukan'], 404);
        }
        $siswa->delete();
        return response()->json(['message' => 'Siswa berhasil dihapus']);
    }
}
