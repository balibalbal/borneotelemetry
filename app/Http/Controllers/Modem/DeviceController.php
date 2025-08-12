<?php

namespace App\Http\Controllers\Modem;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeviceController extends Controller
{
    public function checkImei($imei)
    {
        try {
            // Cek apakah IMEI ada di tabel devices dengan
            // Melakukan join antara tabel devices dan vehicles
            $device = DB::table('devices')
            ->join('vehicles', 'devices.vehicle_id', '=', 'vehicles.id')
            ->join('customers', 'devices.customer_id', '=', 'customers.id')
            ->where('devices.imei', $imei)
            ->select('devices.id as device_id', 'devices.customer_id', 'devices.vehicle_id', 'vehicles.no_pol', 'customers.geofence')
            ->first();
//dd($device);
            if ($device) {
                
                return response()->json([
                    'message' => 'IMEI sudah terdaftar',
                    'device_id' => $device->device_id,
                    'customer_id' => $device->customer_id,
                    'vehicle_id' => $device->vehicle_id,
                    'no_pol' => $device->no_pol,
                    'geofence' => $device->geofence
                ], 200);
            } else {
                return response()->json([
                    'message' => 'IMEI tidak terdaftar'
                ], 404);  // Kode status HTTP 404 untuk not found
            }

        } catch (\Exception $e) {
            // Tangani kesalahan jika terjadi kesalahan dalam query
            return response()->json([
                'message' => 'Terjadi kesalahan saat memeriksa IMEI',
                'error' => $e->getMessage()  // Memberikan rincian kesalahan
            ], 500);  // Kode status HTTP 500 untuk kesalahan server internal
        }
    }

}
