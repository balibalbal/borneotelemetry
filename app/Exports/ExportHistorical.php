<?php 

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\DB;

class ExportHistorical implements FromCollection, WithHeadings, WithMapping
{
    private $startDate;
    private $endDate;
    private $no_pol;
    private $noCounter = 1;

    public function __construct($startDate, $endDate, $no_pol)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->no_pol = $no_pol;
    }

    public function collection()
    {
        $customer_id = auth()->user()->customer_id;

        $data = DB::select("
            SELECT 
                histories.time,
                histories.no_pol,
                histories.latitude,
                histories.longitude,
                histories.speed,
                histories.distance,
                histories.total_distance,
                histories.course,
                CASE 
                    WHEN histories.status = 'bergerak' THEN 'Bergerak'
                    WHEN histories.status = 'mati' THEN 'Mati'
                    WHEN histories.status = 'diam' THEN 'Diam'
                    WHEN histories.status = 'berhenti' THEN 'Berhenti'
                    ELSE histories.status
                END AS status,    
                address
            FROM histories 
            WHERE histories.customer_id = ? AND histories.vehicle_id = ? 
            AND DATE(histories.time) BETWEEN ? AND ?", [
                $customer_id,         
                $this->no_pol,       
                $this->startDate,       
                $this->endDate          
        ]);

        return collect($data);        
    }

    // **Tambahkan Header Kolom**
    public function headings(): array
    {
        return [
            'No', // **Nomor Urut**
            'Tanggal Update',
            'Nopol',
            'Latitude',
            'Longitude',
            'Speed (kph)',
            'Distance (KM)',
            'Total Distance (KM)',
            'Course (Arah Derajat)',
            'Status',
            'Alamat'
        ];
    }

    // **Tambahkan Nomor Urut ke Data**
    public function map($data): array
    {
        static $index = 0; // **Variabel static untuk nomor urut**
        $index++;

        return [
            $index, // **Nomor Urut**
            $data->time,
            $data->no_pol,
            $data->latitude,
            $data->longitude,
            $data->speed,
            $data->distance,
            $data->total_distance,
            $data->course,
            $data->status,
            $data->address
        ];
    }
}
