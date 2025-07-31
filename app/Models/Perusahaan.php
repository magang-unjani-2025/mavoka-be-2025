<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Perusahaan extends Authenticatable implements JWTSubject
{
    use HasFactory;

    protected $table = 'perusahaan';

    protected $fillable = [
        'username',
        'email',
        'password',
        'status_verifikasi',
        'tanggal_verifikasi',
        'nama_perusahaan',
        'bidang_usaha',
        'deskripsi_usaha',
        'alamat',
        'kontak',
        'logo_perusahaan',
        'tanggal_verifikasi',
        'divisi_penempatan',
        'mentor',
        'otp',
        'otp_expired_at',
    ];

    protected $hidden = [
        'password',
    ];

    public function lowonganMagang()
{
    return $this->hasMany(LowonganMagang::class);
}

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
