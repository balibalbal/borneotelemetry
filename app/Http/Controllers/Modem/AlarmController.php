<?php

namespace App\Http\Controllers\Modem;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception; 

class AlarmController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->all();

        try {
            // Insert data ke database
            DB::table('alarms')->insert([
                'vehicle_id' => $request->input('vehicle_id'),
                'device_id' => $request->input('device_id'),
                'customer_id' => $request->input('customer_id'),
                'no_pol' => $request->input('no_pol'),
                'latitude' => $request->input('latitude'),
                'longitude' => $request->input('longitude'),
                'time' => $request->input('time'),
                //'oil_electricity' => $request->input('oil_electricity'),
                //'gps_positioned' => $request->input('gps_positioned'),
                'alarm_info' => $request->input('alarm_info'),
                //'defense' => $request->input('defense'),
                //'charge' => $request->input('charging'),
                'speed' => $request->input('speed'),
                'acc' => $request->input('hex_data'),
                'voltage_level' => $request->input('voltage_level'),
                'gsm_signal' => $request->input('gsm_signal'),
                'charge' => $request->input('buffer_data')
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
