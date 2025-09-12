<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelamar extends Model
{
    use HasFactory;

    protected $table = 'pelamar';

    protected $fillable = [
        'siswa_id',
        'lowongan_id',
        'tanggal_lamaran',
        'status_lamaran',
        'cv',
        'transkrip',
    ];
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function lowongan()
    {
        return $this->belongsTo(LowonganMagang::class);
    }
}
