<?php

namespace App\Http\Controllers\Modem;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Traccar;
use App\Models\Vehicle;
use App\Events\UpdateTraccar;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class TraccarModemController extends Controller
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

        // Mengonversi koordinat ke alamat
        $address = $this->getAddressFromCoordinates($data['latitude'], $data['longitude']);
        $data['address'] = $address; // Menyimpan alamat ke field 'address'

        // Simpan atau perbarui data ke database
        $traccar = Traccar::updateOrCreate(
            $criteria,
            $data
        );

        // Kirim data ke websocket
        if ($traccar) {
                        Vehicle::where('id', $criteria)->update([
                            'latitude' => $data['latitude'],
                            'longitude' => $data['longitude'],
                        ]);
        }
        // if ($traccar) {
        //     event(new UpdateTraccar($traccar));
        // }

        // if ($data['speed'] != 0) {
            // Insert data ke tabel histories
            DB::table('histories')->insert([
                'vehicle_id' => $data['vehicle_id'],
                'no_pol' => $data['no_pol'],
                'customer_id' => $data['customer_id'],
                'device_id' => $data['device_id'],
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
                'speed' => $data['speed'],
                'time' => $data['time'],
                'course' => $data['course'],
                'status' => $data['status'],
                'distance' => $data['distance'],
                'total_distance' => $data['total_distance'],
                'ignition_status' => $data['ignition_status'],
                'vendor_gps' => $data['vendor_gps'],
                'address' => $address
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

    /**
     * Mendapatkan alamat dari koordinat latitude dan longitude menggunakan API LocationIQ.
     *
     * @param float $lat Latitude
     * @param float $lon Longitude
     * @return string Alamat lengkap
     */
    private function getAddressFromCoordinates($lat, $lon)
    {
        $apiKey = 'pk.f7c52dfe9402adacecc82fa7cb6d406f';
        $response = Http::get("https://eu1.locationiq.com/v1/reverse.php", [
            'key' => $apiKey,
            'lat' => $lat,
            'lon' => $lon,
            'format' => 'json',
            'zoom' => 18,
            'countrycodes' => 'ID'
        ]);

        // Cek apakah permintaan berhasil
        if ($response->successful()) {
            $data = $response->json();
            return $data['display_name'] ?? 'Alamat tidak ditemukan';
        } else {
            return 'Error mendapatkan alamat';
        }
    }
}
