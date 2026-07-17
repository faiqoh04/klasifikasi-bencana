<?php

namespace App\Imports;

use App\Models\HistoricalDisaster;
use App\Models\PredictionHistory;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithTitle;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ClassificationSheetImport implements OnEachRow, WithHeadingRow, WithTitle
{
    private $year;
    public $successCount = 0;
    public $failedCount = 0;

    public function __construct(string $year)
    {
        $this->year = $year;
    }

    public function title(): string
    {
        return $this->year;
    }

    public function onRow(Row $row)
    {
        $rowData = $row->toArray();

        $disasterType = $rowData['disaster_type'] ?? $rowData['disastertype'] ?? null;
        $eventDateRaw = $rowData['event_date'] ?? $rowData['eventdate'] ?? null;

        // Skip baris kosong
        if (empty($disasterType) && empty($eventDateRaw)) {
            return;
        }

        // Parsing tanggal kejadian
        $sheetYear = (int) $this->year;
        $eventDate = null;
        if (!empty($eventDateRaw)) {
            if (is_numeric($eventDateRaw)) {
                try {
                    $parsed = Carbon::instance(ExcelDate::excelToDateTimeObject((float)$eventDateRaw));
                    if ($parsed->year >= 2010 && $parsed->year <= 2030) {
                        $eventDate = $parsed;
                    } else {
                        $eventDate = Carbon::createFromDate($sheetYear, 1, 1);
                    }
                } catch (\Exception $e) {
                    $eventDate = Carbon::createFromDate($sheetYear, 1, 1);
                }
            } else {
                try {
                    $parsed = Carbon::parse($eventDateRaw);
                    if ($parsed->year >= 2010 && $parsed->year <= 2030) {
                        $eventDate = $parsed;
                    } else {
                        $eventDate = Carbon::createFromDate($sheetYear, 1, 1);
                    }
                } catch (\Exception $e) {
                    $eventDate = Carbon::createFromDate($sheetYear, 1, 1);
                }
            }
        } else {
            $eventDate = Carbon::createFromDate($sheetYear, 1, 1);
        }

        // Bersihkan data numerik korban
        $dead = $this->cleanInt($rowData['dead'] ?? 0);
        $missing = $this->cleanInt($rowData['missing'] ?? 0);
        $seriousWound = $this->cleanInt($rowData['serious_wound'] ?? $rowData['seriouswound'] ?? 0);
        $minorInjuries = $this->cleanInt($rowData['minor_injuries'] ?? $rowData['minorinjuries'] ?? 0);

        // Taksiran kerugian
        $lossesRaw = $rowData['losses'] ?? null;
        $losses = null;
        if (isset($lossesRaw)) {
            $cleanedLosses = str_replace(',', '', (string)$lossesRaw);
            $losses = is_numeric($cleanedLosses) ? (float)$cleanedLosses : null;
        }

        // Bersihkan koordinat
        $latitude = isset($rowData['latitude']) && is_numeric($rowData['latitude']) ? (float)$rowData['latitude'] : null;
        $longitude = isset($rowData['longitude']) && is_numeric($rowData['longitude']) ? (float)$rowData['longitude'] : null;

        if ($latitude !== null && ($latitude < -90 || $latitude > 90)) {
            $latitude = null;
        }
        if ($longitude !== null && ($longitude < -180 || $longitude > 180)) {
            $longitude = null;
        }

        $damageText = $rowData['damage'] ?? '';

        // Panggil Server Python Flask API untuk Prediksi
        $predictedLevel = 'RENDAH';
        $probLow = 0.0;
        $probMedium = 0.0;
        $probHigh = 0.0;

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
                // Kalikan 100 agar sesuai format persen (decimal 5,2 di database)
                $probLow    = (float)($resData['data']['probability']['RENDAH'] ?? 0) * 100;
                $probMedium = (float)($resData['data']['probability']['SEDANG'] ?? 0) * 100;
                $probHigh   = (float)($resData['data']['probability']['TINGGI'] ?? 0) * 100;
                $this->successCount++;
            } else {
                Log::warning("API Prediksi mengembalikan status non-200 untuk baris ID Logs: " . ($rowData['id_logs'] ?? 'N/A'));
                $this->failedCount++;
            }
        } catch (\Exception $e) {
            Log::error("Gagal terhubung ke API Prediksi saat batch import: " . $e->getMessage());
            $this->failedCount++;
        }

        // Simpan sebagai HistoricalDisaster
        $disaster = HistoricalDisaster::create([
            'bpbd_log_id'    => isset($rowData['id_logs']) && is_numeric($rowData['id_logs']) ? (int)$rowData['id_logs'] : (isset($rowData['idlogs']) && is_numeric($rowData['idlogs']) ? (int)$rowData['idlogs'] : null),
            'disaster_type'  => $this->limitString($disasterType ?? 'Lain-lain', 100),
            'event_date'     => $eventDate,
            'regency'        => $this->limitString($rowData['regency'] ?? 'Jawa Timur', 100),
            'area'           => $rowData['area'] ?? null,
            'latitude'       => $latitude,
            'longitude'      => $longitude,
            'weather'        => $this->limitString($rowData['weather'] ?? null, 255),
            'chronology'     => $rowData['chronology'] ?? null,
            'dead'           => $dead,
            'missing'        => $missing,
            'serious_wound'  => $seriousWound,
            'minor_injuries' => $minorInjuries,
            'damage'         => $damageText,
            'losses'         => $losses,
            'response'       => $rowData['response'] ?? null,
            'photos'         => $rowData['photos'] ?? null,
            'source'         => $this->limitString($rowData['source'] ?? null, 255),
            'status'         => $this->limitString($rowData['status'] ?? null, 255),
            'level_bpbd'     => $predictedLevel, // Gunakan hasil prediksi ML
        ]);

        // Simpan log PredictionHistory
        PredictionHistory::create([
            'historical_disaster_id' => $disaster->id,
            'prediction_date'        => now(),
            'algorithm'              => 'Random Forest',
            'predicted_level'        => $predictedLevel,
            'probability_low'        => $probLow,
            'probability_medium'     => $probMedium,
            'probability_high'       => $probHigh
        ]);
    }

    private function cleanInt($val): int
    {
        if (is_numeric($val)) {
            return (int)$val;
        }
        return 0;
    }

    private function limitString(?string $str, int $limit): ?string
    {
        if ($str === null) {
            return null;
        }
        return mb_substr($str, 0, $limit);
    }
}
