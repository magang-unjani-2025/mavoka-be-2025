<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\LembagaPelatihan;
use App\Helpers\ExcelSeederHelper;

class LembagaSeeder extends Seeder
{
    public function run(): void
    {
        $path = base_path('public/data-dummy/lembaga-pelatihan.xlsx');
        if (!file_exists($path)) {
            $this->command?->warn('File data-dummy/lembaga-pelatihan.xlsx tidak ditemukan. Menyisipkan data default.');
            $this->seedFallback();
            return;
        }
        $this->command?->info('Import lembaga pelatihan dari: '.$path);
        try { $rows = ExcelSeederHelper::loadFirstSheet($path); }
        catch (\Throwable $e) { $this->command?->error('Gagal baca Excel: '.$e->getMessage()); return; }
        if (empty($rows)) { $this->command?->warn('Sheet kosong.'); return; }

        $headerIndex = ExcelSeederHelper::findHeaderIndex($rows);
        if ($headerIndex === null) { $this->command?->error('Header tidak ditemukan.'); return; }
        $rawHeaders = $rows[$headerIndex];
        $dataRows = array_slice($rows, $headerIndex + 1);

        $columnMap = [
            'nama_lembaga' => ['nama_lembaga','lembaga','nama'],
            'bidang_pelatihan' => ['bidang_pelatihan','bidang','kelas_pelatihan'],
            'deskripsi_lembaga' => ['deskripsi_lembaga','deskripsi','keterangan'],
            'web_lembaga' => ['web_lembaga','website','web'],
            'alamat' => ['alamat','alamat_lengkap'],
            'kontak' => ['kontak','no_hp','telepon','telp','hp'],
            'email' => ['email','surelem'],
            'status_akreditasi' => ['status_akreditasi','akreditasi'],
        ];
        $resolved = ExcelSeederHelper::mapHeaders($rawHeaders, $columnMap);

        $created=0; $updated=0; $skipped=0; $errors=[]; $now=now();
        foreach ($dataRows as $rowIndex => $row) {
            if (collect($row)->filter(fn($v)=>trim((string)$v)!=='')->isEmpty()) continue;
            $payload=[];
            foreach ($row as $i=>$val){ if(!isset($resolved[$i])) continue; $field=$resolved[$i]; $payload[$field]=is_string($val)?trim($val):$val; }

            if(empty($payload['nama_lembaga'])) { $skipped++; $errors[]=['row'=>$headerIndex+2+$rowIndex,'messages'=>['nama_lembaga kosong']]; continue; }

            $validator=Validator::make($payload,[
                'nama_lembaga'=>'required|string',
                'bidang_pelatihan'=>'nullable|string',
                'deskripsi_lembaga'=>'nullable|string',
                'web_lembaga'=>'nullable|string',
                'alamat'=>'nullable|string',
                'kontak'=>'nullable|string',
                'email'=>'nullable|email',
                'status_akreditasi'=>'nullable|string|max:5'
            ]);
            if($validator->fails()) { $skipped++; $errors[]=['row'=>$headerIndex+2+$rowIndex,'messages'=>$validator->errors()->all()]; continue; }
            $data=$validator->validated();
            $usernameBase = Str::slug(substr($data['nama_lembaga'],0,25)) ?: 'lpk';
            $candidate=$usernameBase; $suffix=1;
            while (LembagaPelatihan::where('username',$candidate)->exists()) { $candidate=$usernameBase.($suffix++); }
            $data['username']=$candidate;
            $data['password']=Hash::make('lpk123');
            $data['status_verifikasi']='Terverifikasi';
            $data['tanggal_verifikasi']=$now;
            $data['otp']=Str::random(6);
            $data['otp_expired_at']=$now->copy()->addMinutes(10);

            $existing = null;
            if(!empty($data['email'])) { $existing = LembagaPelatihan::where('email',$data['email'])->first(); }
            if(!$existing) { $existing = LembagaPelatihan::where('nama_lembaga',$data['nama_lembaga'])->first(); }

            if($existing) { $existing->fill($data); $existing->updated_at=$now; $existing->save(); $updated++; }
            else { $data['created_at']=$now; $data['updated_at']=$now; LembagaPelatihan::create($data); $created++; }
        }

        $this->command?->info("Import lembaga selesai: created=$created updated=$updated skipped=$skipped");
        if($errors){ $this->command?->warn('Contoh error:'); foreach(array_slice($errors,0,5) as $e){ $this->command?->line('- Row '.$e['row'].': '.implode('; ',$e['messages'])); } }
    }

    private function seedFallback(): void
    {
        LembagaPelatihan::updateOrCreate(
            ['email' => 'admin@lpkbangun.com'],
            [
                'username' => 'lpkbangun',
                'password' => Hash::make('lpkbangun'),
                'status_verifikasi' => 'Terverifikasi',
                'tanggal_verifikasi' => now(),
                'nama_lembaga' => 'LPK Bangun Karier',
                'deskripsi_lembaga' => 'Lembaga IT',
                'bidang_pelatihan' => 'Desain Grafis',
                'web_lembaga' => 'https://pmb.unjaya.ac.id/',
                'alamat' => 'Jl. Pelatihan No.23, Yogyakarta',
                'kontak' => '081234567890',
                'status_akreditasi' => 'A',
            ]
        );
        $this->command?->info('Fallback lembaga seeded.');
    }
}
