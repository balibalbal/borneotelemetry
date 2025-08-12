<?php

namespace App\Exports;

use App\Models\Depo;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class ExportDepo implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data = DB::select("SELECT 
        id, name, address, latitude, longitude, radius, created_at, updated_at FROM depos");

        return collect($data);
    }

    public function headings(): array
    {
        return [
            'ID',
            'NAMA DEPO/PELABUHAN',
            'ALAMAT',
            'LATITUDE',
            'LONGITUDE',
            'RADIUS (METER)',
            'DIBUAT TANGGAL',
            'DI UPDATE TANGGAL'
        ];
    }
}
