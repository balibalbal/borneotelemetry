<?php 

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class ExportHistoricalAdmin implements FromCollection, WithHeadings
{
    private $startDate;
    private $endDate;
    private $no_pol;
    private $customer;
    private $noCounter = 1;

    public function __construct($startDate, $endDate, $no_pol, $customer)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->no_pol = $no_pol;
        $this->customer = $customer;
    }

    public function collection()
    {
        $data = DB::select("
            SELECT 
                histories.time,
                customers.name,
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
                histories.address
            FROM histories 
            LEFT JOIN customers ON histories.customer_id = customers.id
            WHERE histories.customer_id = ? AND histories.no_pol = ? AND DATE(histories.time) BETWEEN ? AND ?", [
            $this->customer,         
            $this->no_pol,       
            $this->startDate,       
            $this->endDate          
        ]);

        // Format data
        $formattedData = array_map(function($item) {
            return (object) [
                'time' => $item->time,
                'name' => $item->name,
                'no_pol' => $item->no_pol,
                'latitude' => $item->latitude,
                'longitude' => $item->longitude,
                'speed' => $item->speed,
                'distance' => number_format($item->distance, 2, ',', '.'),
                'total_distance' => number_format($item->total_distance, 2, ',', '.'), 
                'course' => $item->course,
                'status' => $item->status,
                'address' => $item->address,
            ];
        }, $data);

        return collect($formattedData);
    }


    public function headings(): array
    {
        return [
            'Tanggal Update',
            'Nama Customer',
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
}
