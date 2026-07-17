<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;

class ClassificationImport implements WithMultipleSheets, SkipsUnknownSheets
{
    protected array $sheetNames;

    public function __construct(array $sheetNames = [])
    {
        $this->sheetNames = $sheetNames;
    }

    public function sheets(): array
    {
        if (empty($this->sheetNames)) {
            // Fallback: coba sheet tahun standar + nama umum
            $names = array_merge(
                array_map('strval', range(2020, 2030)),
                ['Sheet1', 'Sheet 1', 'Sheet2', 'Data', 'Bencana', 'Kejadian']
            );
        } else {
            $names = $this->sheetNames;
        }

        $sheets = [];
        foreach ($names as $name) {
            $sheets[$name] = new ClassificationSheetImport($name);
        }
        return $sheets;
    }

    public function onUnknownSheet($sheetName)
    {
        // Sheet tidak dikenali dilewati tanpa error
    }
}
