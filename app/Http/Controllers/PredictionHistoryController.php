<?php

namespace App\Http\Controllers;

use App\Models\PredictionHistory;
use Illuminate\Http\Request;

class PredictionHistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = PredictionHistory::with('historicalDisaster');

        // Filter berdasarkan Tahun Kejadian Bencana
        if ($request->filled('year')) {
            $year = $request->year;
            $query->whereHas('historicalDisaster', function ($q) use ($year) {
                $q->whereYear('event_date', $year);
            });
        }

        // Filter berdasarkan Kabupaten / Kota
        if ($request->filled('regency')) {
            $regency = $request->regency;
            $query->whereHas('historicalDisaster', function ($q) use ($regency) {
                $q->where('regency', 'like', '%' . $regency . '%');
            });
        }

        // Filter berdasarkan Jenis Bencana
        if ($request->filled('type')) {
            $type = $request->type;
            $query->whereHas('historicalDisaster', function ($q) use ($type) {
                $q->where('disaster_type', 'like', '%' . $type . '%');
            });
        }

        // Filter berdasarkan Hasil Prediksi Tingkat Keparahan
        if ($request->filled('level')) {
            $query->where('predicted_level', $request->level);
        }

        $histories = $query->latest('prediction_date')->paginate(20);

        return view('history.index', compact('histories'));
    }

    public function show($id)
    {
        $history = PredictionHistory::with('historicalDisaster')->findOrFail($id);
        return view('history.show', compact('history'));
    }
}