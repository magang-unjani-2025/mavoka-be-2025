<?php

namespace Database\Seeders;

use App\Models\LowonganMagang;
use App\Models\Perusahaan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Helpers\ExcelSeederHelper;
use Maatwebsite\Excel\Facades\Excel;
use App\Helpers\GenericImport;

class LowonganMagangSeeder extends Seeder
{
    public function run(): void
    {
        $dir = base_path('data-dummy/lowongan-magang');
        if (!is_dir($dir)) { $this->command?->warn('Folder '.$dir.' tidak ditemukan.'); return; }
        $files = glob($dir.'/*.{xlsx,xls,csv,txt}', GLOB_BRACE);
        if (empty($files)) { $this->command?->warn('Tidak ada file Excel/CSV lowongan.'); return; }

        $columnMap = [
            'judul_lowongan' => ['judul_lowongan','judul','title'],
            'deskripsi' => ['deskripsi','deskripsi_lowongan','deskripsi_pekerjaan','description'],
            'posisi' => ['posisi','position','role'],
            'kuota' => ['kuota','quota'],
            'lokasi_penempatan' => ['lokasi_penempatan','lokasi','lokasi_kota'],
            'persyaratan' => ['persyaratan','syarat','requirements'],
            'benefit' => ['benefit','keuntungan','fasilitas'],
            'tugas_tanggung_jawab' => ['tugas_tanggung_jawab','tugas','job_desc'],
            'status' => ['status','aktif'],
            'deadline_lamaran' => ['deadline_lamaran','deadline','batas_akhir'],
            'periode_awal' => ['periode_awal','mulai','start_date'],
            'periode_akhir' => ['periode_akhir','selesai','end_date'],
            'perusahaan_ref' => ['perusahaan','nama_perusahaan','company'],
        ];

        $created=0; $updated=0; $skipped=0; $errors=[]; $now=now();

        foreach ($files as $file) {
            $this->command?->info('Proses file: '.basename($file));
            try {
                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                if (in_array($ext,['xlsx','xls'])) { $rows = ExcelSeederHelper::loadFirstSheet($file); }
                else { // CSV/TXT simple load
                    $rows = [];
                    $handle = fopen($file,'r');
                    if ($handle) { while(($data=fgetcsv($handle,0,','))!==false){ $rows[]=$data; } fclose($handle); }
                }
            } catch (\Throwable $e) { $this->command?->error('Gagal baca '.$file.': '.$e->getMessage()); continue; }
            if (empty($rows)) { $this->command?->warn('Sheet kosong: '.basename($file)); continue; }
            $headerIndex = ExcelSeederHelper::findHeaderIndex($rows);
            if ($headerIndex === null) { $this->command?->warn('Header tidak ditemukan: '.basename($file)); continue; }
            $rawHeaders = $rows[$headerIndex];
            $dataRows = array_slice($rows, $headerIndex + 1);
            $resolved = ExcelSeederHelper::mapHeaders($rawHeaders, $columnMap);

            foreach ($dataRows as $rowIndex => $row) {
                if (collect($row)->filter(fn($v)=>trim((string)$v)!=='')->isEmpty()) continue;
                $payload=[];
                foreach ($row as $i=>$val){ if(!isset($resolved[$i])) continue; $field=$resolved[$i]; $payload[$field]=is_string($val)?trim($val):$val; }

                if(isset($payload['kuota'])) $payload['kuota']=(int)preg_replace('/[^0-9]/','',(string)$payload['kuota']);
                if(isset($payload['deadline_lamaran'])) $payload['deadline_lamaran']=ExcelSeederHelper::parseDateFlexible($payload['deadline_lamaran']);
                if(isset($payload['periode_awal'])) $payload['periode_awal']=ExcelSeederHelper::parseDateFlexible($payload['periode_awal']);
                if(isset($payload['periode_akhir'])) $payload['periode_akhir']=ExcelSeederHelper::parseDateFlexible($payload['periode_akhir']);
                if(isset($payload['status'])) {
                    $st = strtolower($payload['status']);
                    $payload['status'] = in_array($st,['buka','open','aktif','ya']) ? 'buka' : (in_array($st,['tutup','close','nonaktif'])?'tutup':$st);
                }

                // Map perusahaan_ref ke perusahaan_id jika disediakan
                $perusahaanId = 1; // default fallback
                if (!empty($payload['perusahaan_ref'])) {
                    $ref = $payload['perusahaan_ref'];
                    $perusahaan = Perusahaan::where('nama_perusahaan','LIKE',"%$ref%")->first();
                    if($perusahaan) $perusahaanId = $perusahaan->id;
                }
                $payload['perusahaan_id'] = $perusahaanId;
                unset($payload['perusahaan_ref']);

                if (empty($payload['judul_lowongan'])) { $skipped++; $errors[]=['file'=>basename($file),'row'=>$headerIndex+2+$rowIndex,'messages'=>['judul_lowongan kosong']]; continue; }

                $validator = Validator::make($payload,[
                    'perusahaan_id'=>'required|integer',
                    'judul_lowongan'=>'required|string',
                    'deskripsi'=>'nullable|string',
                    'posisi'=>'nullable|string',
                    'kuota'=>'nullable|integer',
                    'lokasi_penempatan'=>'nullable|string',
                    'persyaratan'=>'nullable|string',
                    'benefit'=>'nullable|string',
                    'tugas_tanggung_jawab'=>'nullable|string',
                    'status'=>'nullable|string',
                    'deadline_lamaran'=>'nullable|date',
                    'periode_awal'=>'nullable|date',
                    'periode_akhir'=>'nullable|date',
                ]);
                if($validator->fails()) { $skipped++; $errors[]=['file'=>basename($file),'row'=>$headerIndex+2+$rowIndex,'messages'=>$validator->errors()->all()]; continue; }
                $data=$validator->validated();

                // Upsert berdasarkan (perusahaan_id + judul_lowongan)
                $existing = LowonganMagang::where('perusahaan_id',$data['perusahaan_id'])
                    ->where('judul_lowongan',$data['judul_lowongan'])
                    ->first();
                if($existing) { $existing->fill($data); $existing->updated_at=$now; $existing->save(); $updated++; }
                else { $data['created_at']=$now; $data['updated_at']=$now; LowonganMagang::create($data); $created++; }
            }
        }

        $this->command?->info("Import lowongan selesai: created=$created updated=$updated skipped=$skipped");
        if($errors){ $this->command?->warn('Contoh error:'); foreach(array_slice($errors,0,5) as $e){ $this->command?->line('- '.$e['file'].' Row '.$e['row'].': '.implode('; ',$e['messages'])); } }
    }
}
