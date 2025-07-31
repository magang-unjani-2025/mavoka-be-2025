<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jurusan;

class JurusanController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'sekolah_id' => 'required|exists:sekolah,id',
            'nama_jurusan' => 'required|string|max:255',
        ]);

        $jurusan = Jurusan::create([
            'sekolah_id' => $request->sekolah_id,
            'nama_jurusan' => $request->nama_jurusan,
        ]);

        return response()->json([
            'message' => 'Jurusan berhasil ditambahkan',
            'data' => $jurusan
        ], 201);
    }

    public function index()
    {
        return response()->json(Jurusan::with('sekolah')->get());
    }
}
