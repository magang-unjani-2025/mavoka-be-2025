<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LowonganMagang extends Model
{
    use HasFactory;

    protected $table = 'lowongan_magang';

    protected $fillable = [
        'perusahaan_id',
        'judul_lowongan',
        'deskripsi',
        'posisi',
        'kuota',
        'lokasi_penempatan',
        'persyaratan',
        'benefit',
        'status',
        'deadline_lamaran',
        'periode_awal',
        'periode_akhir',
    ];

    // Relasi ke Perusahaan
    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class);
    }

    // Relasi ke Pelamar
    public function pelamar()
    {
        return $this->hasMany(Pelamar::class);
    }
}
