<?php

namespace App\Http\Controllers\Modem;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Traccar;
use App\Events\UpdateTraccar;
use Illuminate\Support\Facades\DB;

class TraccarModemController extends Controller
{
    public function store(Request $request)
    {
        // $data = $request->validate([
        //     'vehicle_id' => 'required',
        //     'no_pol' => 'required',
        //     'latitude' => 'required',
        //     'longitude' => 'required',
        //     'speed' => 'required',
        //     'time' => 'required',
        //     'course' => 'required',
        //     'status' => 'required',
        //     'total_distance' => 'required',
        //     'uniqueId' => 'required',
        //     'ignition_status' => 'required',
        //     'vendor_gps' => 'required'
        // ]);

        // try {
        //     // Insert atau update data di tabel traccars
        //     DB::table('traccars')->updateOrInsert(
        //         [
        //             'vehicle_id' => $data['vehicle_id'],  // Hanya kunci unik atau kombinasi yang tepat
        //         ],
        //         [
        //             'no_pol' => $data['no_pol'],
        //             'uniqueId' => $data['uniqueId'],
        //             'lat' => $data['latitude'],
        //             'long' => $data['longitude'],
        //             'speed' => $data['speed'],
        //             'time' => $data['time'],
        //             'course' => $data['course'],
        //             'status' => $data['status'],
        //             'ignition_status' => $data['ignition_status'],
        //             'total_distance' => $data['total_distance'],
        //             'vendor_gps' => $data['vendor_gps']
        //         ]
        //     );

        //     // Cek jika speed tidak sama dengan 0
        //     if ($data['speed'] != 0) {
        //         // Insert data ke tabel histories
        //         DB::table('histories')->insert([
        //             'vehicle_id' => $data['vehicle_id'],
        //             'no_pol' => $data['no_pol'],
        //             'lat' => $data['latitude'],
        //             'long' => $data['longitude'],
        //             'speed' => $data['speed'],
        //             'time' => $data['time'],
        //             'course' => $data['course'],
        //             'status' => $data['status'],
        //             'total_distance' => $data['total_distance'],
        //             'vendor_gps' => $data['vendor_gps']
        //         ]);
        //     }

        //     return response()->json(['message' => 'Data berhasil diinsert atau diupdate'], 200);

        // } catch (\Exception $e) {
        //     // Tangani kesalahan jika insert gagal
        //     return response()->json([
        //         'message' => 'Gagal menyimpan data',
        //         'error' => $e->getMessage()  // Memberikan rincian kesalahan
        //     ], 500);  // Kode status HTTP 500 untuk kesalahan server internal
        // }

        $data = $request->all();

            // Tentukan kriteria pencarian, dalam hal ini vehicle_id
            $criteria = ['vehicle_id' => $data['vehicle_id']];

            // Simpan atau perbarui data ke database
            $traccar = Traccar::updateOrCreate(
                $criteria,
                $data // Data yang akan diupdate atau dimasukkan
            );

            // Jika alarmType adalah 'sos', pancarkan event
            // if ($information->alarmType === 'normal') {
            if ($traccar) {
                event(new UpdateTraccar($traccar));
            }

            if ($data['speed'] != 0) {
                // Insert data ke tabel histories
                DB::table('histories')->insert([
                    'vehicle_id' => $data['vehicle_id'],
                    'no_pol' => $data['no_pol'],
                    'lat' => $data['lat'],
                    'long' => $data['long'],
                    'speed' => $data['speed'],
                    'time' => $data['time'],
                    'course' => $data['course'],
                    'status' => $data['status'],
                    'total_distance' => $data['total_distance'],
                    'vendor_gps' => $data['vendor_gps']
                ]);
            }


            return response()->json(['message' => 'Data berhasil disimpan'], 200);
    }
}
