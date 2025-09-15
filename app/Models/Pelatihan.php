<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelatihan extends Model
{
    use HasFactory;

    protected $table = 'pelatihan';

    protected $fillable = [
        'lembaga_id',
        'nama_pelatihan',
        'deskripsi',
        'kategori',
    ];

    public function lembaga()
    {
        return $this->belongsTo(LembagaPelatihan::class, 'lembaga_id');
    }
}
