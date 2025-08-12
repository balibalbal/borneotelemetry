<?php

namespace App\Http\Controllers\Modem;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransmissionModemController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->all();

        try {
            // Insert data ke database
            DB::table('transmisions')->insert([
                'vehicle_id' => $request->input('vehicle_id'),
                'device_id' => $request->input('device_id'),
                'customer_id' => $request->input('customer_id'),
                'no_pol' => $request->input('no_pol'),
                'information_type' => $request->input('information_type'),                
            ]);
    
            return response()->json(['message' => 'Data berhasil di insert'], 200);
    
        } catch (Exception $e) {
            // Tangani kesalahan jika insert gagal
            return response()->json([
                'message' => 'Gagal menyimpan data',
                'error' => $e->getMessage()  // Memberikan rincian kesalahan
            ], 500);  // Kode status HTTP 500 untuk kesalahan server internal
        }
    }
}
