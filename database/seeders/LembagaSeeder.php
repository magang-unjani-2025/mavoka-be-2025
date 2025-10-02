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
            ['nexatech_academy', 'nexatech_academy@gmail.com', 'nexa1234', 'Terverifikasi', '2024-03-15', 'Nexatech Academy', 'Web Development, Data Science, Cloud Computing', 'LPK NexaTech Academy hadir untuk mengembangkan talenta digital masa depan', 'https://nexatech.id', 'Jl. Dipatiukur N  Azure', '872349865733', 'A'],
            ['brightskill_center', 'brightskill_center@gmail.com', 'bright!23', 'Terverifikasi', '2024-05-20', 'Brightskill Center', 'Komunikasi Efektif, Public Speaking, Leadership', 'BrightSkill Center berfokus pada pengembangan soft-skill profesional', 'https://brightskill.com', 'Jl. Ahmad Yani 327879456712', 'A'],
            ['unigama_biz', 'unigama_biz@gmail.com', 'unigama55', 'Terverifikasi', '2024-05-22', 'Unigama Biz', 'Akuntansi, Administrasi Perkantoran, Manajemen', 'Unigama Business Institute menawarkan program bisnis praktis', 'https://unigama-biz.com', 'Jl. Kaliurang K 878345671230', 'B'],
            ['asahi_lang_inst', 'asahi_lang_inst@gmail.com', 'asahi12', 'Terverifikasi', '2024-04-11', 'Asahi Lang Inst', 'Bahasa Jepang, JLPT Preparation, Budaya Kerja', 'Asahi Language Institute mengedepankan kurikulum adaptif', 'https://asahi-lang.jp', 'Jl. Cendana N 813777889234', 'A'],
            ['digicreative_hub', 'digicreative_hub@gmail.com', 'digi1234', 'Terverifikasi', '2024-06-02', 'Digicreative Hub', 'Desain Grafis, Animasi 2D, Animasi 3D, Multimedia', 'Digital Creative Hub menawarkan program kreatif industri', 'https://digicreativehub.com', 'Jl. Anggrek No 85298761234', 'A'],
            ['automech_training', 'automech_training@gmail.com', 'auto789', 'Terverifikasi', '2024-05-29', 'Automech Training', 'Bahasa Inggris, TOEFL Preparation, IELTS Preparation', 'AutoMech Academy menekankan praktik bahasa modern', 'https://automech.co.id', 'Jl. Gatot Subro 821786974512', 'B'],
            ['grandtourism_school', 'grandtourism_school@gmail.com', 'grand123', 'Terverifikasi', '2024-05-07', 'Grandtourism School', 'Pariwisata, Perhotelan, Tata Boga, Hospitality Management', 'GrandTourism School menyediakan kurikulum hospitality internasional', 'https://grandtourism.com', 'Jl. Merdeka No 812736451278', 'A'],
            ['visualart_academy', 'visualart_academy@gmail.com', 'visual12', 'Terverifikasi', '2024-04-21', 'Visualart Academy', 'Seni Rupa, Fotografi, Animasi Digital, Desain Karakter', 'VisualArt Academy membina peserta dalam seni visual modern', 'https://visualartacademy.org', 'Jl. Braga No. 3 812578984612', 'A'],
            ['electrolab_center', 'electrolab_center@gmail.com', 'electro1', 'Terverifikasi', '2024-08-05', 'Electrolab Center', 'Teknik Elektro, Listrik Industri, Mekatronika, Instrumentasi', 'ElectroLab Skill Center fokus pada skill engineering terapan', 'https://electrolab.id', 'Jl. Ahmad Dah 821789254123', 'A'],
            ['financia_academy', 'financia_academy@gmail.com', 'financi1', 'Terverifikasi', '2024-06-30', 'Financia Academy', 'Akuntansi & Keuangan', 'Financia Academy menyediakan kursus berbasis praktik', 'https://financiaacademy.com', 'Jl. Diponegoro 821987654322', 'B'],
            ['medialab_institute', 'medialab_institute@gmail.com', 'medialab1', 'Terverifikasi', '2024-03-22', 'Medialab Institute', 'Kesehatan & Laboratorium Klinik', 'MediaLab Institute menyelenggarakan program lab modern', 'https://medialab.co.id', 'Jl. Kesehatan 821878654322', 'A'],
            ['agrofield_training', 'agrofield_training@gmail.com', 'agro1234', 'Terverifikasi', '2024-07-14', 'Agrofield Training', 'Pertanian & Agroteknologi', 'AgroField Training Center berfokus pada pertanian modern', 'https://agrofield.id', 'Jl. Raya Utama 821234567890', 'B'],
            ['buildmaster_academy', 'buildmaster_academy@gmail.com', 'build123', 'Terverifikasi', '2024-06-04', 'Buildmaster Academy', 'Konstruksi & Teknik Sipil', 'BuildMaster Academy menghadirkan pelatihan proyek nyata', 'https://buildmaster.com', 'Jl. Sudirman N 812456789001', 'C'],
            ['greenenergy_center', 'greenenergy_center@gmail.com', 'green123', 'Terverifikasi', '2024-05-08', 'Greenenergy Center', 'Energi Terbarukan', 'Green Energy Skill Center mendukung transisi energi', 'https://greenenergy.id', 'Jl. Merdeka No 821567899120', 'A'],
            ['cybersecure_institute', 'cybersecure_institute@gmail.com', 'cyber123', 'Terverifikasi', '2024-04-08', 'Cybersecure Institute', 'Keamanan Siber & Forensik Digital', 'CyberSecure Institute fokus pada bidang keamanan informasi', 'https://cybersecure.id', 'Jl. Veteran No 821678990123', 'A'],
            ['fashionista_academy', 'fashionista_academy@gmail.com', 'fashion1', 'Terverifikasi', '2024-06-28', 'Fashionista Academy', 'Fashion Design & Tata Busana', 'Fashionista Academy mengajarkan keterampilan busana modern', 'https://fashionista.com', 'Jl. Asia Afrika 821789902134', 'A'],
            ['globalchef_institute', 'globalchef_institute@gmail.com', 'global12', 'Terverifikasi', '2024-05-25', 'Globalchef Institute', 'Tata Boga & Kuliner', 'GlobalChef Institute mengedepankan praktik dapur profesional', 'https://globalchef.com', 'Jl. Kusumane 821890123454', 'A'],
            ['logispro_training', 'logispro_training@gmail.com', 'logispro', 'Terverifikasi', '2024-07-21', 'Logispro Training', 'Logistik & Supply Chain', 'LogisPro Training Center menyiapkan tenaga logistik kompeten', 'https://logispro.id', 'Jl. Raya Pelab 821987654322', 'B'],
            ['smartedu_center', 'smartedu_center@gmail.com', 'smartedu', 'Terverifikasi', '2024-05-15', 'Smartedu Center', 'Pendidikan & Pelatihan Guru', 'SmartEdu Training Center mendukung kompetensi pendidikan', 'https://smartedu.id', 'Jl. Pendidikan 821789254321', 'A'],
            ['startup_hub', 'startup_hub@gmail.com', 'startup1', 'Terverifikasi', '2024-06-01', 'Startup Hub', 'Kewirausahaan & Startup', 'StartUp Hub Academy hadir untuk membina wirausahawan baru', 'https://startuphub.com', 'Jl. Raya ITC N 821345678901', 'B'],
            ['healthcare_training', 'healthcare_training@gmail.com', 'healthcare', 'Terverifikasi', '2024-03-30', 'Healthcare Training', 'Perawat & Tenaga Medis', 'HealthCare Training Center mengembangkan tenaga kesehatan', 'https://healthcare.id', 'Jl. Kesehatan 1 821987432410', 'A'],
            ['techwork_academy', 'techwork_academy@gmail.com', 'techwork', 'Terverifikasi', '2024-08-15', 'Techwork Academy', 'Teknologi Industri & Manufaktur', 'TechWork Academy berfokus pada pelatihan manufaktur modern', 'https://techwork.com', 'Jl. Industri No 821456789701', 'A'],
            ['agritech_academy', 'agritech_academy@gmail.com', 'agritech', 'Terverifikasi', '2024-07-18', 'Agritech Academy', 'Pertanian Modern & Agribisnis', 'AgriTech Academy berfokus pada pelatihan agribisnis inovatif', 'https://agritechacademy.com', 'Jl. Raya Tlogi 821876543210', 'A'],
            ['maritim_skillcenter', 'maritim_skillcenter@gmail.com', 'maritim1', 'Terverifikasi', '2024-08-02', 'Maritim Skillcenter', 'Kelautan & Perkapalan', 'Maritim Skill Center menyediakan pelatihan maritim profesional', 'https://maritimskills.id', 'Jl. Pelabuhan 1 852133245678', 'B'],
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
