<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\Device;
use App\Http\Requests\DeviceRequest;
use App\Models\DeviceType;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DeviceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customer_id = auth()->user()->customer_id;
        
        $items = Device::orderBy('id', 'desc')->get();
        
        return view('pages.devices.index')->with([
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
        //$customer_id = auth()->user()->customer_id; 

        //$groups = Area::all();
        // $customers = Customer::where('status', 1)->get();
        $vehicles = Vehicle::where('status', 1)->get();

        $modem_type = DeviceType::all();

        return view('pages.devices.create')->with([
            // 'customers' => $customers,
            'modem_type' => $modem_type,
            'vehicles' => $vehicles,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DeviceRequest $request)
    {
        $data = $request->all();

        Device::create($data);

        session()->flash('pesan', 'Data berhasil di simpan.');

        return redirect()->route('devices.index');
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
        $item = Device::findOrFail($id);

        // $customers = Customer::where('status', 1)->get();
        $vehicles = Vehicle::where('status', 1)->get();

        $modem_type = DeviceType::all();

        return view('pages.devices.edit')->with([
            // 'customers' => $customers,
            'modem_type' => $modem_type,
            'vehicles' => $vehicles,
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
    public function update(DeviceRequest $request, $id)
    {
        $data = $request->all();
        $item = Device::findOrFail($id);

        $item->update($data);

        session()->flash('pesan', 'ID ' . $id . ' berhasil di update.');

        return redirect()->route('devices.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $item = Device::findOrFail($id);

        $item->delete();

        session()->flash('pesan', 'ID ' . $id . ' berhasil di hapus.');
        return redirect()->route('devices.index');
    }
}
