<?php

namespace App\Http\Controllers;

use App\Exports\ExportLastPositionAdmin;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportHistoricalAdmin;
use App\Exports\ExportKecepatanAdmin;
use App\Exports\ExportJarakAdmin;
use App\Exports\ExportParkir;
use App\Http\Requests\ReportRequest;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\History;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class ReportAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //$customer_id = auth()->user()->customer_id;

        // Ambil data dari model Traccar
        $customers = Customer::where('status', 1)->get();
        
        // Kirim data yang sudah diproses ke view
        return view('pages.report_admin.last_position', [
            //'items' => $items,
            'customers' => $customers
        ]);
    }

    public function exportLastPosition(Request $request) 
    {
        $customer = $request->input('customer');

        return Excel::download(new ExportLastPositionAdmin($customer), 'Laporan_Posisi_Akhir.xlsx');
    }

    public function exportLaporanHistorical(ReportRequest $request) 
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $no_pol = $request->input('no_pol');
        $customer = $request->input('customer');
    
        return Excel::download(
            new ExportHistoricalAdmin($startDate, $endDate, $no_pol, $customer), 
            'laporan_historical_' . Carbon::now()->format('Y-m-d_H-i-s') . '.xlsx'
        );
    }

    public function exportLaporanKecepatan(ReportRequest $request) 
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $no_pol = $request->input('no_pol');
        $customer = $request->input('customer');
    
        return Excel::download(new ExportKecepatanAdmin($startDate, $endDate, $no_pol, $customer), 'laporan_kecepatan_kendaraan_' . Carbon::now()->format('Y-m-d_H-i-s') . '.xlsx');
    }

    public function exportLaporanJarak(ReportRequest $request) 
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $customer = $request->input('customer');
    
        return Excel::download(new ExportJarakAdmin($startDate, $endDate, $customer), 'laporan_jarak_tempuh_kendaraan_' . Carbon::now()->format('Y-m-d_H-i-s') . '.xlsx');
    }

    public function exportLaporanParkir(ReportRequest $request) 
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $no_pol = $request->input('no_pol');
    
        return Excel::download(new ExportParkir($startDate, $endDate, $no_pol), 'laporan_parkir_kendaraan_' . Carbon::now()->format('Y-m-d_H-i-s') . '.xlsx');
    }
    
    
    public function historicalReport() 
    {
        $customers = Customer::where('status', 1)->get();
        
        return view('pages.report_admin.historical_report')->with([
            'customers' => $customers
        ]);
    }

    public function getVehicles($customer_id)
    {
        $vehicles = Vehicle::where('customer_id', $customer_id)->where('status', 1)->get();
        return response()->json($vehicles);
    }


    public function laporanKecepatan() 
    {
        $customers = Customer::where('status', 1)->get();
        
        return view('pages.report_admin.kecepatan')->with([
            'customers' => $customers
        ]);
    }

    public function laporanJarak() 
    {
        $customers = Customer::where('status', 1)->get();
        
        return view('pages.report_admin.jarak')->with([
            'customers' => $customers
        ]);
    }

    public function laporanParkir() 
    {
        $customers = Customer::where('status', 1)->get();
        
        return view('pages.report_admin.parkir')->with([
            'customers' => $customers
        ]);
    }

    public function exportParkirToExcel(ReportRequest $request)
    {
        $no_pol = $request->input('no_pol');
        $startDate = Carbon::parse($request->input('start_date'))->startOfDay();
        $endDate = Carbon::parse($request->input('end_date'))->endOfDay();

        $vehicles = Vehicle::where('no_pol', $no_pol)->firstOrFail();

        $histories = History::where('vehicle_id', $vehicles->id)
                    ->whereBetween('time', [$startDate, $endDate])
                    ->orderBy('time')
                    ->get();

        $parkirData = [];
        $startParkirTime = null;

        foreach ($histories as $history) {
            $currentTime = Carbon::parse($history->time);

            if ($history->ignition_status == 'Off') {
                if (!$startParkirTime) {
                    $startParkirTime = $currentTime;
                }
            } elseif ($history->ignition_status == 'On' && $startParkirTime) {
                $durasiParkirSeconds = $currentTime->diffInSeconds($startParkirTime);
                $parkirData[] = [
                    'no_pol' => $history->no_pol,
                    'start_time' => $startParkirTime,
                    'end_time' => $currentTime,
                    'durasi' => $this->formatDurasi($durasiParkirSeconds),
                    'alamat' => $history->address,
                ];
                $startParkirTime = null;
            }
        }

        $filename = sprintf(
            'parkir_data_%s_%s_%s.xlsx',
            $no_pol,
            $startDate->format('Y-m-d'),
            $endDate->format('Y-m-d')
        );

        return Excel::download(new ExportParkir($parkirData), $filename);
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

    
    public function listHistorical(Request $request)
    {
        $query = DB::table('histories');
            //->join('customers', 'histories.customer_id', '=', 'customers.id')
            //->select('histories.*', 'customers.name as customer_name');

        // Filter berdasarkan no_pol jika ada
        if ($request->no_pol) {
            $query->where('histories.no_pol', $request->no_pol);
        }

        // Filter berdasarkan tanggal jika ada
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('histories.time', [$request->start_date, $request->end_date]);
        }

        $data = $query->get();

        return DataTables::of($data)
            ->addColumn('actions', function($item) {
                return '<a href="' . route('traccars.show', $item->id) . '" class="btn btn-icon btn-label-primary waves-effect" data-bs-toggle="tooltip" data-bs-placement="top" title="Lihat Data"><i class="mdi mdi-eye-outline"></i></a>
                <a href="' . route('traccars.edit', $item->id) . '" class="btn btn-icon btn-label-success waves-effect" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Data"><i class="mdi mdi-pencil-outline"></i></a>';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function listLastPosition(Request $request)
    {
        // $query = DB::table('traccars');
        
        // // Filter berdasarkan customer_id jika ada
        // if ($request->customer) {
        //     $query->where('customer_id', $request->customer);
        // }

        // // Ambil data dari database
        // $data = $query->get();

        $query = DB::table('traccars')
            ->join('devices', 'traccars.device_id', '=', 'devices.id') // Join dengan tabel devices
            ->select('traccars.*', 'devices.sim_number'); // Pilih kolom dari traccars dan devices

        // Filter berdasarkan customer_id jika ada
        if ($request->customer) {
            $query->where('traccars.customer_id', $request->customer);
        }

        // Ambil data dari database
        $data = $query->get();

        // Menambahkan transformasi untuk menghitung selisih waktu dan menentukan kelas
        $data->transform(function($item) {
            $now = Carbon::now(); // Waktu saat ini
            $time = Carbon::parse($item->time); // Waktu dari database
            $diffInHours = $time->diffInHours($now); // Selisih waktu dalam jam

            // Tentukan kelas CSS berdasarkan selisih waktu
            $statusClass = 'status-default'; // Kelas default
            if ($diffInHours > 24) {
                $statusClass = 'status-red'; // Lebih dari 24 jam
            } elseif ($diffInHours > 12) {
                $statusClass = 'status-yellow'; // Lebih dari 12 jam
            }

            // Menambahkan properti untuk kelas CSS dan waktu selisih
            $item->time_diff_class = $statusClass;  // Kelas untuk 'time_diff'
            $item->time_diff = $time->locale('id')->diffForHumans($now);  // Format waktu yang lebih manusiawi

            return $item;
        });

        // Mengembalikan data ke DataTables
        return DataTables::of($data)
        ->addColumn('time_diff', function($item) {
            return '<span>' . $item->time_diff . '</span>';
        })
        ->addColumn('time_diff_class', function($item) {
            return $item->time_diff_class;
        })
        ->rawColumns(['time_diff'])    
        ->make(true);
    }

    public function listDistance(Request $request)
    {
        $customer = $request->customer;
        $startDate = $request->start_date;
        $endDate = $request->end_date;

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
                      

        return DataTables::of($formattedData)        
        ->rawColumns(['actions'])
        ->make(true);
    }

    
    public function listParkingDatatable(Request $request)
    {
        $no_pol = $request->input('no_pol');
        $startDate = Carbon::parse($request->input('start_date'))->startOfDay();
        $endDate = Carbon::parse($request->input('end_date'))->endOfDay();

        $vehicles = Vehicle::where('no_pol', $no_pol)->firstOrFail();

        $histories = History::where('vehicle_id', $vehicles->id)
                    ->whereBetween('time', [$startDate, $endDate])
                    ->orderBy('time')
                    ->get();

        $parkirData = [];
        $startParkirTime = null;

        foreach ($histories as $history) {
            $currentTime = Carbon::parse($history->time);

            if ($history->ignition_status == 'Off') {
                if (!$startParkirTime) {
                    $startParkirTime = $currentTime;
                }
            } elseif ($history->ignition_status == 'On' && $startParkirTime) {
                $durasiParkirSeconds = $currentTime->diffInSeconds($startParkirTime);
                $parkirData[] = [
                    'no_pol' => $history->no_pol,
                    'start_time' => $startParkirTime->format('Y-m-d H:i:s'),
                    'end_time' => $currentTime->format('Y-m-d H:i:s'),
                    'durasi' => $this->formatDurasi($durasiParkirSeconds),
                    'alamat' => $history->address,
                ];
                
                $startParkirTime = null;
            }
        }

        return DataTables::of($parkirData)
           ->make(true);            

        return view('pages.report_admin.parkir');
    }


}
