<?php 

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\DB;

class ExportKecepatan implements FromCollection, WithHeadings, WithMapping
{
    private $startDate;
    private $endDate;
    private $no_pol;
    private $data;

    public function __construct($startDate, $endDate, $no_pol)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->no_pol = $no_pol;

        $customer_id = auth()->user()->customer_id;

        $this->data = DB::select("
            SELECT 
                histories.time,
                histories.no_pol,
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
            WHERE histories.customer_id = ? 
            AND histories.vehicle_id = ? 
            AND DATE(histories.time) BETWEEN ? AND ?", [
                $customer_id,         
                $this->no_pol,       
                $this->startDate,       
                $this->endDate          
        ]);
    }

    public function collection()
    {
        return collect($this->data);
    }

    public function headings(): array
    {
        return [
            'No', // **Nomor urut**
            'Tanggal Update',
            'Nopol',
            'Speed (kph)',
            'Distance (KM)',
            'Total Distance (KM)',
            'Course (Arah Derajat)',
            'Status',
            'Alamat'
        ];
    }

    public function map($row): array
    {
        static $index = 0; // **Variabel statis agar nomor urut bertambah**
        $index++;

        return [
            $index, // **Auto number**
            $row->time,
            $row->no_pol,
            $row->speed,
            $row->distance,
            $row->total_distance,
            $row->course,
            $row->status,
            $row->address
        ];
    }
}
