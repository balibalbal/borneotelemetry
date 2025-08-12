<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExportCustomer implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // Ambil semua customer
        $customers = Customer::all();

        // Modifikasi status di dalam koleksi
        $customers->transform(function ($customer) {
            $customer->status = $customer->status == 1 ? 'Aktif' : 'Tidak Aktif';
            return $customer;
        });

        return $customers;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'NAMA CUSTOMER',
            'ALAMAT',
            'PHONE',
            'EMAIL',
            'STATUS',
            'DELETE_AT',
            'CREATE_AT',
            'EDIT_AT'
        ];
    }
}
