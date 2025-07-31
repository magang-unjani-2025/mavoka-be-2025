<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Sekolah;
use App\Models\Perusahaan;
use App\Models\LembagaPelatihan;

class StatistikController extends Controller
{
    public function totalSiswa()
    {
        $jumlah = Siswa::count();
        return response()->json(['total_siswa' => $jumlah]);
    }

    public function totalSekolah()
    {
        $jumlah = Sekolah::count();
        return response()->json(['total_sekolah' => $jumlah]);
    }

    public function totalPerusahaan()
    {
        $jumlah = Perusahaan::count();
        return response()->json(['total_perusahaan' => $jumlah]);
    }

    public function totalLembagaPelatihan()
    {
        $jumlah = LembagaPelatihan::count();
        return response()->json(['total_lembaga_pelatihan' => $jumlah]);
    }
}
