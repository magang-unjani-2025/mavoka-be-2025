<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LowonganMagang extends Model
{
    use HasFactory;

    public const STATUS_AKTIF = 'aktif';
    public const STATUS_TIDAK = 'tidak';
    public const STATUS_DRAFT = 'draft';
    public const STATUSES = [
        self::STATUS_AKTIF,
        self::STATUS_TIDAK,
        self::STATUS_DRAFT,
    ];

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
        'tugas_tanggung_jawab',
        'status',
        'deadline_lamaran',
        'periode_awal',
        'periode_akhir',
    ];

    protected $casts = [
        'tugas_tanggung_jawab' => 'array',
    ];

    /**
     * Mutator: simpan array tugas_tanggung_jawab sebagai JSON rapi (tanpa escape slash) atau null jika kosong.
     */
    public function setTugasTanggungJawabAttribute($value): void
    {
        if (is_array($value)) {
            $clean = array_values(array_filter($value, function($v){
                return is_string($v) ? trim($v) !== '' : !is_null($v);
            }));
            $this->attributes['tugas_tanggung_jawab'] = $clean ? json_encode($clean, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null;
        } elseif (is_string($value) && $value !== '') {
            $this->attributes['tugas_tanggung_jawab'] = $value; // diasumsikan sudah JSON valid
        } else {
            $this->attributes['tugas_tanggung_jawab'] = null;
        }
    }

    /**
     * Normalisasi status agar hanya salah satu dari: aktif | tidak | draft
     */
    public function setStatusAttribute($value): void
    {
        $v = strtolower((string)$value);
        if (!in_array($v, self::STATUSES, true)) {
            // Mapping nilai lama
            $v = match($v) {
                'buka' => self::STATUS_AKTIF,
                'tutup' => self::STATUS_TIDAK,
                default => self::STATUS_DRAFT,
            };
        }
        $this->attributes['status'] = $v;
    }

    /**
     * Scope hanya status aktif.
     */
    public function scopeAktif($query)
    {
        return $query->where('status', self::STATUS_AKTIF);
    }

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
