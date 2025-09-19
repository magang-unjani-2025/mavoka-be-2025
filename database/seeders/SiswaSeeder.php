<?php

// database/seeders/SiswaSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Siswa;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Helpers\ExcelSeederHelper;
use Illuminate\Support\Str;

class SiswaSeeder extends Seeder
{
    public function run(): void
    {
        $path = base_path('data-dummy/data-siswa.xlsx');
        if (!file_exists($path)) {
            $this->command?->warn("File Excel data-dummy/data-siswa.xlsx tidak ditemukan. Menyisipkan data fallback.");
            $this->seedFallback();
            return;
        }

        $this->command?->info('Membaca file: ' . $path);

        try { $rows = ExcelSeederHelper::loadFirstSheet($path); }
        catch (\Throwable $e) {
            $this->command?->error('Gagal membaca Excel: '.$e->getMessage());
            return; }
        if (empty($rows)) {
            $this->command?->warn('Sheet kosong. Menggunakan fallback.');
            $this->seedFallback();
            return;
        }

        // Cari baris header pertama yang punya minimal 1 sel terisi
        $headerIndex = ExcelSeederHelper::findHeaderIndex($rows);
        if ($headerIndex === null) {
            $this->command?->error('Header tidak ditemukan. Menghentikan seeder.');
            return;
        }

        $rawHeaders = $rows[$headerIndex];
        $dataRows = array_slice($rows, $headerIndex + 1);

        // Pemetaan alias header -> kolom database / atribut model
        $columnMap = [
            'nisn' => ['nisn','no_nisn','id_nisn'],
            'nama_lengkap' => ['nama_lengkap','nama','nama_siswa'],
            'email' => ['email','surelem'],
            'kelas' => ['kelas','tingkat'],
            // Catatan: model mendefinisikan 'jurusan' (string) namun migrasi memiliki 'jurusan_id'. Hindari isi hingga konsisten.
            'tahun_ajaran' => ['tahun_ajaran','thn_ajaran','tahun'],
            'sekolah_id' => ['sekolah_id','id_sekolah'],
            'tanggal_lahir' => ['tanggal_lahir','tgl_lahir','dob'],
            'jenis_kelamin' => ['jenis_kelamin','jk','gender'],
            'alamat' => ['alamat','alamat_lengkap'],
            'kontak' => ['kontak','no_hp','telepon','telp','hp'],
        ];

        // Resolusi kolom excel ke field
        $resolved = ExcelSeederHelper::mapHeaders($rawHeaders, $columnMap);

        $created = 0; $updated = 0; $skipped = 0; $errors = [];
        $now = now();

        foreach ($dataRows as $rowIndex => $row) {
            // Skip baris kosong
            if (collect($row)->filter(fn($v) => trim((string)$v) !== '')->isEmpty()) continue;

            $payload = [];
            foreach ($row as $i => $val) {
                if (!array_key_exists($i, $resolved)) continue;
                $field = $resolved[$i];
                $payload[$field] = is_string($val) ? trim($val) : $val;
            }

            // Normalisasi & transform
            if (isset($payload['nisn'])) {
                $payload['nisn'] = preg_replace('/[^0-9]/', '', (string)$payload['nisn']);
            }
            if (isset($payload['kelas']) && $payload['kelas'] !== '') {
                $payload['kelas'] = (int)filter_var($payload['kelas'], FILTER_SANITIZE_NUMBER_INT);
            }
            if (isset($payload['tahun_ajaran']) && $payload['tahun_ajaran'] !== '') {
                $payload['tahun_ajaran'] = (int)preg_replace('/[^0-9]/','', (string)$payload['tahun_ajaran']);
            }
            if (isset($payload['jenis_kelamin'])) {
                $jk = strtolower($payload['jenis_kelamin']);
                if (in_array($jk, ['l','laki','laki_laki','male','m'])) $payload['jenis_kelamin'] = 'L';
                elseif (in_array($jk, ['p','perempuan','female','f'])) $payload['jenis_kelamin'] = 'P';
            }
            if (isset($payload['tanggal_lahir'])) {
                $payload['tanggal_lahir'] = ExcelSeederHelper::parseDateFlexible($payload['tanggal_lahir']);
            }

            // Validasi minimal
            $validator = Validator::make($payload, [
                'nisn' => 'required|string|min:5',
                'nama_lengkap' => 'nullable|string',
                'email' => 'nullable|email',
                'kelas' => 'nullable|integer',
                'tahun_ajaran' => 'nullable|integer',
                'sekolah_id' => 'nullable|integer',
                'tanggal_lahir' => 'nullable|date',
                'jenis_kelamin' => 'nullable|in:L,P',
                'alamat' => 'nullable|string',
                'kontak' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                $errors[] = [
                    'row' => $headerIndex + 2 + $rowIndex,
                    'messages' => $validator->errors()->all(),
                ];
                $skipped++;
                continue;
            }

            $data = $validator->validated();
            // Password default jika email tersedia (opsional
            if (!empty($data['email']) && empty($data['password'])) {
                // jangan set mass assignment jika tidak ada kolom, skip
                $data['password'] = Hash::make('password123');
            }

            // Upsert by nisn
            $existing = Siswa::where('nisn', $data['nisn'])->first();
            if ($existing) {
                $existing->fill($data);
                $existing->updated_at = $now;
                $existing->save();
                $updated++;
            } else {
                $data['created_at'] = $now; $data['updated_at'] = $now;
                try {
                    Siswa::create($data);
                    $created++;
                } catch (\Throwable $e) {
                    $errors[] = [
                        'row' => $headerIndex + 2 + $rowIndex,
                        'messages' => [$e->getMessage()],
                    ];
                    $skipped++;
                }
            }
        }

        $this->command?->info("Selesai import siswa: created=$created updated=$updated skipped=$skipped");
        if (!empty($errors)) {
            $this->command?->warn('Ringkasan error (max 5 contoh):');
            foreach (array_slice($errors, 0, 5) as $err) {
                $this->command?->line('- Row '.$err['row'].': '.implode('; ', $err['messages']));
            }
        }
    }

    private function seedFallback(): void
    {
        // Data minimal jika Excel tidak tersedia
        Siswa::updateOrCreate(
            ['nisn' => '9988776655'],
            [
                'nama_lengkap' => 'Contoh Satu',
                'sekolah_id' => 1,
                'kelas' => 12,
                'tahun_ajaran' => 2024,
            ]
        );
        Siswa::updateOrCreate(
            ['nisn' => '1010101010'],
            [
                'nama_lengkap' => 'Contoh Dua',
                'sekolah_id' => 1,
                'kelas' => 12,
                'tahun_ajaran' => 2025,
            ]
        );
        $this->command?->info('Fallback siswa seeded.');
    }

    // parseDateFlexible now provided by ExcelSeederHelper
}

