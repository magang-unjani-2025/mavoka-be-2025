<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluasiMagangMingguan extends Model
{
    use HasFactory;

    protected $table = 'evaluasi_magang_mingguan';
    protected $primaryKey = 'evaluasi_id';

    protected $fillable = [
        'perusahaan_id',
        'siswa_id',
        'magang_id',
        'aspek_teknis',
        'aspek_komunikasi',
        'aspek_kerjasama',
        'aspek_disiplin',
        'aspek_inisiatif',
        'nilai_aspek_teknis',
        'nilai_aspek_komunikasi',
        'nilai_aspek_kerjasama',
        'nilai_aspek_disiplin',
        'nilai_aspek_inisiatif',
        'nilai_rata_rata',
        'upload_at',
    ];

    protected $casts = [
        'upload_at' => 'datetime',
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
