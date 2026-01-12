<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Traccar;
use App\Models\Information;
use App\Models\Transmission;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // $customer_id = auth()->user()->customer_id;

        // Ambil tanggal saat ini
        $currentDate = Carbon::now();

        // Ambil bulan dan tahun saat ini
        $currentMonth = $currentDate->month;
        $currentYear = $currentDate->year;

            // Hitung jumlah kendaraan dengan status 'offline'
            $offlineCount = Traccar::where('status', 'mati')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('active', 1)
            ->count();

            $overKemiringanCount = Traccar::where('roll', '>', 2.3)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('active', 1)
            ->count();

            // Hitung jumlah kendaraan dengan status 'online'
            $onlineCount = Traccar::where('status', 'bergerak')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('active', 1)
            ->count();

            // Hitung jumlah kendaraan dengan status 'engine'
            $berhentiCount = Traccar::where('status', 'diam')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('active', 1)
            ->count();

            // Hitung jumlah kendaraan dengan status 'ack'
            $diamCount = Traccar::where('status', 'berhenti')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('active', 1)
            ->count();

            // Hitung total kendaraan dengan koordinat yang tersedia
            $totalVehicles = Traccar::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('active', 1)
            ->count();

            $totalMobil = Traccar::join('vehicles', 'traccars.vehicle_id', '=', 'vehicles.id')
            ->whereNotNull('traccars.latitude')
            ->whereNotNull('traccars.longitude')
            ->where('vehicles.vehicle_type', 0)
            ->count();
            $totalMobilTidakAktif = Traccar::join('vehicles', 'traccars.vehicle_id', '=', 'vehicles.id')
            ->whereNotNull('traccars.latitude')
            ->whereNotNull('traccars.longitude')
            ->where('traccars.active', 0)
            ->where('vehicles.vehicle_type', 0)
            ->count();
            $totalMobilAktif = Traccar::join('vehicles', 'traccars.vehicle_id', '=', 'vehicles.id')
            ->whereNotNull('traccars.latitude')
            ->whereNotNull('traccars.longitude')
            ->where('traccars.active', 1)
            ->where('vehicles.vehicle_type', 0)
            ->count();

            $totalMotor = Traccar::join('vehicles', 'traccars.vehicle_id', '=', 'vehicles.id')
            ->whereNotNull('traccars.latitude')
            ->whereNotNull('traccars.longitude')
            ->where('vehicles.vehicle_type', 1)
            ->count();
            $totalMotorAktif = Traccar::join('vehicles', 'traccars.vehicle_id', '=', 'vehicles.id')
            ->whereNotNull('traccars.latitude')
            ->whereNotNull('traccars.longitude')
            ->where('traccars.active', 1)
            ->where('vehicles.vehicle_type', 1)
            ->count();
            $totalMotorTidakAktif = Traccar::join('vehicles', 'traccars.vehicle_id', '=', 'vehicles.id')
            ->whereNotNull('traccars.latitude')
            ->whereNotNull('traccars.longitude')
            ->where('traccars.active', 0)
            ->where('vehicles.vehicle_type', 1)
            ->count();


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

            $now = Carbon::now();
            // Format durasi offline terlama
            //$formattedDuration = $longestOfflineDuration->locale('id')->diffForHumans($now);
            $formattedDuration = $longestOfflineDuration->locale('id')->diffForHumans();


            // ========================== ini untuk datatable dashboard =====================
            // Ambil data dari model Traccar
            $items = Traccar::where('roll', '>', 3)
                ->where('active', 1)
                ->get();

            // Proses data untuk menghitung waktu yang telah berlalu
            $items->transform(function($item) {
                $time = Carbon::parse($item->time); // Waktu dari database
                $now = Carbon::now(); // Waktu saat ini
                //$item->time_diff = $time->locale('id')->diffForHumans($now); // Selisih waktu
                $item->time_diff = $time->locale('id')->diffForHumans(); // Selisih waktu
                return $item;
            });
            
            // cari apakah customer memiliki mobil
            $punyaMobil = Vehicle::where('vehicle_type', '0')
                // ->where('customer_id', $customer_id)
                ->exists();

            // cari apakah customer memiliki mobil
            $punyaMotor = Vehicle::where('vehicle_type', '1')
                // ->where('customer_id', $customer_id)
                ->exists();
            
                                    
        return view('pages.dashboard.index')->with([
            'overKemiringanCount' => $overKemiringanCount,
            'offlineCount' => $offlineCount,
            'onlineCount' => $onlineCount,
            'berhentiCount' => $berhentiCount,
            'diamCount' => $diamCount,
            'totalVehicles' => $totalVehicles,
            'totalMobil' => $totalMobil,
            'totalMobilAktif' => $totalMobilAktif,
            'totalMobilTidakAktif' => $totalMobilTidakAktif,
            'totalMotor' => $totalMotor,
            'totalMotorAktif' => $totalMotorAktif,
            'totalMotorTidakAktif' => $totalMotorTidakAktif,
            'longestOfflineDuration' => $formattedDuration,
            'vehicleWithLongestOffline' => $vehicleWithLongestOffline,
            'items' => $items,
            'punyaMobil' => $punyaMobil,
            'punyaMotor' => $punyaMotor,
        ]);
    }


    public function listTraccar($status = null)
    {
        // Pastikan $status merupakan nilai yang valid (engine, ack, offline, online) jika tidak kosong
        $validStatusValues = ['mati', 'bergerak', 'diam', 'berhenti'];
        if ($status !== null && !in_array($status, $validStatusValues)) {
            // Handle jika status tidak valid, misalnya redirect atau response lainnya
            abort(404, 'Invalid status value');
        }

        // Jika status tidak diberikan, maka ambil semua data tanpa memfilter berdasarkan status
        $query = Traccar::whereNotNull('latitude')->whereNotNull('longitude');

        if ($status !== null) {
            $query->where('status', $status);
        }

        $items = $query->get();

        return view('pages.dashboard.list_traccar')->with([
            'items' => $items
        ]);
    }

    public function listDump($status = null)
    {
        // Pastikan $status merupakan nilai yang valid (engine, ack, offline, online) jika tidak kosong
        $validStatusValues = [0, 1];
        if ($status !== null && !in_array($status, $validStatusValues)) {
            // Handle jika status tidak valid, misalnya redirect atau response lainnya
            abort(404, 'Invalid status value');
        }

        // Jika status tidak diberikan, maka ambil semua data tanpa memfilter berdasarkan status
        $query = Transmission::where('transmissions.information_type', 5)
            ->where('transmissions.customer_id', '=', 4)
            ->whereIn('transmissions.device_id', [514, 515, 516, 517, 518])
            ->leftJoin('traccars', 'transmissions.device_id', '=', 'traccars.device_id')
            ->select('transmissions.no_pol', 'transmissions.information_type', 'transmissions.door', 
                'traccars.latitude', 'traccars.longitude', 'transmissions.updated_at as time', 'traccars.address', 'traccars.status'
            );

            

        if ($status !== null) {
            $query->where('door', $status);
        }

        $items = $query->get();

        return view('pages.dashboard.list_dump')->with([
            'items' => $items
        ]);
    }  

}
