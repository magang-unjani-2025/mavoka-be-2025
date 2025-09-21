<?php

// database/seeders/SiswaSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Siswa;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\Sekolah;
use Illuminate\Support\Str;

class SiswaSeeder extends Seeder
{
    public function run(): void
    {
        $rawData = [
            ['Lisa Mariana', 'lisaaja@gmail.com', '213564565', 11, 'Otomatisasi dan Tata Kelola Perkantoran', '2025/2026', 'Lisa Mariana', 'pass1234', 'Perempuan'],
            ['Raka Adi Pratama', 'raka.pratama@gmail.com', '213564566', 11, 'Akuntansi', '2025/2026', 'Raka Adi Pratama', 'pass1235', 'Laki laki'],
            ['Bagas Nugroho', 'bagas.nugroho@gmail.com', '783450021', 11, 'Multimedia', '2025/2026', 'Bagas Nugroho', 'pass1236', 'Laki laki'],
            ['Dwi Lestari Putri', 'dwi.lestari@gmail.com', '783450022', 11, 'Tata Boga', '2025/2026', 'Dwi Lestari Putri', 'pass1237', 'Perempuan'],
            ['Aldi Saputra', 'aldi.saputra@gmail.com', '232346518', 11, 'Teknik Kendaraan Ringan Otomotif', '2025/2026', 'Aldi Saputra', 'pass1238', 'Laki laki'],
            ['Nisa Anggraini', 'nisa.anggraini@gmail.com', '232346519', 11, 'Farmasi', '2025/2026', 'Nisa Anggraini', 'pass1239', 'Perempuan'],
            ['Bayu Firmansyah', 'bayu.firmansyah@gmail.com', '1357812945', 11, 'Perhotelan', '2025/2026', 'Bayu Firmansyah', 'pass1240', 'Laki laki'],
            ['Clara Widya Kusuma', 'clara.widya@gmail.com', '1357812946', 11, 'Perhotelan', '2025/2026', 'Clara Widya Kusuma', 'pass1241', 'Perempuan'],
            ['Andi Prakoso', 'andi.prakoso@gmail.com', '4423895711', 11, 'Akuntansi', '2025/2026', 'Andi Prakoso', 'pass1242', 'Laki laki'],
            ['Rani Oktaviani', 'rani.okta@gmail.com', '4423895712', 11, 'Bisnis Daring dan Pemasaran', '2025/2026', 'Rani Oktaviani', 'pass1243', 'Perempuan'],
            ['Fajar Adi Nugroho', 'fajar.adi@gmail.com', '5634789211', 11, 'Rekayasa Perangkat Lunak', '2025/2026', 'Fajar Adi Nugroho', 'pass1244', 'Laki laki'],
            ['Clara Widya Kusuma', 'mega.lestari@gmail.com', '5634789212', 11, 'Akuntansi', '2025/2026', 'Clara Widya Kusuma', 'pass1245', 'Perempuan'],
            ['Rizky Ramadhan', 'rizky.ramadhan@gmail.com', '5634789213', 11, 'Multimedia', '2025/2026', 'Rizky Ramadhan', 'pass1246', 'Laki laki'],
            ['Ayu Kartika Sari', 'ayu.kartika@gmail.com', '5634789214', 11, 'Otomatisasi dan Tata Kelola Kantor', '2025/2026', 'Ayu Kartika Sari', 'pass1247', 'Perempuan'],
            ['Dimas Bayu Saputra', 'dimas.bayu@gmail.com', '5634789215', 11, 'Teknik Kendaraan Ringan Otomotif', '2025/2026', 'Dimas Bayu Saputra', 'pass1248', 'Laki laki'],
            ['Rafi Andhika Putra', 'rafi.andhika@gmail.com', '671230001', 11, 'Teknik Komputer dan Jaringan', '2025/2026', 'Rafi Andhika Putra', 'pass1249', 'Laki laki'],
            ['Dina Ayu Lestari', 'dina.ayu@gmail.com', '671230002', 11, 'Akuntansi', '2025/2026', 'Dina Ayu Lestari', 'pass1250', 'Perempuan'],
            ['Farhan Nugroho', 'farhan.nugroho@gmail.com', '672310001', 11, 'Multimedia', '2025/2026', 'Farhan Nugroho', 'pass1251', 'Laki laki'],
            ['Sari Melati Kusuma', 'sari.melati@gmail.com', '672310002', 11, 'Tata Boga', '2025/2026', 'Sari Melati Kusuma', 'pass1252', 'Perempuan'],
            ['Arga Prasetyo', 'arga.prasetyo@gmail.com', '781245001', 11, 'Teknik Kendaraan Ringan Otomotif', '2025/2026', 'Arga Prasetyo', 'pass1253', 'Laki laki'],
            ['Widya Rahayu', 'widya.rahayu@gmail.com', '781245002', 11, 'Farmasi', '2025/2026', 'Widya Rahayu', 'pass1254', 'Perempuan'],
            ['Galih Firmansyah', 'galih.firmansyah@gmail.com', '895620001', 11, 'Keperawatan', '2025/2026', 'Galih Firmansyah', 'pass1255', 'Laki laki'],
            ['Intan Cahyani', 'intan.cahyani@gmail.com', '895620002', 11, 'Analis Kesehatan', '2025/2026', 'Intan Cahyani', 'pass1256', 'Perempuan'],
            ['Yuda Saputra', 'yuda.saputra@gmail.com', '564120001', 11, 'Rekayasa Perangkat Lunak', '2025/2026', 'Yuda Saputra', 'pass1257', 'Laki laki'],
            ['Lestari Anggraini', 'lestari.anggra@gmail.com', '564120002', 11, 'Akuntansi', '2025/2026', 'Lestari Anggraini', 'pass1258', 'Perempuan'],
            ['Rian Mahendra', 'rian.mahendra@gmail.com', '564230001', 11, 'Multimedia', '2025/2026', 'Rian Mahendra', 'pass1259', 'Laki laki'],
            ['Ayu Safitri', 'ayu.safitri@gmail.com', '564230002', 11, 'Tata Boga', '2025/2026', 'Ayu Safitri', 'pass1260', 'Perempuan'],
            ['Dito Prabowo', 'dito.prabowo@gmail.com', '564560001', 11, 'Teknik Komputer dan Jaringan', '2025/2026', 'Dito Prabowo', 'pass1261', 'Laki laki'],
            ['Rani Oktaviana', 'rani.oktaviana@gmail.com', '564560002', 11, 'Farmasi', '2025/2026', 'Rani Oktaviana', 'pass1262', 'Perempuan'],
            ['Aldi Kurniawan', 'aldi.kurniawan@gmail.com', '564670001', 11, 'Bisnis Daring dan Pemasaran', '2025/2026', 'Aldi Kurniawan', 'pass1263', 'Laki laki'],
            ['Putri Anggun Dewi', 'putri.anggun@gmail.com', '564670002', 11, 'Akuntansi', '2025/2026', 'Putri Anggun Dewi', 'pass1264', 'Perempuan'],
            ['Yoga Firmansyah', 'yoga.firmansyah@gmail.com', '564890001', 11, 'Multimedia', '2025/2026', 'Yoga Firmansyah', 'pass1265', 'Laki laki'],
            ['Ayu Kartika', 'ayu.kartika@gmail.com', '564890002', 11, 'Tata Boga', '2025/2026', 'Ayu Kartika', 'pass1266', 'Perempuan'],
            ['Andi Setiawan', 'andi.setiawan@gmail.com', '565110001', 11, 'Teknik Kendaraan Ringan Otomotif', '2025/2026', 'Andi Setiawan', 'pass1267', 'Laki laki'],
            ['Nisa Khairunnisa', 'nisa.khairun@gmail.com', '565110002', 11, 'Akuntansi', '2025/2026', 'Nisa Khairunnisa', 'pass1268', 'Perempuan'],
            ['Bagas Prasetya', 'bagas.prasetya@gmail.com', '565220001', 11, 'Rekayasa Perangkat Lunak', '2025/2026', 'Bagas Prasetya', 'pass1269', 'Laki laki'],
            ['Dwi Lestari', 'dwi.lestari@gmail.com', '565220002', 11, 'Bisnis Daring dan Pemasaran', '2025/2026', 'Dwi Lestari', 'pass1270', 'Perempuan'],
            ['Riko Aditya', 'riko.aditya@gmail.com', '565330001', 11, 'Multimedia', '2025/2026', 'Riko Aditya', 'pass1271', 'Laki laki'],
            ['Maya Wulandari', 'maya.wulan@gmail.com', '565330002', 11, 'Tata Boga', '2025/2026', 'Maya Wulandari', 'pass1272', 'Perempuan'],
            ['Hendra Gunawan', 'hendra.gunawan@gmail.com', '565440001', 11, 'Teknik Komputer dan Jaringan', '2025/2026', 'Hendra Gunawan', 'pass1273', 'Laki laki'],
            ['Siti Aisyah', 'siti.aisyah@gmail.com', '565440002', 11, 'Keperawatan', '2025/2026', 'Siti Aisyah', 'pass1274', 'Perempuan'],
            ['Rehan Saputra', 'rehan.saputra@gmail.com', '565550001', 11, 'Teknik Kendaraan Ringan Otomotif', '2025/2026', 'Rehan Saputra', 'pass1275', 'Laki laki'],
            ['Putri Amelia', 'putri.amelia@gmail.com', '565550002', 11, 'Akuntansi', '2025/2026', 'Putri Amelia', 'pass1276', 'Perempuan'],
            ['Rangga Pratama', 'rangga.pratama@gmail.com', '565660001', 11, 'Multimedia', '2025/2026', 'Rangga Pratama', 'pass1277', 'Laki laki'],
            ['Intan Permata Sari', 'intan.permata@gmail.com', '565770001', 11, 'Tata Boga', '2025/2026', 'Intan Permata Sari', 'pass1278', 'Perempuan'],
            ['Dimas Cahyo', 'dimas.cahyo@gmail.com', '565770001', 11, 'Rekayasa Perangkat Lunak', '2025/2026', 'Dimas Cahyo', 'pass1279', 'Laki laki'],
            ['Fitri Anggraini', 'fitri.anggraini@gmail.com', '565770002', 11, 'Farmasi', '2025/2026', 'Fitri Anggraini', 'pass1280', 'Perempuan'],
            ['Aldi Yulianto', 'aldi.yulianto@gmail.com', '565880001', 11, 'Teknik Kendaraan Ringan Otomotif', '2025/2026', 'Aldi Yulianto', 'pass1281', 'Laki laki'],
            ['Salsabila Putri', 'salsa.putri@gmail.com', '565880002', 11, 'Bisnis Daring dan Pemasaran', '2025/2026', 'Salsabila Putri', 'pass1282', 'Perempuan'],
            ['Wahyu Adi Nugroho', 'wahyu.adi@gmail.com', '565990001', 11, 'Keperawatan', '2025/2026', 'Wahyu Adi Nugroho', 'pass1283', 'Laki laki'],
        ];

        $created = 0;
        $updated = 0;
        $now = now();
        $sekolahIds = Sekolah::pluck('id')->all();
        if (empty($sekolahIds)) {
            $sekolahIds = [1];
        }
        $countSekolah = count($sekolahIds);
        $i = 0;
    // Track email yang sudah digunakan (baik di DB maupun batch ini)
    $usedEmails = array_fill_keys(Siswa::pluck('email')->all(), true);

        foreach ($rawData as $row) {
            [$nama, $emailRaw, $nisn, $kelas, $jurusan, $tahunAjaran, $usernameRaw, $passwordPlain, $genderRaw] = $row;

            // Pastikan email unik: jika sudah ada, tambahkan suffix numerik sebelum '@'
            $email = strtolower(trim($emailRaw));
            if ($email === '') { $email = Str::slug($nama, '.') . '@example.local'; }
            if (isset($usedEmails[$email]) || Siswa::where('email',$email)->exists()) {
                [$local,$domain] = explode('@',$email,2) + ['', 'example.local'];
                $n=1; $candidate = $local.$n.'@'.$domain;
                while (isset($usedEmails[$candidate]) || Siswa::where('email',$candidate)->exists()) { $n++; $candidate = $local.$n.'@'.$domain; }
                $email = $candidate;
            }
            $usedEmails[$email] = true;

            // Normalisasi username -> slug dasar (tanpa spasi & karakter khusus)
            $baseUsername = trim($usernameRaw) !== '' ? $usernameRaw : $nama;
            $candidate = Str::slug($baseUsername, '_');
            if ($candidate === '') { $candidate = 'user'; }
            $original = $candidate;
            $suffix = 1;
            while (Siswa::where('username', $candidate)->exists()) {
                $candidate = $original . '_' . $suffix++;
            }
            $username = $candidate;

            $gender = strtolower(trim($genderRaw));
            $jenisKelamin = null;
            if (str_starts_with($gender, 'l')) { // "laki laki"
                $jenisKelamin = 'L';
            } elseif (str_starts_with($gender, 'p')) { // "perempuan"
                $jenisKelamin = 'P';
            }

            $data = [
                'nama_lengkap' => $nama,
                'email' => $email,
                'nisn' => preg_replace('/[^0-9]/', '', $nisn),
                'kelas' => (int) $kelas,
                'jurusan' => $jurusan,
                'tahun_ajaran' => $tahunAjaran, // simpan string penuh
                'username' => $username,
                'password' => Hash::make($passwordPlain),
                'jenis_kelamin' => $jenisKelamin,
                'sekolah_id' => $sekolahIds[$i % $countSekolah],
                'updated_at' => $now,
            ];
            $i++;

            // Upsert berbasis nisn unik; jika email sudah ada beda nisn, suffix email nanti bisa ditangani manual jika perlu
            $existing = Siswa::where('nisn', $data['nisn'])->first();
            if ($existing) {
                $existing->fill($data)->save();
                $updated++;
            } else {
                $data['created_at'] = $now;
                Siswa::create($data);
                $created++;
            }
        }

        // ---------------------------------------------
        // Tambahan siswa acak per sekolah (nama realistis)
        // ---------------------------------------------
        $minPerSchool = 10; // tetap minimal 10 (kalau sudah lebih tidak ditambah)
        $jurusanOptions = [
            'Akuntansi','Multimedia','Tata Boga','Teknik Kendaraan Ringan Otomotif','Farmasi',
            'Perhotelan','Bisnis Daring dan Pemasaran','Rekayasa Perangkat Lunak',
            'Teknik Komputer dan Jaringan','Keperawatan','Analis Kesehatan',
            'Otomatisasi dan Tata Kelola Perkantoran','Otomatisasi dan Tata Kelola Kantor'
        ];

        $firstMale = ['Rizky','Fajar','Dimas','Bagus','Reza','Ardi','Aldi','Yoga','Rangga','Rafi','Farhan','Riko','Andi','Hendra','Bayu','Galih','Arga','Rian','Wahyu','Rehan'];
        $firstFemale = ['Ayu','Siti','Dinda','Nisa','Clara','Rani','Maya','Putri','Intan','Lisa','Dina','Sari','Widya','Lestari','Fitri','Salsabila','Mega','Kartika','Amelia','Permata'];
        $lastNames = ['Saputra','Maulana','Pratama','Wulandari','Anggraini','Ramadhani','Nugroho','Kusuma','Santoso','Setiawan','Firmansyah','Wijaya','Kurniawan','Permatasari','Handayani','Cahyani','Prasetyo','Gunawan','Mahendra','Yulianto'];

        // Kumpulan cache untuk uniqueness tambahan
        $usedUsernames = array_fill_keys(Siswa::pluck('username')->all(), true);
        $usedNisn = array_fill_keys(Siswa::pluck('nisn')->all(), true);

        $generateEmail = function(string $base) use (&$usedEmails) {
            $local = Str::slug($base, '.');
            if ($local==='') $local='user';
            $email = $local.'@example.local';
            if(!isset($usedEmails[$email])) { $usedEmails[$email]=true; return $email; }
            $n=1; while(isset($usedEmails[$local.$n.'@example.local'])) $n++; $candidate=$local.$n.'@example.local'; $usedEmails[$candidate]=true; return $candidate;
        };
        $generateUsername = function(string $base) use (&$usedUsernames) {
            $u = Str::slug($base,'_'); if($u==='') $u='user';
            if(!isset($usedUsernames[$u])) { $usedUsernames[$u]=true; return $u; }
            $n=1; $cand=$u.'_'.$n; while(isset($usedUsernames[$cand])) { $n++; $cand=$u.'_'.$n; } $usedUsernames[$cand]=true; return $cand;
        };
        $generateNisn = function() use (&$usedNisn) { do { $nisn=(string)mt_rand(100000000,999999999); } while(isset($usedNisn[$nisn])); $usedNisn[$nisn]=true; return $nisn; };

        $existingCounts = Siswa::selectRaw('sekolah_id, COUNT(*) as c')->groupBy('sekolah_id')->pluck('c','sekolah_id');
        foreach ($sekolahIds as $sid) {
            $count = $existingCounts[$sid] ?? 0;
            if ($count >= $minPerSchool) continue; // sudah cukup
            $need = $minPerSchool - $count;
            for ($k=0;$k<$need;$k++) {
                $isMale = rand(0,1)===1;
                $first = $isMale ? $firstMale[array_rand($firstMale)] : $firstFemale[array_rand($firstFemale)];
                $second = $lastNames[array_rand($lastNames)];
                // 30% peluang dua last name
                if(rand(1,100)<=30) { $second .= ' '.$lastNames[array_rand($lastNames)]; }
                $fullName = $first.' '.$second;
                $gender = $isMale ? 'L' : 'P';
                $jurusan = $jurusanOptions[array_rand($jurusanOptions)];
                $kelas = rand(10,12);
                $tahun = '2025/2026';
                $username = $generateUsername($fullName);
                $email = $generateEmail($fullName);
                $nisnGen = $generateNisn();

                Siswa::create([
                    'nama_lengkap' => $fullName,
                    'email' => $email,
                    'nisn' => $nisnGen,
                    'kelas' => $kelas,
                    'jurusan' => $jurusan,
                    'tahun_ajaran' => $tahun,
                    'username' => $username,
                    'password' => Hash::make('password'),
                    'jenis_kelamin' => $gender,
                    'sekolah_id' => $sid,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                $created++;
            }
        }

        $this->command?->info("Seed siswa statis selesai: created=$created updated=$updated (termasuk nama acak realistis)");
    }
}

