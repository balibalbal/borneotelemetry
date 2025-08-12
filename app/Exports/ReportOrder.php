<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Facades\DB;

class ReportOrder implements FromCollection, WithHeadings, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $startDate;
    private $endDate;
    private $noCounter = 1;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        //set_time_limit(120); 

        $data = DB::select("SELECT
            DATE(assign_orders.transfer_date) AS tgl_transfer,
            DATE(orders.order_date) AS order_date, 
            customers.name AS nama_customer, 
            orders.da_si, 
            assign_orders.no_pol, 
            assign_orders.driver_code, 
            assign_orders.driver_name AS nama_supir, 
            fares.name as muatan,
            orders.zack_packing, 
            orders.atas_pt, 
            orders.grade, 
            orders.closing,  
            orders.no_order,
            depos.name AS nama_depo,
            orders.nama_geofence_bongkar AS bongkar, 
            orders.consignee,
            routes.name AS nama_rute, 
            orders.quantity, 
            orders.nama_barang, 
            assign_orders.no_container, 
            assign_orders.seal, 
            '-' AS no_sa,
            orders.uang_jalan, 
            assign_orders.potongan, 
            assign_orders.tambahan, 
            assign_orders.upah_kuli, 
            assign_orders.transfer_supir, 
            DATE(assign_orders.transfer_date) AS transfer_date, 
            users.name as cmt,
            assign_orders.no_transfer,
            TIME(assign_orders.transfer_date) AS transfer_time,
            '' as no_sj_customer,
            IF(orders.insentif IS NOT NULL AND orders.insentif != 0, orders.enter_time, '') AS in_customer,
            IF(orders.insentif IS NOT NULL AND orders.insentif != 0, orders.out_time, '') AS out_customer,
            orders.insentif,
            assign_orders.note,
            CASE WHEN orders.status = 0 THEN 'Menunggu Assign' 
            WHEN orders.status = 1 THEN 'Menuju Depo/Pelabuhan' 
            WHEN orders.status = 2 THEN 'Di Depo/Pelabuhan' 
            WHEN orders.status = 3 THEN 'OTW' 
            WHEN orders.status = 4 THEN 'CUST' 
            WHEN orders.status = 5 THEN 'BACK' 
            WHEN orders.status = 6 THEN 'FINISH' 
            WHEN orders.status = 7 THEN 'Menunggu Antrian' 
            ELSE orders.status END AS status,
            '-' AS odol
        FROM 
            assign_orders 
            LEFT JOIN users ON assign_orders.user_id = users.id
            JOIN orders ON assign_orders.order_id = orders.id 
            LEFT JOIN fares ON orders.fare_id = fares.id
            LEFT JOIN depos ON orders.depo_id = depos.id 
            LEFT JOIN customers ON orders.customer_id = customers.id 
            LEFT JOIN routes ON orders.route_id = routes.id 
            LEFT JOIN drivers ON assign_orders.driver_id = drivers.id 
            LEFT JOIN vehicles ON assign_orders.vehicle_id = vehicles.id
        WHERE
            orders.deleted_at IS NULL AND assign_orders.deleted_at IS NULL AND
            DATE(assign_orders.transfer_date) BETWEEN ? AND ?", 
            [
                $this->startDate, 
                $this->endDate
            ]
        );
        
        // Mengurutkan data berdasarkan da_si
        usort($data, function($a, $b) {
            return strcmp($a->da_si, $b->da_si);
        });

        // Tambahkan nomor urut di awal setiap objek dan tambahkan baris kosong jika da_si berbeda
        $numberedData = [];
        $groupCounter = 1;
        $prevDaSi = null;

        foreach ($data as $index => $item) {
            $currentDaSi = $item->da_si;

            if ($prevDaSi !== null && $currentDaSi !== $prevDaSi) {
                // Tambahkan baris kosong jika da_si berbeda
                $numberedData[] = (object)[];
                $groupCounter = 1;
            }

            $itemArray = (array)$item;
            $itemArray = array_merge(['no' => $groupCounter], $itemArray);
            $numberedData[] = (object)$itemArray;

            $prevDaSi = $currentDaSi;
            $groupCounter++;
        }

        return collect($numberedData);        
        
    }

    public function headings(): array
    {
        return [
            'NO',
            'TANGGAL TRANSFER',
            'TANGGAL DO',
            'NAMA CUSTOMER',
            'DA/SI',
            'NOPOL',
            'KODE SUPIR',
            'NAMA SUPIR',
            '20 / 40 / COMBO',
            'ZACK/PACKING',
            'PT',
            'GRADE',
            'CLOSING',
            'BOOK NUMBER',
            'DEPO',
            'BONGKAR',
            'PENERIMA/CONSIGNEE',
            'NAMA RUTE',
            'QUANTITY',
            'NAMA BARANG',
            'NO. CONTAINER',
            'NO. SEAL',
            'NO. SA',
            'UANG JALAN',
            'POTONGAN',
            'TAMBAHAN',
            'KULI',
            'TRANSFER SUPIR',
            'TANGGAL TRANSFER',
            'CMT',
            'NO. TRANSFER',
            'JAM TRANSFER',
            'NO SJ CUSTOMER',
            'IN CUSTOMER',
            'OUT CUSTOMER',
            'INSENTIF',
            'KETERANGAN',
            'STATUS ORDER',
            'ODOL (OVERLOAD)'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $dataRows = $sheet->toArray();
                $currentRow = 2; // Mulai dari baris setelah header
                $prevDaSi = null;

                // Apply blue background to the header row
                $sheet->getStyle('A1:AM1')->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'rgb' => '3FA2F6' // Light Blue
                        ]
                    ],
                    'font' => [
                        'bold' => true
                    ]
                ]);
                
                foreach ($dataRows as $rowIndex => $row) {
                    if ($rowIndex === 0) continue; // Skip the header row

                    $zakColumnIndex = 9; // zero-indexed
                    // $statusColumnIndex = 9; // zero-indexed
                    $daSiColumnIndex = 5; // zero-indexed
                    $gradeColumnIndex = 12; // zero-indexed
                    // $closingColumnIndex = 16; // zero-indexed
                    $orderDateColumnIndex = 3;
                    $transferDateColumnIndex = 2;
                    $transferDateColumnIndex2 = 29;

                    // $status = $row[$statusColumnIndex - 1];
                    $zak = $row[$zakColumnIndex - 1];
                    $daSi = $row[$daSiColumnIndex - 1];
                    $grade = $row[$gradeColumnIndex - 1];
                    // $closing = $row[$closingColumnIndex - 1];
                    $orderDate = $row[$orderDateColumnIndex - 1];
                    $transferDate = $row[$transferDateColumnIndex - 1];
                    $transferDate2 = $row[$transferDateColumnIndex2 - 1];

                    // Convert order_date format
                    $orderDateCell = $sheet->getCellByColumnAndRow($orderDateColumnIndex, $currentRow);
                    if ($orderDate != null) {
                        $date = \PhpOffice\PhpSpreadsheet\Shared\Date::stringToExcel($orderDate);
                        $orderDateCell->setValue($date);
                        $sheet->getStyle($orderDateCell->getCoordinate())->getNumberFormat()->setFormatCode('d/m/yyyy');
                    }

                    // Convert transfer_date format
                    $transferDateCell = $sheet->getCellByColumnAndRow($transferDateColumnIndex, $currentRow);
                    if ($transferDate != null) {
                        $date = \PhpOffice\PhpSpreadsheet\Shared\Date::stringToExcel($transferDate);
                        $transferDateCell->setValue($date);
                        $sheet->getStyle($transferDateCell->getCoordinate())->getNumberFormat()->setFormatCode('d/m/yyyy');
                    }

                    // Convert transfer_date format dua
                    $transferDateCell2 = $sheet->getCellByColumnAndRow($transferDateColumnIndex2, $currentRow);
                    if ($transferDate2 != null) {
                        $date = \PhpOffice\PhpSpreadsheet\Shared\Date::stringToExcel($transferDate2);
                        $transferDateCell2->setValue($date);
                        $sheet->getStyle($transferDateCell2->getCoordinate())->getNumberFormat()->setFormatCode('d/m/yyyy');
                    }

                    // Add an empty row if the da_si has changed
                    if ($prevDaSi !== null && $daSi !== $prevDaSi) {
                        $sheet->insertNewRowBefore($currentRow, 1);

                        // Apply black background to the new row
                        $sheet->getStyle("A{$currentRow}:AM{$currentRow}")->applyFromArray([
                            'fill' => [
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'startColor' => [
                                    'rgb' => '000000' // Black
                                ]
                            ]
                        ]);

                        $currentRow++;
                    }

                    $prevDaSi = $daSi;

                    // $cell = $sheet->getCellByColumnAndRow($statusColumnIndex, $currentRow);

                    // // Apply conditional formatting based on the status value
                    // if ($status == 'CUST') {
                    //     $cell->getStyle()->applyFromArray([
                    //         'fill' => [
                    //             'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    //             'startColor' => [
                    //                 'rgb' => '06D001' // HIJAU
                    //             ]
                    //         ]
                    //     ]);
                    // } elseif ($status == 'BACK') {
                    //     $cell->getStyle()->applyFromArray([
                    //         'fill' => [
                    //             'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    //             'startColor' => [
                    //                 'rgb' => '615EFC' // Blue
                    //             ]
                    //         ],
                    //         'font' => [
                    //             'color' => [
                    //                 'rgb' => 'FFFFFF' // white
                    //             ]
                    //         ]
                    //     ]);
                    // } elseif ($status == 'FINISH') {
                    //     $cell->getStyle()->applyFromArray([
                    //         'fill' => [
                    //             'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    //             'startColor' => [
                    //                 'rgb' => 'FF8F00' // Orange
                    //             ]
                    //         ]
                    //     ]);
                    // } elseif ($status == 'OTW') {
                    //     $cell->getStyle()->applyFromArray([
                    //         'fill' => [
                    //             'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    //             'startColor' => [
                    //                 'rgb' => 'FFEEA9' // Orange muda
                    //             ]
                    //         ]
                    //     ]);
                    // }

                    $cellZak = $sheet->getCellByColumnAndRow($zakColumnIndex, $currentRow);

                    // Apply conditional formatting based on the zak value
                    if ($zak == 'Curah') {
                        $cellZak->getStyle()->applyFromArray([
                            'fill' => [
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'startColor' => [
                                    'rgb' => 'FF0000' // merah
                                ]
                            ],
                            'font' => [
                                'color' => [
                                    'rgb' => 'FFFFFF' // white
                                ]
                            ]
                        ]);
                    }

                    // $cellPosisi = $sheet->getCellByColumnAndRow($posisiColumnIndex, $currentRow);
                    // $currentPosisi = $cellPosisi->getValue();

                    // // $cellMesin = $sheet->getCellByColumnAndRow($mesinColumnIndex, $currentRow);

                    // if ($mesin == 'offline') {
                    //     $newPosisi = $currentPosisi . ' OFF';
                    //     $cellPosisi->setValue($newPosisi);
                    //     $cellPosisi->getStyle()->applyFromArray([
                    //         'fill' => [
                    //             'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    //             'startColor' => [
                    //                 'rgb' => 'FF0000' // merah
                    //             ]
                    //         ],
                    //         'font' => [
                    //             'color' => [
                    //                 'rgb' => 'FFFFFF' // white
                    //             ]
                    //         ]
                    //     ]);
                    // }

                    $cellGrade = $sheet->getCellByColumnAndRow($gradeColumnIndex, $currentRow);

                    // Apply yellow background to grade column
                    $cellGrade->getStyle()->applyFromArray([
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => [
                                'rgb' => 'FFFF00' // Yellow
                            ]
                        ],
                        'font' => [
                            'color' => [
                                'rgb' => 'FF0000' // Red
                            ]
                        ]
                    ]);

                    // $cell = $sheet->getCellByColumnAndRow($closingColumnIndex, $currentRow);

                    // // Apply conditional formatting based on the status value
                    // if ($closing != '' && substr($closing, 0, 10) == $today) {
                    //     $cell->getStyle()->applyFromArray([
                    //         'fill' => [
                    //             'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    //             'startColor' => [
                    //                 'rgb' => 'FF0000' // red
                    //             ]
                    //         ],
                    //         'font' => [
                    //             'color' => [
                    //                 'rgb' => 'FFFFFF' // white
                    //             ]
                    //         ]
                    //     ]);
                    // }

                    // Apply border to the entire sheet
                    // $highestRow = $sheet->getHighestRow();
                    // $highestColumn = $sheet->getHighestColumn();
                    // $sheet->getStyle("A1:{$highestColumn}{$highestRow}")->applyFromArray([
                    //     'borders' => [
                    //         'allBorders' => [
                    //             'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    //             'color' => ['argb' => '000000']
                    //         ]
                    //     ]
                    // ]);
                    
                    $currentRow++;
                }

                // Hapus kolom mesin
                //$sheet->removeColumn('K');
            }
        ];
    }
}
