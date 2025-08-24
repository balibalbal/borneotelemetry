<?php

namespace App\Http\Controllers;

use App\Exports\ExportLastPosition;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportHistorical;
use App\Exports\ExportKecepatan;
use App\Exports\ExportJarak;
use App\Exports\ExportParkir;
use App\Models\Traccar;
use App\Http\Requests\ReportRequest;
use App\Models\Group;
use Illuminate\Http\Request;
use App\Models\History;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $groups = Group::where('status', 1)->get();


        // Kirim data yang sudah diproses ke view
        return view('pages.reports.last_position', [
            // 'items' => $items,
            'groups' => $groups,
        ]);
    }

    public function exportLastPosition(Request $request)
    {
        $filters = $request->all();

        ini_set('max_execution_time', 1800);
        ini_set('memory_limit', '1G');

        $allData = \App\Exports\ReportPosisiAkhir::getData($filters);
        $chunks = $allData->chunk(500);

        return Excel::download(
            new \App\Exports\ReportPosisiAkhirChunk($chunks),
            'Laporan_Posisi Akhir_' . now()->format('Y-m-d_H-i-s') . '.xlsx'
        );
    }

    public function exportLaporanHistorical(Request $request)
    {
        $filters = $request->all();

        ini_set('max_execution_time', 1800);
        ini_set('memory_limit', '1G');

        $allData = \App\Exports\ReportHistorical::getData($filters);
        $chunks = $allData->chunk(500);

        return Excel::download(
            new \App\Exports\ReportHistoricalChunk($chunks),
            'Laporan_Historical_' . now()->format('Y-m-d_H-i-s') . '.xlsx'
        );
    }

    public function laporanDump() 
    {
        return view('pages.reports.dump_truck');
    }

    public function downloadDumpReport(Request $request)
    {
        $filters = [
            // 'driver_id' => $request->input('driver_id'),
            // 'tanggal_type' => $request->input('tanggal_type', 'transfer_date'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
        ];

        ini_set('max_execution_time', 1800);
        ini_set('memory_limit', '1G');

        $allData = \App\Exports\ReportDump::getData($filters);
        $chunks = $allData->chunk(500);

        return Excel::download(
            new \App\Exports\ReportDumpChunk($chunks),
            'Laporan_Dump_' . now()->format('Y-m-d_H-i-s') . '.xlsx'
        );
    }

    public function previewDumpReport(Request $request)
    {
        $filters = $request->all();
        $data = \App\Exports\ReportDump::getData($filters); // Ambil data sesuai filter

        return response()->json([
            'success' => true,
            'data' => $data ?? [], // kalau null, kasih array kosong
        ]);
    }

    public function exportLaporanKecepatan(ReportRequest $request) 
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $no_pol = $request->input('no_pol');
    
        return Excel::download(new ExportKecepatan($startDate, $endDate, $no_pol), 'laporan_kecepatan_kendaraan.xlsx');
    }

    public function exportLaporanJarak(ReportRequest $request) 
    {
        $startDate = Carbon::parse($request->input('start_date'))->startOfDay(); // Mulai dari jam 00:00:00
        $endDate = Carbon::parse($request->input('end_date'))->endOfDay(); //berakhir pada 23:59:00
        $nopol = $request->input('no_pol');
        $group = $request->input('group_id');
        $customer = auth()->user()->customer_id;

        //var_dump($group); exit;
    
        return Excel::download(new ExportJarak($startDate, $endDate, $nopol, $group, $customer), 'laporan_jarak_tempuh_kendaraan.xlsx');
    }

    
    public function exportJarakPDF(ReportRequest $request)
    {
        $startDate = Carbon::parse($request->input('start_date'))->startOfDay(); // Mulai dari jam 00:00:00
        $endDate = Carbon::parse($request->input('end_date'))->endOfDay();
        $nopol = $request->input('no_pol');
        $group = $request->input('group_id');
        $customer = auth()->user()->customer_id;

        // Ambil data sesuai dengan parameter
        $data = (new ExportJarak($startDate, $endDate, $nopol, $group, $customer))->collection();

        // Variabel untuk total jarak per nopol
        $totalDistancePerNoPol = 0;

        foreach ($data as $item) {
            $totalDistancePerNoPol += $item->total_distance;
        }

        return view('pages.reports.jarak_pdf')->with([
            'data' => $data,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);

            // // Rendering view ke PDF
            // $pdf = PDF::loadView('pages.reports.jarak_pdf', compact('data', 'totalDistancePerNoPol'));

            // // Download PDF
            // return $pdf->download('jarak_tempuh_kendaraan.pdf');
    }
    public function cetakParkir(ReportRequest $request)
    {
        $startDate = Carbon::parse($request->input('start_date'))->startOfDay(); // Mulai dari jam 00:00:00
        $endDate = Carbon::parse($request->input('end_date'))->endOfDay();
        $no_pol = $request->input('no_pol');        

        //$vehicles = Vehicle::where('no_pol', $no_pol)->firstOrFail();

        $histories = History::where('vehicle_id', $no_pol)
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
                
                // Cek jika durasi parkir minimal 5 menit (300 detik) yang akan di anggap parkir
                if ($durasiParkirSeconds >= 300) { 
                    $parkirData[] = [
                        'no_pol' => $history->no_pol,
                        'start_time' => $startParkirTime,
                        'end_time' => $currentTime,
                        'durasi' => $this->formatDurasi($durasiParkirSeconds),
                        'alamat' => $history->address,
                    ];
                }

                $startParkirTime = null;
                
            }
        }

        return view('pages.reports.cetak_parkir')->with([
            'data' => $parkirData,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);

    }
        
    public function listDistance(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $nopol = $request->no_pol;
        $group = $request->group_id;
        //var_dump($group);
        //var_dump($nopol);exit;

        // 1 = semua group dan semua nopol
        if ((int)$nopol[0] === 1 && (int)$group[0] === 1) {
            $data = History::select('no_pol',
                        DB::raw('DATE(time) as date'), 
                        DB::raw('SUM(distance) as total_distance')
                    )
                    // ->whereIn('vehicle_id', $nopol)
                    ->whereDate('time', '>=', $startDate)
                    ->whereDate('time', '<=', $endDate)
                    ->groupBy(DB::raw('DATE(time)'), 'no_pol')
                    ->orderBy('no_pol', 'asc') // Urutkan berdasarkan no_pol terlebih dahulu
                    ->orderBy(DB::raw('DATE(time)'), 'asc') // Urutkan berdasarkan tanggal setelah no_pol
                    ->get();
        } elseif ((int)$nopol[0] === 1 && (int)$group[0] !== 1){
            $data = History::select('histories.no_pol', 
                    DB::raw('DATE(histories.time) as date'), 
                    DB::raw('SUM(histories.distance) as total_distance')
                )
                ->join('vehicles as v', 'histories.vehicle_id', '=', 'v.id') 
                ->whereIn('v.group_id', $group)
                ->whereDate('histories.time', '>=', $startDate)
                ->whereDate('histories.time', '<=', $endDate)
                ->groupBy(DB::raw('DATE(histories.time)'), 'histories.no_pol')
                ->orderBy('histories.no_pol', 'asc')
                ->orderBy(DB::raw('DATE(histories.time)'), 'asc')
                ->get();

        
        } else {
            $data = History::select('no_pol',
                        DB::raw('DATE(time) as date'), 
                        DB::raw('SUM(distance) as total_distance')
                    )
                    ->whereIn('vehicle_id', $nopol)
                    ->whereDate('time', '>=', $startDate)
                    ->whereDate('time', '<=', $endDate)
                    ->groupBy(DB::raw('DATE(time)'), 'no_pol')
                    ->orderBy('no_pol', 'asc') // Urutkan berdasarkan no_pol terlebih dahulu
                    ->orderBy(DB::raw('DATE(time)'), 'asc') // Urutkan berdasarkan tanggal setelah no_pol
                    ->get();
        }

        return response()->json([
            'data' => $data
        ]);
    }

    public function historicalReport() 
    {                
        $vehicles = Vehicle::where('status', 1)
                    ->get();
        
        return view('pages.reports.historical_report')->with([
            'vehicles' => $vehicles
        ]);
    }

    public function laporanKecepatan() 
    {                
        $vehicles = Vehicle::where('status', 1)
                    ->get();
        
        return view('pages.reports.kecepatan')->with([
            'vehicles' => $vehicles
        ]);
    }

    public function laporanJarak() 
    {                
        $vehicles = Vehicle::where('status', 1)
                    ->get();

        $groups = Group::where('status', 1)
                    ->get();
        
        return view('pages.reports.jarak')->with([
            'vehicles' => $vehicles,
            'groups' => $groups
        ]);
    }

    public function getVehicleByGroup($groupId)
    {
            // Jika hanya satu group_id yang dipilih
            if ($groupId == 1) {
                //var_dump($groupId); exit;
                $vehicles = Vehicle::where('status', 1)->get();
            } else {
                // var_dump($groupId); exit;
                $groupId = explode(',', $groupId);
                //var_dump($groupId); exit;
                $vehicles = Vehicle::whereIn('group_id', $groupId)
                    ->get();
            }       

        return response()->json($vehicles);
    }


    public function laporanParkir() 
    {
        $vehicles = Vehicle::where('status', 1)
                    ->get();
        
        return view('pages.reports.parkir')->with([
            'vehicles' => $vehicles
        ]);
    }

    public function exportParkirToExcel(ReportRequest $request)
    {
        // $request->validate([
        //     'no_pol' => 'required|string',
        //     'start_date' => 'required|date',
        //     'end_date' => 'required|date|after_or_equal:start_date',
        // ]);

        $no_pol = $request->input('no_pol');
        $startDate = Carbon::parse($request->input('start_date'))->startOfDay();
        $endDate = Carbon::parse($request->input('end_date'))->endOfDay();

        //$vehicles = Vehicle::where('id', $no_pol)->firstOrFail();

        $histories = History::where('vehicle_id', $no_pol)
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
                
                // Cek jika durasi parkir minimal 5 menit (300 detik) yang akan di anggap parkir
                if ($durasiParkirSeconds >= 300) { 
                    $parkirData[] = [
                        'no_pol' => $history->no_pol,
                        'start_time' => $startParkirTime,
                        'end_time' => $currentTime,
                        'durasi' => $this->formatDurasi($durasiParkirSeconds),
                        'alamat' => $history->address,
                    ];
                }

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

    public function tampilkanListParkir(Request $request)
    {
        // Pastikan startDate mulai dari jam 00:00:00 dan endDate sampai jam 23:59:59
        $startDate = Carbon::parse($request->start_date)->startOfDay(); // 00:00:00
        $endDate = Carbon::parse($request->end_date)->endOfDay(); // 23:59:59
        $no_pol = $request->no_pol;

        //$vehicles = Vehicle::where('no_pol', $no_pol)->firstOrFail();

        $histories = History::where('vehicle_id', $no_pol)
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
                
                // Cek jika durasi parkir minimal 5 menit (300 detik) yang akan di anggap parkir
                if ($durasiParkirSeconds >= 300) { 
                    $parkirData[] = [
                        'no_pol' => $history->no_pol,
                        'start_time' => $startParkirTime,
                        'end_time' => $currentTime,
                        'durasi' => $this->formatDurasi($durasiParkirSeconds),
                        'alamat' => $history->address,
                    ];
                }

                $startParkirTime = null;
                
            }
        }

        return response()->json([
            'data' => $parkirData
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

    public function listHistorical()
    {

        // $data = History::where('customer_id', $customer_id)->get();
        $data = History::where('time', today())
                ->get();

        return DataTables::of($data)
        ->addColumn('actions', function($item) {
            return '<a href="' . route('traccars.show', $item->id) . '" class="btn btn-icon btn-label-primary waves-effect" data-bs-toggle="tooltip" data-bs-placement="top" title="Lihat Data"><i class="mdi mdi-eye-outline"></i></a>
            <a href="' .route('traccars.edit', $item->id) . '" class="btn btn-icon btn-label-success waves-effect" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Data"><i class="mdi mdi-pencil-outline"></i></a>';

        })
        ->rawColumns(['actions'])
        ->make(true);
    }

    
    public function listParkingDatatable(Request $request)
    {
        if ($request->ajax()) {
            $vehicles = Vehicle::where('status', 1)
                        ->get();

            $data = [];

            foreach ($vehicles as $vehicle) {
                $histories = History::where('vehicle_id', $vehicle->id)
                            ->whereDate('time', today())
                            ->orderBy('time')
                            ->get();

                $startParkirTime = null;

                foreach ($histories as $history) {
                    $currentTime = Carbon::parse($history->time);

                    if ($history->ignition_status == 'Off') {
                        if (!$startParkirTime) {
                            $startParkirTime = $currentTime;
                        }
                    } elseif ($history->ignition_status == 'On' && $startParkirTime) {
                        $durasiParkirSeconds = $currentTime->diffInSeconds($startParkirTime);
                        $data[] = [
                            'no_pol' => $history->no_pol,
                            'start_time' => $startParkirTime->format('Y-m-d H:i:s'),
                            'end_time' => $currentTime->format('Y-m-d H:i:s'),
                            'durasi' => $this->formatDurasi($durasiParkirSeconds),
                            'alamat' => $history->address,
                        ];
                        $startParkirTime = null;
                    }
                }
            }

            return DataTables::of($data)
            ->make(true);
            // return response()->json(['data' => $data]);
        }

        return view('pages.reports.parkir');
    }

    public function tampilkanListKecepatan(Request $request)
    {        
        // Pastikan startDate mulai dari jam 00:00:00 dan endDate sampai jam 23:59:59
        $startDate = Carbon::parse($request->start_date)->startOfDay(); // 00:00:00
        $endDate = Carbon::parse($request->end_date)->endOfDay(); // 23:59:59

        $no_pol = $request->no_pol;

        $speeds = History::where('vehicle_id', $no_pol)
                    ->whereBetween('time', [$startDate, $endDate])
                    ->orderBy('time')
                    ->get();
//var_dump($speeds); exit;
        return response()->json([
            'data' => $speeds
        ]);
    }

    public function tampilkanListHistorical(Request $request) 
    {
        $filters = $request->all();
        $data = \App\Exports\ReportHistorical::getData($filters); // Ambil data sesuai filter

        return response()->json([
            'success' => true,
            'data' => $data ?? [], // kalau null, kasih array kosong
        ]);
    }

    public function tampilkanListPosisiAkhir(Request $request) 
    {
        $filters = $request->all();
        $data = \App\Exports\ReportPosisiAkhir::getData($filters); // Ambil data sesuai filter

        return response()->json([
            'success' => true,
            'data' => $data ?? [], // kalau null, kasih array kosong
        ]);
    }

}
