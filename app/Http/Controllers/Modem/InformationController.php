<?php

namespace App\Http\Controllers\Modem;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Information;
use App\Events\NewAlarm;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
// use Symfony\Component\HttpFoundation\StreamedResponse;

class InformationController extends Controller
{
    public function index()
    {        
        return view('pages.information.index');
    }
    // public function store(Request $request)
    // {
    //     $data = $request->all();
    //     $data['time'] = Carbon::now(); // Tambahkan timestamp ke field time

    //     // Ambil data yang sudah tersimpan sementara
    //     $gpsData = Cache::get('gps_data', []);

    //     // Tambahkan data baru ke array
    //     $gpsData[] = $data;

    //     // Jika jumlah data sudah 50, lakukan insert sekaligus
    //     if (count($gpsData) >= 50) {
    //         Information::insert($gpsData);
    //         Cache::forget('gps_data'); // Kosongkan cache setelah insert

    //         //Log::info('Batch insert berhasil', ['count' => count($gpsData), 'data' => $gpsData]);
    //     } else {
    //         Cache::put('gps_data', $gpsData, now()->addSeconds(10)); // Simpan sementara

    //         //Log::info('Data ditambahkan ke cache', ['count' => count($gpsData)]);
    //     }

    //     return response()->json(['message' => 'Data diterima'], 200);
    // }

    public function store(Request $request)
    {
        $data = $request->all();
        $data['time'] = Carbon::now(); // Tambahkan timestamp ke field time

        // Langsung insert ke database
        Information::create($data); // pastikan model pakai fillable

        return response()->json(['message' => 'Data berhasil disimpan'], 200);
    }

    // public function store(Request $request)
    // {
    //     $data = $request->all();
    //     $data['time'] = Carbon::now();

    //     $gpsData = Cache::get('gps_data', []);
    //     $firstTime = Cache::get('gps_first_time');

    //     // Kalau pertama kali data masuk, simpan timestamp awal
    //     if (!$firstTime) {
    //         $firstTime = now();
    //         Cache::put('gps_first_time', $firstTime, now()->addSeconds(40));
    //     }

    //     $gpsData[] = $data;

    //     $shouldInsert = false;

    //     // Kalau sudah 50 data atau lebih
    //     if (count($gpsData) >= 50) {
    //         $shouldInsert = true;
    //     }

    //     // Kalau sudah lebih dari 10 detik sejak data pertama masuk
    //     if (Carbon::parse($firstTime)->diffInSeconds(now()) >= 30) {
    //         $shouldInsert = true;
    //     }

    //     if ($shouldInsert) {
    //         Information::insert($gpsData);
    //         Cache::forget('gps_data');
    //         Cache::forget('gps_first_time');
    //         //Log::info('Data diinsert karena batas waktu atau jumlah');
    //     } else {
    //         // Simpan lagi ke cache
    //         Cache::put('gps_data', $gpsData, now()->addSeconds(40));
    //     }

    //     return response()->json(['message' => 'Data diterima'], 200);
    // }


    // public function stream()
    // {
    //     $response = new StreamedResponse(function() {
    //         $lastId = 0;

    //         while (true) {
    //             $newData = Information::where('id', '>', $lastId)->get();

    //             foreach ($newData as $data) {
    //                 $lastId = $data->id;
    //                 echo "data: " . json_encode($data) . "\n\n";
    //                 ob_flush();
    //                 flush();
    //             }

    //             sleep(60); // Check for new data every 1 second
    //         }
    //     });

    //     $response->headers->set('Content-Type', 'text/event-stream');
    //     $response->headers->set('Cache-Control', 'no-cache');
    //     $response->headers->set('Connection', 'keep-alive');

    //     return $response;
    // }
}
