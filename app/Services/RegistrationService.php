<?php

namespace App\Services;

use App\Models\Siswa;
use App\Models\Sekolah;
use App\Models\Perusahaan;
use App\Models\LembagaPelatihan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;

class RegistrationService
{
    public function registerSekolah($data)
    {
        $otp = random_int(100000, 999999);
        $data['password'] = Hash::make($data['password']);
        $data['otp'] = $otp;
        $data['otp_expired_at'] = now()->addMinutes(10);

        $sekolah = Sekolah::create($data);
        Mail::to($sekolah->email)->send(new OtpMail($otp));

        return $sekolah;
    }

    public function registerSiswa($data)
    {
        $otp = random_int(100000, 999999);
        $data['password'] = Hash::make($data['password']);
        $data['otp'] = $otp;
        $data['otp_expired_at'] = now()->addMinutes(10);

        $siswa = Siswa::create($data);
        Mail::to($siswa->email)->send(new OtpMail($otp));

        return $siswa;
    }

    public function registerPerusahaan($data)
    {
        $otp = random_int(100000, 999999);
        $data['password'] = Hash::make($data['password']);
        $data['otp'] = $otp;
        $data['otp_expired_at'] = now()->addMinutes(10);

        $perusahaan = Perusahaan::create($data);
        Mail::to($perusahaan->email)->send(new OtpMail($otp));

        return $perusahaan;
    }
    
    public function registerLembagaPelatihan($data)
    {
        $otp = random_int(100000, 999999);
        $data['password'] = Hash::make($data['password']);
        $data['otp'] = $otp;
        $data['otp_expired_at'] = now()->addMinutes(10);

        $lembaga = LembagaPelatihan::create($data);
        Mail::to($lembaga->email)->send(new OtpMail($otp));

        return $lembaga;
    }
}
