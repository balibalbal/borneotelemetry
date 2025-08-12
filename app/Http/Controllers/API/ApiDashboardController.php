<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Traccar;
use App\Models\Customer;
use App\Models\Information;
use App\Models\Vehicle;

class ApiDashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $customer_id = $request->query('customer_id');

        // Validasi apakah customer_id di input di endpoint
        if (is_null($customer_id)) {
            return response()->json([
                'status' => false,
                'message' => 'customer_id kosong, silahkan isi dulu',
                'code' => 400
            ], 400);
        }

        // Cek apakah customer_id ada di tabel customers
        $customerExists = Customer::where('id', $customer_id)->exists();
        
        if (!$customerExists) {
            return response()->json([
                'status' => false,
                'message' => 'customer_id tidak ditemukan',
                'code' => 404
            ], 404);
        }

        // Ambil tanggal saat ini
        $currentDate = Carbon::now();

        // Ambil bulan dan tahun saat ini
        $currentMonth = $currentDate->month;
        $currentYear = $currentDate->year;

        if ($customer_id == 1) {
            // Hitung jumlah kendaraan dengan status 'offline'
            $offlineCount = Traccar::where('status', 'mati')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->count();

            // Hitung jumlah kendaraan dengan status 'online'
            $onlineCount = Traccar::where('status', 'bergerak')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->count();

            // Hitung jumlah kendaraan dengan status 'engine'
            $berhentiCount = Traccar::where('status', 'diam')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->count();

            // Hitung jumlah kendaraan dengan status 'ack'
            $diamCount = Traccar::where('status', 'berhenti')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->count();

            // Hitung total kendaraan dengan koordinat yang tersedia
            $totalVehicles = Traccar::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->count();

            // Ambil kendaraan dengan kecepatan tertinggi untuk customer tertentu
            $topSpeedVehicle = Traccar::whereNotNull('speed')
            ->orderBy('speed', 'desc') // Urutkan berdasarkan kecepatan tertinggi
            ->first(['speed', 'no_pol']); // Ambil kolom speed dan nopol

            if ($topSpeedVehicle) {
                $topSpeed = $topSpeedVehicle->speed;
                $nopol = $topSpeedVehicle->no_pol;
            } else {
                $topSpeed = null;
                $nopol = null;
            }

            // Ambil kendaraan dengan jarak terjauh untuk customer tertentu
            $topDistanceVehicle = Traccar::whereNotNull('total_distance')
            ->orderBy('total_distance', 'desc')
            ->first(['total_distance', 'no_pol']);

            if ($topDistanceVehicle) {
                $topDistance = $topDistanceVehicle->total_distance;
                $nopolDistance = $topDistanceVehicle->no_pol;
            } else {
                $topDistance = null;
                $nopolDistance = null;
            }

            // Ambil kendaraan dengan jarak terjauh untuk customer tertentu
            $topDistanceVehicle = Traccar::whereNotNull('total_distance')
            ->orderBy('total_distance', 'desc')
            ->first(['total_distance', 'no_pol']);

            if ($topDistanceVehicle) {
                //$topDistance = $topDistanceVehicle->total_distance;
                $topDistance = number_format($topDistanceVehicle->total_distance, 2, ',', '.');
                $nopolDistance = $topDistanceVehicle->no_pol;
            } else {
                $topDistance = null;
                $nopolDistance = null;
            }

            // Ambil data kendaraan dengan status offline yang memiliki waktu
            $offlineData = Traccar::where('active', 1)
            ->whereNotNull('time')
            ->get(['time', 'no_pol']);

            // Inisialisasi variabel untuk menyimpan durasi offline terlama
            $longestOfflineDuration = Carbon::now(); // Mulai dengan waktu saat ini
            $vehicleWithLongestOffline = null;

            // Iterasi melalui data offline untuk menghitung durasi offline
            foreach ($offlineData as $data) {
                $offlineTime = Carbon::parse($data->time);

                // Periksa apakah waktu offline ini lebih lama dari yang sebelumnya
                if ($offlineTime < $longestOfflineDuration) {
                    $longestOfflineDuration = $offlineTime;
                    $vehicleWithLongestOffline = $data->no_pol;
                }
            }

            // Format durasi offline terlama
            $formattedDuration = $longestOfflineDuration->diffForHumans();

            // ==================== ini untuk ambil alarm dan di munculkan di dahsboard ====================
            // Ambil data alarm dengan status sos yang memiliki waktu
            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;

            // Query untuk mendapatkan data alarm terbaru dari bulan ini
            $latestAlarmData = Information::where('alarmType', 'sos')
                        ->whereMonth('updated_at', $currentMonth) // Memfilter berdasarkan bulan
                        ->whereYear('updated_at', $currentYear)
                        ->orderBy('updated_at', 'desc') // Urutkan berdasarkan updated_at menurun
                        ->first(['no_pol', 'updated_at', 'alarmType']); 

            if ($latestAlarmData) {
                // Proses data untuk menghitung waktu yang telah berlalu
                $time = Carbon::parse($latestAlarmData->updated_at); // Waktu dari database
                $now = Carbon::now(); // Waktu saat ini
                $time_diff = $time->locale('id')->diffForHumans($now); // Selisih waktu

                $nopol_alarm = $latestAlarmData->no_pol;
                $tgl_alarm = $time_diff;
                $tipe_alarm = $latestAlarmData->alarmType;
            } else {
                $nopol_alarm = '-';
                $tgl_alarm = '';
                $tipe_alarm = 'tidak ada data';
            }

            $customer = Customer::where('id', $customer_id)->first(['name', 'address', 'phone']);
            
            // cari apakah customer memiliki mobil
            $punyaMobil = Vehicle::where('vehicle_type', '0')
                // ->where('customer_id', $customer_id)
                ->exists();

            // cari apakah customer memiliki mobil
            $punyaMotor = Vehicle::where('vehicle_type', '1')
                // ->where('customer_id', $customer_id)
                ->exists();

        } else {
            // Hitung jumlah kendaraan dengan status 'offline'
            $offlineCount = Traccar::where('status', 'mati')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('customer_id', $customer_id)
            ->count();

            // Hitung jumlah kendaraan dengan status 'online'
            $onlineCount = Traccar::where('status', 'bergerak')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('customer_id', $customer_id)
            ->count();

            // Hitung jumlah kendaraan dengan status 'engine'
            $berhentiCount = Traccar::where('status', 'diam')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('customer_id', $customer_id)
            ->count();

            // Hitung jumlah kendaraan dengan status 'ack'
            $diamCount = Traccar::where('status', 'berhenti')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('customer_id', $customer_id)
            ->count();

            // Hitung total kendaraan dengan koordinat yang tersedia
            $totalVehicles = Traccar::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('customer_id', $customer_id)
            ->count();

            // Ambil kendaraan dengan kecepatan tertinggi untuk customer tertentu
            $topSpeedVehicle = Traccar::where('customer_id', $customer_id)
            ->whereNotNull('speed') // Pastikan kecepatan tidak null
            ->orderBy('speed', 'desc') // Urutkan berdasarkan kecepatan tertinggi
            ->first(['speed', 'no_pol']); // Ambil kolom speed dan nopol

            if ($topSpeedVehicle) {
                $topSpeed = $topSpeedVehicle->speed;
                $nopol = $topSpeedVehicle->no_pol;
            } else {
                $topSpeed = null;
                $nopol = null;
            }

            // Ambil kendaraan dengan jarak terjauh untuk customer tertentu
            $topDistanceVehicle = Traccar::where('customer_id', $customer_id)
            ->whereNotNull('total_distance')
            ->orderBy('total_distance', 'desc')
            ->first(['total_distance', 'no_pol']);

            if ($topDistanceVehicle) {
                //$topDistance = $topDistanceVehicle->total_distance;
                $topDistance = number_format($topDistanceVehicle->total_distance, 2, ',', '.');
                $nopolDistance = $topDistanceVehicle->no_pol;
            } else {
                $topDistance = null;
                $nopolDistance = null;
            }

            // Ambil data kendaraan dengan status offline yang memiliki waktu
            $offlineData = Traccar::where('customer_id', $customer_id)
            ->where('active', 1)
            ->whereNotNull('time')
            ->get(['time', 'no_pol']);

            // Inisialisasi variabel untuk menyimpan durasi offline terlama
            $longestOfflineDuration = Carbon::now(); // Mulai dengan waktu saat ini
            $vehicleWithLongestOffline = null;

            // Iterasi melalui data offline untuk menghitung durasi offline
            foreach ($offlineData as $data) {
                $offlineTime = Carbon::parse($data->time);

                // Periksa apakah waktu offline ini lebih lama dari yang sebelumnya
                if ($offlineTime < $longestOfflineDuration) {
                    $longestOfflineDuration = $offlineTime;
                    $vehicleWithLongestOffline = $data->no_pol;
                }
            }

            // Format durasi offline terlama
            $formattedDuration = $longestOfflineDuration->locale('id')->diffForHumans();
            
            // ==================== ini untuk ambil alarm dan di munculkan di dahsboard ====================
            // Ambil data alarm dengan status sos yang memiliki waktu
            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;

            // Query untuk mendapatkan data alarm terbaru dari bulan ini
            $latestAlarmData = Information::where('customer_id', $customer_id)
                        // ->where('alarmType', 'sos')
                        ->whereMonth('updated_at', $currentMonth) // Memfilter berdasarkan bulan
                        ->whereYear('updated_at', $currentYear)
                        ->orderBy('updated_at', 'desc') // Urutkan berdasarkan updated_at menurun
                        ->first(['no_pol', 'updated_at', 'alarmType']); 

            if ($latestAlarmData) {
                // Proses data untuk menghitung waktu yang telah berlalu
                $time = Carbon::parse($latestAlarmData->updated_at); // Waktu dari database
                $now = Carbon::now(); // Waktu saat ini
                $time_diff = $time->locale('id')->diffForHumans($now); // Selisih waktu
                $time_diff = $time->locale('id')->diffForHumans(); // Selisih waktu

                $nopol_alarm = $latestAlarmData->no_pol;
                $tgl_alarm = $time_diff;
                $tipe_alarm = $latestAlarmData->alarmType;
            } else {
                $nopol_alarm = '-';
                $tgl_alarm = '';
                $tipe_alarm = 'tidak ada data';
            }

            $customer = Customer::where('id', $customer_id)->first(['name', 'address', 'phone']);

            // cari apakah customer memiliki mobil
            $punyaMobil = Vehicle::where('vehicle_type', '0')
                ->where('customer_id', $customer_id)
                ->exists();

            // cari apakah customer memiliki mobil
            $punyaMotor = Vehicle::where('vehicle_type', '1')
                ->where('customer_id', $customer_id)
                ->exists();
        }    
        
                        
        // Mengembalikan data jika ada
        return response()->json([
            'status' => true,
            'message' => 'Data ditemukan',
            'data' => [
                'offlineCount' => $offlineCount,
                'onlineCount' => $onlineCount,
                'berhentiCount' => $berhentiCount,
                'diamCount' => $diamCount,
                'totalVehicles' => $totalVehicles,
                'topSpeed' => $topSpeed,
                'nopol' => $nopol,
                'topDistance' => $topDistance,
                'nopolDistance' => $nopolDistance,
                'longestOfflineDuration' => $formattedDuration,
                'vehicleWithLongestOffline' => $vehicleWithLongestOffline,
                'nopol_alarm' => $nopol_alarm,
                'tgl_alarm' => $tgl_alarm,
                'tipe_alarm' => $tipe_alarm,
                'customerName'  => $customer->name ?? 'tidak ada',
                'customerAddress' => $customer->address ?? 'tidak ada',
                'customerPhone' => $customer->phone ?? 'tidak ada',
                'punyaMobil' => $punyaMobil,
                'punyaMotor' => $punyaMotor,
            ],
            'code' => 200
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Traccar  $traccar
     * @return \Illuminate\Http\Response
     */
    public function show(Traccar $traccar)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Traccar  $traccar
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Traccar $traccar)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Traccar  $traccar
     * @return \Illuminate\Http\Response
     */
    public function destroy(Traccar $traccar)
    {
        //
    }
}
