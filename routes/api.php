<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\VerifikasiController;
use App\Http\Controllers\SekolahController;
use App\Http\Controllers\JurusanController;
use App\Http\Controllers\StatistikController;
use App\Http\Controllers\LowonganMagangController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\SiswaAuthController;

Route::prefix('user')->group(function () {
    // Auth & register
    Route::post('/login/{role}', [LoginController::class, 'login']);
    Route::post('/logout', [LoginController::class, 'logout']);

    Route::post('/register/siswa', [AuthController::class, 'registerSiswa']);
    Route::post('/siswa/lengkapi-registrasi', [AuthController::class, 'siswaLengkapiRegistrasi']);
    Route::post('/register/sekolah', [AuthController::class, 'registerSekolah']);
    Route::post('/register/perusahaan', [AuthController::class, 'registerPerusahaan']);
    Route::post('/register/lpk', [AuthController::class, 'registerLembagaPelatihan']);
    Route::post('/verify-otp/{role}', [AuthController::class, 'verifyOTP']);
    Route::post('/resend-otp/{role}', [AuthController::class, 'resendOtp']);

    // Update akun (middleware sesuai role)
    Route::middleware('auth:siswa')->put('/update-akun/{role}/{id}', [AuthController::class, 'updateAccount']);
    Route::middleware('auth:sekolah')->put('/update-akun/{role}/{id}', [AuthController::class, 'updateAccount']);
    Route::middleware('auth:perusahaan')->put('/update-akun/{role}/{id}', [AuthController::class, 'updateAccount']);
    Route::middleware('auth:lpk')->put('/update-akun/{role}/{id}', [AuthController::class, 'updateAccount']);

    // Get all akun
    Route::get('/show-akun/{role}', [AuthController::class, 'getAllAccounts']);
    Route::middleware('auth:admin')->get('/show-akun/siswa', [AuthController::class, 'getAllAccounts']);

    // Delete akun
    Route::delete('/delete-akun/{role}/{id}', [AuthController::class, 'deleteAccount']);

    // Forgot / reset password
    Route::post('/forgot-password/{role}', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password/{role}', [AuthController::class, 'resetPassword']);

    // *Get akun by ID (semua role)*
    Route::get('/{role}/{id}', [UserController::class, 'getById']);
});

Route::prefix('sekolah')->group(function () {
    Route::get('/all-sekolah', [SekolahController::class, 'getAllSekolah']);
    Route::get('/jurusan/{sekolah_id}', [SekolahController::class, 'getJurusanBySekolah']);
    Route::post('/create-jurusan', [JurusanController::class, 'store']);
    Route::post('/upload-siswa-single', [SekolahController::class, 'uploadSiswaSingle']);
    Route::post('/upload-siswa-bulk', [SekolahController::class, 'uploadSiswaBulk']);
});

Route::prefix('siswa')->group(function () {
    Route::get('/all', [SiswaAuthController::class, 'getAll']);
    Route::get('/{id}', [SiswaAuthController::class, 'getById']);
    Route::put('/update/{id}', [SiswaAuthController::class, 'update']);
    Route::delete('/delete/{id}', [SiswaAuthController::class, 'delete']);
});

Route::middleware('auth:admin')->put('/verifikasi/{role}/{id}', [VerifikasiController::class, 'verifikasiAkun']);

Route::prefix('statistik')->group(function () {

    Route::get('/siswa', [StatistikController::class, 'totalSiswa']);
    Route::get('/sekolah', [StatistikController::class, 'totalSekolah']);
    Route::get('/perusahaan', [StatistikController::class, 'totalPerusahaan']);
    Route::get('/lpk', [StatistikController::class, 'totalLembagaPelatihan']);

    Route::get('/bulanan/{role}', [StatistikController::class, 'statistikBulanan']);
    Route::get('/tahunan/{role}', [StatistikController::class, 'statistikTahunan']);
});

Route::prefix('lowongan')->group(function () {
    Route::get('/all-lowongan', [LowonganMagangController::class, 'listAll']);
    Route::get('/show-lowongan/{id}', [LowonganMagangController::class, 'detail']);
});

Route::prefix('lowongan')->middleware(['auth:perusahaan'])->group(function () {
    Route::get('/lowongan-perusahaan', [LowonganMagangController::class, 'index']);
    Route::post('/create-lowongan', [LowonganMagangController::class, 'store']);
    Route::put('/update-lowongan/{id}', [LowonganMagangController::class, 'update']);
    Route::delete('/delete-lowongan/{id}', [LowonganMagangController::class, 'destroy']);
});
