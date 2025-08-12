<?php

namespace App\Console\Commands;

use App\Models\Vehicle;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\History;
use App\Models\Parking;

class CheckParkingHso extends Command
{
    protected $signature = 'parkingHSO:check';
    protected $description = 'Check if vehicles are parking and update to parking table';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $vehicles = Vehicle::where('customer_id', 3)
                        ->where('status', 1)
                        ->get();

        // $data = [];

        foreach ($vehicles as $vehicle) {
            // ambil data history kendaraan perhari ini
            $histories = History::where('vehicle_id', $vehicle->id)
                        ->whereDate('time', today())
                        ->orderBy('time')
                        ->get();

            //dd('data history: ', $histories); exit;

            $startParkirTime = null;

            foreach ($histories as $history) {
                // ambil waktu berjalan sekarang
                $currentTime = Carbon::parse($history->time);

                // jika status mesin mati dan belum ada start parkir (belum parkir), maka ambil waktu sekarang sebagai mulai parkir
                if ($history->ignition_status == 'Off') {
                        if (!$startParkirTime) {
                            $startParkirTime = $currentTime;
                        }
                } elseif ($history->ignition_status == 'On' && $startParkirTime) {
                    $durasiParkirSeconds = $currentTime->diffInSeconds($startParkirTime);
                    if ($durasiParkirSeconds >= 300) {
                        $dataEntry = [
                            'histories_id' => $history->id,
                            'customer_id' => $history->customer_id,
                            'vehicle_id' => $history->vehicle_id,
                            'no_pol' => $history->no_pol,
                            'off' => $startParkirTime->format('Y-m-d H:i:s'),
                            'on' => $currentTime->format('Y-m-d H:i:s'),
                            'duration' => $this->formatDurasi($durasiParkirSeconds),
                            'latitude' => $history->latitude,
                            'longitude' => $history->longitude,
                            'address' => $history->address,
                            'acc' => $history->ignition_status,
                            'status' => 0,
                        ];

                        // Cek apakah histories_id sudah ada
                        if (!Parking::where('histories_id', $history->id)->exists()) {
                            // Simpan ke model HsoParking jika belum ada
                            Parking::create($dataEntry);
                        }
                    }
                    
                    // Reset startParkirTime
                    $startParkirTime = null;
                }
            }
        }
        
    }

    function formatDurasi($seconds)
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = $seconds % 60;

        $durasi = [];

        if ($hours > 0) {
            $durasi[] = "{$hours} jam";
        }

        if ($minutes > 0) {
            $durasi[] = "{$minutes} menit";
        }

        if ($seconds > 0 || empty($durasi)) { 
            $durasi[] = "{$seconds} detik";
        }

        return implode(' ', $durasi);
    }
    
}
