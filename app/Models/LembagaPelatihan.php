<?php

// app/Models/LembagaPelatihan.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;

class LembagaPelatihan extends Authenticatable implements JWTSubject
{
    use HasFactory;

    protected $table = 'lembaga_pelatihan';

    protected $fillable = [
        'username',
        'email',
        'password',
        'status_verifikasi',
        'tanggal_verifikasi',
        'nama_lembaga',
        'web_lembaga',
        'bidang_pelatihan',
        'deskripsi_lembaga',
        'alamat',
        'kontak',
        'logo_lembaga',
        'status_akreditasi',
        'dokumen_akreditasi',
        'otp',
        'otp_expired_at',
    ];

    public function getJWTIdentifier() {
        return $this->getKey();
    }

    public function getJWTCustomClaims() {
        return [];
    }
}
