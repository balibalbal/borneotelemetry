<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Facades\DB;

class ReportDump implements FromCollection, WithHeadings, WithEvents
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
        // $tanggalType = $filters['tanggal_type'] ?? 'transfer_date';
        $startDate = $filters['start_date'];
        $endDate = $filters['end_date'];
        // $daSI = $filters['da_si'];

        // $dateColumn = $tanggalType == 'order_date' ? 'orders.order_date' : 'assign_orders.transfer_date';

        $query = "
            SELECT
                created_at,
                no_pol,
                CASE
                    WHEN door = 0 THEN 'open'
                    WHEN door = 1 THEN 'close'
                    ELSE 'unknown'  -- jika ada nilai lain selain 0 atau 1
                END AS door_status,
                address
            FROM 
                dump_trucks                 
            WHERE
                DATE(created_at) BETWEEN ? AND ?
        ";

        $parameters = [$startDate, $endDate];

        
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
            'NO', 'TANGGAL DUMP', 'NOPOL',
            'STATUS DUMP', 'LOKASI'
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

