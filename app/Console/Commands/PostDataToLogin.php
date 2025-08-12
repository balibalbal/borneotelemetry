<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class PostDataToLogin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:data-to-login';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync data from traccars to eventdata in login server';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Mengambil data dari tabel traccars
        $customerIds = [9,11]; 
        $traccarsData = DB::table('traccars')->whereIn('customer_id', $customerIds)->get();

        foreach ($traccarsData as $data) {
            // Tentukan accountid berdasarkan customer_id
            //$accountId = ($data->customer_id == 9 ? 104 : null);

            if ($data->customer_id == 9) {
                $accountId = 104;
            } elseif ($data->customer_id == 11) {
                $accountId = 150;
            } else {
                $accountId = null;
            }

            // Persiapkan data untuk di-insert
            $insertData = [
                'accountid' => $accountId,
                'deviceid' => $data->device_id,
                'timestamp' => $data->time,
                'statusCode' => ($data->ignition_status === 'On') ? 1 : ($data->ignition_status === 'Off' ? 0 : $data->ignition_status),
                'latitude' => $data->latitude,
                'longitude' => $data->longitude,
                'speedkph' => $data->speed,
                'heading' => $data->course,
                'inputmask' => ($data->ignition_status === 'On') ? 56 : ($data->ignition_status === 'Off' ? 55 : -99),
                'distancekm' => $data->total_distance,
                'odometerKM' => 0,
                'trv_ID' => 0,
                'gpsAge' => 0,
                'geozoneID' => 0,
                'geozoneIndex' => 0,
                'address' => $data->address,
            ];

            try {

                $exists = DB::connection('database_gps')->table('eventdata')
                    ->where('deviceid', $data->device_id)
                    ->where('timestamp', $data->time)
                    ->exists();

                if (!$exists) {
                    $inserted = DB::connection('database_login')->table('eventdata')->insert($insertData);

                    if ($inserted) {
                        Log::channel('gpslogin')->info("Berhasil insert data untuk device id: {$data->device_id} ke tabel eventdata server LOGIN");
                    } else {
                        Log::channel('gpslogin')->warning("Gagal insert data untuk device id: {$data->device_id} ke tabel eventdata server LOGIN");
                    }
                } else {
                    Log::channel('gpslogin')->info("Data sudah ada untuk device id: {$data->device_id}, timestamp: {$data->time}, insert dibatalkan ke server LOGIN.");
                }

                // Melakukan update
                $updateLastEventData = DB::connection('database_login')->update("
                    UPDATE lasteventdata 
                    JOIN (
                        SELECT timestamp, latitude, longitude, address, speedKPH, heading, inputMask, distanceKM, statusCode 
                        FROM eventdata 
                        WHERE deviceID = ? 
                        ORDER BY timestamp DESC 
                        LIMIT 1
                    ) AS eventdata 
                    SET 
                        lasteventdata.timestamp = eventdata.timestamp, 
                        lasteventdata.latitude = eventdata.latitude, 
                        lasteventdata.longitude = eventdata.longitude, 
                        lasteventdata.distanceKM = eventdata.distanceKM, 
                        lasteventdata.speedKPH = eventdata.speedKPH, 
                        lasteventdata.heading = eventdata.heading, 
                        lasteventdata.statusCode = eventdata.statusCode, 
                        lasteventdata.inputMask = eventdata.inputMask, 
                        lasteventdata.address = eventdata.address, 
                        lasteventdata.rawData = 'Mtrack Generasi Dua' 
                    WHERE 
                        lasteventdata.deviceID = ?
                ", [$data->device_id, $data->device_id]);
                
                // Cek apakah update berhasil
                if ($updateLastEventData > 0) {
                    Log::channel('gpslogin')->info("Update berhasil untuk device id: {$data->device_id}, {$updateLastEventData} row(s) terupdate ke server LOGIN.");
                } else {
                    Log::channel('gpslogin')->warning("Tidak ada baris yang di update untuk device id: {$data->device_id} di server LOGIN");
                }
            } catch (\Exception $e) {
                $this->error("Failed to sent data for device id: {$data->device_id}. Error: {$e->getMessage()}");
                Log::channel('gpslogin')->error("Failed to sent data for device id {$data->device_id}: {$e->getMessage()} to server LOGIN");
            }

            // try {
            //     // Insert data ke database lain
            //     $inserted = DB::connection('database_login')->table('eventdata')->insert($insertData);

            //     if ($inserted) {
            //         // Log jika insert berhasil
            //         Log::channel('gpslogin')->info("Berhasil insert data untuk device id: {$data->device_id} ke tabel eventdata server LOGIN");
            //     } else {
            //         // Log jika insert gagal
            //         Log::channel('gpslogin')->warning("Gagal insert data untuk device id: {$data->device_id} ke tabel eventdata server LOGIN");
            //     }

            //     // Melakukan update
            //     $updateLastEventData = DB::connection('database_login')->update("
            //         UPDATE lasteventdata 
            //         JOIN (
            //             SELECT timestamp, latitude, longitude, address, speedKPH, heading, distanceKM, statusCode 
            //             FROM eventdata 
            //             WHERE deviceID = ? 
            //             ORDER BY timestamp DESC 
            //             LIMIT 1
            //         ) AS eventdata 
            //         SET 
            //             lasteventdata.timestamp = eventdata.timestamp, 
            //             lasteventdata.latitude = eventdata.latitude, 
            //             lasteventdata.longitude = eventdata.longitude, 
            //             lasteventdata.distanceKM = eventdata.distanceKM, 
            //             lasteventdata.speedKPH = eventdata.speedKPH, 
            //             lasteventdata.heading = eventdata.heading, 
            //             lasteventdata.statusCode = eventdata.statusCode, 
            //             lasteventdata.address = eventdata.address, 
            //             lasteventdata.rawData = 'Mtrack Generasi Dua' 
            //         WHERE 
            //             lasteventdata.deviceID = ?
            //     ", [$data->device_id, $data->device_id]);

            //     // Cek apakah update berhasil
            //     if ($updateLastEventData > 0) {
            //         Log::channel('gpslogin')->info("Update berhasil untuk device_id: {$data->device_id}, {$updateLastEventData} row(s) terupdate ke server LOGIN.");
            //     } else {
            //         Log::channel('gpslogin')->warning("No rows updated for device id: {$data->device_id}");
            //     }

            // } catch (\Exception $e) {
            //     // Tangani exception dan log error
            //     $this->error("Failed to sent data for device id: {$data->device_id}. Error: {$e->getMessage()}");
            //     Log::channel('gpslogin')->error("Failed to sent data for device id {$data->device_id}: {$e->getMessage()} to server LOGIN");
            // }
        }
    }
}
