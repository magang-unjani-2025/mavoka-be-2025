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
use App\Http\Controllers\PelamarController;
use App\Http\Controllers\PerusahaanController;
use App\Http\Controllers\PelatihanController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\LembagaPelatihanController;
use App\Http\Controllers\LaporanMagangController;

// =================== ROUTE USER (AUTH, REGISTER, ACCOUNT MGMT) ====================
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

    // Update akun berbagai role (PUT/PATCH/POST override)
    Route::middleware('auth:siswa,sekolah,perusahaan,lpk')
        ->match(['put','patch', 'post'],'/update-akun/{role}/{id}', [AuthController::class, 'updateAccount']);

    // List akun per role & khusus siswa via admin
    Route::get('/show-akun/{role}', [AuthController::class, 'getAllAccounts']);
    Route::middleware('auth:admin')->get('/show-akun/siswa', [AuthController::class, 'getAllAccounts']);

    // Hapus akun
    Route::delete('/delete-akun/{role}/{id}', [AuthController::class, 'deleteAccount']);

    // Forgot / reset password
    Route::post('/forgot-password/{role}', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password/{role}', [AuthController::class, 'resetPassword']);

    // Get akun by ID (semua role)
    Route::get('/{role}/{id}', [UserController::class, 'getById']);
});

// =================== ROUTE SEKOLAH (DATA & UPLOAD) ====================
Route::prefix('sekolah')->group(function () {
    Route::get('/all-sekolah', [SekolahController::class, 'getAllSekolah']);
    Route::get('/detail/{id}', [SekolahController::class, 'detail']);
    Route::get('/jurusan/{sekolah_id}', [SekolahController::class, 'getJurusanBySekolah']);
    Route::post('/create-jurusan', [JurusanController::class, 'store']);
    Route::post('/upload-siswa-single', [SekolahController::class, 'uploadSiswaSingle']);
    Route::post('/upload-siswa-bulk', [SekolahController::class, 'uploadSiswaBulk']);
    Route::get('/lamaran-siswa/{sekolah_id}', [SekolahController::class, 'getLamaranSiswaBySekolah']);
    Route::post('/{sekolah_id}/upload-logo', [SekolahController::class, 'uploadLogoSekolah']);
});

// =================== ROUTE SISWA (PROFILE & DATA) ====================
Route::prefix('siswa')->group(function () {
    Route::get('/all', [SiswaAuthController::class, 'getAll']);
    Route::get('/{id}', [SiswaAuthController::class, 'getById']);
    Route::put('/update/{id}', [SiswaAuthController::class, 'update']);
});

// =================== ROUTE VERIFIKASI (ADMIN ONLY) ====================
Route::middleware('auth:admin')->put('/verifikasi/{role}/{id}', [VerifikasiController::class, 'verifikasiAkun']);

// =================== ROUTE STATISTIK (AGGREGATION) ====================
Route::prefix('statistik')->group(function () {
    Route::get('/siswa', [StatistikController::class, 'totalSiswa']);
    Route::get('/sekolah', [StatistikController::class, 'totalSekolah']);
    Route::get('/perusahaan', [StatistikController::class, 'totalPerusahaan']);
    Route::get('/lpk', [StatistikController::class, 'totalLembagaPelatihan']);
    Route::get('/bulanan/{role}', [StatistikController::class, 'statistikBulanan']);
    Route::get('/tahunan/{role}', [StatistikController::class, 'statistikTahunan']);
});

// =================== ROUTE LOWONGAN MAGANG (PUBLIC) ====================
Route::prefix('lowongan')->group(function () {
    Route::get('/all-lowongan', [LowonganMagangController::class, 'listAll']);
    Route::get('/show-lowongan/{id}', [LowonganMagangController::class, 'detail']);
});

// =================== ROUTE PERUSAHAAN (PUBLIC DETAIL) ====================
Route::get('/perusahaan/detail/{id}', [PerusahaanController::class, 'detail']);

// =================== ROUTE LEMBAGA PELATIHAN (PUBLIC DETAIL) ====================
Route::get('/lpk/detail/{id}', [LembagaPelatihanController::class, 'detail']);

// =================== ROUTE LOWONGAN MAGANG (PERUSAHAAN AUTH) ====================
Route::prefix('lowongan')->middleware(['auth:perusahaan'])->group(function () {
    Route::get('/lowongan-perusahaan', [LowonganMagangController::class, 'index']);
    Route::post('/create-lowongan', [LowonganMagangController::class, 'store']);
    Route::post('/upload-bulk', [LowonganMagangController::class, 'uploadBulk']);
    Route::post('/update-lowongan/{id}', [LowonganMagangController::class, 'update']);
    Route::delete('/delete-lowongan/{id}', [LowonganMagangController::class, 'destroy']);
});

// ==================== ROUTE PERUSAHAAN (DAFTAR PELAMAR) ====================
Route::middleware('auth:perusahaan')->get('/perusahaan/pelamar', [PerusahaanController::class, 'listPelamar']);

// ==================== ROUTE PELAMAR (LAMARAN) ====================
Route::prefix('pelamar')->group(function () {
    Route::middleware('auth:siswa')->post('/', [PelamarController::class, 'store']);
    Route::middleware('auth:perusahaan,admin')->put('/{id}/status', [PelamarController::class, 'updateStatus']);
    Route::middleware('auth:siswa')->post('/{id}/respond-penawaran', [PelamarController::class, 'respondPenawaran']);
});

// =================== ROUTE PELATIHAN (PUBLIC) ====================
Route::prefix('pelatihan')->group(function () {
    Route::get('/all', [PelatihanController::class, 'listAll']);
    Route::get('/detail/{id}', [PelatihanController::class, 'detail']);
    // Batch public
    Route::get('/{pelatihanId}/batch', [BatchController::class, 'listByPelatihan']);
    Route::get('/batch/detail/{id}', [BatchController::class, 'detail']);
});

// =================== ROUTE PELATIHAN (LPK AUTH) ====================
Route::prefix('pelatihan')->middleware(['auth:lpk'])->group(function () {
    Route::get('/mine', [PelatihanController::class, 'index']);
    Route::post('/create', [PelatihanController::class, 'store']);
    Route::post('/upload-bulk', [PelatihanController::class, 'uploadBulk']);
    Route::put('/update/{id}', [PelatihanController::class, 'update']);
    Route::delete('/delete/{id}', [PelatihanController::class, 'destroy']);
    // Batch CRUD (LPK only)
    Route::get('/{pelatihanId}/batch', [BatchController::class, 'index']);
    Route::post('/{pelatihanId}/batch', [BatchController::class, 'store']);
    Route::post('/{pelatihanId}/batch/{id}', [BatchController::class, 'update']);
    Route::delete('/{pelatihanId}/batch/{id}', [BatchController::class, 'destroy']);
});

// =================== ROUTE MAGANG: LAPORAN & EVALUASI ====================
Route::prefix('magang')->group(function () {
    // Siswa membuat laporan harian (require auth siswa)
    Route::middleware('auth:siswa')->post('/laporan-harian', [LaporanMagangController::class, 'createLaporanHarian']);
    // Perusahaan mengevaluasi laporan harian (require auth perusahaan)
    Route::middleware('auth:perusahaan')->post('/laporan-harian/{id}/evaluasi', [LaporanMagangController::class, 'evaluasiLaporanHarian']);
    // Perusahaan input penilaian mingguan (require auth perusahaan)
    Route::middleware('auth:perusahaan')->post('/evaluasi-mingguan/{magangId}', [LaporanMagangController::class, 'createEvaluasiMingguan']);
    // Listing
    Route::get('/laporan-harian/siswa/{siswaId}', [LaporanMagangController::class, 'listLaporanSiswa']);
    Route::get('/evaluasi-mingguan/siswa/{siswaId}', [LaporanMagangController::class, 'listEvaluasiSiswa']);
    // Sekolah view evaluations & daily reports for its students
    Route::middleware('auth:sekolah')->get('/sekolah/{siswaId}/evaluasi', [LaporanMagangController::class, 'sekolahEvaluasiMagang']);
});
