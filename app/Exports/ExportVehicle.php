<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class ExportVehicle implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data = DB::select(
            "SELECT 
            vehicles.no_pol,
            vehicles.nama_pt,
            vehicles.address,
            vehicles.vehicle_type,
            vehicles.divisi,
            vehicles.tahun_kendaraan,
            vehicles.warna,
            vehicles.`type`,
            vehicles.no_rangka,
            vehicles.no_mesin,
            vehicles.expired_stnk,
            vehicles.expired_pajak,
            vehicles.head_kir,
            vehicles.expired_head_kir,
            vehicles.chasis_kir,
            vehicles.expired_chasis_kir,
            vehicles.nama_pt_chasis_kir,
            vehicles.jenis_chasis,
            vehicles.nomor_chasis,
            vehicles.model_chasis,
            vehicles.divisi_chasis,
            vehicles.no_rekom_b3_klhk,
            vehicles.expired_rekom,
            vehicles.expired_kartu_kemenhub,
            vehicles.no_single_tid,
            vehicles.expired_single_tid,
            vehicles.nama_gps,
            drivers.driver_code AS code_driver,
            drivers.name AS driver_name,
            areas.name AS area_name,
            vehicles.keterangan
            FROM vehicles
            LEFT JOIN areas ON vehicles.area_id = areas.id
            LEFT JOIN drivers ON vehicles.driver_id = drivers.id
            WHERE vehicles.status = 1"
        );

        // Tambahkan nomor urut di awal setiap objek
        $numberedData = collect($data)->map(function($item, $index) {
            // Ubah objek data menjadi array untuk memudahkan penambahan kolom di awal
            $itemArray = (array) $item;
            
            // Tambahkan kolom "no" di awal
            $itemArray = array_merge(['no' => $index + 1], $itemArray);

            return (object) $itemArray;
        });

        return $numberedData;
    }

    public function headings(): array
    {
        return [
            'NO',
            'NO POL',
            'NAMA PT',
            'ALAMAT',
            'TIPE KENDARAAN',
            'DIVISI',
            'TAHUN KENDARAAN',
            'WARNA',
            'TIPE',
            'NOMOR RANGKA',
            'NOMOR MESIN',
            'MASA BERLAKU STNK',
            'MASA BERLAKU PAJAK',
            'HEAD KIR',
            'MASA BERLAKU HEAD KIR',
            'CHASIS KIR',
            'MASA BERLAKU CHASIS KIR',
            'NAMA PT CHASIS KIR',
            'JENIS CHASIS',
            'NOMOR CHASIS',
            'MODEL CHASIS',
            'DIVISI CHASIS',
            'NO REKOM B3 KLHK',
            'MASA BERLAKU REKOM KLHK',
            'MASA BERLAKU KARTU KEMENHUB',
            'NO SINGLE TID',
            'MASA BERLAKU SINGLE TID',
            'NAMA GPS',
            'KODE SUPIR',
            'NAMA SUPIR',
            'AREA KENDARAAN',
            'KETERANGAN'
        ];
    }
}
