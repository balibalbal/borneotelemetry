<?php

namespace App\Http\Controllers\Modem;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Traccar;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class TraccarMobileController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->all();

        // Tentukan kriteria pencarian, dalam hal ini vehicle_id
        $criteria = ['vehicle_id' => $data['vehicle_id']];

        // Ambil data sebelumnya dari database
        $previousTraccar = Traccar::where($criteria)->first();

        // Jika ada data sebelumnya, hitung jarak
        if ($previousTraccar) {
            $previousLat = $previousTraccar->latitude;
            $previousLong = $previousTraccar->longitude;
            $currentLat = $data['latitude'];
            $currentLong = $data['longitude'];

            // Hitung jarak menggunakan rumus Haversine
            $distance = $this->calculateDistance($previousLat, $previousLong, $currentLat, $currentLong);
            
            // Update jarak dalam data yang akan diupdate
            $data['distance'] = $distance;
            $data['total_distance'] = $distance + $previousTraccar->total_distance; // Tambahkan jarak sebelumnya
        } else {
            // Jika tidak ada data sebelumnya, jarak adalah 0
            $data['distance'] = 0;
            $data['total_distance'] = 0;
        }

        // Simpan atau perbarui data ke database
        $traccar = Traccar::updateOrCreate(
            $criteria,
            array_merge($data, [
                // 'geom' => DB::raw("ST_SetSRID(ST_MakePoint({$data['longitude']}, {$data['latitude']}), 4326)"),
                'geo_point' => DB::raw("ST_SetSRID(ST_MakePoint({$data['longitude']}, {$data['latitude']}), 4326)")
            ])
        );


        // if ($traccar) {
        //                 Vehicle::where('id', $criteria)->update([
        //                     'latitude' => $data['latitude'],
        //                     'longitude' => $data['longitude'],
        //                 ]);
        // }
        
        // if ($data['speed'] != 0) {
            // Insert data ke tabel histories
            DB::table('histories')->insert([
                'vehicle_id' => $data['vehicle_id'],
                'no_pol' => $data['no_pol'],
                'device_id' => $data['device_id'],
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
                'speed' => $data['speed'],
                'time' => $data['time'],
                'course' => $data['heading'],
                'status' => $data['status'],
                'distance' => $data['distance'],
                'total_distance' => $data['total_distance'],
                'ignition_status' => $data['ignition_status'],
                'address' => $data['address'],
                'geom' => DB::raw("ST_SetSRID(ST_MakePoint({$data['longitude']}, {$data['latitude']}), 4326)"),
                'geo_point' => DB::raw("ST_SetSRID(ST_MakePoint({$data['longitude']}, {$data['latitude']}), 4326)")
            ]);
        //}

        return response()->json(['message' => 'Data berhasil disimpan'], 200);
    }

    /**
     * Menghitung jarak antara dua koordinat latitude/longitude menggunakan rumus Haversine.
     *
     * @param float $lat1 Latitude titik pertama
     * @param float $lon1 Longitude titik pertama
     * @param float $lat2 Latitude titik kedua
     * @param float $lon2 Longitude titik kedua
     * @return float Jarak dalam kilometer
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Radius Bumi dalam kilometer

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
