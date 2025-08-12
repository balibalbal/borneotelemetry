<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\Customer;

class ApiVehicleController extends Controller
{
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
            $data = Vehicle::leftjoin('drivers', 'vehicles.driver_id', '=', 'drivers.id')
              ->join('customers', 'vehicles.customer_id', '=', 'customers.id')              
              ->leftjoin('traccars', 'vehicles.id', '=', 'traccars.vehicle_id')
              ->where('vehicles.status', 1)
              ->select('vehicles.*', 'customers.name as customer', 'drivers.name as driver', 'traccars.address as alamat', 'traccars.course', 'traccars.status as statusTraccar', 'traccars.ignition_status')
              ->get();
        } else {
            $data = Vehicle::leftjoin('drivers', 'vehicles.driver_id', '=', 'drivers.id')
              ->join('customers', 'vehicles.customer_id', '=', 'customers.id')
              ->leftjoin('traccars', 'vehicles.id', '=', 'traccars.vehicle_id')
              ->where('vehicles.customer_id', $customer_id)
              ->where('vehicles.status', 1)
              ->select('vehicles.*', 'customers.name as customer', 'drivers.name as driver', 'traccars.address as alamat', 'traccars.course', 'traccars.status as statusTraccar', 'traccars.ignition_status')
              ->get();
        }

        //$data = Vehicle::where('customer_id', $customer_id)->get();
        //dd('data', $id); exit;
        return response()->json([
            'status' => true,
            'message' => 'data ditemukan',
            'data' => $data,
            'code' => 200
        ]);
    }

    public function show(string $id)
    {
        $data = Vehicle::where('id', $id)->get();
        //dd('data', $id); exit;
        return response()->json([
            'status' => true,
            'message' => 'data ditemukan',
            'data' => $data
        ]);
    }
}
