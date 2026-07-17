<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil input filter tanggal
        $startDate = $request->input('start_date', '2020-01-01');
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        // Pastikan format tanggal valid
        try {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();
        } catch (\Exception $e) {
            $start = Carbon::parse('2020-01-01')->startOfDay();
            $end = Carbon::now()->endOfDay();
        }

        // Query dasar dengan filter tanggal
        $baseQuery = DB::table('historical_disasters')
            ->whereBetween('event_date', [$start, $end]);

        // 2. Hitung KPI Card
        $totalDisaster = (clone $baseQuery)->count();
        
        $totalLow = (clone $baseQuery)->where('level_bpbd', 'RENDAH')->count();
        $totalMedium = (clone $baseQuery)->where('level_bpbd', 'SEDANG')->count();
        $totalHigh = (clone $baseQuery)->where('level_bpbd', 'TINGGI')->count();

        // 3. Distribusi Level (Donut Chart)
        $levelDistribution = (clone $baseQuery)
            ->select('level_bpbd', DB::raw('COUNT(*) as total'))
            ->groupBy('level_bpbd')
            ->get();

        // 4. Tren Kejadian Bencana per Tahun per Level (Multi-line Chart)
        $yearTrend = (clone $baseQuery)
            ->select(
                DB::raw('YEAR(event_date) as year'),
                'level_bpbd',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy(DB::raw('YEAR(event_date)'), 'level_bpbd')
            ->orderBy('year')
            ->get();

        // 5. 10 Data Historis Terbaru
        $latestDisaster = (clone $baseQuery)
            ->latest('event_date')
            ->limit(10)
            ->get();

        // 6. Titik individual bencana (peta)
        $mapData = (clone $baseQuery)
            ->select('disaster_type', 'regency', 'level_bpbd', 'latitude', 'longitude')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        // 7. Statistik per kabupaten (Choropleth & bubble)
        $regencyStats = (clone $baseQuery)
            ->select(
                'regency',
                DB::raw('COUNT(*) as total'),
                DB::raw('AVG(latitude) as avg_lat'),
                DB::raw('AVG(longitude) as avg_lng'),
                DB::raw('SUM(CASE WHEN level_bpbd = "RENDAH" THEN 1 ELSE 0 END) as total_rendah'),
                DB::raw('SUM(CASE WHEN level_bpbd = "SEDANG" THEN 1 ELSE 0 END) as total_sedang'),
                DB::raw('SUM(CASE WHEN level_bpbd = "TINGGI" THEN 1 ELSE 0 END) as total_tinggi')
            )
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->groupBy('regency')
            ->orderByDesc('total')
            ->get();

        return view(
            'dashboard.index',
            compact(
                'totalDisaster',
                'totalLow',
                'totalMedium',
                'totalHigh',
                'levelDistribution',
                'yearTrend',
                'latestDisaster',
                'mapData',
                'regencyStats',
                'startDate',
                'endDate'
            )
        );
    }
}