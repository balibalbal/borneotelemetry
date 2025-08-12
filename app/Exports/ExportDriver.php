<?php

namespace App\Exports;

use App\Models\Driver;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class ExportDriver implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data = DB::select("SELECT 
        id, driver_code, name as nama, 
        CASE 
            WHEN divisi = 'J' THEN 'DIVISI KOTA - KOTA JAKARTA'
            WHEN divisi = 'S' THEN 'DIVISI BPPI'
            WHEN divisi = 'A' THEN 'DIVISI KOTA - KOTA SURABAYA'
            WHEN divisi = 'M' THEN 'DIVISI INDORAMA'
            WHEN divisi = 'G' THEN 'DIVISI GANDENGAN'
            WHEN divisi = 'T' THEN 'DIVISI TANGKI'
            WHEN divisi = 'W' THEN 'DIVISI WINGBOX'
        END AS divisi, 
        DATE(start_date) as start_date, 
        DATE(contract_end_date) as contract_end_date, 
        address, phone, driver_position, debt, note, 
        sim_type, sim_number, DATE(expired_sim) as expired_sim, 
        rekening_number, rekening_name, no_certificate, 
        DATE(masa_berlaku_certificate) as masa_berlaku_certificate, 
        nama_pt, 
        CASE 
            WHEN status = 0 THEN 'Tidak Aktif'
            WHEN status = 1 THEN 'Aktif'
        END AS status, 
        created_at, updated_at 
    FROM drivers"
);


        return collect($data);
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Kode Supir',
            'Nama',
            'Divisi',
            'Tanggal Mulai Kerja',
            'Tanggal Habis Kontrak',
            'Alamat',
            'Telepon',
            'Posisi',
            'Hutang',
            'Note',
            'Tipe Sim',
            'ID SIM',
            'Masa Berlaku SIM',
            'Nomor Rekening',
            'Nama Rekening',
            'Nomor Sertifikat',
            'Masa Berlaku Sertifikat',
            'Nama PT',
            'Status',
            'Tanggal Dibuat',
            'Tanggal Diedit'
        ];
    }
}
