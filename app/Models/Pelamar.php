<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelamar extends Model
{
    use HasFactory;

    protected $table = 'loeongan_magang';

    // protected $fillable = [
    //     'sekolah_id',
    //     'nama_jurusan',
    // ];
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function lowongan()
    {
        return $this->belongsTo(LowonganMagang::class);
    }
}
