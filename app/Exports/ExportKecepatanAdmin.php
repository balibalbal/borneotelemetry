<?php 

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithStyles;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportKecepatanAdmin implements FromCollection, WithHeadings, WithTitle, WithCustomStartCell, WithStyles
{
    private $customer;
    private $startDate;
    private $endDate;
    private $no_pol;

    public function __construct($startDate, $endDate, $no_pol, $customer)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->no_pol = $no_pol;
        $this->customer = $customer;
    }

    public function collection()
    {
        // Ambil data dari database sesuai rentang tanggal
        $data = DB::select("
            SELECT 
                histories.time AS 'Tanggal Update',
                histories.no_pol AS 'Nopol',
                histories.speed AS 'Speed (kph)',
                histories.distance AS 'Distance (KM)',
                histories.total_distance AS 'Total Distance (KM)',
                histories.course AS 'Course (Arah Derajat)',
                CASE 
                    WHEN histories.status = 'bergerak' THEN 'Bergerak'
                    WHEN histories.status = 'mati' THEN 'Mati'
                    WHEN histories.status = 'diam' THEN 'Diam'
                    WHEN histories.status = 'berhenti' THEN 'Berhenti'
                    ELSE histories.status
                END AS 'Status',    
                address AS 'Alamat'
            FROM histories 
            WHERE histories.customer_id = ? AND histories.no_pol = ? AND DATE(histories.time) BETWEEN ? AND ?", [
            $this->customer,         
            $this->no_pol,       
            $this->startDate,       
            $this->endDate          
        ]);

        // Mengkonversi hasil query ke dalam collection
        return collect($data);
    }

    public function headings(): array
    {
        return [
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

    public function title(): string
    {
        return 'Kecepatan Report';  // Judul sheet Excel
    }

    public function startCell(): string
    {
        return 'A4'; // Data dan header akan dimulai dari sel A3
    }

    public function styles(Worksheet $sheet)
    {
        // Menambahkan Judul Report
        $sheet->setCellValue('A1', 'Laporan Kecepatan Kendaraan');
        $sheet->mergeCells('A1:H1'); // Menggabungkan sel A1 hingga H1 untuk judul
        $sheet->getStyle('A1')->getFont()->setBold(true); // Membuat teks judul tebal

        // Menambahkan rentang tanggal di sel A1
        $sheet->setCellValue('A2', 'Rentang Tanggal Laporan: ' . $this->startDate . ' s/d ' . $this->endDate);
        $sheet->mergeCells('A2:H2'); 
        $sheet->getStyle('A2')->getFont()->setItalic(true);
    }
}
