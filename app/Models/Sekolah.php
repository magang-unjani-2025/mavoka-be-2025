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

    protected $casts = [
        'jurusan' => 'array', // simpan sebagai JSON array di DB
    ];

    // Otomatis ikut dalam serialisasi JSON
    protected $appends = ['logo_url'];

    public function siswa()
    {
        return $this->hasMany(Siswa::class);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Accessor logo_url: menghasilkan URL penuh atau null jika tidak ada.
     */
    public function getLogoUrlAttribute(): ?string
    {
        if (empty($this->logo_sekolah)) {
            return null;
        }
        // Jika sudah berupa URL absolut, kembalikan langsung
        if (preg_match('~^https?://~i', $this->logo_sekolah)) {
            return $this->logo_sekolah;
        }
        // Asumsikan path relatif ke public/ (karena disimpan oleh seeder)
        return asset($this->logo_sekolah);
    }
}
