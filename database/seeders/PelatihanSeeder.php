<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pelatihan;
use App\Models\LembagaPelatihan;

class PelatihanSeeder extends Seeder
{
    public function run(): void
    {
        // Data statis dari gambar (3 blok lembaga). Pastikan LembagaSeeder sudah dijalankan.
        // Normalisasi nama lembaga ke bentuk slug sederhana untuk memudahkan pencocokan
        $normalizer = function(string $v){
            $v = strtolower($v);
            $v = preg_replace('/[^a-z0-9]+/','', $v); // hilangkan semua non-alnum
            return $v;
        };
        $rawLembagaMap = LembagaPelatihan::pluck('id','nama_lembaga');
        $mapNamaLembagaKeId = [];
        foreach ($rawLembagaMap as $nama=>$id) {
            $mapNamaLembagaKeId[$normalizer($nama)] = $id;
        }

        $createdLembaga = 0;
        // Helper untuk ambil / buat lembaga
        $resolveLembaga = function(string $search) use (&$mapNamaLembagaKeId,$normalizer,&$createdLembaga) {
            $key = $normalizer($search);
            if (isset($mapNamaLembagaKeId[$key])) return $mapNamaLembagaKeId[$key];
            // coba partial (contains) terhadap key yang ada
            foreach ($mapNamaLembagaKeId as $existingKey=>$id) {
                if (str_contains($existingKey, $key) || str_contains($key,$existingKey)) {
                    return $id;
                }
            }
            // Auto-create jika belum ada
            // Auto-create minimal record dengan kolom wajib agar lolos constraint tabel
            $now = now();
            $model = LembagaPelatihan::create([
                'username' => substr(preg_replace('/[^a-z0-9]+/','', $key),0,18) . rand(10,99),
                'email' => strtolower($key).'@example.local',
                'password' => bcrypt('password'),
                'status_verifikasi' => 'Terverifikasi',
                'tanggal_verifikasi' => $now,
                'nama_lembaga' => $search,
                'bidang_pelatihan' => 'Umum',
                'deskripsi_lembaga' => 'Generated placeholder',
                'web_lembaga' => 'https://placeholder.' . substr(preg_replace('/[^a-z0-9]+/','', $key),0,25) . '.local',
                'alamat' => '-',
                'kontak' => '-',
                'otp' => null,
                'otp_expired_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $mapNamaLembagaKeId[$key] = $model->id;
            $createdLembaga++;
            return $model->id;
        };

        $datasets = [
            'NexaTech' => [
                ['Web Development Fundamental','Pelatihan dasar pembuatan website','Web Development','Mampu membuat halaman web statis dengan HTML & CSS, Mampu menambahkan interaktivitas sederhana dengan JavaScript'],
                ['Data Science with Python','Pelatihan analisis data dasar','Data Science','Mampu mengolah data dengan Python & Pandas, Mampu membuat visualisasi data dasar dengan Matplotlib'],
                ['Cloud Computing Essentials','Pengantar layanan cloud','Cloud Computing','Memahami konsep dasar cloud & deployment, Mampu menggunakan layanan cloud untuk hosting aplikasi sederhana'],
                ['Cybersecurity Basics','Pengenalan keamanan informasi','Cybersecurity','Memahami konsep dasar keamanan informasi, Mampu menerapkan praktik keamanan dasar pada sistem komputer'],
                ['Digital Project Management','Pelatihan manajemen proyek digital','Project Management','Mampu menyusun rencana proyek digital, Mampu mengelola timeline & resource dengan tools manajemen proyek'],
                ['Agile & Scrum Fundamentals','Pengenalan metodologi Agile','Agile & Scrum','Memahami prinsip Agile Manifesto, Mampu menjalankan peran dasar dalam Scrum (PO, SM, Dev Team)'],
            ],
            'BrightSkill' => [
                ['Komunikasi Efektif','Pelatihan teknik komunikasi profesional','Komunikasi Efektif','Mampu menyampaikan pesan dengan jelas & tepat, Mampu menggunakan bahasa tubuh yang mendukung komunikasi'],
                ['Leadership & Teamwork','Pelatihan kepemimpinan & kolaborasi','Leadership & Teamwork','Mampu memimpin diskusi kelompok secara efektif, Mampu bekerja sama dalam tim untuk mencapai target bersama'],
                ['Manajemen Perkantoran','Pengenalan tata kelola administrasi','Manajemen Perkantoran','Mampu mengelola dokumen & arsip, Mampu menyusun jadwal pertemuan dan agenda kerja'],
                ['Customer Service Excellence','Pelatihan layanan pelanggan profesional','Customer Service Excellence','Mampu memberikan pelayanan responsif & ramah, Mampu menangani komplain pelanggan dengan solusi yang tepat'],
                ['Personal Branding','Pelatihan membangun citra profesional','Personal Branding','Mampu mengidentifikasi keunikan diri, Mampu mengembangkan profil profesional di media sosial'],
            ],
            'Unigama' => [
                ['Akuntansi','Pelatihan dasar akuntansi','Akuntansi','Mampu menyusun laporan keuangan sederhana, Mampu memahami prinsip debit-kredit dasar'],
                ['Administrasi Perkantoran','Pelatihan manajemen dokumen & arsip','Administrasi Perkantoran','Mampu mengelola dokumen & arsip, Mampu menyusun jadwal pertemuan dan agenda kerja'],
                ['Manajemen Bisnis','Pengenalan prinsip manajemen modern','Manajemen Bisnis','Mampu menyusun rencana bisnis sederhana, Mampu memahami fungsi manajemen dalam organisasi'],
                ['Audit Internal','Pelatihan teknik audit internal','Audit Internal','Mampu menyusun laporan audit, Mampu mengevaluasi kepatuhan terhadap SOP dan regulasi'],
                ['Entrepreneurship','Pelatihan kewirausahaan','Entrepreneurship','Mampu mengidentifikasi peluang usaha, Mampu menyusun model bisnis dan strategi pemasaran'],
            ],
            // Tambahan dari gambar baru
            'Asahi' => [
                ['Bahasa Jepang','Pelatihan penguasaan dasar bahasa Jepang','Bahasa Jepang','Mampu membaca dan menulis hiragana & katakana, Menguasai kosakata dasar sehari-hari, Memahami tata bahasa dasar, Mampu memperkenalkan diri & menanyakan informasi sederhana, Mendengarkan percakapan sederhana dengan baik'],
                ['JLPT Preparation','Persiapan menghadapi ujian JLPT','JLPT Preparation','Memahami pola grammar sesuai level JLPT, Menguasai kosakata & kanji sesuai tingkatan, Mampu mengerjakan soal latihan JLPT, Mampu memahami percakapan listening JLPT, Menyusun strategi pengerjaan ujian'],
                ['Budaya Kerja Jepang','Pengenalan budaya dan etos kerja Jepang','Budaya Kerja Jepang','Memahami etos kerja Jepang (Kaizen, disiplin, teamwork), Mampu menerapkan etika komunikasi di kantor Jepang, Memahami sistem hierarki & tata krama kerja, Mampu mengikuti prosedur rapat & laporan, Mampu menyesuaikan diri dengan lingkungan kerja Jepang'],
                ['Conversation Skill','Latihan kemampuan percakapan Jepang','Conversation Skill','Mampu melakukan percakapan sehari-hari dengan lancar, Mampu berdiskusi dalam kelompok kecil, Mampu melakukan presentasi singkat dalam bahasa Jepang, Mampu berkomunikasi dalam situasi formal, Menguasai ekspresi idiomatik Jepang'],
            ],
            'Digital Creative' => [
                ['Desain Grafis','Pelatihan penguasaan tools desain grafis','Desain Grafis','Mampu menggunakan tools dasar desain grafis, Mampu membuat layout visual sederhana, Mampu mengolah gambar dan tipografi, Mampu membuat desain untuk media sosial dan cetak, Mampu menerapkan prinsip warna dan komposisi'],
                ['Animasi 2D','Pengenalan animasi 2D','Animasi 2D','Mampu membuat ilustrasi karakter 2D, Mampu membuat storyboard sederhana, Mampu menganimasikan gerakan dasar, Mampu menggunakan software animasi 2D, Mampu membuat video animasi pendek'],
                ['Animasi 3D','Pelatihan pembuatan animasi 3D dasar','Animasi 3D','Mampu membuat model 3D sederhana, Mampu melakukan rigging dan texturing, Mampu menganimasikan karakter 3D, Mampu menggunakan software 3D populer, Mampu membuat scene animasi 3D sederhana'],
                ['Multimedia','Pelatihan penguasaan multimedia interaktif','Multimedia','Mampu mengintegrasikan teks, gambar, audio, dan video, Mampu membuat presentasi multimedia interaktif, Mampu membuat konten interaktif untuk pembelajaran, Mampu menggunakan software multimedia populer, Mampu mengelola proyek multimedia'],
                ['Editing Video','Pelatihan editing video dasar','Editing Video','Mampu memotong dan menggabungkan klip video, Mampu menambahkan audio dan efek transisi, Mampu mengatur color grading dasar, Mampu menggunakan software editing video populer, Mampu mengekspor video dengan format yang sesuai'],
                ['Motion Graphic','Pelatihan pembuatan motion graphic','Motion Graphic','Mampu membuat animasi tipografi, Mampu menggunakan shape dan efek visual, Mampu membuat bumper iklan pendek, Mampu menggunakan software motion graphic, Mampu membuat motion graphic untuk promosi digital'],
            ],
            'VisualArt' => [
                ['Seni Rupa','Pelatihan dasar seni rupa','Seni Rupa','Mampu memahami prinsip seni rupa, Mampu menggunakan teknik menggambar dasar, Mampu menciptakan karya seni dua dimensi, Mampu mengolah komposisi dan perspektif, Mampu mengekspresikan ide melalui media visual'],
                ['Fotografi','Pelatihan teknik fotografi dasar','Fotografi','Mampu memahami fungsi kamera dan lensa, Mampu menggunakan pencahayaan alami dan buatan, Mampu mengatur komposisi fotografi, Mampu melakukan editing foto dasar, Mampu menghasilkan portofolio foto profesional'],
                ['Animasi Digital','Pelatihan pembuatan animasi digital dasar','Animasi Digital','Mampu memahami prinsip dasar animasi, Mampu membuat storyboard, Mampu menggunakan software animasi, Mampu mengembangkan karakter animasi sederhana, Mampu menghasilkan video animasi pendek'],
                ['Desain Karakter','Pelatihan pembuatan desain karakter','Desain Karakter','Mampu memahami anatomi karakter, Mampu membuat sketsa karakter, Mampu memberikan warna dan detail desain, Mampu mengembangkan gaya visual unik, Mampu menyiapkan karakter untuk animasi atau game'],
                ['Portofolio Kreatif','Pelatihan penyusunan portofolio kreatif','Portofolio Kreatif','Mampu memahami struktur portofolio, Mampu memilih karya terbaik untuk ditampilkan, Mampu mendesain layout portofolio, Mampu membuat portofolio digital, Mampu mempresentasikan portofolio dengan percaya diri'],
            ],
            'GrandTourism' => [
                ['Pariwisata','Pelatihan dasar pariwisata','Pariwisata','Mampu memahami konsep dasar pariwisata, Mampu mengenali destinasi wisata populer, Mampu menyusun paket wisata sederhana, Mampu memberikan penjelasan tentang budaya lokal, Mampu menerapkan etika pelayanan wisata'],
                ['Perhotelan','Pelatihan dasar perhotelan','Perhotelan','Mampu memahami struktur organisasi hotel, Mampu melakukan pelayanan front office, Mampu melaksanakan prosedur housekeeping, Mampu menangani reservasi tamu, Mampu menjaga standar kualitas pelayanan hotel'],
                ['Tata Boga','Pelatihan memasak dasar','Tata Boga','Mampu menguasai teknik dasar memasak, Mampu menyusun menu harian, Mampu mengatur standar kebersihan dapur, Mampu menyajikan makanan dengan estetika, Mampu menghitung kebutuhan bahan makanan'],
                ['Hospitality Management','Pelatihan manajemen hospitality','Hospitality Management','Mampu memahami konsep manajemen hospitality, Mampu mengatur standar pelayanan, Mampu mengelola operasional harian, Mampu menyusun laporan pelayanan, Mampu menangani keluhan pelanggan dengan tepat'],
                ['Event Management','Pelatihan pengelolaan event','Event Management','Mampu menyusun konsep acara, Mampu membuat timeline kegiatan, Mampu mengelola anggaran event, Mampu berkoordinasi dengan vendor dan tim, Mampu melakukan evaluasi keberhasilan acara'],
            ],
            'AutoMech' => [ // Fluent English Academy (diasumsikan AutoMech English Academy)
                ['Bahasa Inggris','Pelatihan bahasa Inggris dasar-menengah','Bahasa Inggris','Mampu memahami kosakata dasar hingga menengah, Mampu menyusun kalimat dengan grammar yang benar, Mampu memahami teks bacaan sederhana, Mampu mendengarkan percakapan sehari-hari dengan baik, Mampu berkomunikasi lisan dengan lancar'],
                ['TOEFL Preparation','Persiapan menghadapi tes TOEFL','TOEFL Preparation','Mampu memahami format tes TOEFL, Mampu mengerjakan soal listening dengan baik, Mampu menjawab soal grammar dengan tepat, Mampu memahami teks reading akademik, Mampu menyusun strategi manajemen waktu ujian'],
                ['IELTS Preparation','Persiapan menghadapi tes IELTS','IELTS Preparation','Mampu memahami format tes IELTS, Mampu menjawab soal reading dengan baik, Mampu menulis esai akademik sesuai struktur IELTS, Mampu melakukan speaking test dengan lancar, Mampu mengatur strategi ujian untuk mencapai skor target'],
                ['Conversation Skill','Pelatihan percakapan aktif','Conversation Skill','Mampu berkomunikasi lisan dalam percakapan sehari-hari, Mampu berdiskusi dalam kelompok kecil, Mampu melakukan presentasi singkat, Mampu memahami ekspresi idiomatik, Mampu menggunakan intonasi dan pelafalan yang jelas'],
                ['Academic Writing','Pelatihan penulisan akademik','Academic Writing','Mampu menyusun esai akademik dengan struktur yang benar, Mampu menggunakan grammar formal dengan tepat, Mampu menyusun referensi dengan format standar, Mampu menulis abstrak dan laporan penelitian, Mampu mengembangkan argumen dengan logis'],
            ],
            // Tambahan set kedua
            'ElectroLab' => [
                ['Teknik Elektro','Pelatihan dasar teknik kelistrikan','Teknik Elektro','Mampu memahami konsep dasar kelistrikan, Mampu membaca dan membuat rangkaian sederhana, Mampu menggunakan alat ukur listrik, Mampu melakukan perawatan instalasi listrik, Mampu menerapkan prinsip keselamatan kerja listrik'],
                ['Listrik Industri','Pelatihan instalasi listrik industri','Listrik Industri','Mampu memahami standar instalasi industri, Mampu mengoperasikan peralatan listrik industri, Mampu melakukan troubleshooting kelistrikan, Mampu membaca diagram kelistrikan, Mampu mengimplementasikan sistem proteksi listrik'],
                ['Mekatronika','Pelatihan integrasi mekanik & elektronik','Mekatronika','Mampu memahami prinsip dasar mekatronika, Mampu merancang sistem mekanik sederhana, Mampu mengintegrasikan sensor dan aktuator, Mampu menggunakan mikrokontroler, Mampu membangun sistem otomasi sederhana'],
                ['Instrumentasi','Pelatihan penggunaan instrumen industri','Instrumentasi','Mampu memahami jenis-jenis instrumen industri, Mampu melakukan kalibrasi instrumen, Mampu membaca hasil pengukuran dengan akurat, Mampu mengintegrasikan instrumen ke sistem kontrol, Mampu melakukan analisis data pengukuran'],
                ['Otomasi Industri','Pelatihan sistem otomasi industri','Otomasi Industri','Mampu memahami konsep otomasi industri, Mampu memprogram PLC dasar, Mampu mengoperasikan sistem SCADA, Mampu mengintegrasikan sensor dan aktuator ke PLC, Mampu membuat sistem otomasi sederhana'],
            ],
            'Financia' => [
                ['Akuntansi Dasar','Pelatihan pengenalan dasar akuntansi','Akuntansi','Mampu memahami prinsip dasar akuntansi, Mampu membuat jurnal transaksi, Mampu menyusun neraca saldo, Mampu menyusun laporan laba rugi, Mampu menganalisis laporan keuangan sederhana'],
                ['Manajemen Keuangan','Pelatihan strategi keuangan perusahaan','Keuangan','Mampu memahami konsep manajemen keuangan, Mampu membuat rencana anggaran, Mampu mengelola arus kas, Mampu menghitung rasio keuangan, Mampu membuat keputusan investasi sederhana'],
                ['Akuntansi Perpajakan','Pelatihan pencatatan dan pelaporan pajak','Akuntansi & Perpajakan','Mampu memahami regulasi perpajakan, Mampu menghitung pajak perusahaan, Mampu membuat laporan SPT, Mampu menggunakan software perpajakan, Mampu memastikan kepatuhan pajak'],
                ['Audit Internal','Pelatihan prosedur audit keuangan','Audit Keuangan','Mampu memahami konsep dasar audit, Mampu menyusun program audit, Mampu melakukan pengujian transaksi, Mampu menyusun laporan audit, Mampu memberikan rekomendasi perbaikan'],
                ['Financial Analysis & Reporting','Pelatihan analisis laporan keuangan','Keuangan','Mampu membaca laporan keuangan, Mampu melakukan analisis rasio, Mampu membuat proyeksi keuangan, Mampu menyiapkan laporan untuk manajemen, Mampu memberikan insight dari data keuangan'],
            ],
            'MediLab' => [
                ['Dasar Kesehatan Klinik','Pelatihan prinsip dasar klinik','Kesehatan Klinik','Mampu memahami dasar anatomi dan fisiologi, Mampu memahami etika profesi kesehatan, Mampu melakukan pemeriksaan dasar pasien, Mampu menerapkan prinsip higiene dan sanitasi, Mampu memahami prosedur keselamatan kerja klinik'],
                ['Teknik Laboratorium Klinik','Pelatihan prosedur laboratorium klinik','Laboratorium Klinik','Mampu memahami fungsi alat laboratorium, Mampu melakukan pemeriksaan darah sederhana, Mampu membaca hasil laboratorium, Mampu menerapkan prosedur sterilisasi, Mampu membuat laporan hasil laboratorium'],
                ['Mikrobiologi Klinik','Pelatihan teknik identifikasi mikroorganisme','Mikrobiologi Klinik','Mampu memahami dasar mikrobiologi, Mampu melakukan kultur mikroorganisme, Mampu menggunakan mikroskop dengan benar, Mampu mengidentifikasi bakteri umum, Mampu menjaga keamanan biologis'],
                ['Patologi Klinik','Pelatihan analisis sampel patologi','Patologi Klinik','Mampu memahami jenis pemeriksaan patologi, Mampu menyiapkan sampel dengan benar, Mampu membaca hasil pemeriksaan patologi, Mampu melakukan analisis sederhana, Mampu menyusun laporan hasil patologi'],
                ['Manajemen Laboratorium Kesehatan','Pelatihan pengelolaan laboratorium','Manajemen Laboratorium Klinik','Mampu memahami standar mutu laboratorium, Mampu menyusun SOP laboratorium, Mampu mengelola logistik dan peralatan, Mampu melakukan audit internal laboratorium, Mampu menyusun laporan manajemen laboratorium'],
            ],
            'AgroField' => [
                ['Dasar Pertanian Modern','Pelatihan konsep pertanian modern','Pertanian Modern','Mampu memahami prinsip dasar pertanian modern, Mampu menggunakan alat pertanian sederhana, Mampu mengelola lahan dengan efisien, Mampu memahami siklus tanaman, Mampu menerapkan praktik pertanian berkelanjutan'],
                ['Agroteknologi Terapan','Pelatihan penerapan teknologi pertanian','Agroteknologi','Mampu memahami konsep agroteknologi, Mampu mengoperasikan alat sensor pertanian, Mampu memanfaatkan teknologi irigasi modern, Mampu menggunakan drone untuk monitoring, Mampu menerapkan sistem pertanian cerdas'],
                ['Manajemen Hama & Penyakit Tanaman','Pelatihan identifikasi hama & penyakit','Proteksi Tanaman','Mampu mengidentifikasi jenis hama tanaman, Mampu mengenali gejala penyakit tanaman, Mampu menerapkan metode pengendalian biologis, Mampu menggunakan pestisida dengan aman, Mampu menyusun strategi proteksi tanaman'],
                ['Teknologi Pangan','Pelatihan pengolahan produk pangan','Teknologi Pangan','Mampu memahami prinsip pengolahan pangan, Mampu menggunakan peralatan pengolahan dasar, Mampu menjaga standar higienitas, Mampu mengembangkan produk pangan sederhana, Mampu melakukan uji kualitas produk'],
                ['Agribisnis & Kewirausahaan','Pelatihan pengembangan usaha agribisnis','Agribisnis','Mampu memahami konsep agribisnis, Mampu membuat rencana usaha pertanian, Mampu menghitung biaya produksi, Mampu memasarkan produk pertanian, Mampu mengelola keuangan usaha pertanian'],
            ],
            'BuildMaster' => [
                ['Dasar Teknik Sipil','Pelatihan pengenalan teknik sipil','Teknik Sipil','Mampu memahami konsep dasar konstruksi, Mampu membaca gambar teknik, Mampu memahami sifat material bangunan, Mampu menerapkan perhitungan sederhana struktur, Mampu memahami prinsip keselamatan konstruksi'],
                ['Manajemen Proyek Konstruksi','Pelatihan perencanaan dan manajemen proyek','Manajemen Proyek Konstruksi','Mampu memahami siklus proyek konstruksi, Mampu menyusun jadwal proyek, Mampu mengelola sumber daya proyek, Mampu menghitung estimasi biaya, Mampu membuat laporan kemajuan proyek'],
                ['Struktur Bangunan','Pelatihan analisis dasar struktur bangunan','Struktur Bangunan','Mampu memahami jenis struktur bangunan, Mampu menghitung beban sederhana, Mampu merancang elemen struktur, Mampu memilih material sesuai kebutuhan, Mampu membuat gambar struktur'],
                ['Teknik Konstruksi Beton','Pelatihan teori dan praktik konstruksi beton','Konstruksi Beton','Mampu memahami sifat beton, Mampu membuat campuran beton sesuai standar, Mampu melakukan pengecoran, Mampu menguji kualitas beton, Mampu merancang elemen beton sederhana'],
                ['Keselamatan & Kesehatan Kerja Konstruksi','Pelatihan penerapan K3 konstruksi','K3 Konstruksi','Mampu memahami prinsip K3, Mampu mengidentifikasi potensi bahaya kerja, Mampu menggunakan APD dengan benar, Mampu menyusun prosedur keselamatan, Mampu melakukan simulasi tanggap darurat'],
            ],
            'Green Energy' => [
                ['Dasar Energi Terbarukan','Pengenalan konsep energi terbarukan','Energi Terbarukan','Mampu memahami konsep energi terbarukan, Mampu mengenali jenis energi terbarukan, Mampu menjelaskan manfaat energi terbarukan, Mampu membedakan sumber energi fosil dan hijau, Mampu memahami tantangan implementasi energi terbarukan'],
                ['Pembangkit Listrik Tenaga Surya','Pelatihan instalasi dan perawatan PLTS','Energi Surya','Mampu memahami prinsip kerja panel surya, Mampu merancang sistem PLTS sederhana, Mampu melakukan instalasi panel surya, Mampu melakukan perawatan sistem surya, Mampu menghitung efisiensi PLTS'],
                ['Pembangkit Listrik Tenaga Angin','Pelatihan perancangan turbin angin kecil','Energi Angin','Mampu memahami prinsip kerja turbin angin, Mampu merakit turbin angin sederhana, Mampu mengoperasikan sistem PLTAngin kecil, Mampu melakukan perawatan rutin, Mampu mengevaluasi potensi lokasi pemasangan'],
                ['Bioenergi & Biogas','Pelatihan pemanfaatan bioenergi','Bioenergi','Mampu memahami prinsip bioenergi, Mampu membuat reaktor biogas sederhana, Mampu mengoperasikan sistem biogas, Mampu menganalisis kualitas gas, Mampu memahami pemanfaatan limbah organik'],
                ['Audit Energi & Efisiensi Bangunan','Pelatihan melakukan audit energi bangunan','Energi Angin','Mampu memahami prinsip audit energi, Mampu mengidentifikasi potensi boros energi, Mampu menggunakan alat ukur energi, Mampu menyusun laporan audit, Mampu memberikan rekomendasi efisiensi energi'],
            ],
            'CyberSecure' => [
                ['Dasar Keamanan Siber','Pengenalan konsep keamanan siber','Keamanan Siber','Mampu memahami konsep dasar keamanan siber, Mampu mengidentifikasi ancaman umum, Mampu menerapkan praktik keamanan dasar, Mampu memahami prinsip kerahasiaan dan integritas data, Mampu memahami risiko serangan siber'],
                ['Ethical Hacking & Penetration Test','Pelatihan teknik hacking etis','Penetration Testing','Mampu memahami prinsip ethical hacking, Mampu menggunakan tools penetration testing, Mampu menganalisis kerentanan sistem, Mampu menyusun laporan hasil uji, Mampu memahami batasan hukum dalam hacking etis'],
                ['Forensik Digital','Pelatihan investigasi bukti digital','Forensik Digital','Mampu memahami prinsip forensik digital, Mampu mengumpulkan bukti digital, Mampu menggunakan tools forensik, Mampu menyusun laporan investigasi, Mampu memahami prosedur hukum dalam bukti digital'],
                ['Incident Response & Recovery','Pelatihan penanganan insiden keamanan','Incident Management','Mampu memahami proses respon insiden, Mampu membuat rencana recovery, Mampu mendokumentasikan insiden, Mampu menggunakan tools monitoring, Mampu memahami komunikasi saat insiden'],
                ['Manajemen Risiko Keamanan Informasi','Pelatihan analisis dan mitigasi risiko keamanan','Risk Management','Mampu memahami prinsip manajemen risiko, Mampu melakukan analisis risiko, Mampu menyusun strategi mitigasi, Mampu menyusun kebijakan keamanan, Mampu melakukan evaluasi berkala'],
            ],
            'Fashionista' => [
                ['Dasar Fashion Design','Pengenalan prinsip desain fashion','Fashion Design','Mampu memahami dasar sketsa fashion, Mampu mengidentifikasi tren mode, Mampu membuat mood board desain, Mampu mengembangkan ide kreatif, Mampu menyusun portofolio awal'],
                ['Tata Busana','Pelatihan keterampilan busana','Tata Busana','Mampu menggunakan mesin jahit dasar, Mampu memahami pola busana, Mampu memotong kain dengan teknik tepat, Mampu menjahit pakaian sederhana, Mampu melakukan finishing sederhana'],
                ['Advanced Pattern Making','Teknik lanjutan pembuatan pola','Fashion Design','Mampu memahami teknik pembuatan pola lanjutan, Mampu membuat pola untuk pakaian formal, Mampu mengadaptasi pola sesuai ukuran, Mampu mengaplikasikan teknik grading, Mampu menyusun koleksi pola'],
                ['Fashion Illustration','Pelatihan menggambar ilustrasi fashion','Fashion Illustration','Mampu menggambar ilustrasi fashion manual, Mampu menggunakan software ilustrasi fashion, Mampu menggabungkan warna & tekstur, Mampu menyajikan ilustrasi fashion untuk presentasi desain, Mampu menyiapkan koleksi busana untuk show'],
                ['Portfolio & Fashion Show Preparation','Persiapan penyusunan portofolio dan fashion show','Fashion Portfolio','Mampu menyusun portofolio profesional, Mampu mempresentasikan desain dengan baik, Mampu menyiapkan koleksi busana untuk show, Mampu memahami manajemen backstage, Mampu melakukan evaluasi hasil presentasi'],
            ],
            'GlobalChef' => [
                ['Dasar Tata Boga','Pengenalan teknik dasar memasak','Tata Boga','Mampu memahami dasar teknik memasak, Mampu menggunakan peralatan dapur dengan benar, Mampu mengolah bahan makanan dasar, Mampu menjaga kebersihan dapur, Mampu memahami standar keamanan pangan'],
                ['Kuliner Nusantara','Pelatihan mengolah masakan nusantara','Kuliner Nusantara','Mampu mengenali ragam kuliner nusantara, Mampu mengolah masakan khas daerah, Mampu menggunakan bumbu tradisional dengan tepat, Mampu menyajikan makanan khas, Mampu memahami nilai budaya kuliner nusantara'],
                ['Pastry & Bakery','Pelatihan membuat pastry & bakery','Pastry & Bakery','Mampu memahami bahan dasar pastry & bakery, Mampu membuat roti dasar, Mampu membuat kue tradisional & modern, Mampu melakukan dekorasi sederhana, Mampu menyusun produk pastry untuk penjualan'],
                ['Culinary International','Pelatihan memasak hidangan internasional','Kuliner Internasional','Mampu memahami ciri khas kuliner internasional, Mampu memasak hidangan utama dari negara tertentu, Mampu menyajikan makanan dengan plating standar internasional, Mampu mengatur bahan sesuai resep, Mampu berekasi dengan menu fusion'],
                ['Food Presentation & Plating','Pelatihan seni penyajian makanan','Food Presentation','Mampu memahami prinsip estetika penyajian, Mampu menggunakan teknik plating modern, Mampu mengombinasikan warna & tekstur, Mampu menyajikan makanan sesuai standar restoran, Mampu mendokumentasikan hasil plating'],
            ],
            'LogisPro' => [
                ['Dasar Logistik','Pengenalan konsep logistik','Logistik Dasar','Mampu memahami konsep dasar logistik, Mampu mengenali alur distribusi barang, Mampu menggunakan istilah logistik dengan benar, Mampu memahami dokumen logistik, Mampu mengidentifikasi peran logistik dalam bisnis'],
                ['Manajemen Rantai Pasok','Pelatihan manajemen supply chain','Supply Chain Management','Mampu memahami konsep supply chain, Mampu mengelola alur bahan baku hingga produk jadi, Mampu menyusun strategi efisiensi rantai pasok, Mampu menggunakan software SCM dasar, Mampu melakukan evaluasi kinerja supply chain'],
                ['Warehouse Management','Pelatihan pengelolaan gudang','Manajemen Gudang','Mampu memahami prinsip manajemen gudang, Mampu menggunakan sistem penyimpanan modern, Mampu mengelola stok barang dengan akurat, Mampu memahami prinsip FIFO & LIFO, Mampu melakukan audit gudang'],
                ['Transportasi & Distribusi','Pelatihan pengelolaan distribusi barang','Transportasi & Distribusi','Mampu memahami prinsip distribusi barang, Mampu menyusun jadwal transportasi, Mampu memilih moda transportasi yang tepat, Mampu mengoptimalkan rute distribusi, Mampu melakukan evaluasi biaya distribusi'],
                ['Procurement & Vendor Management','Pelatihan pengadaan dan manajemen vendor','Procurement','Mampu memahami proses procurement, Mampu menyusun dokumen pengadaan, Mampu melakukan evaluasi vendor, Mampu menyusun kontrak sederhana, Mampu membangun hubungan kerja sama dengan vendor'],
            ],
            'SmartEdu' => [
                ['Dasar Metodologi Pengajaran','Pengenalan teori, pendekatan & model pembelajaran','Metodologi Pengajaran','Mampu memahami konsep dasar pedagogi, Mampu memilih metode pembelajaran sesuai kebutuhan, Mampu merancang skenario pembelajaran, Mampu melakukan refleksi metode, Mampu mengevaluasi hasil belajar siswa'],
                ['Manajemen Kelas','Pelatihan keterampilan manajemen kelas','Manajemen Kelas','Mampu memahami prinsip manajemen kelas, Mampu mengatur dinamika siswa, Mampu menyusun aturan kelas, Mampu mengelola konflik kecil, Mampu menciptakan lingkungan belajar positif'],
                ['Teknologi dalam Pendidikan','Pemanfaatan teknologi pembelajaran','EdTech','Mampu menggunakan aplikasi pembelajaran, Mampu mengembangkan media interaktif, Mampu menggunakan LMS untuk kelas online, Mampu memanfaatkan teknologi evaluasi, Mampu mengintegrasikan teknologi dalam kurikulum'],
                ['Kurikulum & Perencanaan Pembelajaran','Pelatihan penyusunan kurikulum dan RPP','Kurikulum & RPP','Mampu memahami prinsip pengembangan kurikulum, Mampu menyusun RPP, Mampu menyesuaikan kurikulum dengan kebutuhan siswa, Mampu menyusun asesmen sesuai tujuan pembelajaran, Mampu mengevaluasi implementasi kurikulum'],
                ['Evaluasi & Asesmen Pembelajaran','Pelatihan teknik evaluasi pembelajaran','Evaluasi Pembelajaran','Mampu memahami jenis asesmen, Mampu menyusun soal sesuai level kognitif, Mampu menggunakan instrumen penilaian alternatif, Mampu melakukan analisis hasil asesmen, Mampu menyusun laporan evaluasi pembelajaran'],
            ],
            'StartUp Hub' => [
                ['Dasar Kewirausahaan','Pengenalan konsep dasar wirausaha','Kewirausahaan Dasar','Mampu memahami prinsip dasar kewirausahaan, Mampu mengidentifikasi peluang bisnis, Mampu menyusun ide bisnis sederhana, Mampu memahami risiko usaha, Mampu menumbuhkan mindset kreatif'],
                ['Business Model Canvas & Lean Startup','Pelatihan penyusunan model bisnis','Lean Startup','Mampu memahami konsep business model canvas, Mampu membuat BMC untuk ide bisnis, Mampu memahami prinsip lean startup, Mampu membuat MVP sederhana, Mampu menguji hipotesis pasar'],
                ['Pitching & Presentasi Bisnis','Pelatihan teknik presentasi bisnis','Pitching Business','Mampu menyusun pitch deck, Mampu melakukan presentasi dengan percaya diri, Mampu menjawab pertanyaan investor, Mampu menyusun narasi bisnis yang menarik, Mampu melakukan simulasi pitching'],
                ['Digital Marketing for Startup','Strategi pemasaran digital untuk startup','Digital Marketing','Mampu memahami konsep digital marketing, Mampu membuat strategi media sosial, Mampu mengoptimalkan SEO & SEM, Mampu membuat kampanye iklan online, Mampu mengevaluasi kinerja digital marketing'],
                ['Startup Fundraising & Investment','Pelatihan strategi mendapatkan pendanaan','Startup Funding','Mampu memahami jenis pendanaan startup, Mampu menyusun proposal pendanaan, Mampu melakukan negosiasi dengan investor, Mampu memahami valuasi startup, Mampu mengelola relasi dengan investor'],
            ],
            'HealthCare' => [
                ['Dasar Keperawatan','Pengenalan konsep dasar keperawatan','Keperawatan Dasar','Mampu memahami peran perawat, Mampu melakukan pemeriksaan dasar, Mampu mengelola kebutuhan dasar pasien, Mampu menerapkan etika profesi, Mampu membuat catatan medis sederhana'],
                ['Perawatan Gawat Darurat (Emergency Care)','Pelatihan penanganan kondisi darurat','Emergency Care','Mampu melakukan triase, Mampu memberikan pertolongan pertama, Mampu menangani pasien trauma, Mampu menggunakan peralatan darurat, Mampu bekerja dalam tim emergensi'],
                ['Asisten Tenaga Medis','Pelatihan keterampilan asisten medis','Asisten Medis','Mampu membantu dokter dalam pemeriksaan, Mampu menyiapkan peralatan medis, Mampu melakukan administrasi pasien, Mampu memahami standar keamanan, Mampu menjaga komunikasi dengan pasien'],
                ['Keperawatan Anak & Lansia','Teknik khusus dalam keperawatan anak & lansia','Specialized Nursing','Mampu memahami kebutuhan pasien anak & lansia, Mampu memberikan perawatan khusus, Mampu mengelola psikologi pasien, Mampu menangani nutrisi pasien, Mampu membuat rencana perawatan jangka panjang'],
                ['Manajemen Rekam Medis & Administrasi','Pelatihan pencatatan & administrasi medis','Administrasi Medis','Mampu membuat dan mengelola rekam medis, Mampu memahami regulasi kesehatan, Mampu mengoperasikan sistem informasi kesehatan, Mampu menjaga kerahasiaan data, Mampu menyusun laporan medis'],
            ],
            'TechWork' => [
                ['Teknologi Produksi Manufaktur','Pengenalan proses produksi manufaktur','Produksi Manufaktur','Mampu memahami alur produksi, Mampu mengoperasikan mesin manufaktur dasar, Mampu mengontrol kualitas produk, Mampu memahami standar keselamatan kerja, Mampu mengelola efisiensi produksi'],
                ['Otomasi & Robotika Industri','Pelatihan tentang sistem otomasi industri','Otomasi Industri','Mampu memahami konsep otomasi, Mampu mengoperasikan robot industri, Mampu menggunakan sensor IoT, Mampu membuat program sederhana PLC, Mampu meningkatkan produktivitas berbasis teknologi'],
                ['Maintenance & Perawatan Mesin Industri','Pelatihan teknik perawatan mesin','Maintenance','Mampu melakukan perawatan mesin, Mampu membaca manual mesin, Mampu mendeteksi kerusakan, Mampu memperbaiki komponen, Mampu membuat jadwal perawatan preventif'],
                ['Manajemen Produksi & Lean Manufacturing','Pelatihan efisiensi produksi & lean','Manajemen Produksi','Mampu memahami konsep lean, Mampu mengidentifikasi pemborosan produksi, Mampu membuat alur kerja efisien, Mampu mengimplementasikan six sigma dasar, Mampu meningkatkan kualitas & produktivitas'],
                ['Keselamatan & Kesehatan Kerja (K3)','Pelatihan standar K3 industri','Safety & K3 Industri','Mampu memahami regulasi K3, Mampu menggunakan alat pelindung diri, Mampu mendeteksi potensi bahaya, Mampu membuat SOP keselamatan, Mampu mengelola laporan kecelakaan kerja'],
            ],
            'AgriTech' => [
                ['Pertanian Modern Berbasis Teknologi','Pengenalan teknologi smart farming','Pertanian Modern','Mampu memahami konsep smart farming, Mampu menggunakan sensor pertanian, Mampu mengelola irigasi otomatis, Mampu meningkatkan produktivitas, Mampu menjaga keberlanjutan lingkungan'],
                ['Agribisnis & Manajemen Usaha Tani','Pelatihan pengelolaan usaha agribisnis','Agribisnis','Mampu menyusun rencana usaha tani, Mampu memahami manajemen biaya, Mampu melakukan pemasaran hasil, Mampu membangun jaringan usaha, Mampu meningkatkan profitabilitas'],
                ['Teknologi Pasca Panen','Pelatihan penanganan hasil panen','Pasca Panen','Mampu melakukan penyimpanan hasil panen, Mampu mengolah produk pertanian, Mampu mengurangi kehilangan hasil, Mampu meningkatkan nilai tambah produk, Mampu memahami standar mutu pangan'],
                ['Agritech Startup & Inovasi Pertanian','Pengenalan inovasi digital agritech','Inovasi Pertanian','Mampu memahami peluang startup pertanian, Mampu menggunakan aplikasi agritech, Mampu mengembangkan ide inovasi, Mampu merancang model bisnis digital, Mampu membangun ekosistem agritech'],
                ['Keberlanjutan & Pertanian Organik','Pelatihan praktik pertanian berkelanjutan','Pertanian Berkelanjutan','Mampu memahami prinsip organik, Mampu membuat pupuk kompos, Mampu mengurangi penggunaan pestisida, Mampu mengelola lahan secara ramah lingkungan, Mampu merancang sistem pertanian berkelanjutan'],
            ],
            'Maritim' => [
                ['Dasar Kelautan & Navigasi Laut','Pengenalan ilmu kelautan, navigasi & dasar maritim','Kelautan Dasar','Mampu memahami dasar kelautan, Mampu membaca peta laut, Mampu menggunakan kompas & GPS, Mampu memahami arus laut, Mampu melakukan navigasi dasar'],
                ['Teknik Perkapalan & Mesin Kapal','Pelatihan mengenai konstruksi kapal & mesin','Perkapalan','Mampu memahami konstruksi kapal, Mampu mengoperasikan mesin kapal, Mampu merawat mesin kapal, Mampu melakukan troubleshooting, Mampu membuat jadwal maintenance'],
                ['Keselamatan & Survival di Laut','Pelatihan prosedur keselamatan & survival maritime','Safety Maritim','Mampu memahami regulasi keselamatan laut, Mampu menggunakan alat keselamatan, Mampu melakukan prosedur evakuasi, Mampu mengelola keadaan darurat, Mampu bertahan hidup di laut'],
                ['Logistik & Manajemen Pelabuhan','Pelatihan manajemen logistik maritim','Manajemen Maritim','Mampu memahami sistem logistik laut, Mampu mengelola arus barang di pelabuhan, Mampu menggunakan sistem informasi pelabuhan, Mampu memahami regulasi ekspor-impor, Mampu meningkatkan efisiensi pelabuhan'],
                ['Teknologi Kelautan & Riset Laut','Pengenalan teknologi & riset kelautan modern','Riset Kelautan','Mampu menggunakan teknologi riset laut, Mampu mengumpulkan data ekosistem, Mampu menganalisis potensi sumber daya laut, Mampu memahami konservasi laut, Mampu membuat laporan penelitian kelautan'],
            ],
        ];

        $created=0; $updated=0; $now=now();

        $skippedGroups = [];
        foreach ($datasets as $lembagaKey => $courses) {
            $lembagaId = $resolveLembaga($lembagaKey);
            if (!$lembagaId) { $skippedGroups[] = $lembagaKey; continue; }
            foreach ($courses as [$nama,$deskripsi,$kategori,$capaian]) {
                $data = [
                    'lembaga_id' => $lembagaId,
                    'nama_pelatihan' => $nama,
                    'deskripsi' => $deskripsi,
                    'kategori' => $kategori,
                    'capaian_pembelajaran' => $capaian,
                    'updated_at' => $now,
                ];
                $existing = Pelatihan::where('lembaga_id',$lembagaId)->where('nama_pelatihan',$nama)->first();
                if ($existing) { $existing->fill($data)->save(); $updated++; }
                else { $data['created_at']=$now; Pelatihan::create($data); $created++; }
            }
        }

        $this->command?->info("Seed pelatihan statis selesai: created=$created updated=$updated lembaga_baru=$createdLembaga");
        if (!empty($skippedGroups)) {
            $this->command?->warn('Kelompok terlewati (tidak bisa dibuat): '.implode(', ', $skippedGroups));
        }
    }
}
