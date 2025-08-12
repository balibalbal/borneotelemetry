<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class PostDataToGPS extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:data-to-gps';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync data from traccars to eventdata in gps server';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Mengambil data dari tabel traccars
        $customerIds = [3, 8]; 
        $traccarsData = DB::table('traccars')->whereIn('customer_id', $customerIds)->get();

        foreach ($traccarsData as $data) {
            // Tentukan accountid berdasarkan customer_id
            // 3 = PT. Marga Jaya, 8 = PT. GTR
            $accountId = ($data->customer_id == 3) ? 160 : ($data->customer_id == 8 ? 148 : null);

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
                'inputmask' => '-99',
                'distancekm' => $data->total_distance,
                'odometerKM' => 0,
                'trv_ID' => 0,
                'gpsAge' => 0,
                'geozoneID' => 0,
                'geozoneIndex' => 0,
                'address' => $data->address,
            ];

            // Insert data ke database lain
            try {

                $exists = DB::connection('database_gps')->table('eventdata')
                    ->where('deviceid', $data->device_id)
                    ->where('timestamp', $data->time)
                    ->exists();

                if (!$exists) {
                    $inserted = DB::connection('database_gps')->table('eventdata')->insert($insertData);

                    if ($inserted) {
                        Log::channel('gpslogin')->info("Berhasil insert data untuk device id: {$data->device_id} ke tabel eventdata server GPS");
                    } else {
                        Log::channel('gpslogin')->warning("Gagal insert data untuk device id: {$data->device_id} ke tabel eventdata server GPS");
                    }
                } else {
                    Log::channel('gpslogin')->info("Data sudah ada untuk device id: {$data->device_id}, timestamp: {$data->time}, insert dibatalkan.");
                }

                // Melakukan update
                $updateLastEventData = DB::connection('database_gps')->update("
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
                    Log::channel('gpslogin')->info("Update berhasil untuk device id: {$data->device_id}, {$updateLastEventData} row(s) terupdate ke server GPS.");
                } else {
                    Log::channel('gpslogin')->warning("No rows updated for device id: {$data->device_id}");
                }
            } catch (\Exception $e) {
                $this->error("Failed to sent data for device id: {$data->device_id}. Error: {$e->getMessage()}");
                Log::channel('gpslogin')->error("Failed to sent data for device id {$data->device_id}: {$e->getMessage()} to server GPS");
            }

            // try {
            //     DB::connection('database_gps')->table('eventdata')->insert($insertData);

            //     // Melakukan update
            //     DB::connection('database_gps')->update("
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

            // } catch (\Exception $e) {
            //     $this->error("Failed to sync data: {$data->id}. Error: {$e->getMessage()}");
            // }
        }

    }
}
