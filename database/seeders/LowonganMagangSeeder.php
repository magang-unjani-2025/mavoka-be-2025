<?php

namespace Database\Seeders;

use App\Models\LowonganMagang;
use App\Models\Perusahaan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

class LowonganMagangSeeder extends Seeder
{

    public function run(): void
    {
        // Data statis gabungan dari semua CSV yang diberikan user
        // Kolom: nama_perusahaan, judul_lowongan, deskripsi, posisi, kuota, lokasi_penempatan, persyaratan, benefit, status, deadline_lamaran
        $rows = [
            // PT Tech Indo Solutions (dari file tech solusi indo)
            ['PT Tech Indo Solutions','Magang Web Developer','Kesempatan bagi siswa SMK untuk belajar membuat website modern menggunakan HTML, CSS, dan JavaScript, serta dikenalkan ke framework populer.','Web Developer Intern',3,'Jakarta Selatan, DKI Jkt','Siswa SMK jurusan RPL/TKJ, dasar HTML & CSS, semangat belajar.','Sertifikat, uang transport, pengalaman proyek nyata, networking.','Aktif','2024-10-15'],
            ['PT Tech Indo Solutions','Magang UI/UX Designer','Belajar langsung dengan tim desain profesional dalam membuat antarmuka aplikasi yang menarik dan mudah digunakan, cocok untuk siswa SMK kreatif.','UI/UX Designer Intern',2,'Jakarta Selatan, DKI Jkt','Siswa SMK RPL/DKV, bisa desain pakai Figma/Canva, kreatif, komunikatif.','Mentoring UI/UX, sertifikat, uang makan & transport, portofolio nyata.','Aktif','2024-10-20'],
            ['PT Tech Indo Solutions','Magang Data Analyst','Program magang untuk mengenalkan siswa SMK bagaimana cara mengolah data sederhana dan membuat laporan yang bermanfaat bagi perusahaan.','Data Analyst Intern',2,'Jakarta Selatan, DKI Jkt','Siswa SMK RPL/Akutansi, bisa Excel dasar, teliti dan mau belajar SQL.','Sertifikat, uang saku, pengalaman proyek data nyata, mentoring.','Aktif','2024-10-25'],
            ['PT Tech Indo Solutions','Magang Mobile Developer','Siswa SMK bisa ikut terlibat langsung dalam pembuatan aplikasi mobile sederhana berbasis Android menggunakan Flutter/Java.','Mobile Dev Intern',2,'Jakarta Selatan, DKI Jkt','Siswa SMK RPL, paham dasar Java/Kotlin, suka coba hal baru.','Sertifikat, uang transport, mentoring langsung dari developer senior.','Aktif','2024-11-01'],
            ['PT Tech Indo Solutions','Magang IT Support','Magang ini memberi pengalaman nyata untuk siswa SMK dalam membantu instalasi software, perawatan hardware, dan support teknis di kantor.','IT Support Intern',3,'Jakarta Selatan, DKI Jkt','Siswa SMK TKJ, dasar troubleshooting komputer & jaringan, komunikatif.','Uang saku, sertifikat, pengalaman kerja lapangan langsung di perusahaan.','Aktif','2024-11-05'],

            // SehatHub Indonesia
            ['SehatHub Indonesia','Magang Administrasi Kesehatan','Program magang ini memberikan kesempatan siswa SMK untuk belajar cara mengelola dokumen pasien, input data kesehatan, dan memahami sistem administrasi rumah sakit digital.','Administrasi Intern',4,'Bandung, Jawa Barat','Siswa SMK AKL/OTKP, bisa Microsoft Word & Excel dasar, teliti, rapi.','Sertifikat, uang transport, pengalaman kerja di industri kesehatan.','Aktif','2024-10-18'],
            ['SehatHub Indonesia','Magang Customer Service','Siswa SMK akan dilatih melayani pasien/klien secara profesional, menjawab pertanyaan, dan membantu pendaftaran layanan kesehatan online maupun offline.','Customer Service Intern',3,'Bandung, Jawa Barat','Siswa SMK semua jurusan, ramah, komunikatif, percaya diri.','Sertifikat, uang makan, uang transport, pengalaman langsung melayani klien.','Aktif','2024-10-22'],
            ['SehatHub Indonesia','Magang IT Support Klinik','Kesempatan belajar bagi siswa SMK untuk membantu tim IT menjaga sistem aplikasi klinik tetap berjalan lancar, termasuk troubleshooting komputer dan jaringan.','IT Support Intern',2,'Bandung, Jawa Barat','Siswa SMK TKJ/RPL, bisa instalasi software, basic jaringan.','Mentoring IT, uang saku, sertifikat, pengalaman kerja lapangan.','Aktif','2024-10-28'],
            ['SehatHub Indonesia','Magang Desain Grafis Kesehatan','Magang ini mengajarkan siswa SMK membuat konten visual edukasi kesehatan untuk sosial media, brosur, dan materi kampanye kesehatan digital.','Graphic Designer Intern',2,'Bandung, Jawa Barat','Siswa SMK MM/DKV, bisa Canva/Figma/CorelDraw, kreatif.','Portofolio nyata, mentoring, sertifikat, uang makan & transport.','Aktif','2024-11-02'],
            ['SehatHub Indonesia','Magang Data Entry Kesehatan','Siswa SMK akan membantu tim menginput data pasien, laporan konsultasi, dan memastikan data tersimpan dengan rapi serta mudah diakses.','Data Entry Intern',3,'Bandung, Jawa Barat','Siswa SMK AKL/OTKP/RPL, bisa mengetik cepat & teliti.','Sertifikat, uang transport, pengalaman kerja di sistem database medis.','Aktif','2024-11-07'],

            // EduSmart Nusantara
            ['EduSmart Nusantara','Magang Content Creator Edu','Kesempatan bagi siswa SMK untuk belajar membuat konten edukasi digital seperti video pembelajaran, artikel, dan infografis yang menarik untuk siswa lain.','Content Creator Intern',3,'Yogyakarta, DIY','Siswa SMK MM/DKV, bisa Canva/CapCut, kreatif, suka berbagi ilmu.','Sertifikat, uang transport, pengalaman produksi konten nyata.','Aktif','2024-10-20'],
            ['EduSmart Nusantara','Magang Admin E-Learning','Siswa SMK akan dilatih mengelola sistem e-learning, mengatur kelas online, dan membantu siswa/guru dalam proses belajar digital.','Admin E-Learning Intern',2,'Yogyakarta, DIY','Siswa SMK RPL/OTKP, bisa komputer dasar & teliti dalam input data.','Sertifikat, uang saku, pengalaman mengelola platform edukasi.','Aktif','2024-10-25'],
            ['EduSmart Nusantara','Magang Desain Multimedia','Magang ini memberi pengalaman membuat desain grafis dan animasi sederhana untuk materi pembelajaran interaktif.','Multimedia Designer Intern',2,'Yogyakarta, DIY','Siswa SMK MM/DKV, bisa Photoshop/Illustrator, suka desain kreatif.','Portofolio nyata, sertifikat, mentoring dari desainer berpengalaman.','Aktif','2024-10-30'],
            ['EduSmart Nusantara','Magang IT Support EduSmart','Belajar langsung tentang cara mendukung sistem komputer di ruang kelas digital dan aplikasi pembelajaran online.','IT Support Intern',3,'Yogyakarta, DIY','Siswa SMK TKJ/RPL, paham instalasi software, rajin & mau belajar.','Uang transport, sertifikat, pengalaman langsung di dunia kerja.','Aktif','2024-11-04'],
            ['EduSmart Nusantara','Magang Social Media Officer','Siswa SMK akan belajar mengelola akun media sosial EduSmart, membuat postingan edukatif, dan meningkatkan interaksi dengan audiens online.','Social Media Intern',2,'Yogyakarta, DIY','Siswa SMK semua jurusan, aktif di medsos, bisa bikin caption menarik.','Mentoring digital marketing, uang saku, sertifikat, portofolio sosial media.','Aktif','2024-11-10'],

            // GreenBuild Indonesia
            ['GreenBuild Indonesia','Magang Drafter Bangunan','Memberi pengalaman bagi siswa SMK untuk membuat gambar kerja bangunan dengan software CAD serta mendukung tim arsitek dalam perencanaan proyek.','Drafter Intern',2,'Bekasi, Jabar','Siswa SMK TGB/DKV, bisa AutoCAD/SketchUp, teliti, suka detail teknis.','Sertifikat, uang saku, mentoring dari arsitek berpengalaman.','Aktif','2024-10-24'],
            ['GreenBuild Indonesia','Magang Administrasi Proyek','Siswa SMK akan belajar mengelola dokumen proyek, laporan harian, dan mendukung kebutuhan administrasi konstruksi di lapangan.','Admin Proyek Intern',3,'Bekasi, Jabar','Siswa SMK OTKP/AKL, bisa Excel dasar, teliti, disiplin.','Sertifikat, uang transport, pengalaman nyata di proyek konstruksi.','Aktif','2024-10-28'],
            ['GreenBuild Indonesia','Magang IT Support Konstruksi','Kesempatan bagi siswa SMK untuk mendukung sistem IT di kantor proyek, instalasi software, serta troubleshooting komputer/laptop tim engineer.','IT Support Intern',2,'Bekasi, Jabar','Siswa SMK TKJ/RPL, bisa komputer dasar, siap kerja di lapangan.','Uang saku, sertifikat, pengalaman kerja langsung di lokasi proyek.','Aktif','2024-11-02'],
            ['GreenBuild Indonesia','Magang Dokumentasi Proyek','Siswa SMK dilatih untuk mengambil foto/video progres pembangunan serta membuat laporan visual untuk keperluan internal perusahaan.','Dokumentasi Proyek Intern',2,'Bekasi, Jabar','Siswa SMK MM/DKV, bisa kamera/HP, kreatif, paham editing dasar.','Portofolio nyata, sertifikat, mentoring multimedia.','Aktif','2024-11-07'],
            ['GreenBuild Indonesia','Magang Quality Control Lapangan','Memberi pengalaman nyata dalam memeriksa hasil pekerjaan konstruksi, memastikan sesuai standar, serta belajar langsung prosedur QC di lapangan.','QC Lapangan Intern',3,'Bekasi, Jabar','Siswa SMK TGB/TKJ, teliti, disiplin, mau belajar prosedur kerja teknis.','Uang transport, sertifikat, pengalaman langsung di dunia konstruksi.','Aktif','2024-11-12'],

            // Foodiez Nusantara
            ['Foodiez Nusantara','Magang Admin Order Online','Kesempatan untuk siswa SMK belajar mengelola pesanan makanan dari aplikasi Foodiez, input data, dan membantu tim customer support.','Admin Order Intern',3,'Surabaya, Jatim','Siswa SMK OTKP/AKL, bisa komputer dasar, teliti, komunikatif.','Sertifikat, uang saku, pengalaman mengelola order e-commerce kuliner.','Aktif','2024-10-22'],
            ['Foodiez Nusantara','Magang Fotografi Kuliner','Siswa SMK akan belajar memotret menu makanan agar terlihat menarik untuk katalog aplikasi dan media sosial.','Food Photographer Intern',2,'Surabaya, Jatim','Siswa SMK MM/DKV, bisa kamera DSLR/HP, kreatif dalam angle foto.','Portofolio nyata, sertifikat, mentoring dari fotografer profesional.','Aktif','2024-10-27'],
            ['Foodiez Nusantara','Magang Desain Konten Sosmed','Magang ini mengajarkan siswa SMK membuat desain konten promosi makanan untuk Instagram, TikTok, dan platform digital lainnya.','Graphic Designer Intern',2,'Surabaya, Jatim','Siswa SMK MM/DKV, bisa Canva/Photoshop, suka desain.','Uang transport, sertifikat, portofolio sosial media nyata.','Aktif','2024-11-01'],
            ['Foodiez Nusantara','Magang Delivery Assistant','Memberi pengalaman langsung untuk siswa SMK dalam mendukung proses pengiriman makanan dan koordinasi dengan kurir aplikasi.','Delivery Support Intern',4,'Surabaya, Jatim','Siswa SMK semua jurusan, fisik sehat, disiplin, komunikatif.','Uang makan, uang transport, pengalaman kerja lapangan langsung.','Aktif','2024-11-06'],
            ['Foodiez Nusantara','Magang Quality Control Makanan','Siswa SMK akan diajarkan cara menjaga kualitas makanan, memeriksa pesanan sebelum dikirim, dan memahami standar higienis di industri kuliner.','QC Food Intern',3,'Surabaya, Jatim','Siswa SMK Boga/TP, paham dasar kebersihan makanan, teliti.','Sertifikat, uang saku, pengalaman langsung di industri F&B.','Aktif','2024-11-12'],

            // Travelo Nusantara / Travelo Indonesia di CSV gunakan "Travelo Indonesia" -> cocokkan ke PerusahaanSeeder 'Travelo Nusantara'
            ['Travelo Nusantara','Magang Admin Reservasi Travel','Siswa SMK akan belajar mengelola reservasi tiket, hotel, dan paket wisata, serta membantu pelanggan dalam proses booking.','Admin Reservasi Intern',3,'Jakarta Pusat','Siswa SMK OTKP/AKL, teliti, bisa komputer dasar, ramah.','Sertifikat, uang saku, pengalaman kerja nyata di travel agency.','Aktif','2024-10-28'],
            ['Travelo Nusantara','Magang Tour Guide Assistant','Memberi pengalaman mendampingi turis lokal maupun mancanegara, mengenal destinasi wisata, serta mempraktikkan komunikasi yang baik.','Tour Guide Intern',4,'Jakarta & Bandung','Siswa SMK Pariwisata/Bahasa, percaya diri, komunikatif.','Sertifikat, uang transport, pengalaman langsung mendampingi turis.','Aktif','2024-11-01'],
            ['Travelo Nusantara','Magang Social Media Tourism','Siswa SMK akan belajar membuat konten promosi destinasi wisata di Instagram, TikTok, dan platform digital lainnya.','Social Media Intern',2,'Jakarta Pusat','Siswa SMK MM/DKV, bisa Canva/CapCut, kreatif, aktif di medsos.','Portofolio nyata, sertifikat, mentoring digital marketing pariwisata.','Aktif','2024-11-05'],
            ['Travelo Nusantara','Magang Desain Brosur & Katalog','Kesempatan untuk siswa SMK membuat brosur paket wisata, katalog tour, serta materi promosi cetak & digital lainnya.','Graphic Designer Intern',2,'Jakarta Pusat','Siswa SMK MM/DKV, bisa Photoshop/CorelDraw, kreatif, detail.','Sertifikat, uang transport, portofolio desain nyata.','Aktif','2024-11-10'],
            ['Travelo Nusantara','Magang Customer Service Travel','Siswa SMK akan dilatih menjawab pertanyaan pelanggan, memberikan informasi destinasi, serta menangani keluhan terkait perjalanan wisata.','Customer Service Intern',3,'Jakarta Pusat','Siswa SMK semua jurusan, komunikatif, ramah, sabar menghadapi pelanggan.','Sertifikat, uang makan, uang transport, pengalaman nyata di bidang pariwisata.','Aktif','2024-11-14'],

            // Fintek Smart Solutions -> PerusahaanSeeder mungkin punya nama berbeda: cek 'Fintek Smart Solutions'? (tidak ada, ada mungkin brand lain). Gunakan pencarian LIKE.
            ['Fintek Smart Solutions','Magang Admin Data Keuangan','Siswa SMK akan belajar menginput transaksi keuangan, laporan harian, serta membantu tim accounting dalam sistem fintech digital.','Admin Data Intern',3,'Jakarta Selatan','Siswa SMK AKL/OTKP, teliti, bisa Excel dasar, suka angka.','Sertifikat, uang saku, pengalaman kerja nyata di bidang fintech.','Aktif','2024-11-02'],
            ['Fintek Smart Solutions','Magang Customer Service Online','Kesempatan bagi siswa SMK untuk melayani nasabah fintech melalui chat/email, menjawab pertanyaan, dan membantu pendaftaran akun.','Customer Service Intern',3,'Jakarta Selatan','Siswa SMK semua jurusan, ramah, komunikatif, percaya diri.','Sertifikat, uang makan, uang transport, pengalaman customer handling.','Aktif','2024-11-06'],
            ['Fintek Smart Solutions','Magang Social Media & EduFin','Siswa SMK akan belajar membuat konten edukasi finansial digital (artikel ringan, video, infografis) untuk media sosial fintech.','Social Media Intern',2,'Jakarta Selatan','Siswa SMK MM/DKV, kreatif, aktif di medsos, bisa Canva/CapCut.','Portofolio nyata, sertifikat, mentoring digital marketing keuangan.','Aktif','2024-11-10'],
            ['Fintek Smart Solutions','Magang IT Support Fintech','Memberikan pengalaman mendukung sistem IT, troubleshooting aplikasi, serta membantu menjaga keamanan data di perusahaan fintech.','IT Support Intern',2,'Jakarta Selatan','Siswa SMK TKJ/RPL, paham komputer & software dasar, teliti.','Sertifikat, uang transport, pengalaman nyata di bidang IT fintech.','Aktif','2024-11-14'],
            ['Fintek Smart Solutions','Magang Desain UI/UX Dasar','Siswa SMK akan dilatih membuat desain aplikasi mobile/web fintech sederhana, wireframe, dan memahami prinsip dasar desain UI/UX.','UI/UX Designer Intern',2,'Jakarta Selatan','Siswa SMK MM/DKV/RPL, kreatif, suka desain aplikasi.','Portofolio nyata, sertifikat, mentoring langsung dari tim desain UI/UX.','Aktif','2024-11-18'],

            // Energi Indo Power
            ['Energi Indo Power','Magang Admin Data Listrik','Siswa SMK akan belajar menginput data penggunaan listrik pelanggan, laporan bulanan, serta mendukung digitalisasi data energi.','Admin Data Intern',3,'Jakarta Timur','Siswa SMK OTKP/AKL, teliti, bisa komputer dasar.','Sertifikat, uang saku, pengalaman mengelola data energi.','Aktif','2024-11-01'],
            ['Energi Indo Power','Magang Teknisi Listrik Dasar','Memberikan pengalaman langsung dalam instalasi listrik skala kecil dan pemeliharaan peralatan listrik di lapangan.','Teknisi Listrik Intern',3,'Jakarta Timur','Siswa SMK TITL/TKJ, paham dasar listrik, disiplin, fisik sehat.','Sertifikat, uang transport, pengalaman langsung di lapangan.','Aktif','2024-11-05'],
            ['Energi Indo Power','Magang IT Support Energi','Siswa SMK akan dilatih untuk mendukung sistem IT perusahaan, termasuk perangkat keras, software, dan aplikasi monitoring energi.','IT Support Intern',2,'Jakarta Timur','Siswa SMK TKJ/RPL, paham komputer & troubleshooting dasar.','Sertifikat, uang saku, pengalaman nyata di bidang IT industri energi.','Aktif','2024-11-09'],
            ['Energi Indo Power','Magang Dokumentasi Proyek Energi','Siswa SMK akan belajar membuat dokumentasi foto/video proyek instalasi listrik & pembangkit, serta membantu laporan visual.','Dokumentasi Proyek Intern',2,'Jakarta Timur','Siswa SMK MM/DKV, bisa fotografi/video editing dasar, kreatif.','Portofolio nyata, sertifikat, mentoring multimedia di industri energi.','Aktif','2024-11-13'],
            ['Energi Indo Power','Magang Quality Control Panel','Memberi pengalaman dalam memeriksa kualitas instalasi panel listrik, mengikuti standar keamanan, serta mendukung tim QC di lapangan.','QC Panel Intern',2,'Jakarta Timur','Siswa SMK TITL/TKJ, teliti, suka kerja detail teknis.','Sertifikat, uang transport, pengalaman nyata di bidang QC industri energi.','Aktif','2024-11-18'],

            // Ocean Blue Logistics
            ['Ocean Blue Logistics','Magang Admin Pengiriman','Siswa SMK akan belajar mengelola data pengiriman, tracking barang, serta membuat laporan keluar-masuk kontainer.','Admin Pengiriman Intern',3,'Surabaya, Jatim','Siswa SMK OTKP/AKL, teliti, bisa komputer dasar.','Sertifikat, uang saku, pengalaman nyata di logistik pelabuhan.','Aktif','2024-11-04'],
            ['Ocean Blue Logistics','Magang Staff Gudang','Memberikan pengalaman mengatur stok barang, mencatat keluar-masuk barang, dan mendukung operasional gudang logistik laut.','Staff Gudang Intern',3,'Surabaya, Jatim','Siswa SMK semua jurusan, fisik sehat, disiplin, teliti.','Uang makan, uang transport, sertifikat, pengalaman kerja di gudang nyata.','Aktif','2024-11-08'],
            ['Ocean Blue Logistics','Magang Customer Service Logistik','Siswa SMK akan belajar menangani pelanggan, menjawab pertanyaan seputar status barang, dan membantu kebutuhan administrasi pelanggan.','Customer Service Intern',2,'Surabaya, Jatim','Siswa SMK semua jurusan, komunikatif, ramah, percaya diri.','Sertifikat, uang transport, pengalaman melayani pelanggan logistik.','Aktif','2024-11-12'],
            ['Ocean Blue Logistics','Magang Dokumentasi Ekspor-Impor','Kesempatan bagi siswa SMK untuk membantu menyiapkan dokumen ekspor-impor, invoice, dan dokumen pelayaran.','Dokumentasi Intern',2,'Surabaya, Jatim','Siswa SMK OTKP/AKL, teliti, suka administrasi, bisa komputer.','Sertifikat, uang saku, pengalaman nyata di ekspor-impor.','Aktif','2024-11-16'],
            ['Ocean Blue Logistics','Magang Tracking & Monitoring','Siswa SMK akan dilatih memantau pergerakan kapal dan kontainer menggunakan software tracking digital, serta melaporkan status pengiriman.','Tracking Intern',2,'Surabaya, Jatim','Siswa SMK TKJ/RPL/OTKP, bisa komputer dasar, suka hal detail.','Sertifikat, uang transport, pengalaman menggunakan sistem tracking logistik.','Aktif','2024-11-21'],

            // Mediatron Media Group
            ['Mediatron Media Group','Magang Reporter Muda','Siswa SMK akan belajar menulis berita sederhana, melakukan wawancara, serta memahami alur kerja redaksi media cetak dan online.','Reporter Intern',3,'Jakarta Pusat','Siswa SMK semua jurusan, suka menulis, komunikatif, percaya diri.','Sertifikat, uang saku, pengalaman langsung di dunia jurnalistik.','Aktif','2024-11-06'],
            ['Mediatron Media Group','Magang Desain Grafis Media','Kesempatan bagi siswa SMK untuk membuat layout majalah, banner digital, dan materi visual untuk promosi konten media.','Desain Grafis Intern',2,'Jakarta Pusat','Siswa SMK MM/DKV, bisa Corel/Photoshop/Canva, kreatif.','Portofolio nyata, sertifikat, mentoring langsung dari desainer media.','Aktif','2024-11-10'],
            ['Mediatron Media Group','Magang Fotografi & Videografi','Siswa SMK akan dilatih untuk dokumentasi foto & video liputan acara, editing dasar, serta mendukung tim produksi konten visual.','Foto & Video Intern',2,'Jakarta Pusat','Siswa SMK MM/DKV, bisa kamera dasar, suka multimedia.','Sertifikat, portofolio nyata, uang transport, pengalaman produksi konten.','Aktif','2024-11-14'],
            ['Mediatron Media Group','Magang Social Media Management','Siswa SMK akan belajar membuat caption, posting, serta analisis engagement untuk akun media sosial perusahaan media.','Social Media Intern',2,'Jakarta Pusat','Siswa SMK MM/DKV, aktif di medsos, kreatif, bisa Canva/CapCut.','Sertifikat, portofolio nyata, uang saku, pengalaman nyata di social media.','Aktif','2024-11-18'],
            ['Mediatron Media Group','Magang Admin Redaksi','Memberikan pengalaman dalam mendukung kegiatan redaksi dengan mengatur jadwal rapat, mendata naskah berita, dan membantu administrasi umum.','Admin Redaksi Intern',2,'Jakarta Pusat','Siswa SMK OTKP/AKL, teliti, suka administrasi.','Sertifikat, uang transport, pengalaman nyata bekerja di lingkungan redaksi.','Aktif','2024-11-23'],

            // Artify Indonesia
            ['Artify Indonesia','Magang Desain Grafis','Siswa SMK akan belajar membuat poster, brosur, logo sederhana, serta desain promosi digital untuk klien.','Desain Grafis Intern',3,'Bandung, Jabar','Siswa SMK MM/DKV, bisa Corel/Photoshop/Canva, kreatif.','Portofolio nyata, sertifikat, mentoring langsung dari desainer profesional.','Aktif','2024-11-05'],
            ['Artify Indonesia','Magang Ilustrasi Digital','Kesempatan untuk siswa SMK membuat ilustrasi karakter, konsep seni, dan membantu project desain digital klien.','Illustrator Intern',2,'Bandung, Jabar','Siswa SMK MM/DKV, bisa menggambar manual/digital, kreatif.','Sertifikat, portofolio, uang saku, pengalaman nyata di industri kreatif.','Aktif','2024-11-09'],
            ['Artify Indonesia','Magang Konten Sosial Media','Siswa SMK akan belajar membuat konten visual (gambar & video) untuk media sosial, serta mengatur kalender posting.','Social Media Content Intern',2,'Bandung, Jabar','Siswa SMK MM/DKV, aktif di medsos, bisa CapCut/Canva.','Portofolio nyata, sertifikat, uang transport, mentoring digital marketing.','Aktif','2024-11-13'],
            ['Artify Indonesia','Magang Fotografi & Videografi','Memberikan pengalaman memotret dan membuat video pendek untuk dokumentasi acara seni, pameran, dan promosi klien.','Foto & Video Intern',2,'Bandung, Jabar','Siswa SMK MM/DKV, bisa kamera dasar, suka multimedia.','Portofolio nyata, sertifikat, uang transport, pengalaman produksi konten.','Aktif','2024-11-17'],
            ['Artify Indonesia','Magang Admin Proyek Kreatif','Siswa SMK akan belajar mengatur jadwal, mendata kebutuhan proyek, serta membantu tim desain dalam administrasi sederhana.','Admin Proyek Intern',2,'Bandung, Jabar','Siswa SMK OTKP/AKL, teliti, suka administrasi.','Sertifikat, uang saku, pengalaman nyata mengelola proyek kreatif.','Aktif','2024-11-22'],
        ];

    $created=0; $updated=0; $now = now(); $createdCompany=0; $skipped=0; $duplicates=0;

    // Normalizer nama perusahaan
    $normalize = function(string $v){ return preg_replace('/[^a-z0-9]+/','', strtolower($v)); };
    $existingCompanyMap = [];
    foreach (Perusahaan::pluck('id','nama_perusahaan') as $nama=>$id) { $existingCompanyMap[$normalize($nama)] = $id; }

        // Alias nama perusahaan untuk menyelaraskan variasi penulisan dengan data di PerusahaanSeeder
        $aliasMap = [
            'foodiez nusantara' => 'Foodez Nusantara', // ejaan berbeda
            'travelo indonesia' => 'Travelo Nusantara', // jika muncul variasi lain
        ];

        foreach ($rows as [$namaPerusahaan,$judul,$deskripsi,$posisi,$kuota,$lokasi,$persyaratan,$benefit,$status,$deadline]) {
            $aliasKey = strtolower(trim($namaPerusahaan));
            if (isset($aliasMap[$aliasKey])) {
                $namaPerusahaan = $aliasMap[$aliasKey];
            }
            // Normalisasi status ke 'buka'/'tutup'
            $st = strtolower(trim($status));
            $statusNorm = in_array($st,['aktif','buka','open']) ? 'buka' : (in_array($st,['tutup','close','nonaktif']) ? 'tutup' : $st);

            // Temukan / buat perusahaan dengan normalisasi
            $norm = $normalize($namaPerusahaan);
            $companyId = $existingCompanyMap[$norm] ?? null;
            if (!$companyId) {
                // Coba partial match
                foreach ($existingCompanyMap as $key=>$val) { if(str_contains($key,$norm) || str_contains($norm,$key)) { $companyId=$val; break; } }
            }
            if (!$companyId) {
                $perusahaan = Perusahaan::create([
                    'username' => Str::slug(substr($namaPerusahaan,0,18)).rand(10,99),
                    'email' => Str::slug(substr($namaPerusahaan,0,18)).rand(100,999).'@example.local',
                    'password' => bcrypt('password'),
                    'status_verifikasi' => 'Terverifikasi',
                    'tanggal_verifikasi' => $now->toDateString(),
                    'nama_perusahaan' => $namaPerusahaan,
                    'bidang_usaha' => 'Lainnya',
                    // Kolom web_perusahaan ternyata NOT NULL di schema, beri placeholder agar lolos constraint
                    'web_perusahaan' => 'https://placeholder.' . Str::slug($namaPerusahaan) . '.local',
                    'deskripsi_usaha' => 'Generated placeholder',
                    'alamat' => '-',
                    'kontak' => '-',
                    'penanggung_jawab' => 'System',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                $companyId = $perusahaan->id; $existingCompanyMap[$norm] = $companyId; $createdCompany++;
            } else {
                $perusahaan = Perusahaan::find($companyId);
            }

            $data = [
                'perusahaan_id' => $perusahaan->id,
                'judul_lowongan' => $judul,
                'deskripsi' => $deskripsi,
                'posisi' => $posisi,
                'kuota' => (int)$kuota,
                'lokasi_penempatan' => $lokasi,
                'persyaratan' => $persyaratan,
                'benefit' => $benefit,
                'tugas_tanggung_jawab' => null,
                'status' => $statusNorm,
                'deadline_lamaran' => $deadline ?: null,
                'periode_awal' => null,
                'periode_akhir' => null,
                'updated_at' => $now,
            ];

            $existing = LowonganMagang::where('perusahaan_id',$data['perusahaan_id'])
                ->where('judul_lowongan',$data['judul_lowongan'])
                ->first();
            if ($existing) { $existing->fill($data)->save(); $updated++; $duplicates++; }
            else { $data['created_at']=$now; LowonganMagang::create($data); $created++; }
        }

        $this->command?->info("Seed lowongan statis selesai: created=$created updated=$updated perusahaan_baru=$createdCompany duplicate_hit=$duplicates");
    }
}
