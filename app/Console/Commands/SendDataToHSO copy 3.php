<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use App\Models\Traccar;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
            } else {
                $this->info('Access token not found in response.');
                Log::channel('hso')->error('Access token not found in response.');
            }
        } catch (RequestException $e) {
            $this->error('Request failed: ' . $e->getMessage());
        }

        $positions = Traccar::where('customer_id', 3)->get();

        // Send data to endpoint
        foreach ($positions as $position) {
            $dataToSend = [
                'VendorCodeGPS' => 'MTRACK',
                'VehicleNo' => $position['no_pol'],
                'TrackingDate' => $position['time'],
                'Coordinate' => $position['latitude'] . "," . $position['longitude'],
                'Address' => $position['address'],
                'Speed' => (int) round($position['speed'])
            ];
        
            // Debug data sebelum dikirim
            var_dump($dataToSend);
        
            // $response = Http::withHeaders([
            //     'Content-Type' => 'application/x-www-form-urlencoded',
            //     'Authorization' => "Bearer $token"
            // ])->post('https://astraapigc-dev.astra.co.id/tmsunit-dev/api/GPS/Tracking', $dataToSend);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => "Bearer $token"
            //])->post('https://astraapigc-dev.astra.co.id/tmsunit-dev/api/GPS/Tracking', $dataToSend);
            ])->post('https://astraapigc.astra.co.id/tmsunit/api/GPS/Tracking', $dataToSend);
            
        
            // Debug respons JSON
            $responseData = json_decode($response->body(), true);
            var_dump($responseData); // Untuk melihat seluruh respons
        
            if ($response->successful() && isset($responseData['Acknowledge']) && $responseData['Acknowledge'] === 1) {
                $this->info('Nopol ' . $position['no_pol'] . ' sent successfully');
                Log::channel('hso')->info('Last position nopol ' . $position['no_pol'] . ' sent successfully'); // Mencatat ke hso.log
            } else {
                $errorMessage = $responseData['Message'] ?? 'Unknown error';
                $this->error('Nopol ' . $position['no_pol'] . ' failed to send data: ' . $errorMessage);
                Log::channel('hso')->error('Last position nopol ' . $position['no_pol'] . ' failed to send data: ' . $errorMessage); // Mencatat error ke hso.log
            }
            
        }     

    }
}
