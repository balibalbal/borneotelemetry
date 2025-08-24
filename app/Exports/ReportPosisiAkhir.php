<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Facades\DB;

class ReportPosisiAkhir implements FromCollection, WithHeadings, WithEvents
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
        $groupIds = $filters['group_id'] ?? [];

        $whereClause = '';
        $bindings = [];

        if (!empty($groupIds) && !(count($groupIds) === 1 && $groupIds[0] == 0)) {
            // Hanya tambahkan WHERE jika group_id TIDAK hanya [0]
            $whereIn = implode(',', array_fill(0, count($groupIds), '?'));
            $whereClause = "WHERE vehicles.group_id IN ($whereIn)";
            $bindings = $groupIds;
        }

        $query = "
            SELECT
                traccars.time,
                traccars.no_pol,
                traccars.speed,
                traccars.latitude,
                traccars.longitude,
                traccars.address,
                traccars.status
            FROM 
                traccars   
            LEFT JOIN vehicles ON traccars.vehicle_id = vehicles.id              
            $whereClause
        ";

        $data = DB::select($query, $bindings);

        // Tambahkan kolom NO (auto number)
        $numbered = collect($data)->map(function ($item, $index) {
            return (object) array_merge(
                ['no' => $index + 1],
                (array) $item
            );
        });

        return $numbered;


    }

    public function headings(): array
    {
        return [
            'NO', 'TANGGAL', 'NOPOL',
            'SPEED','LATITUDE','LONGITUDE', 'ALAMAT', 'STATUS'
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

