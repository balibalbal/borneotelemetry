<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transmission;
use Illuminate\Support\Facades\Http;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class TransmissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customer_id = auth()->user()->customer_id;
        
        // if ($customer_id == 163) {
        //     $items = Information::all();
        // } else {
        //     //$items = Information::where('customer_id', $customer_id)->get();
        // }
        return view('pages.transmission.index');
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

    public function listDataTransmission()
    {
        $data = Transmission::select(
                    'transmissions.id',
                    'transmissions.no_pol',
                    'transmissions.information_type',
                    'transmissions.updated_at',
                    'transmissions.door',
                    'customers.name as customer_name',
                    'traccars.status',
                    'traccars.address',
                )
                ->leftJoin('customers', 'transmissions.customer_id', '=', 'customers.id')
                ->leftJoin('traccars', 'transmissions.device_id', '=', 'traccars.device_id')
                ->whereIn('transmissions.device_id', [514, 515, 516, 517, 518])
                ->get();

        return DataTables::of($data)
            ->addColumn('updated_at', function($item) {
                return \Carbon\Carbon::parse($item->updated_at)->diffForHumans();
            })
            ->addColumn('actions', function($item) {
                return '<a href="' . route('events.show', $item->id) . '" class="btn btn-icon btn-label-primary waves-effect" data-bs-toggle="tooltip" data-bs-placement="top" title="Lihat Data"><i class="mdi mdi-eye-outline"></i></a>';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

      

}
