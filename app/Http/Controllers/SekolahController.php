<?php

namespace App\Http\Controllers;

use App\Models\Sekolah;
use App\Models\Jurusan;

class SekolahController extends Controller
{
    public function getAllSekolah()
    {
        return response()->json(Sekolah::all());
    }

    public function getJurusanBySekolah($sekolah_id)
    {
        $jurusan = Jurusan::where('sekolah_id', $sekolah_id)->get();

        if ($jurusan->isEmpty()) {
            return response()->json(['message' => 'Tidak ada jurusan yang terdaftar untuk sekolah ini.'], 404);
        }

        return response()->json($jurusan);
    }
}
