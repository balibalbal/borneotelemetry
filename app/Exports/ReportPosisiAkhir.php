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
//dd($groupIds); exit;
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
                traccars.course,
                traccars.latitude,
                traccars.longitude,
                traccars.address,
                traccars.axisx,
                traccars.axisy,
                traccars.axisz,
                traccars.roll,
                traccars.pitch,
                traccars.status
            FROM 
                traccars   
            LEFT JOIN vehicles ON traccars.vehicle_id = vehicles.id              
            $whereClause
        ";
//dd($query); exit;
        $data = DB::select($query, $bindings);

        // Tambahkan kolom NO (auto number) dan format roll & pitch
        $numbered = collect($data)->map(function ($item, $index) {
            $roll = $item->roll;
            $pitch = $item->pitch;
            
            $formattedRoll = self::formatRoll($roll);
            $formattedPitch = self::formatPitch($pitch);

            return (object) array_merge(
                ['no' => $index + 1], // NO mulai dari 1
                (array) $item,
                [
                    'roll' => $formattedRoll, // Override kolom roll dengan format yang baru
                    'pitch' => $formattedPitch // Override kolom pitch dengan format yang baru
                ]
            );
        });

        return $numbered;
    }

    // Method static untuk format roll
    private static function formatRoll($roll)
    {
        // Bulatkan nilai roll tanpa koma
        $roundedRoll = round($roll);
        
        if ($roundedRoll > 0) {
            return "Miring Ke Kanan {$roundedRoll}°";
        } else if ($roundedRoll < 0) {
            return "Miring Ke Kiri {$roundedRoll}°";
        } else {
            return "Kemiringan {$roundedRoll}°";
        }
    }

    // Method static untuk format pitch
    private static function formatPitch($pitch)
    {
        // Bulatkan nilai pitch tanpa koma
        $roundedPitch = round($pitch);
        
        if ($roundedPitch > 0) {
            return "Miring Ke Belakang {$roundedPitch}°";
        } else if ($roundedPitch < 0) {
            return "Miring Ke Depan {$roundedPitch}°";
        } else {
            return "Kemiringan {$roundedPitch}°";
        }
    }

    public function headings(): array
    {
        return [
            'NO', 'TANGGAL', 'NOPOL',
            'SPEED', 'ANGLE/ARAH', 'LATITUDE','LONGITUDE', 'ALAMAT', 'X VALUE', 'Y VALUE', 'Z VALUE', 'ROLL', 'PITCH', 'STATUS'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Apply blue background to the header row
                $sheet->getStyle('A1:N1')->applyFromArray([
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