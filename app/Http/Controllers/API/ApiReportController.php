<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Traccar;
use App\Models\Customer;
use App\Models\History;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ApiReportController extends Controller
{
    public function last_position(Request $request)
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

        if ($customer_id == 1) {
            $data = Traccar::where('active', 1)
                ->get();
        } else {
            $data = Traccar::where('customer_id', $customer_id)
                ->where('active', 1)
                ->get();
        }
        
        //dd('data', $id); exit;
        return response()->json([
            'status' => true,
            'message' => 'data ditemukan',
            'data' => $data
        ]);
    }

    public function historical(Request $request)
    {
        $customer_id = $request->query('customer_id');
        $start_date = $request->query('start_date');
        $end_date = $request->query('end_date');
        $no_pol = $request->query('no_pol');
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

        // ambil data history berdasarkan customer terlebih dahulu
        if ($customer_id == 1) {
            $query = History::query();
        } else {
            $query = History::where('histories.customer_id', $customer_id);
        }
        
        // Lakukan filter berdasarkan no_pol jika no_pol tidak kosong di endpoint
        if (!is_null($no_pol)) {
            $query->where('histories.no_pol', $no_pol);
        }
        
        // Filter berdasarkan start_date dan end_date jika endpoint terdapat tanggal dan tidak kosong
        if (!is_null($start_date) && !is_null($end_date)) {
            $start_date = date('Y-m-d', strtotime($start_date));
            $end_date = date('Y-m-d', strtotime($end_date . ' +1 day'));
            $query->whereBetween('time', [$start_date, $end_date]);
        }
        
        // Join dengan tabel vehicles untuk mengambil vehicle_type
        $query->join('vehicles', 'histories.vehicle_id', '=', 'vehicles.id')
              ->select('histories.*', 'vehicles.vehicle_type')
              ->orderBy('histories.time', 'asc');
        
        // Ambil data yang telah difilter
        $data = $query->get();

        //dd('data', $id); exit;
        return response()->json([
            'status' => true,
            'message' => 'data ditemukan',
            'data' => $data
        ]);
    }

    public function speed(Request $request)
    {
        $customer_id = $request->query('customer_id');
        $start_date = $request->query('start_date');
        $end_date = $request->query('end_date');
        $no_pol = $request->query('no_pol');
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

        // ambil data history berdasarkan customer terlebih dahulu
        if ($customer_id == 1) {
            $query = History::query();
        } else {
            $query = History::where('histories.customer_id', $customer_id);
        }
        
        // Lakukan filter berdasarkan no_pol jika no_pol tidak kosong di endpoin
        if (!is_null($no_pol)) {
            $query->where('histories.no_pol', $no_pol);
        }

        // Filter berdasarkan start_date dan end_date jika endpoin terdapat tanggal dan tidak kosong
        if (!is_null($start_date) && !is_null($end_date)) {
            $start_date = date('Y-m-d', strtotime($start_date));
            $end_date = date('Y-m-d', strtotime($end_date . ' +1 day'));
            $query->whereBetween('time', [$start_date, $end_date]);
        }

        $query->join('vehicles', 'histories.vehicle_id', '=', 'vehicles.id')
              ->select('histories.no_pol', 'histories.time', 'histories.latitude', 'histories.longitude', 'histories.course', 'vehicles.vehicle_type'); 
        
        // Ambil data yang telah difilter
        $data = $query->get();

        //dd('data', $id); exit;
        return response()->json([
            'status' => true,
            'message' => 'data ditemukan',
            'data' => $data
        ]);
    }

    public function jarak(Request $request)
    {
        $customer_id = $request->query('customer_id');
        $start_date = $request->query('start_date');
        $end_date = $request->query('end_date');

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

        // ambil data history berdasarkan customer terlebih dahulu
        if ($customer_id == 1) {
            $query = History::query();
        } else {
            $query = History::where('histories.customer_id', $customer_id);
        }

        // Filter berdasarkan start_date dan end_date jika endpoin terdapat tanggal dan tidak kosong
        if (!is_null($start_date) && !is_null($end_date)) {
            $start_date = date('Y-m-d', strtotime($start_date));
            $end_date = date('Y-m-d', strtotime($end_date . ' +1 day')); // Pastikan end_date inclusive
            $query->whereBetween('time', [$start_date, $end_date]);
        }

        // Menambahkan pengelompokan berdasarkan no_pol dan menghitung total distance
        $data = $query->select('no_pol', DB::raw('SUM(distance) as total_distance'))
                    ->groupBy('no_pol')
                    ->get();

        return response()->json([
            'status' => true,
            'message' => 'data ditemukan',
            'data' => $data
        ]);
    }


    public function parking(Request $request)
    {
        $customer_id = $request->query('customer_id');
        $no_pol = $request->query('no_pol');
        // $startDate = $request->input('start_date');
        // $endDate = $request->input('end_date');
        $startDate = Carbon::parse($request->input('start_date'))->startOfDay();
        $endDate = Carbon::parse($request->input('end_date'))->endOfDay();
        // var_dump($startDate);
        // var_dump($endDate);
        // var_dump($no_pol); exit;
        $histories = History::where('no_pol', $no_pol)
                    ->where('customer_id', $customer_id)
                    ->whereBetween('time', [$startDate, $endDate])
                    ->orderBy('time')
                    ->get();

        //var_dump($histories); exit;
        
        $parkirData = [];
        $startParkirTime = null;

        foreach ($histories as $history) {
            $currentTime = Carbon::parse($history->time);

            if ($history->ignition_status === 'Off') { 
                if (!$startParkirTime) {
                    $startParkirTime = $currentTime;
                }
            } elseif ($history->ignition_status === 'On' && $startParkirTime) {
                $durasiParkirSeconds = $currentTime->diffInSeconds($startParkirTime);
                // Cek jika durasi parkir minimal 5 menit (300 detik) yang akan di anggap parkir
                if ($durasiParkirSeconds >= 300) { 
                    $parkirData[] = [
                        'no_pol' => $history->no_pol,
                        'course' => $history->course,
                        'start_time' => $startParkirTime->format('d-m-Y H:i:s'),
                        'end_time' => $currentTime->format('d-m-Y H:i:s'),
                        'durasi' => $this->formatDurasi($durasiParkirSeconds),
                        'alamat' => $history->address,
                        'latitude' => $history->latitude,
                        'longitude' => $history->longitude,
                    ];
                }
                
                $startParkirTime = null; // Reset startParkirTime
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'data ditemukan',
            'data' => $parkirData
        ]);
    }

    public function distance(Request $request)
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

        // Mendapatkan data traccars dengan total distance berdasarkan tanggal hari ini
        if ($customer_id == 1) {
            $data = Traccar::select(
                'traccars.id',
                'traccars.time',
                'traccars.no_pol',
                'traccars.course',
                'traccars.status',
                'traccars.latitude',
                'traccars.longitude',
                'traccars.total_distance',
                'traccars.address',
                DB::raw('ROUND(COALESCE(SUM(histories.distance), 0), 3) AS distance_today') // Menggunakan ROUND untuk membulatkan
            )
            ->leftJoin('histories', 'traccars.vehicle_id', '=', 'histories.vehicle_id')
            ->whereDate('histories.time', now()->toDateString())
            ->groupBy('traccars.id', 'traccars.no_pol', 'traccars.status', 'traccars.total_distance', 'traccars.address')
            ->get();
        } else {
            $data = Traccar::select(
                'traccars.id',
                'traccars.time',
                'traccars.no_pol',
                'traccars.course',
                'traccars.status',
                'traccars.latitude',
                'traccars.longitude',
                'traccars.total_distance',
                'traccars.address',
                DB::raw('ROUND(COALESCE(SUM(histories.distance), 0), 3) AS distance_today') // Menggunakan ROUND untuk membulatkan
            )
            ->leftJoin('histories', 'traccars.vehicle_id', '=', 'histories.vehicle_id')
            ->where('traccars.customer_id', $customer_id)
            ->whereDate('histories.time', now()->toDateString())
            ->groupBy('traccars.id', 'traccars.no_pol', 'traccars.status', 'traccars.total_distance', 'traccars.address')
            ->get();
        }
        
        return response()->json([
        'status' => true,
        'message' => 'data ditemukan',
        'data' => $data
        ]);
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

        if ($seconds > 0 || empty($durasi)) { // Menampilkan detik jika tidak ada jam dan menit
            $durasi[] = "{$seconds} detik";
        }

        return implode(' ', $durasi);
    }
}
