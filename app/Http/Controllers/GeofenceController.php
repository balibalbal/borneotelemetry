<?php

namespace App\Http\Controllers;

use App\Models\Geofence;
use App\Http\Requests\GeofenceRequest;
use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;

class GeofenceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items = DB::table('geofences')
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
            //->where('geofences.status', 1)
            ->whereNull('geofences.deleted_at')
            ->get();

        return view('pages.geofences.index')->with([
            'items' => $items
        ]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $customers = Customer::where('status', 1)->get();
        
        return view('pages.geofences.create')->with([
            'customers' => $customers
        ]);

        //return view('pages.geofences.create');
    }

    public function baru()
    {
        $customers = Customer::where('status', 1)->get();
        
        return view('pages.geofences.create_radius')->with([
            'customers' => $customers
        ]);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    

    public function store(Request $request)
{
    $type = $request->input('type'); // 1: radius, 2: polygon

    // Data umum
    $data = [
        'customer_id' => $request->input('customer_id'),
        'name' => $request->input('name'),
        'type' => $type,
        'status' => $request->input('status'),
        'created_at' => now(),
        'updated_at' => now(),
    ];

    if ($type == 1) {
        // Sanitize dan cast angka
        $lat = (float) $request->input('latitude');
        $lng = (float) $request->input('longitude');
        $radius = (float) $request->input('radius');

        DB::table('geofences')->insert([
            ...$data,
            'center_point' => DB::raw("ST_SetSRID(ST_MakePoint($lng, $lat), 4326)::geometry"),
            'radius' => $radius,
        ]);

    } elseif ($type == 2) {
        if ($type == 2) {
            // Ambil GeoJSON dari request
            $geojson = $request->input('geojson');
        
            DB::statement(
                "INSERT INTO geofences (customer_id, name, type, status, created_at, updated_at, polygon_area)
                VALUES (?, ?, ?, ?, ?, ?, ST_SetSRID(ST_GeomFromGeoJSON(?), 4326)::geometry)",
                [
                    $request->input('customer_id'),
                    $request->input('name'),
                    $type,
                    $request->input('status'),
                    now(),
                    now(),
                    $geojson
                ]
            );

        }
        
    }

    session()->flash('pesan', 'Data berhasil disimpan.');
    return redirect()->route('geofence.index');
}



    public function simpan(Request $request)
    {
        // Validasi request
        $validatedData = $request->validate([
            'name' => 'required|string|max:150',
            'lat' => 'required',
            'longi' => 'required',
            'radius' => 'required|integer|min:100',
            'type' => 'required',
            'customer_id' => 'required',
            'status' => 'required',
        ]);

        // Gabungkan lat dan longi menjadi satu string dipisah dengan koma
        $validatedData['latlong'] = $validatedData['lat'] . ',' . $validatedData['longi'];

        // Hapus field lat dan longi dari data karena tidak diperlukan lagi
        unset($validatedData['lat'], $validatedData['longi']);

        // Simpan data ke database
        Geofence::create($validatedData);

        // Set flash message dan redirect
        session()->flash('pesan', 'Data berhasil disimpan.');
        return redirect()->route('geofence.index');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $areaField = DB::raw('CASE WHEN geofences.type = 2 THEN ST_Area(geofences.polygon_area::geography) ELSE NULL END as area');

        $item = DB::table('geofences')
            ->join('customers', 'geofences.customer_id', '=', 'customers.id')
            ->select(
                'geofences.id',
                'geofences.name',
                'geofences.radius',
                'geofences.type',
                'geofences.status',
                'customers.name as customer_name',
                DB::raw('ST_AsGeoJSON(geofences.center_point) as center_point'),
                DB::raw('ST_AsGeoJSON(geofences.polygon_area) as polygon_area'),
                $areaField
            )
            ->where('geofences.id', $id)
            ->first();

        return view('pages.geofences.view')->with([
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
        $item = Geofence::findOrFail($id);

        return view('pages.geofences.edit')->with([
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
        $data = $request->all();

        $item = Geofence::findOrFail($id);
        $item->update($data);

        session()->flash('pesan', 'ID ' . $id . ' berhasil di update.');

        return redirect()->route('geofence.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $item = Geofence::findOrFail($id);
        $item->delete();

        session()->flash('pesan', 'ID ' .$id. ' berhasil dihapus.');

        return redirect()->route('geofence.index');
    }
}
