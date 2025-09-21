<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Perusahaan;

class PerusahaanSeeder extends Seeder
{
    public function run(): void
    {
        // Data statis dari gambar: username,email,password(status plain -> di-hash), status_verifikasi, tanggal_verifikasi,
        // nama_perusahaan, bidang_usaha, web_perusahaan, deskripsi_usaha, alamat, kontak, penanggung_jawab
        $rows = [
            ['techindo01','info@techindo.com','pass1234','Terverifikasi','2024-08-12','PT Tech Indo Solutions','Teknologi','www.oceshakins.com','Penyedia solusi IT untuk perusahaan menengah dan besar','Jakarta Selatan, DKI Jakarta','81234567890','Andi Pratama'],
            ['sehcatare','contact@sehahub.id','sehat202','Terverifikasi','2024-09-01','SehatHub Indonesia','Kesehatan','www.oceshakins.com','Platform kesehatan digital & konsultasi dokter','Sleman, DIY','82197865432','Siti Rahmawati'],
            ['edusmart','support@edusmart.co.id','eduSmart','Terverifikasi','2024-09-01','EduSmart Nusantara','Pendidikan','www.oceshakins.com','Startup e-learning interaktif','Yogyakarta, DIY','8122341234','Budi Santoso'],
            ['foodez','hello@foodez.com','foodez@9','Terverifikasi','2024-07-20','Foodez Nusantara','Kuliner','www.oceshakins.com','Aplikasi delivery makanan khas Nusantara','Surabaya, Jawa Timur','83145678912','Lina Kartika'],
            ['greenbuild','admin@greenbuild.id','green2023','Terverifikasi','2024-09-01','GreenBuild Indonesia','Konstruksi','www.oceshakins.com','Perusahaan konstruksi ramah lingkungan','Bandung, Jawa Barat','82917865432','Rizky Maulana'],
            ['fashionly','cs@fashionly.co','style123','Terverifikasi','2024-09-01','Fashionly Co.','Fashion & Retail','www.oceshakins.com','Marketplace fashion modern & UMKM','Denpasar, Bali','82345678901','Ayu Lestari'],
            ['agritechd','info@agritechind.com','agri2024','Terverifikasi','2024-08-30','AgriTech Indonesia','Pertanian','www.oceshakins.com','Solusi digital untuk petani Indonesia','Solo, Jawa Tengah','81456788934','Dedi Gunawan'],
            ['traveloka02','contact@travelco.id','travel@2','Terverifikasi','2024-08-19','Travelo Nusantara','Pariwisata','www.oceshakins.com','Aplikasi pemesanan tiket & hotel','Medan, Sumatera Utara','8122345678','Irma Suryani'],
            ['medisafe','admin@medisafe.id','medisafe','Terverifikasi','2024-09-01','MediSafe Indonesia','Farmasi','www.oceshakins.com','Distributor obat dan alat kesehatan','Makassar, Sulawesi Selatan','82197823456','Hasan Basri'],
            ['kreatify','hello@kreatify.com','kreatif2','Terverifikasi','2024-09-01','Kreatify Digital Agency','Digital Marketing','www.oceshakins.com','Agensi kreatif untuk branding','Malang, Jawa Timur','81234567890','Nadia Putri'],
            ['energindo','info@energindo.com','energi20','Terverifikasi','2024-08-25','Energi Indo Power','Energi','www.oceshakins.com','Penyedia energi terbarukan untuk industri','Balikpapan, Kalimantan Timur','82199988877','Joko Pranoto'],
            ['finteksmart','admin@finteksmart.id','fintek@3','Terverifikasi','2024-09-01','Fintek Smart Solutions','Keuangan','www.oceshakins.com','Startup fintech untuk UMKM','Jakarta Pusat, DKI Jakarta','81345678945','Ratna Wulandari'],
            ['rumahceria','cs@rumahceria.co.id','rumah123','Terverifikasi','2024-09-10','Rumah Ceria Property','Properti','www.oceshakins.com','Agen properti dan rumah hunian','Tangerang, Banten','81366777788','Yusuf Ibrahim'],
            ['oceanblue','hello@oceanblue.com','ocean888','Terverifikasi','2024-09-05','Ocean Blue Logistics','Logistik','www.oceshakins.com','Jasa ekspedisi laut domestik','Batam, Kepulauan Riau','82145678901','Mega Andriani'],
            ['artify','contact@artify.id','artify20','Terverifikasi','2024-08-27','Artify Indonesia','Seni & Kreatif','www.oceshakins.com','Platform seni digital untuk kreator lokal','Solo, Jawa Tengah','81922334455','Galih Saputra'],
            ['mediatrn','support@mediatrn.co','media123','Terverifikasi','2024-08-30','Mediatron Media Group','Media & Hiburan','www.oceshakins.com','Produksi konten & media publik','Bandung, Jawa Barat','82134567890','Dian Anggraini'],
            ['smarttrans','info@smarttrans.id','trans%202','Terverifikasi','2024-09-02','SmartTrans Indonesia','Transportasi','www.oceshakins.com','Layanan transportasi dan logistik','Samarinda, Kalimantan Timur','81399912345','Farhan Yusuf'],
            ['gardenfresh','hello@gardenfresh.com','garden112','Terverifikasi','2024-09-01','Garden Fresh Foods','Agribisnis','www.oceshakins.com','Produksi dan distribusi sayuran segar','Bogor, Jawa Barat','82123498765','Marlina Dewi'],
            ['luxehotel','admin@luxehotel.id','luxe2024','Terverifikasi','2024-08-28','Luxe Hotel Indonesia','Hospitality','www.oceshakins.com','Jaringan hotel premium di Indonesia','Bali, Denpasar','81255567891','Kevin Pradipta'],
            ['gameverse','cs@gameverse.co','game2024','Terverifikasi','2024-08-30','GameVerse Nusantara','Game & Esports','www.oceshakins.com','Platform game online & turnamen','Bandung, Jawa Barat','81123456789','Arya Nugraha'],
            ['ecofuture','info@ecofuture.id','eco20245','Terverifikasi','2024-09-06','EcoFuture Nusantara','Lingkungan','www.oceshakins.com','Startup pengolahan limbah modern','Depok, Jawa Barat','81266665544','Rani Prameswari'],
            ['cyberguard','admin@cyberguard.co.id','cyber%99','Terverifikasi','2024-08-29','CyberGuard Indonesia','Keamanan IT','www.oceshakins.com','Penyedia jasa keamanan siber','Jakarta Utara, DKI Jakarta','82777778990','Aditya Surya'],
            ['bamboolife','hello@bamboolife.com','bamboo20','Terverifikasi','2024-09-09','BambooLife Indonesia','Furniture & Dekor','www.oceshakins.com','Produk furniture bambu ramah lingkungan','Sleman, DIY','82333444555','Maya Handayani'],
            ['stellartech','support@stellartech.id','st3llar!','Terverifikasi','2024-09-14','StellarTech Nusantara','Teknologi','www.oceshakins.com','Startup AI & big data untuk industri','Bandung, Jawa Barat','82199992233','Rangga Wirawan'],
        ];

        $created=0; $updated=0; $now = now();
        foreach ($rows as [$username,$email,$plainPassword,$status,$tglVerif,$namaPerusahaan,$bidang,$web,$deskripsi,$alamat,$kontak,$pic]) {
            $data = [
                'username' => $username,
                'email' => $email,
                'password' => Hash::make($plainPassword),
                'status_verifikasi' => $status,
                'tanggal_verifikasi' => $tglVerif ?: $now,
                'nama_perusahaan' => $namaPerusahaan,
                'bidang_usaha' => $bidang,
                'web_perusahaan' => $web,
                'deskripsi_usaha' => $deskripsi,
                'alamat' => $alamat,
                'kontak' => $kontak,
                'penanggung_jawab' => $pic,
                'otp' => Str::random(6),
                'otp_expired_at' => $now->copy()->addMinutes(10),
                'updated_at' => $now,
            ];

            $existing = Perusahaan::where('email',$email)->first();
            if(!$existing) { $existing = Perusahaan::where('nama_perusahaan',$namaPerusahaan)->first(); }
            if ($existing) {
                $existing->fill($data)->save();
                $updated++;
            } else {
                $data['created_at'] = $now;
                Perusahaan::create($data);
                $created++;
            }
        }
        $this->command?->info("Seed perusahaan statis selesai: created=$created updated=$updated");
    }
}
