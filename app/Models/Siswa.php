<?php

// app/Models/Siswa.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Siswa extends Authenticatable implements JWTSubject
{
    use HasFactory;

    protected $table = 'siswa';

    protected $fillable = [
        'username',
        'email',
        'password',
        'status_verifikasi',
        'tanggal_verifikasi',
        'nama_lengkap',
        'nisn',
        'kelas',
        'jurusan',
        'tahun_ajaran',
        'tanggal_lahir',
        'jenis_kelamin',
        'alamat',
        'kontak',
        'foto_profil',
        'sekolah_id',
        'otp',
        'otp_expired_at',
    ];

    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class);
    }

    // kolom jurusan sekarang bertipe string, relasi jurusan dihapus

    public function lamaran()
    {
        return $this->hasMany(Pelamar::class);
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
