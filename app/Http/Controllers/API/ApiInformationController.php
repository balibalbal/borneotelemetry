<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Information;
use App\Models\Customer;

class ApiInformationController extends Controller
{
    public function index(Request $request)
    {
        $customer_id = $request->query('customer_id'); //dd($customer_id); exit;
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

        if ($customer_id == 1) {
            $data = Information::leftjoin('customers', 'information.customer_id', '=', 'customers.id')
              ->where('customers.status', 1)
              ->select('information.*', 'customers.name as customer')
              ->get();
        } else {
            $data = Information::leftjoin('customers', 'information.customer_id', '=', 'customers.id')
              ->where('information.customer_id', $customer_id)
              ->where('customers.status', 1)
              ->select('information.*', 'customers.name as customer')
              ->get();
        }
        
        //dd('data', $id); exit;
        return response()->json([
            'status' => true,
            'message' => 'data ditemukan',
            'data' => $data
        ]);
    }
}
