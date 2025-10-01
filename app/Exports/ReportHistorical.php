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
                histories.course,
                histories.latitude,
                histories.longitude,
                histories.ignition_status,
                histories.address,
                histories.axisx,
                histories.axisy,
                histories.axisz,
                histories.roll,
                histories.pitch,
                histories.status
            FROM 
                histories         
            WHERE
                DATE(histories.time) BETWEEN ? AND ?
                AND vehicle_id = ?
        ";

        $parameters = [$startDate, $endDate, $noPol];
        
        $data = DB::select($query, $parameters);

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
            'NO', 'TANGGAL', 'NOPOL', 'SPEED', 'ANGLE/ARAH', 'LATITUDE','LONGITUDE',
            'IGNITION', 'ALAMAT', 'X VALUE', 'Y VALUE', 'Z VALUE', 'ROLL', 'PITCH', 'STATUS'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Apply blue background to the header row
                $sheet->getStyle('A1:O1')->applyFromArray([
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