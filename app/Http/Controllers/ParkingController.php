<?php

namespace App\Http\Controllers;

use App\Models\History;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ParkingController extends Controller
{
    public function index(Request $request)
    {
        // Ambil parameter filter dari request
        $vehicle_id = $request->input('vehicle_id');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');

        // Default query untuk mendapatkan data parkir kendaraan
        $query = DB::table('histories as h')
            ->select('h.vehicle_id', 
                     DB::raw('MIN(h.time) as start_time'),
                     DB::raw('MAX(h.time) as end_time'),
                     DB::raw('ST_AsText(h.geom) as location'),
                     DB::raw('EXTRACT(EPOCH FROM (MAX(h.time) - MIN(h.time))) as duration_seconds'))
            ->where('h.ignition_status', 'Off')
            ->groupBy('h.vehicle_id', 'h.geom')
            ->havingRaw('EXTRACT(EPOCH FROM (MAX(h.time) - MIN(h.time))) > 0'); // Durasi parkir lebih dari 0 detik

        // Tambahkan filter jika ada parameter vehicle_id
        if ($vehicle_id) {
            $query->where('h.vehicle_id', $vehicle_id);
        }

        // Tambahkan filter berdasarkan tanggal
        if ($start_date) {
            $query->where('h.time', '>=', Carbon::parse($start_date)->startOfDay());
        }
        if ($end_date) {
            $query->where('h.time', '<=', Carbon::parse($end_date)->endOfDay());
        }

        // Ambil data parkir kendaraan
        $parkirData = $query->get();

        // Mengonversi durasi dalam detik menjadi format jam:menit:detik
        foreach ($parkirData as $data) {
            $duration = $data->duration_seconds;
            $hours = floor($duration / 3600);
            $minutes = floor(($duration % 3600) / 60);
            $seconds = $duration % 60;
            $data->formatted_duration = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        }

        // Analisa apakah kendaraan parkir di tempat yang sama dalam satu bulan
        $parkirAnalysis = $this->analyzeParking($parkirData);

        // Kirim data ke view
        return view('pages.parking.index', compact('parkirData', 'parkirAnalysis', 'vehicle_id', 'start_date', 'end_date'));
    }

    // Fungsi untuk menganalisis apakah kendaraan parkir di tempat yang sama selama satu bulan
    private function analyzeParking($parkirData)
    {
        $analysis = [];
        
        foreach ($parkirData as $data) {
            $vehicle_id = $data->vehicle_id;
            $location = $data->location;

            // Cek apakah kendaraan parkir di lokasi yang sama dalam satu bulan
            $sameLocation = DB::table('histories as h')
                ->where('h.vehicle_id', $vehicle_id)
                ->where('h.ignition_status', 'Off')
                ->whereRaw('ST_DWithin(h.geom, ST_SetSRID(ST_GeomFromText(?), 4326), 10)', [$location]) // Jarak 10 meter
                ->whereBetween('h.time', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
                ->count();

            if ($sameLocation > 0) {
                $analysis[] = [
                    'vehicle_id' => $vehicle_id,
                    'location' => $location,
                    'count' => $sameLocation,
                ];
            }
        }

        return $analysis;
    }
}
