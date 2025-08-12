<?php

namespace App\Http\Controllers;

use App\Exports\ExportVehicle;
use App\Models\Vehicle;
use App\Http\Requests\VehicleRequest;
use App\Models\Driver;
use App\Models\Customer;
use App\Models\Group;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Http;

class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // cek account mana yang sedang login
        $customer_id = auth()->user()->customer_id;
        
        
            $items = Vehicle::join('groups', 'vehicles.group_id', '=', 'groups.id')
               ->select('vehicles.*', 'groups.name as group_name')
               ->orderBy('vehicles.id', 'desc')
               ->get();

        
        return view('pages.vehicles.index')->with([
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
        
        return view('pages.vehicles.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(VehicleRequest $request)
    {
        $data = $request->all();
        
        Vehicle::create($data);

        session()->flash('pesan', 'Data berhasil di simpan.');

        return redirect()->route('vehicles.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $item = Vehicle::findOrFail($id);

        return view('pages.vehicles.view')->with([
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
        
            $groups = Group::whereNull('deleted_at')
               ->get();
        

        $drivers = Driver::where('status', 1)
        ->get();

        $item = Vehicle::findOrFail($id);

        return view('pages.vehicles.edit')->with([
            'item' => $item, 
            'groups' => $groups,
            'drivers' => $drivers
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(VehicleRequest $request, $id)
    {
        $data = $request->all();
        $item = Vehicle::findOrFail($id);

        if ($request->hasFile('photo_head_kir')) {
            // Hapus file lama jika ada
            if ($item->photo_head_kir) {
                Storage::disk('public')->delete($item->photo_head_kir);
            }

            // Simpan file baru
            $data['photo_head_kir'] = $request->file('photo_head_kir')->store('assets/vehicle', 'public');
        }

        if ($request->hasFile('photo_chasis_kir')) {
            // Hapus file lama jika ada
            if ($item->photo_chasis_kir) {
                Storage::disk('public')->delete($item->photo_chasis_kir);
            }

            // Simpan file baru
            $data['photo_chasis_kir'] = $request->file('photo_chasis_kir')->store('assets/vehicle', 'public');
        }

        if ($request->hasFile('photo_stnk')) {
            // Hapus file lama jika ada
            if ($item->photo_stnk) {
                Storage::disk('public')->delete($item->photo_stnk);
            }

            // Simpan file baru
            $data['photo_stnk'] = $request->file('photo_stnk')->store('assets/vehicle', 'public');
        }

        if ($request->hasFile('photo_b3_klhk')) {
            // Hapus file lama jika ada
            if ($item->photo_b3_klhk) {
                Storage::disk('public')->delete($item->photo_b3_klhk);
            }

            // Simpan file baru
            $data['photo_b3_klhk'] = $request->file('photo_b3_klhk')->store('assets/vehicle', 'public');
        }

        if ($request->hasFile('photo_kartu_pengawasan_kemenhub')) {
            // Hapus file lama jika ada
            if ($item->photo_kartu_pengawasan_kemenhub) {
                Storage::disk('public')->delete($item->photo_kartu_pengawasan_kemenhub);
            }

            // Simpan file baru
            $data['photo_kartu_pengawasan_kemenhub'] = $request->file('photo_kartu_pengawasan_kemenhub')->store('assets/vehicle', 'public');
        }

        $item->update($data);

        session()->flash('pesan', 'ID ' . $id . ' berhasil di update.');

        return redirect()->route('vehicles.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $item = Vehicle::findOrFail($id);
        if ($item->photo_stnk) {
            Storage::disk('public')->delete($item->photo_stnk);
        }
        if ($item->photo_head_kir) {
            Storage::disk('public')->delete($item->photo_head_kir);
        }
        if ($item->photo_chasis_kir) {
            Storage::disk('public')->delete($item->photo_chasis_kir);
        }
        if ($item->photo_b3_klhk) {
            Storage::disk('public')->delete($item->photo_b3_klhk);
        }
        if ($item->photo_kartu_pengawasan_kemenhub) {
            Storage::disk('public')->delete($item->photo_kartu_pengawasan_kemenhub);
        }

        
        $item->delete();

        // Set pesan berhasil dihapus dalam sesi
        session()->flash('pesan', 'Kendaraan berhasil dihapus.');

        return redirect()->route('vehicles.index');
    }

    

    public function exportVehicle() 
    {
        return Excel::download(new ExportVehicle, 'Data_Kendaraan.xlsx');
    }

    public function getGroupsByCustomer($customerId)
    {
        $groups = Group::where('customer_id', $customerId)->get();
        return response()->json($groups);
    }

    
}
