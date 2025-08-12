<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\Customer;
use Illuminate\Http\Request;

class ApiDriverController extends Controller
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
            $data = Driver::join('customers', 'drivers.customer_id', '=', 'customers.id')
              ->where('drivers.status', 1)
              ->select('drivers.*', 'customers.name as customer')
              ->get();


        } else {
            $data = Driver::join('customers', 'drivers.customer_id', '=', 'customers.id')
              ->where('drivers.customer_id', $customer_id)
              ->where('drivers.status', 1)
              ->select('drivers.*', 'customers.name as customer')
              ->get();
        }
        
        return response()->json([
            'status' => true,
            'message' => 'data ditemukan',
            'data' => $data,
            'code' => 200
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Driver  $driver
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Driver::where('id', $id)->get();
        return response()->json([
            'status' => true,
            'message' => 'data ditemukan',
            'data' => $data,
            'code' => 200
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Driver  $driver
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Driver $driver)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Driver  $driver
     * @return \Illuminate\Http\Response
     */
    public function destroy(Driver $driver)
    {
        //
    }
}
