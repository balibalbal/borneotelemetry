<?php

namespace App\Http\Controllers;

use App\Models\Traccar;
use Illuminate\Http\Request;
use App\Models\Geofence;
use App\Models\History;
use App\Http\Requests\HistoryRequest;

class HsoHistoryController extends Controller
{
    public function index()
    {
        //$areaId = auth()->user()->area_id;
                
        $vehicles = Traccar::where('hso_status', 1)->where('active', 1)->get();
        
        return view('pages.hso_monitoring.history')->with([
            'vehicles' => $vehicles
        ]);

    }

    public function getMapDataHSO(HistoryRequest $request) {
        $customer_id = auth()->user()->customer_id;

        $data = $request->all();
    
        $start_date = $data['start_date'];
        $end_date = $data['end_date'];
        $no_pol = $data['no_pol'];
    
        // Membuat query dengan filter
        $query = History::whereBetween('time', [$start_date, $end_date]);
    
        if ($no_pol) {
            $query->where('no_pol', $no_pol);
        }
    
        $histories = $query->get();
    
        $geoItems = Geofence::where('customer_id', 3)->get();
        $geofenceMapData = [];
    
        // Format the retrieved geofence data
        foreach ($geoItems as $geo) {
            $geofenceMapData[] = [
                'id' => $geo->id,
                'latlong' => $geo->latlong,
                'name' => $geo->name,
                'type' => $geo->type,
                'radius' => $geo->radius
            ];
        }

        // Mengemas semua data ke dalam satu array asosiatif
        $data = [
            'data' => $histories,
            'geofenceMapData' => $geofenceMapData,
        ];
                
        return response()->json($data);
    }
}
