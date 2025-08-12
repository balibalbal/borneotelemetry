<?php 

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;
use App\Models\History;

class ExportJarakAdmin implements FromCollection, WithHeadings
{
    private $startDate;
    private $endDate;
    private $customer;
    private $noCounter = 1;

    public function __construct($startDate, $endDate, $customer)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->customer = $customer;
    }

    public function collection()
    {
        // Ambil total distance per no_pol
        $data = History::select(
                'no_pol',
                DB::raw('ROUND(COALESCE(SUM(distance), 0), 3) AS distance_today')
            )
            ->where('customer_id', $this->customer)
            ->whereDate('time', '>=', $this->startDate)
            ->whereDate('time', '<=', $this->endDate)
            ->groupBy('no_pol')
            ->get();

        // Mengambil data terbaru per no_pol
        $latestData = History::select('no_pol', 'time', 'total_distance', 'latitude', 'longitude', 'address', 'status')
            ->where('customer_id', $this->customer)
            ->whereIn('no_pol', $data->pluck('no_pol')) // Hanya mengambil no_pol yang relevan
            ->whereDate('time', '>=', $this->startDate)
            ->whereDate('time', '<=', $this->endDate)
            ->orderBy('time', 'desc')
            ->get()
            ->groupBy('no_pol');

        // Array untuk menyimpan hasil akhir
        $formattedData = [];
        foreach ($data as $index => $item) {
            // Ambil data terbaru berdasarkan no_pol
            $latest = $latestData->get($item->no_pol)->first(); // Ambil data terbaru

            $formattedData[] = [
                'no' => $index + 1, 
                'rentang_tanggal' => $this->startDate . ' s/d ' . $this->endDate,
                'tanggal' => $latest->time,
                'nopol' => $item->no_pol,
                'latitude' => $latest->latitude ?? null,
                'longitude' => $latest->longitude ?? null,
                'distance_today' => number_format($item->distance_today, 2, ',', '.'), // Format angka Indonesia
                'total_distance' => number_format($latest->total_distance ?? 0, 2, ',', '.'), // Ganti jika ada total_distance
                'alamat_terakhir' => $latest->address ?? null,
                'status_terakhir' => $latest->status ?? null,
            ];
        }

        return collect($formattedData);        
    }


    public function headings(): array
    {
        return [
            'No',
            'Rentang Tanggal',
            'Tanggal Update',
            'Nopol',
            'Latitude',
            'Longitude',            
            'Jarak Tempuh (KM)',
            'Total Jarak Tempuh (KM)',
            'Alamat Terakhir',            
            'Status Terakhir',
        ];
    }
}
