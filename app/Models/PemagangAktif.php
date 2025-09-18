<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemagangAktif extends Model
{
    use HasFactory;

    protected $table = 'pemagang_aktif';
    protected $primaryKey = 'magang_id';

    protected $fillable = [
        'pelamar_id',
        'perusahaan_id',
        'lowongan_id',
        'sekolah_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'status_magang',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function pelamar()
    {
        return $this->belongsTo(Pelamar::class);
    }

    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class);
    }

    public function lowongan()
    {
        return $this->belongsTo(LowonganMagang::class, 'lowongan_id');
    }
}
