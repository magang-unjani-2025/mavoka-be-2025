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

    protected $appends = ['logo_url'];

    public function getJWTIdentifier() {
        return $this->getKey();
    }

    public function getJWTCustomClaims() {
        return [];
    }

    // Relasi ke Pelatihan (LPK memiliki banyak pelatihan)
    public function pelatihan()
    {
        return $this->hasMany(Pelatihan::class, 'lembaga_id');
    }

    public function getLogoUrlAttribute()
    {
        if (!$this->logo_lembaga) return null;
        $path = $this->logo_lembaga;
        // Absolute URL langsung dikembalikan
        if (preg_match('/^https?:\/\//i', $path)) {
            return $path;
        }
        // Backward compatibility: ganti prefix jika file baru tersedia
        if (str_starts_with($path, 'logos/lembaga/')) {
            $candidate = str_replace('logos/lembaga/', 'logos/lembaga-pelatihan/', $path);
            $oldFull = public_path($path);
            $newFull = public_path($candidate);
            if (!file_exists($oldFull) && file_exists($newFull)) {
                $path = $candidate;
            }
        }
        return asset($path);
    }
}
