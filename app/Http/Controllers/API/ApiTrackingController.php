<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\History;

class ApiTrackingController extends Controller
{
    public function index(Request $request)
    {
        $customer_id = $request->query('customer_id');
        $start_date = $request->query('start_date');
        $end_date = $request->query('end_date');
        $no_pol = $request->query('no_pol');
        // Validasi apakah customer_id di input di endpoint
        if (is_null($customer_id)) {
            return response()->json([
                'status' => false,
                'message' => 'customer_id kosong, silahkan isi dulu',
                'code' => 400
            ], 400);
        }

        // Cek apakah customer_id ada di tabel customers
        $customerExists = Customer::where('id', $customer_id)->exists();
        
        if (!$customerExists) {
            return response()->json([
                'status' => false,
                'message' => 'customer_id tidak ditemukan',
                'code' => 404
            ], 404);
        }

        // ambil data history berdasarkan customer terlebih dahulu
        //$query = History::where('customer_id', $customer_id);
        $query = History::where('histories.customer_id', $customer_id)
                ->join('vehicles', 'vehicles.id', '=', 'histories.vehicle_id')
                ->select('traccars.*', 'vehicles.vehicle_type') // Ambil semua data traccars + vehicle_type
                ->orderBy('histories.time', 'asc')
                ->get();

        // Lakukan filter berdasarkan no_pol jika no_pol tidak kosong di endpoin
        if (!is_null($no_pol)) {
            $query->where('no_pol', $no_pol);
        }

        // Filter berdasarkan start_date dan end_date jika endpoin terdapat tanggal dan tidak kosong
        if (!is_null($start_date) && !is_null($end_date)) {
            $start_date = date('Y-m-d', strtotime($start_date));
            $end_date = date('Y-m-d', strtotime($end_date . ' +1 day'));
            $query->whereBetween('time', [$start_date, $end_date]);
        }

        // Ambil data yang telah difilter
        $data = $query->get();

        //dd('data', $id); exit;
        return response()->json([
            'status' => true,
            'message' => 'data ditemukan',
            'data' => $data
        ]);
    }
}
