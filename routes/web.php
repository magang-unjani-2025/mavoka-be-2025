<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SiswaAuthController;

Route::get('/', function () {
    return view('/emails/otp');
});

// Route::get('/siswa-register', function () {
//     return view('siswa-register');
// });

Route::get('/register-siswa', [SiswaAuthController::class, 'showRegisterForm']);
Route::post('/register-siswa', [SiswaAuthController::class, 'register']);

Route::get('/login-siswa', [SiswaAuthController::class, 'showLoginForm']);
Route::post('/login-siswa', [SiswaAuthController::class, 'login']);

Route::post('/logout-siswa', [SiswaAuthController::class, 'logout']);
