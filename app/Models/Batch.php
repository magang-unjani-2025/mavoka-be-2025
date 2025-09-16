<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    use HasFactory;

    protected $table = 'batch';

    protected $fillable = [
        'pelatihan_id',
        'nama_batch',
        'mulai',
        'selesai',
        'status',
    ];

    protected $casts = [
        'mulai' => 'date',
        'selesai' => 'date',
    ];

    public function pelatihan()
    {
        return $this->belongsTo(Pelatihan::class, 'pelatihan_id');
    }

    protected static function booted(): void
    {
        static::saved(function (Batch $batch) {
            // Jika status selesai, catat ke history_batch pelatihan terkait
            if (is_string($batch->status) && strtolower($batch->status) === 'selesai') {
                $pelatihan = $batch->pelatihan; // gunakan relasi yang sudah dimuat
                if (!$pelatihan) {
                    $pelatihan = $batch->pelatihan()->first();
                }
                if (!$pelatihan) {
                    return; // tidak ada pelatihan terkait
                }

                $history = $pelatihan->history_batch;
                if (!is_array($history)) {
                    $history = [];
                }

                // Siapkan entri ringkas batch
                $entry = [
                    'batch_id' => $batch->id,
                    'nama_batch' => $batch->nama_batch,
                    'mulai' => optional($batch->mulai)->toDateString(),
                    'selesai' => optional($batch->selesai)->toDateString(),
                    'status' => $batch->status,
                    'recorded_at' => now()->toDateTimeString(),
                ];

                // Upsert: jika sudah ada berdasarkan batch_id, update; kalau belum, tambahkan
                $updated = false;
                foreach ($history as $i => $item) {
                    if (is_array($item) && isset($item['batch_id']) && (int)$item['batch_id'] === (int)$batch->id) {
                        $history[$i] = array_merge($item, $entry);
                        $updated = true;
                        break;
                    }
                }
                if (!$updated) {
                    $history[] = $entry;
                }

                $pelatihan->history_batch = $history;
                $pelatihan->save();
            }
        });
    }
}
