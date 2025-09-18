<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanHarian extends Model
{
    use HasFactory;

    protected $table = 'laporan_harian';
    protected $primaryKey = 'laporan_harian_id';

    protected $fillable = [
        'perusahaan_id',
        'siswa_id',
        'magang_id',
        'tanggal_laporan',
        'dokumentasi_foto',
        'deskripsi',
        'output',
        'hambatan',
        'solusi',
        'evaluasi_perusahaan',
    ];

    protected $casts = [
        'tanggal_laporan' => 'date',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class);
    }
}
