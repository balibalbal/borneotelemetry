<?php

namespace App\Console\Commands;

use App\Models\Vehicle;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckGeofenceHso extends Command
{
    protected $signature = 'geofenceHso:check';
    protected $description = 'Check if vehicles are within geofences and update histories';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        
        $traccars = DB::table('traccars')
                        ->where('active', 1)
                        ->where('customer_id', 3)
                        ->get();

        foreach ($traccars as $traccar) {
            $geofences = DB::table('geofences')
                        ->where('type', 1)
                        ->where('customer_id', 3)
                        ->get();

           
            foreach ($geofences as $geofence) {
                list($geofenceLat, $geofenceLong) = explode(',', $geofence->latlong);
                $distance = $this->haversineDistance($traccar->latitude, $traccar->longitude, $geofenceLat, $geofenceLong);
                
                if ($distance <= $geofence->radius) {
                    //dd($distance); exit;
                    $last_geo = DB::table('histories_geofences')
                        ->where('vehicle_id', $traccar->vehicle_id)
                        ->where('geofence_id', $geofence->id)
                        ->orderBy('enter_time', 'desc') // Urutkan berdasarkan waktu masuk
                        ->pluck('status_geofence') // Ambil hanya field status_geofence
                        ->first(); // Ambil nilai pertama
                    // jika status geo != 1 maka mobil sudah keluar dari geofence dan kembali masuk lagi 
                    if ($last_geo != 1) {                        
                        
                        DB::table('histories_geofences')->insert([
                            //'traccar_id' => $traccar->id,
                            'geofence_id' => $geofence->id,
                            'device_id' => $traccar->device_id,
                            'customer_id' => $traccar->customer_id,
                            'vehicle_id' => $traccar->vehicle_id,
                            'no_pol' => str_replace(' ', '', $traccar->no_pol),
                            'geofence_name' => $geofence->name,
                            'acc' => $traccar->ignition_status,
                            'enter_time' => now(),
                            'status_geofence' => 1,
                            'status_kirim' => 0,
                        ]);

                        $this->info("Traccar ID: {$traccar->id}, enter area geofence");

                    } 
                } else {
                    $last_geo = DB::table('histories_geofences')
                        ->where('vehicle_id', $traccar->vehicle_id)
                        ->where('geofence_id', $geofence->id)
                        ->orderBy('enter_time', 'desc') // Urutkan berdasarkan waktu masuk
                        ->pluck('status_geofence') // Ambil hanya field status_geofence
                        ->first(); // Ambil nilai pertama
                    
                    if ($last_geo != 2) {
                            DB::table('histories_geofences')
                                ->where('vehicle_id', $traccar->vehicle_id)
                                //->where('status_geofence', 1)
                                ->where('geofence_id', $geofence->id)    
                                ->update([
                                    'exit_time' => now(),
                                    'acc' => $traccar->ignition_status,
                                    'status_geofence' => 2,
                            ]);

                            $this->info("Traccar ID: {$traccar->id}, exit area geofence");
                    }
                }
            }
            
        }
    }


    private function haversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // Radius Bumi dalam meter

        $lat1Rad = deg2rad($lat1);
        $lon1Rad = deg2rad($lon1);
        $lat2Rad = deg2rad($lat2);
        $lon2Rad = deg2rad($lon2);

        $latDelta = $lat2Rad - $lat1Rad;
        $lonDelta = $lon2Rad - $lon1Rad;

        $distance = 2 * $earthRadius * asin(sqrt(
            sin($latDelta / 2) * sin($latDelta / 2) +
            cos($lat1Rad) * cos($lat2Rad) *
            sin($lonDelta / 2) * sin($lonDelta / 2)
        ));

        return $distance;
    }
}
