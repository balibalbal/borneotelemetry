<?php

namespace App\Console\Commands;

use App\Models\GeofenceSession;
use App\Models\Parking;
use Illuminate\Console\Command;
use GuzzleHttp\Client;
use App\Models\Traccar;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SendDataToHSO extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:data-to-hso';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get token and send data to client every day';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user = 'c23ebfbf-824a-4455-a7a9-cdc6b932d00b';
        $password = 'TBLBYh4YieLFqTEHJSkROyahyuEYXjepRtT/4HAk2yI=';
        // $user = 'e06c4a77-1254-4b57-b2bd-2e3765230add';
        // $password = 'PQh7V6EgaFVhOtwHBff9z/Ldms4HDuAFzT2YvdwdCao=';
        $url = 'https://astraapigc.astra.co.id/usermanagementapi/Token';
        //$url = 'https://astraapigc-dev.astra.co.id/usermanagementapi/Token';

        $client = new Client();
        try {
            $response = $client->post($url, [
                'headers' => ['Content-Type' => 'text/plain'],
                'auth' => [$user, $password],
                'form_params' => ['grant_type' => 'client_credentials'],
                'debug' => false,
            ]);
        
            // Decode JSON response
            $responseArray = json_decode($response->getBody(), true);
        
            // Print response
            $this->info('Response: ' . print_r($responseArray, true));
        
            // Check jika 'access_token' ada di dalam response
            if (isset($responseArray['access_token'])) {
                $token = $responseArray['access_token'];
                $this->info('TMS token: ' . $responseArray['access_token']);

                // panggil function
                $this->sendDataLastPosition($token);
                $this->sendDataGeofenceEnter($token);
                $this->sendDataGeofenceExit($token);
                $this->sendDataParking($token);

            } else {
                $this->info('Access token not found in response.');
                Log::channel('hso')->error('Access token not found in response.');
            }
        } catch (RequestException $e) {
            $this->error('Request failed: ' . $e->getMessage());
        }            

    }

    // Kirim data lokasi terakhir
    private function sendDataLastPosition($token)
    {
        try {
            $positions = Traccar::where('customer_id', 3)
                ->whereNotNull('address')
                ->where('address', '!=', '')
                ->get();
            
            foreach ($positions as $position) {
                $dataToSend = [
                    'VendorCodeGPS' => 'MTRACK',
                    'VehicleNo' => str_replace(' ', '', $position['no_pol']),
                    'TrackingDate' => $position['time'],
                    'Coordinate' => $position['latitude'] . "," . $position['longitude'],
                    'Address' => $position['address'],
                    'Speed' => (int) round($position['speed'])
                ];

                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => "Bearer $token"
                ])->post('https://astraapigc.astra.co.id/tmsunit/api/GPS/Tracking', $dataToSend);

                // Debug respons JSON
                $responseData = json_decode($response->body(), true);

                if ($response->successful() && isset($responseData['Acknowledge']) && $responseData['Acknowledge'] === 1) {
                    $this->info('Nopol ' . $position['no_pol'] . ' sent successfully');
                    Log::channel('hso')->info('Last position nopol ' . $position['no_pol'] . ' - ' . $responseData['Message']);
                } else {
                    $errorMessage = $responseData['Message'] ?? 'Unknown error';
                    $this->error('Nopol ' . $position['no_pol'] . ' failed to send data: ' . $errorMessage);
                    Log::channel('hso')->error('Last position nopol ' . $position['no_pol'] . ' failed to send data: ' . $errorMessage);
                }
            }
        } catch (\Exception $e) {
            $this->error('Error in sendDataLastPosition: ' . $e->getMessage());
            Log::channel('hso')->error('Error in sendDataLastPosition: ' . $e->getMessage());
        }
    }


    private function sendDataGeofenceEnter($token)
    {
        try {
            $fenceEnters = GeofenceSession::where('customer_id', 3)
                ->where('status_geofence', 1)
                ->where('status_kirim', 0)
                ->get();
                //var_dump($fenceEnters); exit;
            
            foreach ($fenceEnters as $fenceEnter) {
                $dataToSend = [
                    'VendorCodeGPS' => 'MTRACK',
                    'VehicleNo' => $fenceEnter['no_pol'],
                    'TrackingDate' => $fenceEnter['time_entered'],
                    'FenceCode' => $fenceEnter['geofence_name'],
                    'EnterDateTimeArea' => $fenceEnter['time_entered'],
                    'OutDateTimeArea' => '',
                    'ACC' => $fenceEnter['acc']
                ];

                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => "Bearer $token"
                ])->post('https://astraapigc.astra.co.id/tmsunit/api/GPS/Fence', $dataToSend);
                

                // Debug respons JSON
                $responseData = json_decode($response->body(), true);

                if ($response->successful() && isset($responseData['Acknowledge']) && $responseData['Acknowledge'] === 1) {
                    $this->info('Nopol ' . $fenceEnter['no_pol'] . ' sent successfully');
                    Log::channel('hso')->info('Geofence enter nopol ' . $fenceEnter['no_pol'] . ' - ' . $responseData['Message']);
                    
                    // Update status_kirim menjadi 1 (sukses kirim data)
                    GeofenceSession::where('id', $fenceEnter['id'])
                        ->update([
                            'status_kirim' => 1,
                            'note' => $responseData['Message']
                        ]);
                } else {
                    $errorMessage = $responseData['Message'];
                    $this->error('Nopol ' . $fenceEnter['no_pol'] . ' failed to send data: ' . $errorMessage);
                    Log::channel('hso')->error('Geofence enter nopol ' . $fenceEnter['no_pol'] . ' failed to send data: ' . $errorMessage);

                    Log::channel('hso')->error('Response server: ' . $response->body());
                    Log::channel('hso')->error('Request failed with status: ' . $response->status());

                    // Update status_kirim menjadi 3 (gagal kirim)
                    GeofenceSession::where('id', $fenceEnter['id'])
                        ->update([
                            'status_kirim' => 3,
                            'note' => $errorMessage
                        ]);
                }
            }
        } catch (\Exception $e) {
            $this->error('Error in send Data Geofence Enter: ' . $e->getMessage());
            Log::channel('hso')->error('Error in send Data Geofence Enter: ' . $e->getMessage());            
        }
    }

    private function sendDataGeofenceExit($token)
    {
        try {
            $fenceEnters = GeofenceSession::where('customer_id', 3)
                ->where('status_geofence', 2)
                ->where('status_kirim', 1)
                ->get();
                //var_dump($fenceEnters); exit;
            
            foreach ($fenceEnters as $fenceEnter) {
                $dataToSend = [
                    'VendorCodeGPS' => 'MTRACK',
                    'VehicleNo' => $fenceEnter['no_pol'],
                    'TrackingDate' => $fenceEnter['time_entered'],
                    'FenceCode' => $fenceEnter['geofence_name'],
                    'EnterDateTimeArea' => $fenceEnter['time_entered'],
                    'OutDateTimeArea' => $fenceEnter['time_exited'],
                    'ACC' => $fenceEnter['acc']
                ];

                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => "Bearer $token"
                ])->post('https://astraapigc.astra.co.id/tmsunit/api/GPS/Fence', $dataToSend);
                

                // Debug respons JSON
                $responseData = json_decode($response->body(), true);

                if ($response->successful() && isset($responseData['Acknowledge']) && $responseData['Acknowledge'] === 1) {
                    $this->info('Nopol ' . $fenceEnter['no_pol'] . ' sent successfully');
                    Log::channel('hso')->info('Geofence exit nopol ' . $fenceEnter['no_pol'] . ' - ' . $responseData['Message']);
                    
                    // Update status_kirim menjadi 1 (sukses kirim data)
                    GeofenceSession::where('id', $fenceEnter['id'])
                        ->update([
                            'status_kirim' => 2,
                            'note' => $responseData['Message']
                        ]);
                } else {
                    $errorMessage = $responseData['Message'];
                    $this->error('Nopol ' . $fenceEnter['no_pol'] . ' failed to send data: ' . $errorMessage);
                    Log::channel('hso')->error('Geofence enter nopol ' . $fenceEnter['no_pol'] . ' failed to send data: ' . $errorMessage);

                    Log::channel('hso')->error('Response server: ' . $response->body());
                    Log::channel('hso')->error('Request failed with status: ' . $response->status());

                    // Update status_kirim menjadi 3 (gagal kirim)
                    GeofenceSession::where('id', $fenceEnter['id'])
                        ->update([
                            'status_kirim' => 3,
                            'note' => $errorMessage
                        ]);
                }
            }
        } catch (\Exception $e) {
            $this->error('Error in send Data Geofence Exit: ' . $e->getMessage());
            Log::channel('hso')->error('Error in send Data Geofence Exit: ' . $e->getMessage());            
        }
    }

    private function sendDataParking($token)
    {
        try {
            $parkings = Parking::where('customer_id', 3)
                ->where('status', 0)
                ->get();
                //var_dump($fenceEnters); exit;
            
            foreach ($parkings as $parking) {
                $dataToSend = [
                    "VendorCodeGPS" => "MTRACK",
                    "VehicleNo" => str_replace(' ', '', $parking['no_pol']),
                    "TrackingDate" => $parking['off'],
                    "ParkingCoordinate" => $parking['latitude'] . "," . $parking['longitude'],
                    "ParkInDateTime" => $parking['off'],
                    "ParkOutDateTime" => $parking['on'],
                    "ParkingAddress" => $parking['address'],
                    "ACC" => $parking['acc'],
                ];
//var_dump($dataToSend); exit;
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => "Bearer $token"
                ])->post('https://astraapigc.astra.co.id/tmsunit/api/GPS/Parking', $dataToSend);
                

                // Debug respons JSON
                $responseData = json_decode($response->body(), true);

                if ($response->successful() && isset($responseData['Acknowledge']) && $responseData['Acknowledge'] === 1) {
                    $this->info('Nopol ' . $parking['no_pol'] . ' sent successfully');
                    Log::channel('hso')->info('Parking nopol ' . $parking['no_pol'] . ' - ' . $responseData['Message']);
                    
                    // Update status_kirim menjadi 1 (sukses kirim data)
                    Parking::where('id', $parking['id'])
                        ->update([
                            'status' => 1,
                            'info' => $responseData['Message']
                        ]);
                } else {
                    $errorMessage = $responseData['Message'];
                    $this->error('Nopol ' . $parking['no_pol'] . ' failed to send data: ' . $errorMessage);
                    Log::channel('hso')->error('Parking nopol ' . $parking['no_pol'] . ' failed to send data: ' . $errorMessage);

                    Log::channel('hso')->error('Response server: ' . $response->body());
                    Log::channel('hso')->error('Request failed with status: ' . $response->status());

                    // Update status_kirim menjadi 2 (gagal kirim)
                    Parking::where('id', $parking['id'])
                        ->update([
                            'status' => 2,
                            'info' => $errorMessage
                        ]);
                }
            }
        } catch (\Exception $e) {
            $this->error('Error in send Data Parking : ' . $e->getMessage());
            Log::channel('hso')->error('Error in send Data Parking : ' . $e->getMessage());            
        }
    }
}
