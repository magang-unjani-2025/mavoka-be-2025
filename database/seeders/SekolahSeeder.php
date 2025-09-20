<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sekolah;
use Illuminate\Support\Facades\Hash;

class SekolahSeeder extends Seeder
{
    public function run(): void
    {
    // Data statis dari gambar: nama_sekolah, npsn, email, kontak, web_sekolah, alamat, username, password
    // Kolom 'jurusan' sementara dihapus (tidak ada di tabel). Jika ingin dikembalikan:
    // 1) Tambah migration kolom jurusan
    // 2) Sisipkan nilai jurusan di setiap baris sebelum alamat
        $rows = [
            ['SMK Teknologi Nusantara','20400001','info01@smk-teknologi-nusantara.sch.id','(0274) 61001','https://smk-teknologi-nusantara.sch.id','Jl. Magelang Km. 8, Sleman, DIY','smkteknologinusantara','pass1234'],
            ['SMK Cendekia Bangsa','20400002','info02@smk-cendekia-bangsa.sch.id','0812-3456-0001','https://smk-cendekia-bangsa.sch.id','Jl. Kaliurang Km. 12, Sleman, DIY','smkcendekiabangsa','pass1234'],
            ['SMK Kreatif Mandiri','20400003','info03@smk-kreatif-mandiri.sch.id','(0274) 61003','https://smk-kreatif-mandiri.sch.id','Jl. Cangkringan No.15, Sleman, DIY','smkkreatifmandiri','pass1236'],
            ['SMK Harapan Sejahtera','20400004','info04@smk-harapan-sejahtera.sch.id','0857-9000-0010','https://smk-harapan-sejahtera.sch.id','Jl. Wates Km. 5, Bantul, DIY','smkharapansejahtera','pass1237'],
            ['SMK Bina Utama Sleman','20400005','info05@smk-bina-utama-sleman.sch.id','(0274) 61005','https://smk-bina-utama-sleman.sch.id','Jl. Godean Km. 7, Sleman, DIY','smkbinautama','pass1237'],
            ['SMK Pelita Bantul','20400006','info06@smk-pelita-bantul.sch.id','0813-2211-0006','https://smk-pelita-bantul.sch.id','Jl. Parangtritis Km. 10, Bantul, DIY','smkpelitabantul','pass1238'],
            ['SMK Purnama Imogiri','20400007','info07@smk-purnama-imogiri.sch.id','(0274) 61007','https://smk-purnama-imogiri.sch.id','Jl. Imogiri Timur No. 21, Bantul, DIY','smkpurnamaimogiri','pass1239'],
            ['SMK Sinar Abadi Kulon Progo','20400008','info08@smk-sinar-abadi-kulon-progo.sch.id','0821-3344-0008','https://smk-sinar-abadi-kulon-progo.sch.id','Jl. Wates No. 33, Kulon Progo, DIY','smksinarabadikp','pass1240'],
            ['SMK Bhakti Mulia Wonosari','20400009','info09@smk-bhakti-mulia-wonosari.sch.id','(0274) 61009','https://smk-bhakti-mulia-wonosari.sch.id','Jl. Baron Km. 3, Wonosari, Gunungkidul, DIY','smkbhaktimulia','pass1241'],
            ['SMK Karya Mandiri Gunungkidul','20400010','info10@smk-karya-mandiri-gunungkidul.sch.id','0819-4455-0010','https://smk-karya-mandiri-gunungkidul.sch.id','Jl. Karangmojo No. 8, Gunungkidul, DIY','smkkaryamandiri','pass1242'],
            ['SMK Insan Cemerlang','20400011','info11@smk-insan-cemerlang.sch.id','(0274) 61011','https://smk-insan-cemerlang.sch.id','Jl. Kusumanegara No. 14, Kota Yogyakarta, DIY','smkinsancemerlang','pass1243'],
            ['SMK Generasi Maju','20400012','info12@smk-generasi-maju.sch.id','0822-5566-0012','https://smk-generasi-maju.sch.id','Jl. Maguwoharjo No. 5, Sleman, DIY','smkgenerasimaju','pass1244'],
            ['SMK Mitra Prestasi','20400013','info13@smk-mitra-prestasi.sch.id','(0274) 61013','https://smk-mitra-prestasi.sch.id','Jl. Bantul Km. 9, Bantul, DIY','smkmitraprestasi','pass1245'],
            ['SMK Cahaya Bangsa','20400014','info14@smk-cahaya-bangsa.sch.id','0812-6677-0014','https://smk-cahaya-bangsa.sch.id','Jl. Ringroad Utara No. 18, Sleman, DIY','smkcahayabangsa','pass1246'],
            ['SMK Global Mandiri','20400015','info15@smk-global-mandiri.sch.id','(0274) 61015','https://smk-global-mandiri.sch.id','Jl. Malioboro No. 22, Kota Yogyakarta, DIY','smkglobalmandiri','pass1247'],
            ['SMK Wijaya Kusuma','20400016','info16@smk-wijaya-kusuma.sch.id','0858-7788-0016','https://smk-wijaya-kusuma.sch.id','Jl. Prambanan No. 8, Sleman, DIY','smkwijayakusuma','pass1248'],
            ['SMK Lestari Bangsa','20400017','info17@smk-lestari-bangsa.sch.id','(0274) 61017','https://smk-lestari-bangsa.sch.id','Jl. Sentolo No. 17, Kulon Progo, DIY','smklestaribangsa','pass1249'],
            ['SMK Sentosa Mulia','20400018','info18@smk-sentosa-mulia.sch.id','0813-8899-0018','https://smk-sentosa-mulia.sch.id','Jl. Depok No. 11, Sleman, DIY','smksentosamulia','pass1250'],
            ['SMK Prima Mandiri','20400019','info19@smk-prima-mandiri.sch.id','(0274) 61019','https://smk-prima-mandiri.sch.id','Jl. Parangtritis No. 40, Bantul, DIY','smkprimamandiri','pass1251'],
            ['SMK Tunas Harapan','20400020','info20@smk-tunas-harapan.sch.id','0821-9900-0020','https://smk-tunas-harapan.sch.id','Jl. Wonosari Km. 7, Gunungkidul, DIY','smktunasharapan','pass1252'],
            ['SMK Citra Nusantara','20400021','info21@smk-citra-nusantara.sch.id','(0274) 61021','https://smk-citra-nusantara.sch.id','Jl. Wirobrajan No. 9, Kota Yogyakarta, DIY','smkcitranusantara','pass1253'],
            ['SMK Pandu Bangsa','20400022','info22@smk-pandu-bangsa.sch.id','0812-1112-0022','https://smk-pandu-bangsa.sch.id','Jl. Berbah No. 4, Sleman, DIY','smkpandubangsa','pass1254'],
            ['SMK Anugerah Sejahtera','20400023','info23@smk-anugerah-sejahtera.sch.id','(0274) 61023','https://smk-anugerah-sejahtera.sch.id','Jl. Panjatan No. 12, Kulon Progo, DIY','smkanugerahsejahtera','pass1255'],
            ['SMK Gemilang Jaya','20400024','info24@smk-gemilang-jaya.sch.id','0857-1314-0024','https://smk-gemilang-jaya.sch.id','Jl. Sewon No. 21, Bantul, DIY','smkgemilangjaya','pass1256'],
            ['SMK Maju Bersama','20400025','info25@smk-maju-bersama.sch.id','(0274) 61025','https://smk-maju-bersama.sch.id','Jl. Depok Timur No. 30, Sleman, DIY','smkmajubersama','pass1257'],
        ];

        $created=0; $updated=0; $now=now();
        foreach($rows as $row) {
            if (count($row) !== 8) {
                $this->command?->warn('Baris sekolah tidak valid (jumlah kolom != 8), dilewati: '.json_encode($row));
                continue;
            }
            [$nama,$npsn,$email,$kontak,$web,$alamat,$username,$plainPass] = $row;
            $data = [
                'nama_sekolah' => $nama,
                'npsn' => $npsn,
                'email' => $email,
                'kontak' => $kontak,
                'web_sekolah' => $web,
                'alamat' => $alamat,
                'username' => $username,
                'password' => Hash::make($plainPass),
                'status_verifikasi' => 'Terverifikasi',
                'tanggal_verifikasi' => $now,
                'updated_at' => $now,
            ];

            $existing = Sekolah::where('npsn',$npsn)->first();
            if ($existing) { $existing->fill($data)->save(); $updated++; }
            else { $data['created_at']=$now; Sekolah::create($data); $created++; }
        }

        $this->command?->info("Seed sekolah statis selesai: created=$created updated=$updated");
    }
}
