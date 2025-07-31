<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jurusan extends Model
{
    use HasFactory;

    protected $table = 'jurusan';

    protected $fillable = [
        'sekolah_id',
        'nama_jurusan',
    ];

    // relasi ke sekolah
    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class);
    }
}
