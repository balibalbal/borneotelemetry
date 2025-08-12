<?php

namespace App\Exports;

use App\Models\History;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Facades\DB;

class ExportJarak implements FromCollection, WithHeadings, WithEvents
{
    private $startDate;
    private $endDate;
    private $nopol;
    private $group;
    private $customer;

    public function __construct($startDate, $endDate, $nopol, $group, $customer)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->nopol = $nopol;
        $this->group = $group;
        $this->customer = $customer;
    }

    public function collection()
    {
        // 1 = semua group dan semua nopol
        if ((int)$this->nopol[0] === 1 && (int)$this->group[0] === 1) {
            $data = History::select('no_pol',
                        DB::raw('DATE(time) as date'), 
                        DB::raw('SUM(distance) as total_distance')
                    )
                    ->where('customer_id', $this->customer)
                    ->whereDate('time', '>=', $this->startDate)
                    ->whereDate('time', '<=', $this->endDate)
                    ->groupBy(DB::raw('DATE(time)'), 'no_pol')
                    ->orderBy('no_pol', 'asc')
                    ->orderBy(DB::raw('DATE(time)'), 'asc')
                    ->get();
        } elseif ((int)$this->nopol[0] === 1 && (int)$this->group[0] !== 1){
            $data = History::select('histories.no_pol', 
                    DB::raw('DATE(histories.time) as date'), 
                    DB::raw('SUM(histories.distance) as total_distance')
                )
                ->join('vehicles as v', 'histories.vehicle_id', '=', 'v.id')
                ->where('histories.customer_id', $this->customer)
                ->whereIn('v.group_id', $this->group)
                ->whereDate('histories.time', '>=', $this->startDate)
                ->whereDate('histories.time', '<=', $this->endDate)
                ->groupBy(DB::raw('DATE(histories.time)'), 'histories.no_pol')
                ->orderBy('histories.no_pol', 'asc')
                ->orderBy(DB::raw('DATE(histories.time)'), 'asc')
                ->get();
        } else {
            $data = History::select('no_pol',
                        DB::raw('DATE(time) as date'), 
                        DB::raw('SUM(distance) as total_distance')
                    )
                    ->where('customer_id', $this->customer)
                    ->whereIn('vehicle_id', $this->nopol)
                    ->whereDate('time', '>=', $this->startDate)
                    ->whereDate('time', '<=', $this->endDate)
                    ->groupBy(DB::raw('DATE(time)'), 'no_pol')
                    ->orderBy('no_pol', 'asc')
                    ->orderBy(DB::raw('DATE(time)'), 'asc')
                    ->get();
        }

        return $data;  // Tidak perlu wrap dengan collect()
    }

    public function headings(): array
    {
        return [
            'Nopol',            
            'Tanggal',            
            'Total Jarak Harian (KM)',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $data = $this->collection();
                $row = 2; // data starts from row 2 (after headers)
                $previousNoPol = null;
                $mergeStartRow = null;
                $totalDistancePerNoPol = 0;

                foreach ($data as $index => $item) {
                    // Merge cells if nopol is the same
                    if ($item->no_pol !== $previousNoPol) {
                        if ($previousNoPol !== null) {
                            // Merge cells from the previousNoPol
                            $event->sheet->mergeCells("A{$mergeStartRow}:A" . ($row - 1));
                            $event->sheet->getStyle("A{$mergeStartRow}:A" . ($row - 1))
                                ->getAlignment()->setVertical('center'); // Set vertical alignment to center
                            
                            // Add row for total distance per nopol
                            $event->sheet->mergeCells("A{$row}:B{$row}");  // Merge A and B for the total row
                            $event->sheet->setCellValue("A{$row}", 'Total Jarak');
                            $event->sheet->setCellValue("C{$row}", number_format($totalDistancePerNoPol, 2));
                            $row++;
                        }
                        
                        $mergeStartRow = $row;
                        $previousNoPol = $item->no_pol;
                        $totalDistancePerNoPol = 0; // Reset total distance
                    }

                    // Add data to the sheet
                    $event->sheet->setCellValue("A{$row}", $item->no_pol);
                    $event->sheet->setCellValue("B{$row}", $item->date);
                    $event->sheet->setCellValue("C{$row}", number_format($item->total_distance, 2));
                    $totalDistancePerNoPol += $item->total_distance;  // Sum total distance for the current no_pol
                    $row++;
                }

                // Merge cells for the last group
                if ($previousNoPol !== null) {
                    $event->sheet->mergeCells("A{$mergeStartRow}:A" . ($row - 1));
                    $event->sheet->getStyle("A{$mergeStartRow}:A" . ($row - 1))
                        ->getAlignment()->setVertical('center'); // Set vertical alignment to center
                    // Add the total distance for the last group
                    $event->sheet->mergeCells("A{$row}:B{$row}");  // Merge A and B for the total row
                    $event->sheet->setCellValue("A{$row}", 'Total Jarak');
                    $event->sheet->setCellValue("C{$row}", number_format($totalDistancePerNoPol, 2));
                }
            },
        ];
    }
}
