<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class UpdateHistoriesAddress extends Command
{
    protected $signature = 'histories:update-address';
    protected $description = 'Update address in histories table from geolocation or Nominatim API';

    public function handle()
    {
        // Ambil data histories yang status_address = 0 dan address masih null atau kosong
        $histories = DB::table('histories')
            ->where('status_address', 1)
            ->where(function ($query) {
                $query->whereNull('address')->orWhere('address', 'Alamat tidak ditemukan');
            })
            ->get();

        // Proses setiap history
        foreach ($histories as $history) {
            $address = $this->getAddress($history->latitude, $history->longitude);
            if ($address) {
                // Update address di tabel histories
                DB::table('histories')
                    ->where('id', $history->id)
                    ->where('status_address', 0)
                    ->update([
                        'address' => $address,
                        'status_address' => 1,
                    ]);

                $this->info("Updated address untuk device_id: {$history->device_id} di histories sukses");

                // Update address di tabel traccars berdasarkan device_id
                DB::table('traccars')
                    ->where('device_id', $history->device_id)
                    ->update([
                        'address' => $address,
                    ]);

                $this->info("Updated address untuk device_id: {$history->device_id} di traccars sukses");
            }
        }
    }

    // Fungsi untuk mendapatkan alamat
    private function getAddress($lat, $lon)
    {
        // Cek dulu di cache
        $address = $this->getAddressFromCache($lat, $lon);

        if (!$address) {
            // Kalau tidak ada di cache, ambil dari Nominatim
            $address = $this->fetchAddressFromNominatim($lat, $lon);
            $this->cacheAddress($lat, $lon, $address);
        }

        return $address;
    }

    // Fungsi untuk mengambil alamat dari cache
    private function getAddressFromCache($lat, $lon)
    {
        $result = DB::table('geolocation_cache')
            ->whereRaw('ST_DWithin(location, ST_SetSRID(ST_Point(?, ?), 4326), 0.001)', [$lat, $lon])
            ->first();

        return $result ? $result->address : null;
    }

    // Fungsi untuk mengambil alamat dari Nominatim
    private function fetchAddressFromNominatim($lat, $lon)
    {
        $response = Http::get('https://nominatim.openstreetmap.org/reverse', [
            'lat' => $lat,
            'lon' => $lon,
            'format' => 'json',
            'zoom' => 18,
            'addressdetails' => 1,
        ]);

        if ($response->successful() && isset($response->json()['address'])) {
            return $response->json()['display_name'] ?? '';
        }

        return '';
    }

    // Fungsi untuk menyimpan alamat ke cache
    private function cacheAddress($lat, $lon, $address)
    {
        DB::table('geolocation_cache')->insert([
            'latitude' => $lat,
            'longitude' => $lon,
            'address' => $address,
            'location' => DB::raw("ST_SetSRID(ST_Point($lat, $lon), 4326)"),
        ]);
    }
}
