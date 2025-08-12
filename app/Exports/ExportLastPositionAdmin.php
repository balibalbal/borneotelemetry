<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class ExportLastPositionAdmin implements FromCollection, WithHeadings
{
    private $customer;
    /**
    * @return \Illuminate\Support\Collection
    */

    public function __construct($customer)
    {
        $this->customer = $customer;
    }
    public function collection()
    {
        //$customer_id = auth()->user()->customer_id;

        // Menggunakan parameter binding untuk menghindari SQL injection
        $data = DB::select("
            SELECT                 
                traccars.time,
                customers.name,
                traccars.no_pol,
                traccars.latitude,
                traccars.longitude,
                traccars.ignition_status,
                traccars.speed,
                traccars.total_distance,
                traccars.course,
                devices.sim_number,
                CASE 
                    WHEN traccars.status = 'bergerak' THEN 'Bergerak'
                    WHEN traccars.status = 'mati' THEN 'Mati'
                    WHEN traccars.status = 'diam' THEN 'Diam'
                    WHEN traccars.status = 'berhenti' THEN 'Berhenti'
                    ELSE traccars.status
                END AS status,    
                traccars.address
            FROM traccars
            LEFT JOIN devices ON traccars.device_id = devices.id
            LEFT JOIN customers ON traccars.customer_id = customers.id
            WHERE active = 1 AND traccars.customer_id = ?
        ", [$this->customer]);

        return collect($data);
    }


    public function headings(): array
    {
        return [
            'Tanggal Update',
            'Nama Customer',
            'Nopol',
            'Latitude',
            'Longitude',
            'Ignition',
            'Speed (kph)',
            'Distance (KM)',
            'Course (Arah Derajat)',
            'Nomor Telepon',
            'Status',
            'Alamat'
        ];
    }
}
