<?php 

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class ExportParkir implements FromCollection, WithHeadings, WithMapping
{
    protected $parkirData;
    private $index = 0; // Tambahkan index untuk nomor urut

    public function __construct($parkirData)
    {
        $this->parkirData = $parkirData;
    }

    public function collection()
    {
        return collect($this->parkirData);
    }

    public function headings(): array
    {
        return [
            'NO', // Tambahkan kolom nomor urut
            'NOPOL',
            'MULAI PARKIR',
            'SELESAI PARKIR',
            'DURASI',
            'LOKASI'
        ];
    }

    public function map($data): array
    {
        return [
            ++$this->index, // Tambahkan nomor urut otomatis
            $data['no_pol'],
            $data['start_time']->format('Y-m-d H:i:s'),
            $data['end_time']->format('Y-m-d H:i:s'),
            $data['durasi'],
            $data['alamat']
        ];
    }
}
