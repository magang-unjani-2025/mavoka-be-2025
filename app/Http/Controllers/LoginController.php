<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Admin;
use App\Models\Siswa;
use App\Models\Sekolah;
use App\Models\Perusahaan;
use App\Models\LembagaPelatihan;

class LoginController extends Controller
{
    public function login(Request $request, $role)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // Pilih model sesuai role
        $model = match ($role) {
            'admin' => Admin::class,
            'siswa' => Siswa::class,
            'sekolah' => Sekolah::class,
            'perusahaan' => Perusahaan::class,
            'lpk' => LembagaPelatihan::class,
            default => null,
        };

        if (!$model) {
            return response()->json(['message' => 'Role tidak valid.'], 400);
        }

        $user = $model::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Username atau password salah.'], 401);
        }

        // If the model has a verification status field, require it to be 'Terverifikasi'
        if (isset($user->status_verifikasi) && $user->status_verifikasi !== 'Terverifikasi') {
            return response()->json([
                'message' => 'Akun belum diverifikasi.',
                'status_verifikasi' => $user->status_verifikasi,
            ], 403);
        }

        // Use the requested guard to attempt login and create a token
        $token = Auth::guard($role)->attempt([
            'username' => $request->username,
            'password' => $request->password,
        ]);

        if (!$token) {
            return response()->json(['message' => 'Gagal login.'], 401);
        }

        return response()->json([
            'message' => 'Login berhasil.',
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function logout(Request $request)
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return response()->json(['message' => 'Logout berhasil.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal logout.'], 500);
        }
    }
}
