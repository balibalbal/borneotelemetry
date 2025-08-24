<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class ReportHistoricalChunk implements FromArray, WithHeadings, WithEvents
{
    private $chunks;

    public function __construct($chunks)
    {
        $this->chunks = $chunks;
    }

    public function array(): array
    {
        // Gabungkan semua chunk jadi satu array
        $combined = [];

        foreach ($this->chunks as $chunk) {
            foreach ($chunk as $row) {
                $combined[] = (array) $row;
            }
        }

        return $combined;
    }

    public function headings(): array
    {
        return (new ReportHistorical([]))->headings(); 
    }

    public function registerEvents(): array
    {
        return (new ReportHistorical([]))->registerEvents();
    }
}
