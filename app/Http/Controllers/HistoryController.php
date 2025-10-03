<?php

namespace App\Http\Controllers;

use App\Models\History;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\HistoryRequest;
use App\Models\Geofence;
use App\Models\Vehicle;

class HistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customer_id = auth()->user()->customer_id;
                
        //if ($customer_id == 1) {
            $vehicles = Vehicle::where('status', 1)
                    ->get();
       // } else {
         //   $vehicles = Vehicle::where('customer_id', $customer_id)
         //           ->where('status', 1)
         //           ->get();
        //}
        
        return view('pages.histories.index')->with([
            'vehicles' => $vehicles
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
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

    public function history()
    {
        return view('pages.histories.history');
    }

    public function getDataHistory()
    {
        $data = History::query()->orderBy('id', 'desc');

        return DataTables::of($data)->make(true);
    }

    

    // public function getMapData(HistoryRequest $request) {
        
    //     $data = $request->all();

    //     $start_date = $data['start_date'];
    //     $end_date = $data['end_date'];
    //     $no_pol = $data['no_pol'];

        
    //     // Membuat query dengan filter
    //     $query = History::whereBetween('time', [$start_date, $end_date]);

    //     if ($no_pol) {
    //         $query->where('no_pol', $no_pol);
    //     }

    //     $data = $query->get();

    //     $depotItems = Depo::all();
        
    //     $depotMapData = [];

    //     // Format the retrieved depot data
    //     foreach ($depotItems as $depot) {
    //         $depotMapData[] = [
    //             'id' => $depot->id,
    //             'polygon' => $depot->polygon,
    //             'name' => $depot->name,
    //             'address' => $depot->address,
    //             'phone' => $depot->phone
    //         ];
    //     }

    //     $customerItems = Customer::all();
        
    //     $customerMapData = [];

    //     // Format the retrieved depot data
    //     foreach ($customerItems as $customer) {
    //         $customerMapData[] = [
    //             'id' => $customer->id,
    //             'polygon' => $customer->polygon,
    //             'name' => $customer->name,
    //             'address' => $customer->address,
    //             'phone' => $customer->phone
    //         ];
    //     }
                
    //     return response()->json($data);
    // }

    public function getMapData(HistoryRequest $request) {
        //$customer_id = auth()->user()->customer_id;

        $data = $request->all();
    
        $start_date = $data['start_date'];
        $end_date = $data['end_date'];
        $no_pol = $data['no_pol'];
    
        // Membuat query dengan filter
        // $query = History::whereBetween('time', [$start_date, $end_date]);
    
        // if ($no_pol) {
        //     $query->where('no_pol', $no_pol);
        // }
    
        // $histories = $query->get(); var_dump($histories); exit;

        // $histories = History::join('vehicles', 'histories.vehicle_id', '=', 'vehicles.id')
        //     ->whereBetween('histories.time', [$start_date, $end_date])
        //     ->when($no_pol, function ($query) use ($no_pol) {
        //         $query->where('histories.no_pol', $no_pol);
        //     })
        //     ->select('histories.*', 'vehicles.vehicle_type') // Memilih field dari tabel histories dan vehicles
        //     ->get(); 

        $histories = History::join('vehicles', 'histories.vehicle_id', '=', 'vehicles.id')
                ->whereBetween('histories.time', [$start_date, $end_date])
                ->when($no_pol, function ($query) use ($no_pol) {
                    $query->where('histories.no_pol', $no_pol);
                })
                ->select('histories.time', 'histories.latitude', 'histories.longitude', 'histories.course', 'histories.status', 'vehicles.vehicle_type') // Memilih field dari tabel histories dan vehicles
                ->orderBy('histories.time', 'asc') // Mengurutkan berdasarkan time (ASC)
                ->get();
    
        // $geoItems = Geofence::where('customer_id', $customer_id)->get();
        $geofenceMapData = [];
    
        // Format the retrieved geofence data
        // foreach ($geoItems as $geo) {
        //     $geofenceMapData[] = [
        //         'id' => $geo->id,
        //         'latlong' => $geo->latlong,
        //         'name' => $geo->name,
        //         'type' => $geo->type,
        //         'radius' => $geo->radius
        //     ];
        // }
    
        // $customerItems = Customer::all();
        // $customerMapData = [];
    
        // // Format the retrieved customer data
        // foreach ($customerItems as $customer) {
        //     $customerMapData[] = [
        //         'id' => $customer->id,
        //         'polygon' => $customer->polygon,
        //         'name' => $customer->name,
        //         'address' => $customer->address,
        //         'phone' => $customer->phone
        //     ];
        // }
    
        // Mengemas semua data ke dalam satu array asosiatif
        $data = [
            'data' => $histories,
            //'geofenceMapData' => $geofenceMapData,
            //'customerMapData' => $customerMapData
        ];
                
        return response()->json($data);
    }
    
    
    
}
