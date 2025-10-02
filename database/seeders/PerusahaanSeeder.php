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
        // flattened list (removed one extra outer array)
        $rows = [
            // username, email, password, status, tanggal verif, nama perusahaan, bidang, web, deskripsi, alamat, kontak, penanggung jawab
            ['techindo01', 'techindo01@gmail.com', 'pass1234', 'Terverifikasi', '2024-08-12', 'PT Tech Indo Solutions', 'Teknologi', 'www.techindo01.com', 'Penyedia solusi IT untuk perusahaan menengah dan besar', 'Jakarta Selatan, DKI Jakarta', '81234567890', 'Techindo01'],
            ['sehcatare', 'sehcatare@gmail.com', 'sehat202', 'Terverifikasi', '2024-09-01', 'SehatHub Indonesia', 'Kesehatan', 'www.sehcatare.com', 'Platform kesehatan digital & konsultasi dokter', 'Sleman, DIY', '82197865432', 'Sehcatare'],
            ['edusmart', 'edusmart@gmail.com', 'eduSmart', 'Terverifikasi', '2024-09-01', 'EduSmart Nusantara', 'Pendidikan', 'www.edusmart.com', 'Startup e-learning interaktif', 'Yogyakarta, DIY', '8122341234', 'Edusmart'],
            ['foodez', 'foodez@gmail.com', 'foodez@9', 'Terverifikasi', '2024-07-20', 'Foodez Nusantara', 'Kuliner', 'www.foodez.com', 'Aplikasi delivery makanan khas Nusantara', 'Surabaya, Jawa Timur', '83145678912', 'Foodez'],
            ['greenbuild', 'greenbuild@gmail.com', 'green2023', 'Terverifikasi', '2024-09-01', 'GreenBuild Indonesia', 'Konstruksi', 'www.greenbuild.com', 'Perusahaan konstruksi ramah lingkungan', 'Bandung, Jawa Barat', '82917865432', 'Greenbuild'],
            ['fashionly', 'fashionly@gmail.com', 'style123', 'Terverifikasi', '2024-09-01', 'Fashionly Co.', 'Fashion & Retail', 'www.fashionly.com', 'Marketplace fashion modern & UMKM', 'Denpasar, Bali', '82345678901', 'Fashionly'],
            ['agritechd', 'agritechd@gmail.com', 'agri2024', 'Terverifikasi', '2024-08-30', 'AgriTech Indonesia', 'Pertanian', 'www.agritechd.com', 'Solusi digital untuk petani Indonesia', 'Solo, Jawa Tengah', '81456788934', 'Agritechd'],
            ['traveloka02', 'traveloka02@gmail.com', 'travel@2', 'Terverifikasi', '2024-08-19', 'Travelo Nusantara', 'Pariwisata', 'www.traveloka02.com', 'Aplikasi pemesanan tiket & hotel', 'Medan, Sumatera Utara', '8122345678', 'Traveloka02'],
            ['medisafe', 'medisafe@gmail.com', 'medisafe', 'Terverifikasi', '2024-09-01', 'MediSafe Indonesia', 'Farmasi', 'www.medisafe.com', 'Distributor obat dan alat kesehatan', 'Makassar, Sulawesi Selatan', '82197823456', 'Medisafe'],
            ['kreatify', 'kreatify@gmail.com', 'kreatif2', 'Terverifikasi', '2024-09-01', 'Kreatify Digital Agency', 'Digital Marketing', 'www.kreatify.com', 'Agensi kreatif untuk branding', 'Malang, Jawa Timur', '81234567890', 'Kreatify'],
            ['energindo', 'energindo@gmail.com', 'energi20', 'Terverifikasi', '2024-08-25', 'Energi Indo Power', 'Energi', 'www.energindo.com', 'Penyedia energi terbarukan untuk industri', 'Balikpapan, Kalimantan Timur', '82199988877', 'Energindo'],
            ['finteksmart', 'finteksmart@gmail.com', 'fintek@3', 'Terverifikasi', '2024-09-01', 'Fintek Smart Solutions', 'Keuangan', 'www.finteksmart.com', 'Startup fintech untuk UMKM', 'Jakarta Pusat, DKI Jakarta', '81345678945', 'Finteksmart'],
            ['rumahceria', 'rumahceria@gmail.com', 'rumah123', 'Terverifikasi', '2024-09-10', 'Rumah Ceria Property', 'Properti', 'www.rumahceria.com', 'Agen properti dan rumah hunian', 'Tangerang, Banten', '81366777788', 'Rumahceria'],
            ['oceanblue', 'oceanblue@gmail.com', 'ocean888', 'Terverifikasi', '2024-09-05', 'Ocean Blue Logistics', 'Logistik', 'www.oceanblue.com', 'Jasa ekspedisi laut domestik', 'Batam, Kepulauan Riau', '82145678901', 'Oceanblue'],
            ['artify', 'artify@gmail.com', 'artify20', 'Terverifikasi', '2024-08-27', 'Artify Indonesia', 'Seni & Kreatif', 'www.artify.com', 'Platform seni digital untuk kreator lokal', 'Solo, Jawa Tengah', '81922334455', 'Artify'],
            ['mediatrn', 'mediatrn@gmail.com', 'media123', 'Terverifikasi', '2024-08-30', 'Mediatron Media Group', 'Media & Hiburan', 'www.mediatrn.com', 'Produksi konten & media publik', 'Bandung, Jawa Barat', '82134567890', 'Mediatrn'],
            ['smarttrans', 'smarttrans@gmail.com', 'trans%202', 'Terverifikasi', '2024-09-02', 'SmartTrans Indonesia', 'Transportasi', 'www.smarttrans.com', 'Layanan transportasi dan logistik', 'Samarinda, Kalimantan Timur', '81399912345', 'Smarttrans'],
            ['gardenfresh', 'gardenfresh@gmail.com', 'garden112', 'Terverifikasi', '2024-09-01', 'Garden Fresh Foods', 'Agribisnis', 'www.gardenfresh.com', 'Produksi dan distribusi sayuran segar', 'Bogor, Jawa Barat', '82123498765', 'Gardenfresh'],
            ['luxehotel', 'luxehotel@gmail.com', 'luxe2024', 'Terverifikasi', '2024-08-28', 'Luxe Hotel Indonesia', 'Hospitality', 'www.luxehotel.com', 'Jaringan hotel premium di Indonesia', 'Bali, Denpasar', '81255567891', 'Luxehotel'],
            ['gameverse', 'gameverse@gmail.com', 'game2024', 'Terverifikasi', '2024-08-30', 'GameVerse Nusantara', 'Game & Esports', 'www.gameverse.com', 'Platform game online & turnamen', 'Bandung, Jawa Barat', '81123456789', 'Gameverse'],
            ['ecofuture', 'ecofuture@gmail.com', 'eco20245', 'Terverifikasi', '2024-09-06', 'EcoFuture Nusantara', 'Lingkungan', 'www.ecofuture.com', 'Startup pengolahan limbah modern', 'Depok, Jawa Barat', '81266665544', 'Ecofuture'],
            ['cyberguard', 'cyberguard@gmail.com', 'cyber%99', 'Terverifikasi', '2024-08-29', 'CyberGuard Indonesia', 'Keamanan IT', 'www.cyberguard.com', 'Penyedia jasa keamanan siber', 'Jakarta Utara, DKI Jakarta', '82777778990', 'Cyberguard'],
            ['bamboolife', 'bamboolife@gmail.com', 'bamboo20', 'Terverifikasi', '2024-09-09', 'BambooLife Indonesia', 'Furniture & Dekor', 'www.bamboolife.com', 'Produk furniture bambu ramah lingkungan', 'Sleman, DIY', '82333444555', 'Bamboolife'],
            ['stellartech', 'stellartech@gmail.com', 'st3llar!', 'Terverifikasi', '2024-09-14', 'StellarTech Nusantara', 'Teknologi', 'www.stellartech.com', 'Startup AI & big data untuk industri', 'Bandung, Jawa Barat', '82199992233', 'Stellartech'],
        ];

        $created = 0;
        $updated = 0;
        $now = now();
        $stripWords = ['pt', 'cv', 'ud', 'pd', 'persero', 'tbk', 'indonesia', 'nusantara'];
        foreach ($rows as [$username, $email, $plainPassword, $status, $tglVerif, $namaPerusahaan, $bidang, $web, $deskripsi, $alamat, $kontak, $pic]) {
            // guard: ensure string before strtolower (defensive)
            if (is_array($namaPerusahaan)) {
                $namaPerusahaan = implode(' ', $namaPerusahaan);
            }

            $base = strtolower($namaPerusahaan);
            $base = preg_replace('/[^a-z0-9]+/i', ' ', $base);
            $tokens = array_filter(array_map('trim', explode(' ', $base)));
            $filtered = [];
            foreach ($tokens as $t) {
                if (!in_array($t, $stripWords)) {
                    $filtered[] = $t;
                }
            }
            if (!$filtered) {
                $filtered = [$username];
            }
            $slug = implode('_', $filtered);
            $logoFile = $slug . '.png';
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
                'logo_perusahaan' => 'logos/perusahaan/' . $logoFile,
                'penanggung_jawab' => $pic,
                'otp' => Str::random(6),
                'otp_expired_at' => $now->copy()->addMinutes(10),
                'updated_at' => $now,
            ];

            $existing = Perusahaan::where('email', $email)->first();
            if (!$existing) {
                $existing = Perusahaan::where('nama_perusahaan', $namaPerusahaan)->first();
            }
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
