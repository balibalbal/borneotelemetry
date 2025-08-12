<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Http\Requests\DriverRequest;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportDriver;


class DriverController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customer_id = auth()->user()->customer_id;
        
        if ($customer_id == 1) {
            // memanggil semua data driver dari database
            $drivers = Driver::all();
        } else {
            $drivers = Driver::where('customer_id', $customer_id)->get();
        }
        
        return view('pages.drivers.index')->with([
            'drivers' => $drivers
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.drivers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DriverRequest $request)
    {
        //dd($request->all());
        $data = $request->all();
        if ($request->hasFile('photo_ktp')) {            
            $data['photo_ktp'] = $request->file('photo_ktp')->store('assets/driver', 'public');
        }
        if ($request->hasFile('photo_certificate_driver')) {            
            $data['photo_certificate_driver'] = $request->file('photo_certificate_driver')->store('assets/driver', 'public');
        }
        if ($request->hasFile('photo_driver')) {            
            $data['photo_driver'] = $request->file('photo_driver')->store('assets/driver', 'public');
        }

        if ($request->hasFile('photo_sim')) {
            $data['photo_sim'] = $request->file('photo_sim')->store('assets/driver', 'public');
        }
        //dd($data);
        Driver::create($data);

        session()->flash('pesan', 'Data berhasil di simpan.');

        return redirect()->route('drivers.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $item = Driver::findOrFail($id);

        return view('pages.drivers.view')->with([
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
        $item = Driver::findOrFail($id);

        return view('pages.drivers.edit')->with([
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
    public function update(DriverRequest $request, $id)
    {
        $data = $request->all();
        $item = Driver::findOrFail($id);

        if ($request->hasFile('photo_driver')) {
            // Hapus file lama jika ada
            if ($item->photo_driver) {
                Storage::disk('public')->delete($item->photo_driver);
            }

            // Simpan file baru
            $data['photo_driver'] = $request->file('photo_driver')->store('assets/driver', 'public');
        }

        if ($request->hasFile('photo_ktp')) {
            // Hapus file lama jika ada
            if ($item->photo_ktp) {
                Storage::disk('public')->delete($item->photo_ktp);
            }

            // Simpan file baru
            $data['photo_ktp'] = $request->file('photo_ktp')->store('assets/driver', 'public');
        }

        if ($request->hasFile('photo_certificate_driver')) {
            // Hapus file lama jika ada
            if ($item->photo_certificate_driver) {
                Storage::disk('public')->delete($item->photo_certificate_driver);
            }

            // Simpan file baru
            $data['photo_certificate_driver'] = $request->file('photo_certificate_driver')->store('assets/driver', 'public');
        }

        if ($request->hasFile('photo_sim')) {
            // Hapus file lama jika ada
            if ($item->photo_sim) {
                Storage::disk('public')->delete($item->photo_sim);
            }

            // Simpan file baru
            $data['photo_sim'] = $request->file('photo_sim')->store('assets/driver', 'public');
        }

        $item->update($data);

        session()->flash('pesan', 'ID ' . $id . ' berhasil di update.');

        return redirect()->route('drivers.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $item = Driver::findOrFail($id);
        if ($item->photo_driver) {
            Storage::disk('public')->delete($item->photo_driver);
        }
        if ($item->photo_sim) {
            Storage::disk('public')->delete($item->photo_sim);
        }
        if ($item->photo_certificate_driver) {
            Storage::disk('public')->delete($item->photo_certificate_driver);
        }
        if ($item->photo_ktp) {
            Storage::disk('public')->delete($item->photo_ktp);
        }
        $item->delete();

        session()->flash('pesan', 'ID ' . $id . ' berhasil di hapus.');
        return redirect()->route('drivers.index');
    }

   
    public function export() 
    {
        return Excel::download(new ExportDriver, 'driverExport.xlsx');
    }
}
