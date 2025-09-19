<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sekolah;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Helpers\ExcelSeederHelper;
use Illuminate\Support\Str;

class SekolahSeeder extends Seeder
{
    public function run(): void
    {
        $path = base_path('data-dummy/data-sekolah.xlsx');
        if (!file_exists($path)) {
            $this->command?->warn('File data-dummy/data-sekolah.xlsx tidak ditemukan. Menyisipkan data default.');
            $this->seedFallback();
            return;
        }

        $this->command?->info('Import sekolah dari: '.$path);
        try { $rows = ExcelSeederHelper::loadFirstSheet($path); }
        catch (\Throwable $e) { $this->command?->error('Gagal baca Excel: '.$e->getMessage()); return; }
        if (empty($rows)) { $this->command?->warn('Sheet kosong.'); return; }

        $headerIndex = ExcelSeederHelper::findHeaderIndex($rows);
        if ($headerIndex === null) { $this->command?->error('Header tidak ditemukan.'); return; }
        $rawHeaders = $rows[$headerIndex];
        $dataRows = array_slice($rows, $headerIndex + 1);

        $columnMap = [
            'nama_sekolah' => ['nama_sekolah','sekolah','nama'],
            'npsn' => ['npsn','kode_npsn'],
            'web_sekolah' => ['web_sekolah','website','web'],
            'alamat' => ['alamat','alamat_sekolah'],
            'kontak' => ['kontak','no_hp','telepon','telp','hp'],
            'email' => ['email','surelem'],
        ];
        $resolved = ExcelSeederHelper::mapHeaders($rawHeaders, $columnMap);

        $created=0; $updated=0; $skipped=0; $errors=[]; $now=now();
        foreach ($dataRows as $rowIndex => $row) {
            if (collect($row)->filter(fn($v)=>trim((string)$v)!=='')->isEmpty()) continue;
            $payload=[];
            foreach ($row as $i=>$val){ if(!isset($resolved[$i])) continue; $field=$resolved[$i]; $payload[$field]=is_string($val)?trim($val):$val; }

            if(isset($payload['npsn'])) $payload['npsn']=preg_replace('/[^0-9]/','',(string)$payload['npsn']);
            if(empty($payload['nama_sekolah'])) { $skipped++; $errors[]=['row'=>$headerIndex+2+$rowIndex,'messages'=>['nama_sekolah kosong']]; continue; }

            $validator=Validator::make($payload,[
                'nama_sekolah'=>'required|string',
                'npsn'=>'nullable|string|min:5',
                'web_sekolah'=>'nullable|string',
                'alamat'=>'nullable|string',
                'kontak'=>'nullable|string',
                'email'=>'nullable|email'
            ]);
            if($validator->fails()) { $skipped++; $errors[]=['row'=>$headerIndex+2+$rowIndex,'messages'=>$validator->errors()->all()]; continue; }
            $data=$validator->validated();

            // Generate username kalau belum ada: slug nama + random
            $username = Str::slug(substr($data['nama_sekolah'],0,20));
            $baseUsername = $username ?: 'sekolah';
            $candidate = $baseUsername; $suffix=1;
            while (Sekolah::where('username',$candidate)->exists()) { $candidate = $baseUsername.($suffix++); }
            $data['username'] = $candidate;
            $data['password'] = Hash::make('sekolah123');
            $data['status_verifikasi'] = 'Terverifikasi';
            $data['tanggal_verifikasi'] = $now;

            $existing = null;
            if (!empty($data['npsn'])) { $existing = Sekolah::where('npsn',$data['npsn'])->first(); }
            elseif (!empty($data['email'])) { $existing = Sekolah::where('email',$data['email'])->first(); }

            if ($existing) {
                $existing->fill($data); $existing->updated_at=$now; $existing->save(); $updated++;
            } else {
                $data['created_at']=$now; $data['updated_at']=$now; Sekolah::create($data); $created++;
            }
        }

        $this->command?->info("Import sekolah selesai: created=$created updated=$updated skipped=$skipped");
        if($errors){ $this->command?->warn('Contoh error:'); foreach(array_slice($errors,0,5) as $e){ $this->command?->line('- Row '.$e['row'].': '.implode('; ',$e['messages'])); } }
    }

    private function seedFallback(): void
    {
        Sekolah::updateOrCreate(
            ['npsn' => '1234567890'],
            [
                'username' => 'sekolah1',
                'email' => 'sekolah1@example.com',
                'password' => Hash::make('sekolah1'),
                'nama_sekolah' => 'SMK Negeri 1',
                'web_sekolah' => 'https://smkn1.sch.id',
                'kontak' => '081234567890',
                'alamat' => 'Jl. Pendidikan No. 1',
                'status_verifikasi' => 'Terverifikasi',
                'tanggal_verifikasi' => now(),
            ]
        );
        $this->command?->info('Fallback sekolah seeded.');
    }
}
