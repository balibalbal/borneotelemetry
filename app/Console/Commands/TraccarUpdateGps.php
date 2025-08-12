<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\Traccar;
use App\Models\History;
use App\Models\Vehicle;

class TraccarUpdateGps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'traccar:updateGps';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get all data last position from api gps.mtrack.co.id';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {        

            //$devicesResponse = Http::get("https://api.inovatrack.com/test/api/VehicleSummary/GetAll?memberCode=stal&password=ywlMSEGt38EYMeKR");
            $devicesResponse = Http::get("https://gps.mtrack.co.id/_engine/AGMobileConnector.php?cmd=dmobile.lastpost_all");

            // Tanggapi respons devices
            $devicesData = $devicesResponse->json();

            //foreach ($devicesData as $deviceData) {
            foreach ($devicesData['lastpost'] as $deviceData) {
                    // Periksa apakah perangkat dengan ID ini sudah ada
                    //$existingDevice = Traccar::find($deviceData['asset_ID']);
                    $existingDevice = Traccar::where('vehicle_id', $deviceData['asset_ID'])->first();

                    $traccarData = [
                        'customer_id' => 29295,
                        'vehicle_id' => $deviceData['asset_ID'],
                        'no_pol' => $deviceData['asset_code'],
                        'latitude' => $deviceData['latitude'],
                        'longitude' => $deviceData['longitude'],
                        'speed' => $deviceData['speedKPH'],
                        'time' => $deviceData['timestamp'],
                        'course' => $deviceData['heading'],
                        'address' => $deviceData['address'],
                        //'protocol' => $deviceData['protocol'],
                        //'latest_positions' => $deviceData['latest_positions'],
                        'engine_status' => $deviceData['statuscode'],
                        'ignition_status' => $deviceData['inputMask'],
                        'total_distance' => $deviceData['distanceKM'],
                        'vendor_gps' => 'gps' 
                    ];                    

                    if ($deviceData['inputMask'] == 'Ignition Off') {
                        $traccarData['status'] = 'ack';
                    } elseif ($deviceData['inputMask'] == 'Ignition On') {
                        $traccarData['status'] = 'online';
                    } elseif ($deviceData['statuscode'] == 'Engine Off') {
                        $traccarData['status'] = 'offline';
                    } elseif ($deviceData['statuscode'] == 'Engine On') {
                        $traccarData['status'] = 'online';
                    }elseif ($deviceData['inputMask'] == 'Periodic') {
                        $traccarData['status'] = 'engine';
                    }

                    $historyData = [
                        //'device_id' => $deviceData['asset_ID'],                        
                        'customer_id' => 29295,
                        'vehicle_id' => $deviceData['asset_ID'],
                        'no_pol' => $deviceData['asset_code'],
                        'latitude' => $deviceData['latitude'],
                        'longitude' => $deviceData['longitude'],
                        'speed' => $deviceData['speedKPH'],
                        'time' => $deviceData['timestamp'],
                        'course' => $deviceData['heading'],
                        'address' => $deviceData['address'],
                        'engine_status' => $deviceData['statuscode'],
                        'vendor_gps' => 'gps' 
                    ];

                    if ($existingDevice) {
                        // Jika sudah ada, perbarui perangkat
                        $existingDevice->update($traccarData);

                        // dimatiin dulu sementara 16-05-2024
                        
                        // Cek data terakhir dari device_id di tabel histories
                        $lastHistory = History::where('vehicle_id', $deviceData['asset_ID'])
                        ->orderBy('created_at', 'desc')
                        ->first();

                        if ($lastHistory) {
                            $lastLatitude = floatval($lastHistory->latitude);
                            $newLatitude = floatval($deviceData['latitude']);
                            $threshold = 0.000001;
                                if (abs($lastLatitude - $newLatitude) >= $threshold) {
                                    History::create($historyData);
                                }                          
                        } else {
                            History::create($historyData);
                        }

                        // Perbarui latitude dan longitude pada model Vehicle
                        $id = $existingDevice->vehicle_id;
                        Vehicle::where('id', $id)->update([
                            'latitude' => $traccarData['latitude'],
                            'longitude' => $traccarData['longitude'],
                        ]);

                    } else {
                        // Jika belum ada, buat perangkat baru
                        Traccar::create($traccarData);

                        // dimatiin dulu sementara 16-05-2024
                        // Cek data terakhir dari device_id di tabel histories
                        $lastHistory = History::where('vehicle_id', $deviceData['asset_ID'])
                        ->orderBy('created_at', 'desc')
                        ->first();

                        if ($lastHistory) {
                            $lastLatitude = floatval($lastHistory->latitude);
                            $newLatitude = floatval($deviceData['latitude']);
                            $threshold = 0.000001;
                                if (abs($lastLatitude - $newLatitude) >= $threshold) {
                                    History::create($historyData);
                                }                          
                        } else {
                            History::create($historyData);
                        }
                    }                  
                    
                   
            }
        
    }

    
}
