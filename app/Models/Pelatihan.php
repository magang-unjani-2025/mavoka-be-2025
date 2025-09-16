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
        'capaian_pembelajaran',
        'detail',
        'history_batch',
    ];

    protected $casts = [
        'history_batch' => 'array',
    ];

    public function lembaga()
    {
        return $this->belongsTo(LembagaPelatihan::class, 'lembaga_id');
    }

    public function batches()
    {
        return $this->hasMany(Batch::class, 'pelatihan_id');
    }
}
