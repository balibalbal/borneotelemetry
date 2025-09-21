<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Geofence;
use App\Models\Vehicle;
use App\Models\Traccar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class TraccarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {        
        // $customer_id = auth()->user()->customer_id;

        // if ($customer_id == 1) {
            $geofences = Geofence::where('customer_id', 3)->get();
        // } else {
            // $geofences = Geofence::where('customer_id', $customer_id)->get();
        // }
        return view('pages.traccars.index')->with([
           'geofences' => $geofences
        ]);

        // return view('pages.traccars.index');
    }

    public function webSocket()
    {        
        $customer_id = auth()->user()->customer_id;

        if ($customer_id == 1) {
            $geofences = Geofence::where('customer_id', 3)->get();
        } else {
            $geofences = Geofence::where('customer_id', $customer_id)->get();
        }
        return view('pages.traccars.websocket')->with([
           'geofences' => $geofences
        ]);

        // return view('pages.traccars.index');
    }

    public function getNearestVehicles(Request $request)
    {
        // Ambil customer_id dari session user yang sedang login
        $customer_id = auth()->user()->customer_id;

        // Validasi jika vehicle_id dan geo_point ada dalam request
        $request->validate([
            'vehicle_id' => 'required|integer',
            'geo_point' => 'required|string',  // Contoh: WKB string
        ]);

        // Ambil data dari request
        $vehicle_id = $request->vehicle_id;
        $geo_point = $request->geo_point;

        // Tambahkan \x jika geo_point tidak dimulai dengan \x
        if (substr($geo_point, 0, 2) !== '\x') {
            $geo_point = '\x' . $geo_point;
        }
        
        // Query untuk mengambil 5 kendaraan terdekat dengan jarak maksimal 1 km (1000 meter)
        // if ($customer_id == 1) {
            $vehicles = DB::table('traccars')
            ->selectRaw('vehicle_id, no_pol, speed, status, 
                ST_Distance(geo_point, ST_SetSRID(ST_GeomFromWKB(?), 4326)) AS distance', 
                [$geo_point])
            ->where('customer_id', $customer_id)
            ->where('vehicle_id', '!=', $vehicle_id)  // Menghindari kendaraan yang sama
            ->whereRaw('ST_Distance(geo_point, ST_SetSRID(ST_GeomFromWKB(?), 4326)) <= 1000', 
                [$geo_point])
            ->orderBy('distance')
            ->limit(5)
            ->get();
        // } else {
        //     $vehicles = DB::table('traccars')
        //     ->selectRaw('vehicle_id, no_pol, speed, status, 
        //         ST_Distance(geo_point, ST_SetSRID(ST_GeomFromWKB(?), 4326)) AS distance', 
        //         [$geo_point])
        //     ->where('customer_id', $customer_id)
        //     ->where('vehicle_id', '!=', $vehicle_id)  // Menghindari kendaraan yang sama
        //     ->whereRaw('ST_Distance(geo_point, ST_SetSRID(ST_GeomFromWKB(?), 4326)) <= 1000', 
        //         [$geo_point])
        //     ->orderBy('distance')
        //     ->limit(5)
        //     ->get();
        // }
        

        // Kembalikan response JSON dengan data kendaraan terdekat
        return response()->json([
            'status' => 'success',
            'vehicles' => $vehicles
        ]);
    }


    public function getData() 
    {
        // cek account mana yang sedang login
        $customer_id = auth()->user()->customer_id;
        
        // Fetch data from the Traccar model
        $vehicleItems = DB::table('traccars')
            ->selectRaw("
                traccars.id as traccar_id,
                traccars.time,
                traccars.vehicle_id,
                traccars.no_pol,
                traccars.speed,
                traccars.total_distance,
                traccars.course,
                traccars.axisx,
                traccars.axisy,
                traccars.axisz,
                traccars.roll,
                traccars.pitch,
                traccars.altitude,
                traccars.status,
                traccars.address,
                traccars.geo_point,
                traccars.status_address,
                traccars.ignition_status,
                vehicles.vehicle_type,
                ST_X(traccars.geo_point::geometry) as longitude,  
                ST_Y(traccars.geo_point::geometry) as latitude 
            ")
            ->join('vehicles', 'traccars.vehicle_id', '=', 'vehicles.id')
            // ->when($customer_id != 1, function ($query) use ($customer_id) {
            //     return $query->where('vehicles.customer_id', $customer_id);  // Filter berdasarkan customer_id
            // })
            ->whereNotNull('traccars.geo_point')  // Pastikan geo_point tidak null
            ->orderBy('traccars.time', 'asc')
            ->get();
        
                
        $vehicleMapData = []; 

        // Format the retrieved vehicle data
        foreach ($vehicleItems as $item) {
            $vehicleMapData[] = [
                'id' => $item->traccar_id,
                'vehicle_id'=> $item->vehicle_id,
                'vehicle_type'=> $item->vehicle_type,
                'no_pol'=> $item->no_pol,
                'latitude' => $item->latitude,
                'longitude' => $item->longitude,
                'name' => $item->no_pol,
                'status' => $item->status,
                'time' => $item->time,
                'course' => $item->course,
                'altitude' => $item->altitude,
                'speed' => $item->speed,
                'ignition' => $item->ignition_status,
                'distance' => $item->total_distance,
                'address' => $item->address,
                'geo_point' => $item->geo_point,
                'axisx' => $item->axisx,
                'axisy' => $item->axisy,
                'axisz' => $item->axisz,
                'roll' => round($item->roll),
                'pitch' => round($item->pitch)
            ];
        }

        

        // Get the total count of vehicles meeting the criteria
        // if ($customer_id == 1) {
            $totalVehicles = Traccar::whereNotNull('latitude')->whereNotNull('longitude')->where('active', 1)->count();
            $totalOnline = Traccar::where('status', 'bergerak')->whereNotNull('latitude')->whereNotNull('longitude')->where('active', 1)->count();
            $totalOffline = Traccar::where('status', 'mati')->whereNotNull('latitude')->whereNotNull('longitude')->where('active', 1)->count();
            $totalAck = Traccar::where('status', 'diam')->whereNotNull('latitude')->whereNotNull('longitude')->where('active', 1)->count();
            $totalEngine = Traccar::where('status', 'berhenti')->whereNotNull('latitude')->whereNotNull('longitude')->where('active', 1)->count(); 
            $maxSpeedRecord = Traccar::join('vehicles', 'traccars.vehicle_id', '=', 'vehicles.id')
                        ->whereNotNull('traccars.latitude')
                        ->whereNotNull('traccars.longitude')
                        ->orderBy('traccars.speed', 'desc')
                        ->first(['traccars.speed', 'traccars.no_pol']);

                    if ($maxSpeedRecord) {
                        $maxSpeed = $maxSpeedRecord->speed;
                        $noPol = $maxSpeedRecord->no_pol;
                    } else {
                        $maxSpeed = 'not found';
                        $noPol = 'not found';
                    }           
        // } else {        
        //     $totalVehicles = Traccar::join('vehicles', 'traccars.vehicle_id', '=', 'vehicles.id')
        //         ->where('vehicles.customer_id', $customer_id)
        //         ->where('active', 1)
        //         ->whereNotNull('traccars.latitude')
        //         ->whereNotNull('traccars.longitude')
        //         ->count();

        //     $totalOnline = Traccar::join('vehicles', 'traccars.vehicle_id', '=', 'vehicles.id')
        //                 ->where('vehicles.customer_id', $customer_id)
        //                 ->where('active', 1)
        //                 ->where('traccars.status', 'bergerak')
        //                 ->whereNotNull('traccars.latitude')
        //                 ->whereNotNull('traccars.longitude')
        //                 ->count();

        //     $totalOffline = Traccar::join('vehicles', 'traccars.vehicle_id', '=', 'vehicles.id')
        //                 ->where('vehicles.customer_id', $customer_id)
        //                 ->where('active', 1)
        //                 ->where('traccars.status', 'mati')
        //                 ->whereNotNull('traccars.latitude')
        //                 ->whereNotNull('traccars.longitude')
        //                 ->count();

        //     $totalAck = Traccar::join('vehicles', 'traccars.vehicle_id', '=', 'vehicles.id')
        //                 ->where('vehicles.customer_id', $customer_id)
        //                 ->where('active', 1)
        //                 ->where('traccars.status', 'diam')
        //                 ->whereNotNull('traccars.latitude')
        //                 ->whereNotNull('traccars.longitude')
        //                 ->count();

        //     $totalEngine = Traccar::join('vehicles', 'traccars.vehicle_id', '=', 'vehicles.id')
        //                 ->where('vehicles.customer_id', $customer_id)
        //                 ->where('active', 1)
        //                 ->where('traccars.status', 'berhenti')
        //                 ->whereNotNull('traccars.latitude')
        //                 ->whereNotNull('traccars.longitude')
        //                 ->count();    
                        
        //     $maxSpeedRecord = Traccar::join('vehicles', 'traccars.vehicle_id', '=', 'vehicles.id')
        //                 ->where('vehicles.customer_id', $customer_id)
        //                 ->whereNotNull('traccars.latitude')
        //                 ->whereNotNull('traccars.longitude')
        //                 ->orderBy('traccars.speed', 'desc')
        //                 ->first(['traccars.speed', 'traccars.no_pol']);

        //             if ($maxSpeedRecord) {
        //                 $maxSpeed = $maxSpeedRecord->speed;
        //                 $noPol = $maxSpeedRecord->no_pol;
        //             } else {
        //                 $maxSpeed = 'not found';
        //                 $noPol = 'not found';
        //             }
        // }
        
        
        // $totalCustomer = Customer::where('status', 1)->count();        

        // Return the formatted data as JSON response
        return response()->json([
            'mapData' => $vehicleMapData,
            'totalVehicles' => $totalVehicles,
            'totalOnline' => $totalOnline,
            'totalOffline' => $totalOffline,
            'totalAck' => $totalAck,
            'totalEngine' => $totalEngine,
            'maxSpeed' => $maxSpeed,
            'noPol' => $noPol,
            // 'totalCustomer' => $totalCustomer,
            // 'depotMapData' => $depotMapData,
            // 'customerMapData' => $customerMapData
        ]);
    }


  
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $item = Traccar::findOrFail($id);

        return view('pages.traccars.view')->with([
            'item' => $item
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $item = Traccar::findOrFail($id);

        return view('pages.traccars.edit')->with([
            'item' => $item
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    
     public function update(Request $request, $id)
    {
        // Validasi data request
        $validatedData = $request->validate([
            'geofence' => 'required|max:255',
            'geofence_name' => 'required|max:255',
        ]);

        // Menemukan item berdasarkan ID atau melempar error jika tidak ditemukan
        $item = Traccar::findOrFail($id);

        // Mengupdate item dengan data yang sudah divalidasi
        $item->update($validatedData);

        // Menyimpan pesan sukses dalam sesi
        session()->flash('pesan', 'ID ' . $id . ' berhasil di update.');

        // Mengarahkan kembali ke halaman daftar
        return redirect('/list-traccars');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    
}
