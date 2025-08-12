<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\DB;

class ExportLastPosition implements FromCollection, WithHeadings, WithMapping
{
    protected $data;

    public function __construct()
    {
        $customer_id = auth()->user()->customer_id;

        // Menggunakan parameter binding untuk menghindari SQL injection
        $this->data = DB::select("
            SELECT 
                traccars.time,
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
                address
            FROM traccars
            LEFT JOIN devices ON traccars.device_id = devices.id
            WHERE active = 1 AND traccars.customer_id = ?
        ", [$customer_id]);
    }

    public function collection()
    {
        return collect($this->data);
    }

    // **Tambahkan Header Kolom**
    public function headings(): array
    {
        return [
            'No', // **Kolom nomor urut**
            'Tanggal Update',
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

    // **Mapping Data untuk Tambahkan Nomor Urut**
    public function map($data): array
    {
        static $index = 0; // **Variabel statis untuk nomor urut**
        $index++;

        return [
            $index, // **Tambahkan nomor urut**
            $data->time,
            $data->no_pol,
            $data->latitude,
            $data->longitude,
            $data->ignition_status,
            $data->speed,
            $data->total_distance,
            $data->course,
            $data->sim_number,
            $data->status,
            $data->address
        ];
    }
}
