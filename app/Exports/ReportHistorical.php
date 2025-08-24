<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Facades\DB;

class ReportHistorical implements FromCollection, WithHeadings, WithEvents
{
    private $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    // Digunakan oleh Excel
    public function collection()
    {
        return $this->getData($this->filters);
    }

    // Method reusable untuk ambil data dari controller
    public static function getData($filters)
    {
        $startDate = $filters['start_date'];
        $endDate = $filters['end_date'];
        $noPol = $filters['no_pol'];

        $query = "
            SELECT
                histories.time,
                histories.no_pol,
                histories.speed,
                histories.latitude,
                histories.longitude,
                histories.ignition_status,
                histories.address,
                histories.status
            FROM 
                histories         
            WHERE
                DATE(histories.time) BETWEEN ? AND ?
                AND vehicle_id = ?
        ";

        $parameters = [$startDate, $endDate, $noPol];
        
        $data = DB::select($query, $parameters);


        // Tambahkan kolom NO (auto number)
        $numbered = collect($data)->map(function ($item, $index) {
            return (object) array_merge(
                ['no' => $index + 1], // NO mulai dari 1
                (array) $item
            );
        });

        return $numbered;
    }

    public function headings(): array
    {
        return [
            'NO', 'TANGGAL', 'NOPOL',
            'SPEED', 'LATITUDE','LONGITUDE', 'IGNITION', 'ALAMAT', 'STATUS'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Apply blue background to the header row
                $sheet->getStyle('A1:K1')->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'rgb' => '3FA2F6' // Light Blue
                        ]
                    ],
                    'font' => [
                        'bold' => true
                    ]
                ]);                
                
            }
        ];
    }
}

