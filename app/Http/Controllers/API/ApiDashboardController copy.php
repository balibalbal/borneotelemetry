<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Traccar;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\Vehicle;

class ApiDashboardController extends Controller
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
            // Hitung jumlah kendaraan berdasarkan status
            $offlineCount = Traccar::where('status', 'mati')
                    //->where('customer_id', $customer_id)
                    ->count();

            $onlineCount = Traccar::where('status', 'bergerak')
                    //->where('customer_id', $customer_id)
                    ->count();

            $berhentiCount = Traccar::where('status', 'berhenti')
                    //->where('customer_id', $customer_id)
                    ->count();

            $diamCount = Traccar::where('status', 'diam')
                    //->where('customer_id', $customer_id)
                    ->count();

            $totalVehicles = Traccar::whereNotNull('latitude')
                    ->whereNotNull('longitude')
                    //->where('customer_id', $customer_id)
                    ->count();  
            
            // cari apakah customer memiliki mobil
            $punyaMobil = Vehicle::where('vehicle_type', '0')
                // ->where('customer_id', $customer_id)
                ->exists();

            // cari apakah customer memiliki mobil
            $punyaMotor = Vehicle::where('vehicle_type', '1')
                // ->where('customer_id', $customer_id)
                ->exists();

            $customer = Customer::where('id', $customer_id)->first(['name', 'address', 'phone']);
        } else {
            // Hitung jumlah kendaraan berdasarkan status
            $offlineCount = Traccar::where('status', 'mati')
                    ->where('customer_id', $customer_id)
                    ->count();

            $onlineCount = Traccar::where('status', 'bergerak')
                    ->where('customer_id', $customer_id)
                    ->count();

            $berhentiCount = Traccar::where('status', 'berhenti')
                    ->where('customer_id', $customer_id)
                    ->count();

            $diamCount = Traccar::where('status', 'diam')
                    ->where('customer_id', $customer_id)
                    ->count();

            $totalVehicles = Traccar::whereNotNull('latitude')
                    ->whereNotNull('longitude')
                    ->where('customer_id', $customer_id)
                    ->count();  

            $customer = Customer::where('id', $customer_id)->first(['name', 'address', 'phone']);
        }

        
        // Cek jika tidak ada data ditemukan
        // if ($offlineCount == 0 && $onlineCount == 0 && $berhentiCount == 0 && $diamCount == 0 && $totalVehicles == 0) {
        //     return response()->json([
        //         'status' => false,
        //         'message' => 'Data tidak ditemukan untuk customer_id: ' . $customer_id,
        //         'code' => 404
        //     ], 404);
        // }

        // Mengembalikan data jika ada
        return response()->json([
            'status' => true,
            'message' => 'Data ditemukan',
            'data' => [
                'offlineCount'  => $offlineCount,
                'onlineCount'   => $onlineCount,
                'berhentiCount' => $berhentiCount,
                'diamCount'     => $diamCount,
                'totalVehicles' => $totalVehicles,
                'customerName'  => $customer->name ?? 'tidak ada',
                'customerAddress' => $customer->address ?? 'tidak ada',
                'customerPhone' => $customer->phone ?? 'tidak ada',
            ],
            'code' => 200
        ], 200);
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
     * @param  \App\Models\Traccar  $traccar
     * @return \Illuminate\Http\Response
     */
    public function show(Traccar $traccar)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Traccar  $traccar
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Traccar $traccar)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Traccar  $traccar
     * @return \Illuminate\Http\Response
     */
    public function destroy(Traccar $traccar)
    {
        //
    }
}
