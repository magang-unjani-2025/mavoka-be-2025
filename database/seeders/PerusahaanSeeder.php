<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Perusahaan;
use App\Helpers\ExcelSeederHelper;

class PerusahaanSeeder extends Seeder
{
    public function run(): void
    {
        $path = base_path('public/data-dummy/data-perusahaan.xlsx');
        if (!file_exists($path)) {
            $this->command?->warn('File data-dummy/data-perusahaan.xlsx tidak ditemukan. Menyisipkan fallback.');
            $this->seedFallback();
            return;
        }
        $this->command?->info('Import perusahaan dari: '.$path);
        try { $rows = ExcelSeederHelper::loadFirstSheet($path); }
        catch (\Throwable $e) { $this->command?->error('Gagal baca Excel: '.$e->getMessage()); return; }
        if (empty($rows)) { $this->command?->warn('Sheet kosong.'); return; }

        $headerIndex = ExcelSeederHelper::findHeaderIndex($rows);
        if ($headerIndex === null) { $this->command?->error('Header tidak ditemukan.'); return; }
        $rawHeaders = $rows[$headerIndex];
        $dataRows = array_slice($rows, $headerIndex + 1);

        $columnMap = [
            'nama_perusahaan' => ['nama_perusahaan','perusahaan','nama','company_name'],
            'bidang_usaha' => ['bidang_usaha','bidang','industri','industry'],
            'deskripsi_usaha' => ['deskripsi_usaha','deskripsi','description'],
            'web_perusahaan' => ['web_perusahaan','website','web'],
            'alamat' => ['alamat','alamat_lengkap','address'],
            'kontak' => ['kontak','no_hp','telepon','telp','hp','phone'],
            'email' => ['email','surelem'],
            'penanggung_jawab' => ['penanggung_jawab','pic','contact_person'],
        ];
        $resolved = ExcelSeederHelper::mapHeaders($rawHeaders, $columnMap);

        $created=0; $updated=0; $skipped=0; $errors=[]; $now=now();
        foreach ($dataRows as $rowIndex => $row) {
            if (collect($row)->filter(fn($v)=>trim((string)$v)!=='')->isEmpty()) continue;
            $payload=[];
            foreach ($row as $i=>$val) { if(!isset($resolved[$i])) continue; $field=$resolved[$i]; $payload[$field]=is_string($val)?trim($val):$val; }

            if (empty($payload['nama_perusahaan'])) { $skipped++; $errors[]=['row'=>$headerIndex+2+$rowIndex,'messages'=>['nama_perusahaan kosong']]; continue; }
            // Cast kontak always string
            if(isset($payload['kontak'])) $payload['kontak'] = (string)$payload['kontak'];

            $validator = Validator::make($payload,[
                'nama_perusahaan'=>'required|string',
                'bidang_usaha'=>'nullable|string',
                'deskripsi_usaha'=>'nullable|string',
                'web_perusahaan'=>'nullable|string',
                'alamat'=>'nullable|string',
                'kontak'=>'nullable|string',
                'email'=>'nullable|email',
                'penanggung_jawab'=>'nullable|string'
            ]);
            if($validator->fails()) { $skipped++; $errors[]=['row'=>$headerIndex+2+$rowIndex,'messages'=>$validator->errors()->all()]; continue; }
            $data = $validator->validated();

            // Username slug unik
            $usernameBase = Str::slug(substr($data['nama_perusahaan'],0,25)) ?: 'company';
            $candidate = $usernameBase; $suffix=1;
            while (Perusahaan::where('username',$candidate)->exists()) { $candidate = $usernameBase.($suffix++); }
            $data['username'] = $candidate;
            $data['password'] = Hash::make('perusahaan123');
            $data['status_verifikasi'] = 'Terverifikasi';
            $data['tanggal_verifikasi'] = $now;
            $data['otp'] = Str::random(6);
            $data['otp_expired_at'] = $now->copy()->addMinutes(10);

            $existing = null;
            if (!empty($data['email'])) { $existing = Perusahaan::where('email',$data['email'])->first(); }
            if (!$existing) { $existing = Perusahaan::where('nama_perusahaan',$data['nama_perusahaan'])->first(); }

            if ($existing) { $existing->fill($data); $existing->updated_at=$now; $existing->save(); $updated++; }
            else { $data['created_at']=$now; $data['updated_at']=$now; Perusahaan::create($data); $created++; }
        }

        $this->command?->info("Import perusahaan selesai: created=$created updated=$updated skipped=$skipped");
        if($errors){ $this->command?->warn('Contoh error:'); foreach(array_slice($errors,0,5) as $e){ $this->command?->line('- Row '.$e['row'].': '.implode('; ',$e['messages'])); } }
    }

    private function seedFallback(): void
    {
        Perusahaan::updateOrCreate(
            ['email' => 'info@techcorp.com'],
            [
                'username' => 'techcorp',
                'password' => Hash::make('techcorp'),
                'status_verifikasi' => 'Terverifikasi',
                'tanggal_verifikasi' => now(),
                'nama_perusahaan' => 'TechCorp Indonesia',
                'bidang_usaha' => 'Teknologi Informasi',
                'deskripsi_usaha' => 'Perusahaan IT yang fokus pada pengembangan perangkat lunak dan solusi cloud.',
                'alamat' => 'Jl. Teknologi No.88, Jakarta',
                'kontak' => '021-12345678',
                'penanggung_jawab' => 'Dian Prasetyo',
                'web_perusahaan' => 'https://pmb.unjaya.ac.id/',
            ]
        );
        $this->command?->info('Fallback perusahaan seeded.');
    }
}
