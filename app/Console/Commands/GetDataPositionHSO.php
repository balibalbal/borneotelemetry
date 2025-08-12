<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\HsoLastPosition;
use App\Models\HsoGeofence;
use App\Models\HsoGeofenceEnter;
use App\Models\HsoParking;
use Illuminate\Support\Facades\Log;

class GetDataPositionHSO extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:data-position-hso';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Proses pengambilan dan penyimpanan data posisi
        $this->fetchAndStorePositionData();

        // Proses pengambilan dan penyimpanan data geofence
        $this->fetchAndStoreGeofenceData();

        // Proses pengambilan dan penyimpanan data geofence enter
        $this->fetchAndStoreGeofenceEnter();
        
        // Proses pengambilan dan penyimpanan data parkir
        $this->fetchAndStoreParkingData();
    }

    protected function fetchAndStorePositionData()
    {
        $positionUrl = 'https://gps.mtrack.co.id/_engine/AGMobileConnector.php?cmd=dmobile.lastpost&uid=29297&acc_id=29295';
        $response = Http::get($positionUrl);
        $data = $response->json();

        if ($response->ok() && isset($data['lastpost']) && is_array($data['lastpost'])) {
            foreach ($data['lastpost'] as $item) {
                try {
                    HsoLastPosition::updateOrCreate(
                        ['asset_ID' => $item['asset_ID']], // Kondisi pencarian
                        [ 
                            // Data yang diupdate atau ditambahkan
                            'event_id' => $item['event_id'],
                            'asset_code' => $item['asset_code'],
                            'latitude' => $item['latitude'],
                            'longitude' => $item['longitude'],
                            'timestamp' => $item['timestamp'],
                            'address' => $item['address'],
                        ]
                    );
                } catch (\Exception $e) {
                    Log::error('Failed to insert or update position data for asset_ID: ' . $item['asset_ID'], [
                        'error' => $e->getMessage(),
                        'data' => $item
                    ]);
                }
            }
        } else {
            Log::error('Failed to fetch or parse position data.');
        }
    }

    protected function fetchAndStoreGeofenceData()
    {
        // Ambil tanggal hari ini
        $today = now()->format('Y-m-d');
        $geofenceUrl = "https://gps.mtrack.co.id/_engine/AGMobileConnector.php?cmd=dmobile.geofencePoi&uid=29297&acc_id=29295&date={$today}";
        $geofenceResponse = Http::get($geofenceUrl);
        $geofenceData = $geofenceResponse->json();

        if ($geofenceResponse->ok() && isset($geofenceData['geofence']) && is_array($geofenceData['geofence'])) {
            foreach ($geofenceData['geofence'] as $geofenceItem) {
                try {
                    HsoGeofence::updateOrCreate(
                        ['idpoi' => $geofenceItem['idpoi']], // Kondisi pencarian
                        [ 
                            // Data yang diupdate atau ditambahkan
                            'transporter' => $geofenceItem['transporter'],
                            'name' => $geofenceItem['name'],
                            'TrackingDate' => $geofenceItem['TrackingDate'],
                            'FenceCode' => $geofenceItem['FenceCode'],
                            'Acc' => $geofenceItem['Acc'],
                            'EnterDateTimeArea' => $geofenceItem['EnterDateTimeArea'],
                            'OutDateTimeArea' => $geofenceItem['OutDateTimeArea'],
                        ]
                    );
                } catch (\Exception $e) {
                    Log::error('Failed to insert or update geofence data for idpoi: ' . $geofenceItem['idpoi'], [
                        'error' => $e->getMessage(),
                        'data' => $geofenceItem
                    ]);
                }
            }
        } else {
            Log::error('Failed to fetch or parse geofence data.');
        }
    }

    protected function fetchAndStoreGeofenceEnter()
    {
        // Ambil tanggal hari ini
        $today = now()->format('Y-m-d');
        $geofenceUrl = "https://gps.mtrack.co.id/_engine/AGMobileConnector.php?cmd=dmobile.geofencePoiEnter&uid=29297&acc_id=29295&date={$today}";
        $geofenceResponse = Http::get($geofenceUrl);
        $geofenceData = $geofenceResponse->json();

        if ($geofenceResponse->ok() && isset($geofenceData['geofence']) && is_array($geofenceData['geofence'])) {
            foreach ($geofenceData['geofence'] as $geofenceItem) {
                try {
                    $outDateTimeArea = ($geofenceItem['OutDateTimeArea'] === '0000-00-00 00:00:00') ? null : $geofenceItem['OutDateTimeArea'];
                    HsoGeofenceEnter::updateOrCreate(
                        ['idpoi' => $geofenceItem['idpoi']], // Kondisi pencarian
                        [ 
                            // Data yang diupdate atau ditambahkan
                            'transporter' => $geofenceItem['transporter'],
                            'name' => $geofenceItem['name'],
                            'TrackingDate' => $geofenceItem['TrackingDate'],
                            'FenceCode' => $geofenceItem['FenceCode'],
                            'Acc' => $geofenceItem['Acc'],
                            'EnterDateTimeArea' => $geofenceItem['EnterDateTimeArea'],
                            'OutDateTimeArea' => $outDateTimeArea
                        ]
                    );
                } catch (\Exception $e) {
                    Log::error('Failed to insert or update geofence enter data for idpoi: ' . $geofenceItem['idpoi'], [
                        'error' => $e->getMessage(),
                        'data' => $geofenceItem
                    ]);
                }
            }
        } else {
            Log::error('Failed to fetch or parse geofence data.');
        }
    }

    protected function fetchAndStoreParkingData()
    {
        // Ambil tanggal hari ini
        $today = now()->format('Y-m-d');
        $startDate = $today;
        $endDate = $today;

        // Ambil asset_ID dari endpoint pertama
        $positionUrl = 'https://gps.mtrack.co.id/_engine/AGMobileConnector.php?cmd=dmobile.lastpost&uid=29297&acc_id=29295';
        $response = Http::get($positionUrl);
        $data = $response->json();

        if ($response->ok() && isset($data['lastpost']) && is_array($data['lastpost'])) {
            foreach ($data['lastpost'] as $item) {
                $assetID = $item['asset_ID'];
                $this->fetchAndStoreParkingForAsset($assetID, $startDate, $endDate);
            }
        } else {
            Log::error('Failed to fetch or parse position data for parking.');
        }
    }

    protected function fetchAndStoreParkingForAsset($assetID, $startDate, $endDate)
    {
        $parkingUrl = "https://gps.mtrack.co.id/_engine/AGMobileConnector.php?cmd=dmobile.parkingreportdua&uid=29297&acc_id=29295&asset_id={$assetID}&date1={$startDate}&date2={$endDate}";
        $response = Http::get($parkingUrl);
        
        // Log the full response for debugging
        Log::info('Parking data response for assetID: ' . $assetID, [
            'response' => $response->body()
        ]);

        $data = $response->json();

        // Check if the response contains 'success' key
        if (isset($data['success'])) {
            if ($data['success'] == 1) {
                // Handle case when parking data is available
                if (isset($data['parking']) && is_array($data['parking'])) {
                    foreach ($data['parking'] as $item) {
                        try {
                            HsoParking::updateOrCreate(
                                ['eventID' => $item['eventID']], // Kondisi pencarian
                                [ 
                                    // Data yang diupdate atau ditambahkan
                                    'accountID' => $item['accountID'],
                                    'assetID' => $item['assetID'],
                                    'asset_code' => $item['asset_code'],
                                    'transporter' => $item['transporter'],
                                    'acc' => $item['acc'],
                                    'latitude' => $item['latitude'],
                                    'longitude' => $item['longitude'],
                                    'off' => $item['off'],
                                    'on' => $item['on'],
                                    'duration' => $item['duration'],
                                    'address' => $item['address'],
                                    'distanceKM' => $item['distanceKM'],
                                ]
                            );
                        } catch (\Exception $e) {
                            Log::error('Failed to insert or update parking data for eventID: ' . $item['eventID'], [
                                'error' => $e->getMessage(),
                                'data' => $item
                            ]);
                        }
                    }
                } else {
                    // Log message if parking array is empty or not set
                    Log::info('No parking data available for assetID: ' . $assetID, [
                        'message' => 'No parking data found in response.'
                    ]);
                }
            } else {
                // Handle case when 'success' is 0
                Log::info('No parking data available for assetID: ' . $assetID, [
                    'message' => isset($data['message']) ? $data['message'] : 'No data available'
                ]);
            }
        } else {
            // Handle unexpected response format
            Log::error('Unexpected response format for assetID: ' . $assetID, [
                'response' => $data
            ]);
        }
    }


}
