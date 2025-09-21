<?php

// app/Models/Sekolah.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Sekolah extends Authenticatable implements JWTSubject
{
    use HasFactory;

    protected $table = 'sekolah';

    protected $fillable = [
        'username',
        'email',
        'password',
        'status_verifikasi',
        'tanggal_verifikasi',
        'nama_sekolah',
        'web_sekolah',
        'logo_sekolah',
        'npsn',
        'jurusan',
        'kontak',
        'alamat',
        'otp',
        'otp_expired_at',
    ];

    public function siswa()
    {
        return $this->hasMany(Siswa::class);
    }

    // Relasi jurusan dihapus; kolom jurusan ada di tabel siswa sebagai string


    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
