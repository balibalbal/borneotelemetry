<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Geofence;
use App\Models\Traccar;
use Illuminate\Support\Facades\DB;

class HsoMonitoringController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customer_id = auth()->user()->customer_id;

        // if ($customer_id == 1) {
        //     $geofences = Geofence::where('customer_id', 3)->get();
        //     //var_dump($geofences); exit;
        // } else {
        //     $geofences = Geofence::where('customer_id', $customer_id)->get();
        // }

        $geofences = DB::table('geofences')
            ->join('customers', 'geofences.customer_id', '=', 'customers.id')
            ->select(
                'geofences.id',
                'geofences.name',
                'geofences.radius',
                'geofences.type',
                'geofences.status',
                'customers.name as customer_name',
                DB::raw('ST_AsGeoJSON(geofences.center_point) as center_point'),
                DB::raw('ST_AsGeoJSON(geofences.polygon_area) as polygon_area')
            )
            ->where('geofences.customer_id', '=', 3)
            ->whereNull('geofences.deleted_at')
            ->get();
        
        return view('pages.hso_monitoring.index')->with([
           'geofences' => $geofences
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function getDataHso() 
    {
        // cek account mana yang sedang login
        $customer_id = auth()->user()->customer_id;

        // Fetch data from the Traccar model
        if ($customer_id == 1) {
            // $vehicleItems = Traccar::whereNotNull('latitude')->whereNotNull('longitude')
            //     ->where('customer_id', 3)->get(); // customer hso (mju)
            $vehicleItems = Traccar::join('vehicles', 'traccars.vehicle_id', '=', 'vehicles.id')
            //->join('information', 'traccars.vehicle_id', '=', 'information.vehicle_id') // Join dengan tabel information
            ->where('vehicles.customer_id', 3)
            ->whereNotNull('traccars.latitude')
            ->whereNotNull('traccars.longitude')
            // Pilih kolom yang diinginkan dari tabel traccars dan information
            ->select('traccars.id as traccar_id','traccars.*', 'traccars.address as traccar_address')
            ->get();
           
        } else {
            $vehicleItems = Traccar::join('vehicles', 'traccars.vehicle_id', '=', 'vehicles.id')
            //->join('information', 'traccars.vehicle_id', '=', 'information.vehicle_id') // Join dengan tabel information
            ->where('vehicles.customer_id', $customer_id)
            ->whereNotNull('traccars.latitude')
            ->whereNotNull('traccars.longitude')
            // Pilih kolom yang diinginkan dari tabel traccars dan information
            ->select('traccars.id as traccar_id','traccars.*', 'traccars.address as traccar_address')
            ->get();

        }
        
                
        $vehicleMapData = [];

        // Format the retrieved vehicle data
        foreach ($vehicleItems as $item) {
            $vehicleMapData[] = [
                'id' => $item->traccar_id,
                'vehicle_id'=> $item->vehicle_id,
                'no_pol'=> $item->no_pol,
                'latitude' => $item->latitude,
                'longitude' => $item->longitude,
                'name' => $item->no_pol,
                'status' => $item->status,
                'time' => $item->time,
                'course' => $item->course,
                'speed' => $item->speed,
                'ignition' => $item->ignition_status,
                //'alarm' => $item->alarmType,
                //'voltageLevel' => $item->voltageLevel,
                //'sinyal' => $item->gsmSigStrength,
                //'charging' => $item->charging,
                //'gpsTracking' => $item->gpsTracking,
                'distance' => $item->total_distance,
                // 'address' => $item->address->address ?? '-'
                'address' => $item->address
            ];
        }

        
        // Get the total count of vehicles meeting the criteria
        if ($customer_id == 1) {
            $totalVehicles = Traccar::whereNotNull('latitude')->whereNotNull('longitude')->where('hso_status', 1)->count();
            $totalOnline = Traccar::where('status', 'bergerak')->whereNotNull('latitude')->where('hso_status', 1)->whereNotNull('longitude')->count();
            $totalOffline = Traccar::where('status', 'mati')->whereNotNull('latitude')->whereNotNull('longitude')->where('hso_status', 1)->count();
            $totalAck = Traccar::where('status', 'diam')->whereNotNull('latitude')->whereNotNull('longitude')->where('hso_status', 1)->count();
            $totalEngine = Traccar::where('status', 'berhenti')->whereNotNull('latitude')->whereNotNull('longitude')->where('hso_status', 1)->count();            
        } else {        
            $totalVehicles = Traccar::join('vehicles', 'traccars.vehicle_id', '=', 'vehicles.id')
                ->where('vehicles.customer_id', $customer_id)
                ->whereNotNull('traccars.latitude')
                ->whereNotNull('traccars.longitude')
                ->count();

            $totalOnline = Traccar::join('vehicles', 'traccars.vehicle_id', '=', 'vehicles.id')
                        ->where('vehicles.customer_id', $customer_id)
                        ->where('traccars.status', 'bergerak')
                        ->whereNotNull('traccars.latitude')
                        ->whereNotNull('traccars.longitude')
                        ->count();

            $totalOffline = Traccar::join('vehicles', 'traccars.vehicle_id', '=', 'vehicles.id')
                        ->where('vehicles.customer_id', $customer_id)
                        ->where('traccars.status', 'mati')
                        ->whereNotNull('traccars.latitude')
                        ->whereNotNull('traccars.longitude')
                        ->count();

            $totalAck = Traccar::join('vehicles', 'traccars.vehicle_id', '=', 'vehicles.id')
                        ->where('vehicles.customer_id', $customer_id)
                        ->where('traccars.status', 'diam')
                        ->whereNotNull('traccars.latitude')
                        ->whereNotNull('traccars.longitude')
                        ->count();

            $totalEngine = Traccar::join('vehicles', 'traccars.vehicle_id', '=', 'vehicles.id')
                        ->where('vehicles.customer_id', $customer_id)
                        ->where('traccars.status', 'berhenti')
                        ->whereNotNull('traccars.latitude')
                        ->whereNotNull('traccars.longitude')
                        ->count();            
        }

        // $enterGeofence = DB::table('histories_geofences')
        //                 ->where('status_geofence', 1)
        //                 ->distinct('vehicle_id')
        //                 ->count();

                    // if ($maxSpeedRecord) {
                    //     $maxSpeed = $maxSpeedRecord->speed;
                    //     $noPol = $maxSpeedRecord->no_pol;
                    // } else {
                    //     $maxSpeed = 'not found';
                    //     $noPol = 'not found';
                    // }
        
             

        // Return the formatted data as JSON response
        return response()->json([
            'mapData' => $vehicleMapData,
            'totalVehicles' => $totalVehicles,
            'totalOnline' => $totalOnline,
            'totalOffline' => $totalOffline,
            'totalAck' => $totalAck,
            'totalEngine' => $totalEngine,
            //'enterGeofence' => $enterGeofence,
            //'noPol' => $noPol,
        ]);
    }
}
