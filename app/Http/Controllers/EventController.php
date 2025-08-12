<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Information;
use Illuminate\Support\Facades\Http;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
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
        //     $items = Information::all();
        // } else {
        //     $items = Information::where('customer_id', $customer_id)->get();
        // }
        return view('pages.events.index');
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

    public function listDataInformation()
    {
        $customer_id = auth()->user()->customer_id;
        
        if ($customer_id == 1) {
            $data = Information::select('information.*', 'vehicles.no_pol')
                ->leftJoin('vehicles', 'information.vehicle_id', '=', 'vehicles.id')
                ->get();
        } else {
            $data = Information::select('information.*', 'vehicles.no_pol')
                ->leftJoin('vehicles', 'information.vehicle_id', '=', 'vehicles.id')
                ->where('information.customer_id', $customer_id)
                ->get();
        }
        
        return DataTables::of($data)
            ->addColumn('actions', function($item) {
                return '<a href="' . route('events.show', $item->id) . '" class="btn btn-icon btn-label-primary waves-effect" data-bs-toggle="tooltip" data-bs-placement="top" title="Lihat Data"><i class="mdi mdi-eye-outline"></i></a>';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }
}
