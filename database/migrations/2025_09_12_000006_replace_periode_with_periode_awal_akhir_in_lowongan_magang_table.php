<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('lowongan_magang', function (Blueprint $table) {
            $table->date('periode_awal')->nullable()->after('deadline_lamaran');
            $table->date('periode_akhir')->nullable()->after('periode_awal');
        });

        // Migrasi data dari kolom periode (jika ada) format bebas "dd-mm-YYYY sampai dd-mm-YYYY"
        if (Schema::hasColumn('lowongan_magang','periode')) {
            $rows = DB::table('lowongan_magang')->select('id','periode')->whereNotNull('periode')->get();
            foreach ($rows as $row) {
                $awal = null; $akhir = null;
                $p = trim($row->periode);
                if ($p !== '') {
                    // Pisah dengan kata 'sampai'
                    $parts = preg_split('/\s+sampai\s+/i', $p);
                    if (count($parts) === 2) {
                        $awal = self::parseDateFlexible($parts[0]);
                        $akhir = self::parseDateFlexible($parts[1]);
                    }
                }
                DB::table('lowongan_magang')->where('id',$row->id)->update([
                    'periode_awal' => $awal,
                    'periode_akhir' => $akhir,
                ]);
            }

            Schema::table('lowongan_magang', function (Blueprint $table) {
                $table->dropColumn('periode');
            });
        }
    }

    public function down(): void
    {
        Schema::table('lowongan_magang', function (Blueprint $table) {
            $table->string('periode')->nullable()->after('deadline_lamaran');
        });

        // Rekonstruksi kolom periode dari periode_awal & periode_akhir
        $rows = DB::table('lowongan_magang')->select('id','periode_awal','periode_akhir')->get();
        foreach ($rows as $row) {
            $formatted = null;
            if ($row->periode_awal && $row->periode_akhir) {
                $formatted = date('d-m-Y', strtotime($row->periode_awal)).' sampai '.date('d-m-Y', strtotime($row->periode_akhir));
            } elseif ($row->periode_awal) {
                $formatted = date('d-m-Y', strtotime($row->periode_awal));
            }
            DB::table('lowongan_magang')->where('id',$row->id)->update(['periode'=>$formatted]);
        }

        Schema::table('lowongan_magang', function (Blueprint $table) {
            $table->dropColumn(['periode_awal','periode_akhir']);
        });
    }

    private static function parseDateFlexible(?string $val): ?string
    {
        if (!$val) return null;
        $val = trim($val);
        if ($val === '') return null;
        // Ganti '/' '.' ke '-'
        $v = str_replace(['/', '.'], '-', $val);
        // Format umum: dd-mm-yyyy atau yyyy-mm-dd
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $v)) {
            return $v; // sudah yyyy-mm-dd
        }
        if (preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $v, $m)) {
            return $m[3].'-'.$m[2].'-'.$m[1];
        }
        // Coba strtotime fallback
        $ts = strtotime($v);
        if ($ts !== false) {
            return date('Y-m-d', $ts);
        }
        return null;
    }
};
