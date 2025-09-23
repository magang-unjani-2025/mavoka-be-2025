<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\LembagaPelatihan;

class LembagaSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['nexatech_academy','info@nexatech.id','nexa1234','Terverifikasi','2024-03-15','LPK NexaTech Academy','Web Development, Data Science, Cloud Computing','LPK NexaTech Academy hadir untuk mengembangkan talenta digital masa depan','https://nexatech.id','Jl. Dipatiukur N  Azure','872349865733','A'],
            ['brightskill_center','contact@brightskill.com','bright!23','Terverifikasi','2024-05-20','LPK BrightSkill Center','Komunikasi Efektif, Public Speaking, Leadership','BrightSkill Center berfokus pada pengembangan soft-skill profesional','https://brightskill.com','Jl. Ahmad Yani 327879456712','A'],
            ['unigama_biz','admin@unigama-biz.com','unigama55','Terverifikasi','2024-05-22','LPK Unigama Business Institute','Akuntansi, Administrasi Perkantoran, Manajemen','Unigama Business Institute menawarkan program bisnis praktis','https://unigama-biz.com','Jl. Kaliurang K 878345671230','B'],
            ['asahi_lang_inst','info@asahi-lang.jp','asahi12','Terverifikasi','2024-04-11','LPK Asahi Language Institute','Bahasa Jepang, JLPT Preparation, Budaya Kerja','Asahi Language Institute mengedepankan kurikulum adaptif','https://asahi-lang.jp','Jl. Cendana N 813777889234','A'],
            ['digicreative_hub','support@digicreativehub.com','digi1234','Terverifikasi','2024-06-02','LPK Digital Creative Hub','Desain Grafis, Animasi 2D, Animasi 3D, Multimedia','Digital Creative Hub menawarkan program kreatif industri','https://digicreativehub.com','Jl. Anggrek No 85298761234','A'],
            ['automech_training','admin@automech.co.id','auto789','Terverifikasi','2024-05-29','LPK AutoMech English Academy','Bahasa Inggris, TOEFL Preparation, IELTS Preparation','AutoMech Academy menekankan praktik bahasa modern','https://automech.co.id','Jl. Gatot Subro 821786974512','B'],
            ['grandtourism_school','info@grandtourism.com','grand123','Terverifikasi','2024-05-07','LPK GrandTourism School','Pariwisata, Perhotelan, Tata Boga, Hospitality Management','GrandTourism School menyediakan kurikulum hospitality internasional','https://grandtourism.com','Jl. Merdeka No 812736451278','A'],
            ['visualart_academy','contact@visualartacademy.org','visual12','Terverifikasi','2024-04-21','LPK VisualArt Academy','Seni Rupa, Fotografi, Animasi Digital, Desain Karakter','VisualArt Academy membina peserta dalam seni visual modern','https://visualartacademy.org','Jl. Braga No. 3 812578984612','A'],
            ['electrolab_center','admin@electrolab.id','electro1','Terverifikasi','2024-08-05','LPK ElectroLab Skill Center','Teknik Elektro, Listrik Industri, Mekatronika, Instrumentasi','ElectroLab Skill Center fokus pada skill engineering terapan','https://electrolab.id','Jl. Ahmad Dah 821789254123','A'],
            ['financia_academy','info@financiaacademy.com','financi1','Terverifikasi','2024-06-30','LPK Financia Academy','Akuntansi & Keuangan','Financia Academy menyediakan kursus berbasis praktik','https://financiaacademy.com','Jl. Diponegoro 821987654322','B'],
            ['medialab_institute','admin@medialab.co.id','medialab1','Terverifikasi','2024-03-22','LPK MediaLab Institute','Kesehatan & Laboratorium Klinik','MediaLab Institute menyelenggarakan program lab modern','https://medialab.co.id','Jl. Kesehatan 821878654322','A'],
            ['agrofield_training','info@agrofield.id','agro1234','Terverifikasi','2024-07-14','LPK AgroField Training Center','Pertanian & Agroteknologi','AgroField Training Center berfokus pada pertanian modern','https://agrofield.id','Jl. Raya Utama 821234567890','B'],
            ['buildmaster_academy','support@buildmaster.com','build123','Terverifikasi','2024-06-04','LPK BuildMaster Academy','Konstruksi & Teknik Sipil','BuildMaster Academy menghadirkan pelatihan proyek nyata','https://buildmaster.com','Jl. Sudirman N 812456789001','C'],
            ['greenenergy_center','admin@greenenergy.id','green123','Terverifikasi','2024-05-08','LPK Green Energy Skill Center','Energi Terbarukan','Green Energy Skill Center mendukung transisi energi','https://greenenergy.id','Jl. Merdeka No 821567899120','A'],
            ['cybersecure_institute','info@cybersecure.id','cyber123','Terverifikasi','2024-04-08','LPK CyberSecure Institute','Keamanan Siber & Forensik Digital','CyberSecure Institute fokus pada bidang keamanan informasi','https://cybersecure.id','Jl. Veteran No 821678990123','A'],
            ['fashionista_academy','admin@fashionista.com','fashion1','Terverifikasi','2024-06-28','LPK Fashionista Academy','Fashion Design & Tata Busana','Fashionista Academy mengajarkan keterampilan busana modern','https://fashionista.com','Jl. Asia Afrika 821789902134','A'],
            ['globalchef_institute','support@globalchef.com','global12','Terverifikasi','2024-05-25','LPK GlobalChef Institute','Tata Boga & Kuliner','GlobalChef Institute mengedepankan praktik dapur profesional','https://globalchef.com','Jl. Kusumane 821890123454','A'],
            ['logispro_training','admin@logispro.id','logispro','Terverifikasi','2024-07-21','LPK LogisPro Training Center','Logistik & Supply Chain','LogisPro Training Center menyiapkan tenaga logistik kompeten','https://logispro.id','Jl. Raya Pelab 821987654322','B'],
            ['smartedu_center','info@smartedu.id','smartedu','Terverifikasi','2024-05-15','LPK SmartEdu Training Center','Pendidikan & Pelatihan Guru','SmartEdu Training Center mendukung kompetensi pendidikan','https://smartedu.id','Jl. Pendidikan 821789254321','A'],
            ['startup_hub','contact@startuphub.com','startup1','Terverifikasi','2024-06-01','LPK StartUp Hub Academy','Kewirausahaan & Startup','StartUp Hub Academy hadir untuk membina wirausahawan baru','https://startuphub.com','Jl. Raya ITC N 821345678901','B'],
            ['healthcare_training','info@healthcare.id','healthcare','Terverifikasi','2024-03-30','LPK HealthCare Training Center','Perawat & Tenaga Medis','HealthCare Training Center mengembangkan tenaga kesehatan','https://healthcare.id','Jl. Kesehatan 1 821987432410','A'],
            ['techwork_academy','support@techwork.com','techwork','Terverifikasi','2024-08-15','LPK TechWork Academy','Teknologi Industri & Manufaktur','TechWork Academy berfokus pada pelatihan manufaktur modern','https://techwork.com','Jl. Industri No 821456789701','A'],
            ['agritech_academy','info@agritechacademy.com','agritech','Terverifikasi','2024-07-18','LPK AgriTech Academy','Pertanian Modern & Agribisnis','AgriTech Academy berfokus pada pelatihan agribisnis inovatif','https://agritechacademy.com','Jl. Raya Tlogi 821876543210','A'],
            ['maritim_skillcenter','support@maritimskills.id','maritim1','Terverifikasi','2024-08-02','LPK Maritim Skill Center','Kelautan & Perkapalan','Maritim Skill Center menyediakan pelatihan maritim profesional','https://maritimskills.id','Jl. Pelabuhan 1 852133245678','B'],
        ];

        $created=0; $updated=0; $now=now();
        foreach ($rows as $row) {
            $colCount = count($row);
            if (!in_array($colCount,[11,12])) {
                $this->command?->warn('Baris lembaga tidak valid (kolom != 11/12), dilewati: '.json_encode($row));
                continue;
            }
            if ($colCount === 12) {
                [$username,$email,$plainPass,$status,$tglVerif,$nama,$bidang,$deskripsi,$web,$alamat,$kontak,$akredit] = $row;
            } else { // 11 kolom -> alamat+kontak digabung, format contoh: 'Jl. Pelabuhan 1 852133245678'
                [$username,$email,$plainPass,$status,$tglVerif,$nama,$bidang,$deskripsi,$web,$alamatRaw,$akredit] = $row;
                // Ambil token terakhir yang panjangnya >=8 dan berisi digit sebagai kontak
                $parts = preg_split('/\s+/', trim($alamatRaw));
                $kontak = null;
                if ($parts) {
                    $last = $parts[count($parts)-1];
                    if (preg_match('/^\+?[0-9()\-]{8,}$/',$last)) {
                        $kontak = $last;
                        array_pop($parts);
                        $alamat = implode(' ', $parts);
                    } else {
                        $alamat = $alamatRaw;
                        $kontak = '-';
                    }
                } else {
                    $alamat = $alamatRaw; $kontak='-';
                }
            }
            // Set logo otomatis berdasarkan username
            $logo = $username . '.png';
            $data = [
                'username' => $username,
                'email' => $email,
                'password' => Hash::make($plainPass),
                'status_verifikasi' => $status,
                'tanggal_verifikasi' => $tglVerif ?: $now,
                'nama_lembaga' => $nama,
                'bidang_pelatihan' => $bidang,
                'deskripsi_lembaga' => $deskripsi,
                'web_lembaga' => $web,
                'alamat' => $alamat,
                'kontak' => $kontak,
                // Sesuaikan folder ke 'lembaga-pelatihan' (folder aktual di public/logos)
                'logo_lembaga' => 'logos/lembaga-pelatihan/' . $logo,
                'status_akreditasi' => $akredit,
                'otp' => Str::random(6),
                'otp_expired_at' => $now->copy()->addMinutes(10),
                'updated_at' => $now,
            ];

            $existing = LembagaPelatihan::where('email',$email)->first();
            if(!$existing) { $existing = LembagaPelatihan::where('nama_lembaga',$nama)->first(); }
            if ($existing) { $existing->fill($data)->save(); $updated++; }
            else { $data['created_at']=$now; LembagaPelatihan::create($data); $created++; }
        }

        $this->command?->info("Seed lembaga pelatihan statis selesai: created=$created updated=$updated");
    }
}
