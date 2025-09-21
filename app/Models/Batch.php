<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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

    // Default status for new records
    protected $attributes = [
        'status' => 'berjalan',
    ];

    protected $casts = [
        'mulai' => 'date',
        'selesai' => 'date',
    ];

    public function pelatihan()
    {
        return $this->belongsTo(Pelatihan::class, 'pelatihan_id');
    }

    /**
     * If the batch has a 'selesai' date in the past and status is not 'selesai',
     * mark it as finished and persist. Returns true if an update occurred.
     */
    public function autoCompleteIfPast(): bool
    {
        $end = $this->selesai instanceof Carbon ? $this->selesai : ($this->selesai ? Carbon::parse($this->selesai) : null);
        $status = is_string($this->status) ? strtolower($this->status) : null;

        if ($end && now()->greaterThan($end->copy()->endOfDay()) && $status !== 'selesai') {
            $this->status = 'selesai';
            // Using save() to trigger model events (e.g., history recording in booted)
            $this->save();
            return true;
        }
        // Jika tidak ada tanggal selesai, tapi tanggal mulai sudah lewat hari ini, anggap selesai
        if (!$end) {
            $start = $this->mulai instanceof Carbon ? $this->mulai : ($this->mulai ? Carbon::parse($this->mulai) : null);
            if ($start && now()->greaterThan($start->copy()->endOfDay()) && $status !== 'selesai') {
                $this->status = 'selesai';
                $this->save();
                return true;
            }
        }
        return false;
    }

    protected static function booted(): void
    {
        // Normalisasi status sebelum disimpan: hanya 'berjalan' atau 'selesai'.
        static::saving(function (Batch $batch) {
            $now = now();
            $end = $batch->selesai ? ( $batch->selesai instanceof Carbon ? $batch->selesai : Carbon::parse($batch->selesai) ) : null;
            $start = $batch->mulai ? ( $batch->mulai instanceof Carbon ? $batch->mulai : Carbon::parse($batch->mulai) ) : null;

            $shouldFinish = false;
            if ($end && $now->greaterThan($end->copy()->endOfDay())) {
                $shouldFinish = true;
            } elseif (!$end && $start && $now->greaterThan($start->copy()->endOfDay())) {
                // Jika tidak ada tanggal selesai, gunakan tanggal mulai sebagai batas
                $shouldFinish = true;
            }

            $batch->status = $shouldFinish ? 'selesai' : 'berjalan';
        });

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
