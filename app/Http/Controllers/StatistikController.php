<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Siswa;
use App\Models\Sekolah;
use App\Models\Perusahaan;
use App\Models\LembagaPelatihan;

class StatistikController extends Controller
{
    // Total semua data
    public function totalSiswa()
    {
        return response()->json(['total_siswa' => Siswa::count()]);
    }

    public function totalSekolah()
    {
        return response()->json(['total_sekolah' => Sekolah::count()]);
    }

    public function totalPerusahaan()
    {
        return response()->json(['total_perusahaan' => Perusahaan::count()]);
    }

    public function totalLembagaPelatihan()
    {
        return response()->json(['total_lembaga_pelatihan' => LembagaPelatihan::count()]);
    }

    // Statistik Bulanan
    public function statistikBulanan($role, Request $request)
    {
        $year = $request->input('year', date('Y'));

        $model = $this->getModel($role);
        if (!$model) {
            return response()->json(['message' => 'Role tidak valid'], 400);
        }

        $data = $model::selectRaw('EXTRACT(MONTH FROM created_at) AS month, COUNT(*) AS total')
            ->whereYear('created_at', $year)
            ->groupByRaw('EXTRACT(MONTH FROM created_at)')
            ->orderByRaw('EXTRACT(MONTH FROM created_at)')
            ->get();


        return response()->json([
            'role' => $role,
            'year' => $year,
            'data' => $data
        ]);
    }

    // Statistik Tahunan
    public function statistikTahunan($role)
    {
        $model = $this->getModel($role);
        if (!$model) {
            return response()->json(['message' => 'Role tidak valid'], 400);
        }

        $data = $model::selectRaw('EXTRACT(YEAR FROM created_at) AS year, COUNT(*) AS total')
            ->groupByRaw('EXTRACT(YEAR FROM created_at)')
            ->orderByRaw('EXTRACT(YEAR FROM created_at)')
            ->get();


        return response()->json([
            'role' => $role,
            'data' => $data
        ]);
    }

    private function getModel($role)
    {
        return match ($role) {
            'siswa' => Siswa::class,
            'sekolah' => Sekolah::class,
            'perusahaan' => Perusahaan::class,
            'lpk' => LembagaPelatihan::class,
            default => null,
        };
    }
}
