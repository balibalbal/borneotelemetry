<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\Customer;
use Illuminate\Http\Request;

class ApiDeviceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $customer_id = $request->query('customer_id');
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
            $data = Device::join('vehicles', 'devices.vehicle_id', '=', 'vehicles.id')
              ->join('customers', 'devices.customer_id', '=', 'customers.id')
              ->where('devices.status', 1)
              ->select('devices.*', 'vehicles.no_pol', 'customers.name as customer')
              ->get();


        } else {
            $data = Device::join('vehicles', 'devices.vehicle_id', '=', 'vehicles.id')
              ->join('customers', 'devices.customer_id', '=', 'customers.id')
              ->where('devices.customer_id', $customer_id)
              ->where('devices.status', 1)
              ->select('devices.*', 'vehicles.no_pol', 'customers.name as customer')
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
