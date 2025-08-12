<?php

namespace App\Http\Controllers;

use App\Exports\ExportLastPositionAdmin;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportHistoricalAdmin;
use App\Exports\ExportKecepatan;
use App\Exports\ExportJarakAdmin;
use App\Exports\ExportParkir;
use App\Models\Traccar;
use App\Http\Requests\ReportRequest;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\History;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class GrafikController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $customer_id = auth()->user()->customer_id;

        // // Ambil data dari model Traccar
        // $vehicles = Vehicle::where('customer_id', $customer_id)->where('status', 1)->get();
        
        // // Kirim data yang sudah diproses ke view
        // return view('pages.report_admin.last_position', [
        //     //'items' => $items,
        //     'vehicles' => $vehicles
        // ]);
    }

    
    
    
    public function historicalReport() 
    {
        // $customers = Customer::where('status', 1)->get();
        
        // return view('pages.report_admin.historical_report')->with([
        //     'customers' => $customers
        // ]);
    }

    // public function getVehicles($customer_id)
    // {
    //     $vehicles = Vehicle::where('customer_id', $customer_id)->where('status', 1)->get();
    //     return response()->json($vehicles);
    // }


    public function grafikKecepatan() 
    {
        $customer_id = auth()->user()->customer_id;

        if ($customer_id == 1) {
            $vehicles = Vehicle::where('status', 1)->get();
        } else {
            $vehicles = Vehicle::where('customer_id', $customer_id)->where('status', 1)->get();
        }
        
        return view('pages.grafik.kecepatan')->with([
            'vehicles' => $vehicles
        ]);
    }

    public function grafikJarak() 
    {
        return view('pages.grafik.jarak');
    }

    // public function laporanParkir() 
    // {
    //     $customers = Customer::where('status', 1)->get();
        
    //     return view('pages.grafik.parkir')->with([
    //         'customers' => $customers
    //     ]);
    // }

    

    // function formatDurasi($seconds)
    // {
    //     $hours = floor($seconds / 3600);
    //     $minutes = floor(($seconds % 3600) / 60);
    //     $seconds = $seconds % 60;

    //     $durasi = [];

    //     if ($hours > 0) {
    //         $durasi[] = "{$hours} jam";
    //     }

    //     if ($minutes > 0) {
    //         $durasi[] = "{$minutes} menit";
    //     }

    //     if ($seconds > 0 || empty($durasi)) { // Menampilkan detik jika tidak ada jam dan menit
    //         $durasi[] = "{$seconds} detik";
    //     }

    //     return implode(' ', $durasi);
    // }

    public function grafikSpeed(Request $request)
    {
        $customerId = auth()->user()->customer_id; // Ganti sesuai kebutuhan
        
        if ($customerId == 1) {
            $noPol = $request->no_pol;
            $startDate = $request->start_date;
            $endDate = $request->end_date;

            // Ambil data dari model History berdasarkan filter yang diberikan
            $data = History::where('no_pol', $noPol)
                ->whereRaw('DATE(time) BETWEEN ? AND ?', [$startDate, $endDate])
                ->get(['time', 'speed']); // Ambil hanya kolom time dan speed
        } else {
            $noPol = $request->no_pol;
            $startDate = $request->start_date;
            $endDate = $request->end_date;

            // Ambil data dari model History berdasarkan filter yang diberikan
            $data = History::where('customer_id', $customerId)
                ->where('no_pol', $noPol)
                ->whereRaw('DATE(time) BETWEEN ? AND ?', [$startDate, $endDate])
                ->get(['time', 'speed']); // Ambil hanya kolom time dan speed
        }

        // Mengembalikan data dalam format JSON
        return response()->json(['data' => $data]);
    }


    public function grafikDistance(Request $request)
    {
        $customer = auth()->user()->customer_id;
        //$customer = 3;
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        if ($customer == 1) {
            $data = History::select(
                    'no_pol',
                    DB::raw('ROUND(COALESCE(SUM(distance), 0), 3) AS distance_today')
                )
                ->whereDate('time', '>=', $startDate)
                ->whereDate('time', '<=', $endDate)
                ->groupBy('no_pol')
                ->get();
    
            // Mengambil data terbaru per no_pol
            $latestData = History::select('no_pol', 'time', 'total_distance', 'latitude', 'longitude', 'address', 'status')
                ->whereIn('no_pol', $data->pluck('no_pol')) // Hanya mengambil no_pol yang relevan
                ->whereDate('time', '>=', $startDate)
                ->whereDate('time', '<=', $endDate)
                ->orderBy('time', 'desc')
                ->get()
                ->groupBy('no_pol');
        } else {
            $data = History::select(
                    'no_pol',
                    DB::raw('ROUND(COALESCE(SUM(distance), 0), 3) AS distance_today')
                )
                ->where('customer_id', $customer)
                ->whereDate('time', '>=', $startDate)
                ->whereDate('time', '<=', $endDate)
                ->groupBy('no_pol')
                ->get();
    
            // Mengambil data terbaru per no_pol
            $latestData = History::select('no_pol', 'time', 'total_distance', 'latitude', 'longitude', 'address', 'status')
                ->where('customer_id', $customer)
                ->whereIn('no_pol', $data->pluck('no_pol')) // Hanya mengambil no_pol yang relevan
                ->whereDate('time', '>=', $startDate)
                ->whereDate('time', '<=', $endDate)
                ->orderBy('time', 'desc')
                ->get()
                ->groupBy('no_pol');
        }

        // Array untuk menyimpan hasil akhir
        $formattedData = [];
        foreach ($data as $index => $item) {
            // Ambil data terbaru berdasarkan no_pol
            $latest = $latestData->get($item->no_pol)->first(); // Ambil data terbaru

            $formattedData[] = [
                'no' => $index + 1, 
                'rentang_tanggal' => $startDate . ' s/d ' . $endDate,
                'tanggal' => $latest->time,
                'nopol' => $item->no_pol,
                'latitude' => $latest->latitude ?? null,
                'longitude' => $latest->longitude ?? null,
                'distance_today' => $item->distance_today, // Format angka Indonesia
                'total_distance' => $latest->total_distance, // Ganti jika ada total_distance
                'alamat_terakhir' => $latest->address ?? null,
                'status_terakhir' => $latest->status ?? null,
            ];
        }
                      
        return response()->json(['data' => $formattedData]);
    }
    
    public function speedDistributionForm(Request $request) {
        $customer_id = auth()->user()->customer_id;

        $vehicles = Vehicle::where('status', 1);

        // Menambahkan filter berdasarkan customer_id jika diperlukan
        if ($customer_id != 1) {
            $vehicles = $vehicles->where('customer_id', $customer_id);
        }

        $vehicles = $vehicles->get();
        
        
        // Kirim data yang sudah diproses ke view
        return view('pages.grafik.speed_distribution', [
            'vehicles' => $vehicles
        ]);
    }
    
    public function speedDistribution(Request $request)
    {
        $vehicleId = $request->input('vehicle_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Validasi format tanggal menggunakan Carbon
        if ($startDate && !Carbon::hasFormat($startDate, 'Y-m-d')) {
            return response()->json(['error' => 'Invalid start date format. Expected format is Y-m-d.'], 400);
        }

        if ($endDate && !Carbon::hasFormat($endDate, 'Y-m-d')) {
            return response()->json(['error' => 'Invalid end date format. Expected format is Y-m-d.'], 400);
        }

        // Ambil data kecepatan dari database dan filter speed > 0, plus filter kendaraan jika dipilih
        $query = DB::table('histories')
            ->select('speed')
            ->whereNotNull('speed')
            ->where('speed', '>', 0);

        // Filter berdasarkan vehicle_id jika ada
        if ($vehicleId) {
            $query->where('vehicle_id', $vehicleId);
        }

        // Filter berdasarkan rentang waktu (start_date dan end_date) jika ada
        if ($startDate && $endDate) {
            // Menggunakan whereBetween untuk memfilter berdasarkan time yang bertipe timestamp
            $query->whereBetween('time', [
                Carbon::parse($startDate)->startOfDay(), 
                Carbon::parse($endDate)->endOfDay()
            ]);
        } 

        // Ambil data kecepatan yang sudah difilter
        $speedData = $query->get();

        // Tentukan bin kecepatan (0-10, 10-20, … hingga 120)
        $maxSpeed = 140;
        $step = 10;
        $bins = [];
        for ($i = 0; $i < $maxSpeed; $i += $step) {
            $bins[] = [$i, $i + $step];
        }

        // Hitung frekuensi untuk setiap bin
        $distribution = array_fill(0, count($bins), 0);
        foreach ($speedData as $data) {
            $speed = (float) $data->speed;
            foreach ($bins as $index => [$min, $max]) {
                if ($speed >= $min && $speed < $max) {
                    $distribution[$index]++;
                    break;
                }
            }
        }

        return response()->json([
            'distribution' => $distribution,
            'bins' => $bins
        ]);
    }


    // panggil form grafik distance
    public function totalDistancePerDayForm()
    {
        $customer_id = auth()->user()->customer_id;

        $vehicles = Vehicle::where('status', 1);

        // Menambahkan filter berdasarkan customer_id jika diperlukan
        if ($customer_id != 1) {
            $vehicles = $vehicles->where('customer_id', $customer_id);
        }

        $vehicles = $vehicles->get();
        
        
        // Kirim data yang sudah diproses ke view
        return view('pages.grafik.distance', [
            'vehicles' => $vehicles
        ]);
    }

    public function totalDistancePerDay(Request $request)
    {
        $vehicleId = $request->input('vehicle_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Validasi format tanggal menggunakan Carbon
        if ($startDate && !Carbon::hasFormat($startDate, 'Y-m-d')) {
            return response()->json(['error' => 'Invalid start date format. Expected format is Y-m-d.'], 400);
        }

        if ($endDate && !Carbon::hasFormat($endDate, 'Y-m-d')) {
            return response()->json(['error' => 'Invalid end date format. Expected format is Y-m-d.'], 400);
        }

        // Menyusun query untuk mengambil data jarak dan durasi
        $query = DB::table('histories')
            ->select(
                'vehicle_id',
                DB::raw('DATE(time) AS day'),
                DB::raw('ROUND(
                    (ST_Length(
                        ST_MakeLine(geom ORDER BY time)::geography
                    ) / 1000.0)::numeric
                , 2) AS total_km'),
                DB::raw('ROUND(
                    EXTRACT(EPOCH FROM (MAX(time) - MIN(time))) / 3600, 2
                ) AS duration_hours')  // Menghitung durasi dalam jam
            )
            ->whereNotNull('geom')
            ->groupBy('vehicle_id', DB::raw('DATE(time)'));

        // Jika vehicle_id ada, tambahkan filter untuk vehicle_id
        if ($vehicleId) {
            $query->where('vehicle_id', $vehicleId);
        }

        // Filter berdasarkan tanggal jika start_date dan end_date diberikan
        if ($startDate) {
            $query->whereDate('time', '>=', Carbon::parse($startDate)->startOfDay());
        }

        if ($endDate) {
            $query->whereDate('time', '<=', Carbon::parse($endDate)->endOfDay());
        }

        // Menjalankan query dan mendapatkan data
        $distances = $query->orderBy('day')->get();

        // Mengembalikan data dalam format JSON
        return response()->json([
            'distances' => $distances
        ]);
    }

    public function getSpeedMapForm()
    {
        $customer_id = auth()->user()->customer_id;

        $vehicles = Vehicle::where('status', 1);

        // Menambahkan filter berdasarkan customer_id jika diperlukan
        if ($customer_id != 1) {
            $vehicles = $vehicles->where('customer_id', $customer_id);
        }

        $vehicles = $vehicles->get();
        
        
        // Kirim data yang sudah diproses ke view
        return view('pages.grafik.speed_map', [
            'vehicles' => $vehicles
        ]);
    }

    public function getSpeedMap(Request $request)
    {
        //$customer_id = auth()->user()->customer_id;

        $data = $request->all();
        $start_date = $data['start_date'];
        $end_date = $data['end_date'];
        $no_pol = $data['no_pol'];

        // Ambil point-point dalam bentuk GeoJSON
        $histories = History::selectRaw("
                histories.time,
                histories.course,
                histories.speed,
                histories.status,
                vehicles.vehicle_type,
                ST_X(geom::geometry) as longitude,
                ST_Y(geom::geometry) as latitude
            ")
            ->join('vehicles', 'histories.vehicle_id', '=', 'vehicles.id')
            ->whereBetween('histories.time', [$start_date, $end_date])
            ->when($no_pol, function ($query) use ($no_pol) {
                $query->where('histories.no_pol', $no_pol);
            })
            ->whereNotNull('histories.geom')
            ->orderBy('histories.time', 'asc')
            ->get();

        return response()->json(['data' => $histories]);
    }

    public function heatmapByVehicleForm(Request $request) {
        $customer_id = auth()->user()->customer_id;

        $vehicles = Vehicle::where('status', 1);

        // Menambahkan filter berdasarkan customer_id jika diperlukan
        if ($customer_id != 1) {
            $vehicles = $vehicles->where('customer_id', $customer_id);
        }

        $vehicles = $vehicles->get();
        
        
        // Kirim data yang sudah diproses ke view
        return view('pages.grafik.heatmap', [
            'vehicles' => $vehicles
        ]);
    }

    public function heatmapByVehicle(Request $request)
    {
        $vehicleId = $request->input('vehicle_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Membuat query dasar untuk mengambil data lokasi
        $query = DB::table('histories')
            ->selectRaw('ST_Y(geom) as latitude, ST_X(geom) as longitude')
            ->whereNotNull('geom');

        // Jika vehicle_id ada, tambahkan filter untuk vehicle_id
        if ($vehicleId) {
            $query->where('vehicle_id', $vehicleId);
        }

        // Filter berdasarkan tanggal jika start_date dan end_date diberikan
        if ($startDate) {
            $query->whereDate('time', '>=', Carbon::parse($startDate)->startOfDay());
        }

        if ($endDate) {
            $query->whereDate('time', '<=', Carbon::parse($endDate)->endOfDay());
        }

        // Ambil data lokasi yang sudah difilter
        $locations = $query->get();

        // Kembalikan data dalam bentuk JSON
        return response()->json([
            'locations' => $locations->map(function ($location) {
                return [
                    'latitude' => $location->latitude,
                    'longitude' => $location->longitude,
                ];
            })
        ]);
    }


    public function heatmapBySpeedOrStatus(Request $request)
    {
        $vehicleId = $request->input('vehicle_id');
        $filterStatus = $request->input('ignition_status'); // optional
        $minSpeed = $request->input('min_speed'); // optional
        $maxSpeed = $request->input('max_speed');

        $vehicles = DB::table('histories')->select('vehicle_id')->distinct()->pluck('vehicle_id');

        $locations = collect();

        if ($vehicleId) {
            $query = DB::table('histories')
                ->selectRaw('ST_Y(geom) as latitude, ST_X(geom) as longitude, speed, ignition_status')
                ->where('vehicle_id', $vehicleId)
                ->whereNotNull('geom');

            if ($filterStatus) {
                $query->where('ignition_status', $filterStatus);
            }

            if ($minSpeed !== null) {
                $query->where('speed', '>=', $minSpeed);
            }

            if ($maxSpeed !== null) {
                $query->where('speed', '<=', $maxSpeed);
            }

            $locations = $query->get();
        }

        // var_dump($locations); exit;

        return view('pages.grafik.heatmap_speed_status', compact('locations', 'vehicleId', 'vehicles'));
    }

    public function clusteringLocation(Request $request)
    {
        // Filter berdasarkan kendaraan dan parameter lainnya jika ada
        $vehicleId = $request->input('vehicle_id');
        $minSpeed = $request->input('min_speed');
        $maxSpeed = $request->input('max_speed');

        // Query untuk mengambil lokasi dengan speed = 0 dan clustering
        $locations = DB::table('histories')
            ->select(DB::raw('ST_X(geom) as longitude, ST_Y(geom) as latitude, ST_ClusterKMeans(geom, 5) OVER() as cluster_id, speed'))
            ->where('speed', 0)
            ->whereNotNull('geom')
            ->when($vehicleId, function ($query) use ($vehicleId) {
                return $query->where('vehicle_id', $vehicleId);
            })
            ->when($minSpeed, function ($query) use ($minSpeed) {
                return $query->where('speed', '>=', $minSpeed);
            })
            ->when($maxSpeed, function ($query) use ($maxSpeed) {
                return $query->where('speed', '<=', $maxSpeed);
            })
            ->get();

        // Mengambil kendaraan yang ada untuk dropdown filter
        $vehicles = DB::table('vehicles')->pluck('id');

        return view('pages.grafik.clustering_location', compact('locations', 'vehicles', 'vehicleId', 'minSpeed', 'maxSpeed'));
    }
}
