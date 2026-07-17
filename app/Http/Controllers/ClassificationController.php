<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\HistoricalDisaster;
use App\Models\PredictionHistory;
use App\Imports\ClassificationImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class ClassificationController extends Controller
{
    public function index()
    {
        // Daftar kabupaten/kota untuk dropdown
        $regenciesList = DB::table('historical_disasters')
            ->select('regency')
            ->distinct()
            ->whereNotNull('regency')
            ->where('regency', '!=', '')
            ->orderBy('regency')
            ->pluck('regency');

        // Normalisasi & filter jenis bencana sesuai Juklak BNPB
        $rawTypes = DB::table('historical_disasters')
            ->select('disaster_type')
            ->distinct()
            ->whereNotNull('disaster_type')
            ->where('disaster_type', '!=', '')
            ->pluck('disaster_type');

        $replaceMap = [
            "Banjir" => "Banjir", "Banjir Rob" => "Banjir Rob",
            "Banjir Bandang" => "Banjir Bandang",
            "Banjir dan Tanah Longsor" => "Banjir dan Tanah Longsor",
            "Banjir Drainase & Selokan" => "Banjir Drainase & Selokan",
            "Banjir Waduk" => "Banjir Waduk", "Banjir Genangan" => "Banjir Genangan",
            "Tanggul Jebol" => "Tanggul Jebol", "Tanah Longsor" => "Tanah Longsor",
            "Gerakan Tanah" => "Gerakan Tanah", "Gelombang Pasang" => "Gelombang Pasang",
            "Abrasi" => "Abrasi Pantai", "Angin Puting Beliung" => "Puting Beliung",
            "Puting Beliung" => "Puting Beliung", "Angin Kencang" => "Angin Kencang",
            "Angin Topan" => "Angin Topan", "Hujan Es" => "Hujan Es",
            "Siklon Tropis" => "Siklon Tropis", "Suhu Udara Ekstrem" => "Suhu Udara Ekstrem",
            "Kekeringan" => "Kekeringan", "Kekeringan Meteorologis" => "Kekeringan Meteorologis",
            "Kekeringan Hidrologis" => "Kekeringan Hidrologis",
            "Kekeringan Pertanian" => "Kekeringan Pertanian",
            "Kebakaran Hutan" => "Kebakaran Hutan", "Kebakaran Lahan" => "Kebakaran Lahan",
            "Kebakaran Lahan Gambut" => "Kebakaran Lahan Gambut",
            "Gempa Bumi" => "Gempa Bumi", "Gempa Tektonik" => "Gempa Tektonik",
            "Gempa Vulkanik" => "Gempa Vulkanik", "Gempa Runtuhan" => "Gempa Runtuhan",
            "Tsunami" => "Tsunami", "Tsunami Seismogenik" => "Tsunami Seismogenik",
            "Tsunami Nonseismik" => "Tsunami Nonseismik", "Tsunami Lokal" => "Tsunami Lokal",
            "Tsunami Regional" => "Tsunami Regional", "Tsunami Jarak" => "Tsunami Jarak",
            "Tsunami Meteorologi" => "Tsunami Meteorologi", "Mikrotsunami" => "Mikrotsunami",
            "Gunungapi" => "Erupsi Gunung Api", "Letusan Gunung Api" => "Erupsi Gunung Api",
            "Erupsi Gunung Api" => "Erupsi Gunung Api",
            "Awan Panas Guguran" => "Awan Panas Guguran", "Awan Panas" => "Awan Panas",
            "Banjir Lahar" => "Banjir Lahar", "Hujan Abu Vulkanik" => "Hujan Abu Vulkanik",
            "Gas Vulkanik Beracun" => "Gas Vulkanik Beracun",
        ];

        $allowedDisasters = array_values(array_unique($replaceMap));

        $disasterTypesList = $rawTypes->map(function ($type) use ($replaceMap) {
            $trimmed = trim($type);
            return $replaceMap[$trimmed] ?? $trimmed;
        })->filter(function ($type) use ($allowedDisasters) {
            return in_array($type, $allowedDisasters);
        })->unique()->sort()->values();

        return view('classification.index', compact('regenciesList', 'disasterTypesList'));
    }

    public function predict(Request $request)
    {
        $request->validate([
            'disaster_type' => 'required|string|max:100',
            'regency' => 'required|string|max:100',
            'event_date' => 'required|date',
            'dead' => 'nullable|integer|min:0',
            'missing' => 'nullable|integer|min:0',
            'serious_wound' => 'nullable|integer|min:0',
            'minor_injuries' => 'nullable|integer|min:0',
            'damage' => 'nullable|string',
            'losses' => 'nullable|numeric|min:0',
        ]);

        $dead = (int) $request->input('dead', 0);
        $missing = (int) $request->input('missing', 0);
        $seriousWound = (int) $request->input('serious_wound', 0);
        $minorInjuries = (int) $request->input('minor_injuries', 0);
        $damageText = $request->input('damage', '');

        try {
            $response = Http::timeout(5)->post('http://127.0.0.1:5000/predict', [
                'dead'           => $dead,
                'missing'        => $missing,
                'serious_wound'  => $seriousWound,
                'minor_injuries' => $minorInjuries,
                'damage'         => $damageText
            ]);

            if ($response->successful()) {
                $resData = $response->json();
                $predictedLevel = $resData['data']['prediction'] ?? 'RENDAH';
                $probLow    = (float)($resData['data']['probability']['RENDAH'] ?? 0) * 100;
                $probMedium = (float)($resData['data']['probability']['SEDANG'] ?? 0) * 100;
                $probHigh   = (float)($resData['data']['probability']['TINGGI'] ?? 0) * 100;

                // Simpan ke database historical_disasters
                $disaster = HistoricalDisaster::create([
                    'bpbd_log_id'    => $request->filled('bpbd_log_id') ? (int)$request->input('bpbd_log_id') : null,
                    'disaster_type'  => $request->input('disaster_type'),
                    'event_date'     => Carbon::parse($request->input('event_date')),
                    'regency'        => $request->input('regency'),
                    'area'           => $request->input('area'),
                    'latitude'       => $request->filled('latitude') ? (float)$request->input('latitude') : null,
                    'longitude'      => $request->filled('longitude') ? (float)$request->input('longitude') : null,
                    'weather'        => $request->input('weather'),
                    'chronology'     => $request->input('chronology'),
                    'dead'           => $dead,
                    'missing'        => $missing,
                    'serious_wound'  => $seriousWound,
                    'minor_injuries' => $minorInjuries,
                    'damage'         => $damageText,
                    'losses'         => $request->filled('losses') ? (float)$request->input('losses') : null,
                    'response'       => $request->input('response'),
                    'photos'         => $request->input('photos'),
                    'source'         => $request->input('source', 'Sistem Klasifikasi'),
                    'status'         => $request->input('status', 'Selesai'),
                    'level_bpbd'     => $predictedLevel,
                ]);

                // Simpan ke prediction_histories
                $history = PredictionHistory::create([
                    'historical_disaster_id' => $disaster->id,
                    'prediction_date'        => now(),
                    'algorithm'              => 'Random Forest',
                    'predicted_level'        => $predictedLevel,
                    'probability_low'        => $probLow,
                    'probability_medium'     => $probMedium,
                    'probability_high'       => $probHigh
                ]);

                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'prediction' => $predictedLevel,
                        'probability' => [
                            'RENDAH' => $probLow / 100,
                            'SEDANG' => $probMedium / 100,
                            'TINGGI' => $probHigh / 100,
                        ],
                        'history_id' => $history->id
                    ]
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'API Prediksi Python mengembalikan status non-200.'
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal terhubung ke API Prediksi (port 5000): ' . $e->getMessage()
            ], 500);
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:20480',
        ]);

        $file = $request->file('file');

        try {
            DB::beginTransaction();

            // Baca nama sheet yang benar-benar ada di file Excel
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getPathname());
            $sheetNames  = $spreadsheet->getSheetNames();
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);

            if (empty($sheetNames)) {
                return redirect()->back()->with('error', 'File Excel tidak memiliki sheet yang dapat dibaca.');
            }

            $importer = new ClassificationImport($sheetNames);
            Excel::import($importer, $file);

            DB::commit();

            return redirect()->route('history.index')->with('success', 'Berhasil memproses batch klasifikasi dari ' . count($sheetNames) . ' sheet (' . implode(', ', $sheetNames) . ').');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat batch klasifikasi: ' . $e->getMessage());
        }
    }
}