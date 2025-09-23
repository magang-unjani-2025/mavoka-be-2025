<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sekolah;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SekolahSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['SMK Teknologi Nusantara','20400001','info01@smk-teknologi-nusantara.sch.id','(0274) 61001','https://smk-teknologi-nusantara.sch.id','Jl. Magelang Km. 8, Sleman, DIY','smkteknologinusantara','pass1234','smk-teknologi-nusantara.png'],
            ['SMK Cendekia Bangsa','20400002','info02@smk-cendekia-bangsa.sch.id','0812-3456-0001','https://smk-cendekia-bangsa.sch.id','Jl. Kaliurang Km. 12, Sleman, DIY','smkcendekiabangsa','pass1234','smk-cendekia-bangsa.png'],
            ['SMK Kreatif Mandiri','20400003','info03@smk-kreatif-mandiri.sch.id','(0274) 61003','https://smk-kreatif-mandiri.sch.id','Jl. Cangkringan No.15, Sleman, DIY','smkkreatifmandiri','pass1236','smk-kreatif-mandiri.png'],
            ['SMK Harapan Sejahtera','20400004','info04@smk-harapan-sejahtera.sch.id','0857-9000-0010','https://smk-harapan-sejahtera.sch.id','Jl. Wates Km. 5, Bantul, DIY','smkharapansejahtera','pass1237','smk-harapan-sejahtera.png'],
            ['SMK Bina Utama Sleman','20400005','info05@smk-bina-utama-sleman.sch.id','(0274) 61005','https://smk-bina-utama-sleman.sch.id','Jl. Godean Km. 7, Sleman, DIY','smkbinautama','pass1237','smk-bina-utama-sleman.png'],
            ['SMK Pelita Bantul','20400006','info06@smk-pelita-bantul.sch.id','0813-2211-0006','https://smk-pelita-bantul.sch.id','Jl. Parangtritis Km. 10, Bantul, DIY','smkpelitabantul','pass1238','smk-pelita-bantul.png'],
            ['SMK Purnama Imogiri','20400007','info07@smk-purnama-imogiri.sch.id','(0274) 61007','https://smk-purnama-imogiri.sch.id','Jl. Imogiri Timur No. 21, Bantul, DIY','smkpurnamaimogiri','pass1239','smk-purnama-imogiri.png'],
            ['SMK Sinar Abadi Kulon Progo','20400008','info08@smk-sinar-abadi-kulon-progo.sch.id','0821-3344-0008','https://smk-sinar-abadi-kulon-progo.sch.id','Jl. Wates No. 33, Kulon Progo, DIY','smksinarabadikp','pass1240','smk-sinar-abadi-kulon-progo.png'],
            ['SMK Bhakti Mulia Wonosari','20400009','info09@smk-bhakti-mulia-wonosari.sch.id','(0274) 61009','https://smk-bhakti-mulia-wonosari.sch.id','Jl. Baron Km. 3, Wonosari, Gunungkidul, DIY','smkbhaktimulia','pass1241','smk-bhakti-mulia-wonosari.png'],
            ['SMK Karya Mandiri Gunungkidul','20400010','info10@smk-karya-mandiri-gunungkidul.sch.id','0819-4455-0010','https://smk-karya-mandiri-gunungkidul.sch.id','Jl. Karangmojo No. 8, Gunungkidul, DIY','smkkaryamandiri','pass1242','smk-karya-mandiri-gunungkidul.png'],
            ['SMK Insan Cemerlang','20400011','info11@smk-insan-cemerlang.sch.id','(0274) 61011','https://smk-insan-cemerlang.sch.id','Jl. Kusumanegara No. 14, Kota Yogyakarta, DIY','smkinsancemerlang','pass1243','smk-insan-cemerlang.png'],
            ['SMK Generasi Maju','20400012','info12@smk-generasi-maju.sch.id','0822-5566-0012','https://smk-generasi-maju.sch.id','Jl. Maguwoharjo No. 5, Sleman, DIY','smkgenerasimaju','pass1244','smk-generasi-maju.png'],
            ['SMK Mitra Prestasi','20400013','info13@smk-mitra-prestasi.sch.id','(0274) 61013','https://smk-mitra-prestasi.sch.id','Jl. Bantul Km. 9, Bantul, DIY','smkmitraprestasi','pass1245','smk-mitra-prestasi.png'],
            ['SMK Cahaya Bangsa','20400014','info14@smk-cahaya-bangsa.sch.id','0812-6677-0014','https://smk-cahaya-bangsa.sch.id','Jl. Ringroad Utara No. 18, Sleman, DIY','smkcahayabangsa','pass1246','smk-cahaya-bangsa.png'],
            ['SMK Global Mandiri','20400015','info15@smk-global-mandiri.sch.id','(0274) 61015','https://smk-global-mandiri.sch.id','Jl. Malioboro No. 22, Kota Yogyakarta, DIY','smkglobalmandiri','pass1247','smk-global-mandiri.png'],
            ['SMK Wijaya Kusuma','20400016','info16@smk-wijaya-kusuma.sch.id','0858-7788-0016','https://smk-wijaya-kusuma.sch.id','Jl. Prambanan No. 8, Sleman, DIY','smkwijayakusuma','pass1248','smk-wijaya-kusuma.png'],
            ['SMK Lestari Bangsa','20400017','info17@smk-lestari-bangsa.sch.id','(0274) 61017','https://smk-lestari-bangsa.sch.id','Jl. Sentolo No. 17, Kulon Progo, DIY','smklestaribangsa','pass1249','smk-lestari-bangsa.png'],
            ['SMK Sentosa Mulia','20400018','info18@smk-sentosa-mulia.sch.id','0813-8899-0018','https://smk-sentosa-mulia.sch.id','Jl. Depok No. 11, Sleman, DIY','smksentosamulia','pass1250','smk-sentosa-mulia.png'],
            ['SMK Prima Mandiri','20400019','info19@smk-prima-mandiri.sch.id','(0274) 61019','https://smk-prima-mandiri.sch.id','Jl. Parangtritis No. 40, Bantul, DIY','smkprimamandiri','pass1251','smk-prima-mandiri.png'],
            ['SMK Tunas Harapan','20400020','info20@smk-tunas-harapan.sch.id','0821-9900-0020','https://smk-tunas-harapan.sch.id','Jl. Wonosari Km. 7, Gunungkidul, DIY','smktunasharapan','pass1252','smk-tunas-harapan.png'],
            ['SMK Citra Nusantara','20400021','info21@smk-citra-nusantara.sch.id','(0274) 61021','https://smk-citra-nusantara.sch.id','Jl. Wirobrajan No. 9, Kota Yogyakarta, DIY','smkcitranusantara','pass1253','smk-citra-nusantara.png'],
            ['SMK Pandu Bangsa','20400022','info22@smk-pandu-bangsa.sch.id','0812-1112-0022','https://smk-pandu-bangsa.sch.id','Jl. Berbah No. 4, Sleman, DIY','smkpandubangsa','pass1254','smk-pandu-bangsa.png'],
            ['SMK Anugerah Sejahtera','20400023','info23@smk-anugerah-sejahtera.sch.id','(0274) 61023','https://smk-anugerah-sejahtera.sch.id','Jl. Panjatan No. 12, Kulon Progo, DIY','smkanugerahsejahtera','pass1255','smk-anugerah-sejahtera.png'],
            ['SMK Gemilang Jaya','20400024','info24@smk-gemilang-jaya.sch.id','0857-1314-0024','https://smk-gemilang-jaya.sch.id','Jl. Sewon No. 21, Bantul, DIY','smkgemilangjaya','pass1256','smk-gemilang-jaya.png'],
            ['SMK Maju Bersama','20400025','info25@smk-maju-bersama.sch.id','(0274) 61025','https://smk-maju-bersama.sch.id','Jl. Depok Timur No. 30, Sleman, DIY','smkmajubersama','pass1257','smk-maju-bersama.png'],
            ['SMK Inovasi Digital','20400026','info26@smk-inovasi-digital.sch.id','0812-3000-0026','https://smk-inovasi-digital.sch.id','Jl. Affandi No. 10, Sleman, DIY','smkinovasidigital','pass1258','smk-inovasi-digital.png'],
            ['SMK Teknologi Kreatif','20400027','info27@smk-teknologi-kreatif.sch.id','0821-3000-0027','https://smk-teknologi-kreatif.sch.id','Jl. Maguwoharjo No. 17, Sleman, DIY','smkteknologikreatif','pass1259','smk-teknologi-kreatif.png'],
            ['SMK Sains Terapan','20400028','info28@smk-sains-terapan.sch.id','(0274) 61028','https://smk-sains-terapan.sch.id','Jl. Kaliurang Km. 14, Sleman, DIY','smksainsterapan','pass1260','smk-sains-terapan.png'],
            ['SMK Agro Mandala','20400029','info29@smk-agro-mandala.sch.id','0858-4455-0029','https://smk-agro-mandala.sch.id','Jl. Wates Km. 12, Kulon Progo, DIY','smkagromandala','pass1261','smk-agro-mandala.png'],
            ['SMK Maritim Bahari','20400030','info30@smk-maritim-bahari.sch.id','0813-5566-0030','https://smk-maritim-bahari.sch.id','Jl. Pelabuhan No. 2, Kulon Progo, DIY','smkmaritimbahari','pass1262','smk-maritim-bahari.png'],
            ['SMK Energi Terbarukan','20400031','info31@smk-energi-terbarukan.sch.id','(0274) 61031','https://smk-energi-terbarukan.sch.id','Jl. Imogiri Barat No. 5, Bantul, DIY','smkenergiterbarukan','pass1263','smk-energi-terbarukan.png'],
            ['SMK Desain Mode','20400032','info32@smk-desain-mode.sch.id','0822-6677-0032','https://smk-desain-mode.sch.id','Jl. Malioboro No. 50, Kota Yogyakarta, DIY','smkdesainmode','pass1264','smk-desain-mode.png'],
            ['SMK Kesehatan Mandiri','20400033','info33@smk-kesehatan-mandiri.sch.id','0857-7788-0033','https://smk-kesehatan-mandiri.sch.id','Jl. Gejayan No. 9, Sleman, DIY','smkkesehatanmandiri','pass1265','smk-kesehatan-mandiri.png'],
            ['SMK Logistik Nusantara','20400034','info34@smk-logistik-nusantara.sch.id','0819-8899-0034','https://smk-logistik-nusantara.sch.id','Jl. Ringroad Selatan No. 4, Bantul, DIY','smklogistiknusantara','pass1266','smk-logistik-nusantara.png'],
            ['SMK Animasi Kreatif','20400035','info35@smk-animasi-kreatif.sch.id','(0274) 61035','https://smk-animasi-kreatif.sch.id','Jl. Parangtritis Km. 6, Bantul, DIY','smkanimasikreatif','pass1267','smk-animasi-kreatif.png'],
            ['SMK Teknik Otomotif','20400036','info36@smk-teknik-otomotif.sch.id','0812-9900-0036','https://smk-teknik-otomotif.sch.id','Jl. Solo Km. 8, Sleman, DIY','smkteknikotomotif','pass1268','smk-teknik-otomotif.png'],
            ['SMK Pariwisata Jogja','20400037','info37@smk-pariwisata-jogja.sch.id','0821-9900-0037','https://smk-pariwisata-jogja.sch.id','Jl. Jend. Sudirman No. 22, Kota Yogyakarta, DIY','smkpariwisatajogja','pass1269','smk-pariwisata-jogja.png'],
            ['SMK Pertanian Lestari','20400038','info38@smk-pertanian-lestari.sch.id','(0274) 61038','https://smk-pertanian-lestari.sch.id','Jl. Patuk No. 7, Gunungkidul, DIY','smkpertanianlestari','pass1270','smk-pertanian-lestari.png'],
            ['SMK Telekomunikasi Nusantara','20400039','info39@smk-telekomunikasi-nusantara.sch.id','0858-1122-0039','https://smk-telekomunikasi-nusantara.sch.id','Jl. Depok No. 25, Sleman, DIY','smktelekomunikasinusantara','pass1271','smk-telekomunikasi-nusantara.png'],
            ['SMK Keuangan Sejahtera','20400040','info40@smk-keuangan-sejahtera.sch.id','0813-2233-0040','https://smk-keuangan-sejahtera.sch.id','Jl. Wates No. 44, Kulon Progo, DIY','smkkeuangansejahtera','pass1272','smk-keuangan-sejahtera.png'],
        ];

        $created=0; $updated=0; $now=now();

        // Daftar file logo yang tersedia di folder public/
        $defaultLogos = [
            'logo-mavoka.png',
            'logo-m.png',
            'logo-fit-academy.png',
        ];
        $logoCount = count($defaultLogos);
        $rowIndex = 0;

        // Default list jurusan (akan dipakai jika sekolah belum punya daftar sendiri)
        $defaultJurusan = [
            'Teknik Komputer dan Jaringan',
            'Rekayasa Perangkat Lunak',
            'Multimedia',
            'Teknik Elektronika Industri',
            'Akuntansi',
            'Perbankan',
            'Bisnis Daring dan Pemasaran'
        ];

        $customJurusanMap = [
        ];
        foreach($rows as $row) {
            if (count($row) < 8) {
                $this->command?->warn('Baris sekolah tidak valid (kolom kurang), dilewati: '.json_encode($row));
                continue;
            }
            // Support 8 kolom (tanpa logo) atau 9 kolom (dengan logo khusus)
            [$nama,$npsn,$email,$kontak,$web,$alamat,$username,$plainPass] = array_slice($row,0,8);
            $explicitLogo = $row[8] ?? null;
            // Tentukan logo:
            $slug = Str::slug($nama);
            $logoFile = null;
            if ($explicitLogo) {
                // Jika user sudah memberi nama file. Jika mengandung '/' anggap path relatif siap pakai.
                $logoCandidate = strpos($explicitLogo,'/') !== false ? $explicitLogo : 'logos/sekolah/'.$explicitLogo;
                $logoFile = file_exists(public_path($logoCandidate)) ? $logoCandidate : null;
            }
            if (!$logoFile) {
                $customLogoPath = 'logos/sekolah/'.$slug.'.png';
                $logoFile = file_exists(public_path($customLogoPath))
                    ? $customLogoPath
                    : $defaultLogos[$rowIndex % $logoCount];
            }
            
            if (isset($customJurusanMap[$slug])) {
                $jurusanArray = $customJurusanMap[$slug];
            } else {
                $shuffled = $defaultJurusan;
                shuffle($shuffled);
                $pickCount = rand(3, min(count($shuffled), 6));
                $jurusanArray = array_slice($shuffled, 0, $pickCount);
            }

            $data = [
                'nama_sekolah' => $nama,
                'npsn' => $npsn,
                'email' => $email,
                'kontak' => $kontak,
                'web_sekolah' => $web,
                'logo_sekolah' => $logoFile,
                'alamat' => $alamat,
                'username' => $username,
                'password' => Hash::make($plainPass),
                'jurusan' => $jurusanArray,
                'status_verifikasi' => 'Terverifikasi',
                'tanggal_verifikasi' => $now,
                'updated_at' => $now,
            ];

            $existing = Sekolah::where('npsn',$npsn)->first();
            if ($existing) {
                // Jika sudah ada logo khusus sebelumnya, jangan timpa kecuali kosong
                if (!empty($existing->logo_sekolah) && $existing->logo_sekolah !== $data['logo_sekolah']) {
                    $data['logo_sekolah'] = $existing->logo_sekolah; // retain existing custom logo
                }
                // Jangan timpa jurusan jika sudah ada dan tidak kosong
                if (!empty($existing->jurusan) && is_array($existing->jurusan) && count($existing->jurusan) > 0) {
                    unset($data['jurusan']);
                }
                $existing->fill($data)->save();
                $updated++;
            } else {
                $data['created_at']=$now; Sekolah::create($data); $created++;
            }

            $rowIndex++;
        }

        $this->command?->info("Seed sekolah statis selesai: created=$created updated=$updated");
    }
}
