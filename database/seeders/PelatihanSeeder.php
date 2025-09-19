<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pelatihan;
use App\Models\LembagaPelatihan;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Helpers\ExcelSeederHelper;
use App\Helpers\DataDummyHelper;

class PelatihanSeeder extends Seeder
{
    public function run(): void
    {
        // File berada di public/data-dummy agar konsisten dengan seeder lain
        $path = DataDummyHelper::resolve('pelatihan.xlsx');
        if (!$path) {
            $this->command?->warn('File pelatihan.xlsx tidak ditemukan di folder data-dummy (public/base). Seeder Pelatihan dilewati.');
            return;
        }
        $this->command?->info('Import pelatihan dari: '.$path);

        try { $rows = ExcelSeederHelper::loadFirstSheet($path); }
        catch (\Throwable $e) { $this->command?->error('Gagal membaca Excel: '.$e->getMessage()); return; }
        if (empty($rows)) { $this->command?->warn('Sheet pelatihan kosong.'); return; }

        $headerIndex = ExcelSeederHelper::findHeaderIndex($rows);
        if ($headerIndex === null) { $this->command?->error('Header pelatihan tidak ditemukan.'); return; }
        $rawHeaders = $rows[$headerIndex];
        $dataRows = array_slice($rows, $headerIndex + 1);

        // Map header ke kolom model
        $columnMap = [
            'nama_pelatihan' => ['nama_pelatihan','pelatihan','nama','judul'],
            'deskripsi' => ['deskripsi','deskripsi_pelatihan','keterangan','description'],
            'kategori' => ['kategori','category','kelompok'],
            'capaian_pembelajaran' => ['capaian_pembelajaran','capaian','learning_outcomes','outcomes'],
            'history_batch' => ['history_batch','batch_history','riwayat_batch'],
            'lembaga_ref' => ['lembaga','nama_lembaga','lpk','provider'],
        ];
        $resolved = ExcelSeederHelper::mapHeaders($rawHeaders, $columnMap);

        $created=0; $updated=0; $skipped=0; $errors=[]; $now=now();

        foreach ($dataRows as $rowIndex => $row) {
            if (collect($row)->filter(fn($v)=>trim((string)$v)!=='')->isEmpty()) continue; // skip baris kosong
            $payload=[];
            foreach ($row as $i=>$val) { if(!isset($resolved[$i])) continue; $field=$resolved[$i]; $payload[$field]= is_string($val)?trim($val):$val; }

            if (empty($payload['nama_pelatihan'])) { $skipped++; $errors[]=['row'=>$headerIndex+2+$rowIndex,'messages'=>['nama_pelatihan kosong']]; continue; }

            // Map lembaga_ref -> lembaga_id
            $lembagaId = null;
            if (!empty($payload['lembaga_ref'])) {
                $ref = $payload['lembaga_ref'];
                $lembaga = LembagaPelatihan::where('nama_lembaga','LIKE',"%$ref%")->first();
                if ($lembaga) $lembagaId = $lembaga->id; else { $errors[]=['row'=>$headerIndex+2+$rowIndex,'messages'=>["lembaga '$ref' tidak ditemukan"]]; }
            }
            if (!$lembagaId) { // fallback pertama lembaga ada?
                $first = LembagaPelatihan::first();
                if ($first) $lembagaId = $first->id;
            }
            $payload['lembaga_id'] = $lembagaId;
            unset($payload['lembaga_ref']);

            // history_batch mungkin dipisah koma atau JSON
            if (!empty($payload['history_batch']) && is_string($payload['history_batch'])) {
                $hb = trim($payload['history_batch']);
                if ($hb !== '') {
                    if (str_starts_with($hb,'[')) {
                        // JSON attempt
                        try { $decoded = json_decode($hb,true); if (is_array($decoded)) $payload['history_batch']=$decoded; } catch (\Throwable $e) {}
                    } else {
                        $payload['history_batch'] = array_values(array_filter(array_map('trim', preg_split('/[,;]+/',$hb))));
                    }
                }
            }

            $validator = Validator::make($payload,[
                'lembaga_id' => 'nullable|integer',
                'nama_pelatihan' => 'required|string',
                'deskripsi' => 'nullable|string',
                'kategori' => 'nullable|string',
                'capaian_pembelajaran' => 'nullable|string',
                'history_batch' => 'nullable|array',
            ]);
            if ($validator->fails()) { $skipped++; $errors[]=['row'=>$headerIndex+2+$rowIndex,'messages'=>$validator->errors()->all()]; continue; }
            $data = $validator->validated();

            // Upsert by (lembaga_id + nama_pelatihan) atau hanya nama jika lembaga null
            $query = Pelatihan::query()->where('nama_pelatihan',$data['nama_pelatihan']);
            if ($data['lembaga_id']) $query->where('lembaga_id',$data['lembaga_id']);
            $existing = $query->first();

            if ($existing) { $existing->fill($data); $existing->updated_at=$now; $existing->save(); $updated++; }
            else { $data['created_at']=$now; $data['updated_at']=$now; Pelatihan::create($data); $created++; }
        }

        $this->command?->info("Import pelatihan selesai: created=$created updated=$updated skipped=$skipped");
        if ($errors) { $this->command?->warn('Contoh error:'); foreach(array_slice($errors,0,5) as $e){ $this->command?->line('- Row '.$e['row'].': '.implode('; ',$e['messages'])); } }
    }
}
