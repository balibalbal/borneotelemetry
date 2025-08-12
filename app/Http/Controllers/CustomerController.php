<?php

namespace App\Http\Controllers;

use App\Exports\ExportCustomer;
use App\Models\Customer;
use App\Models\Province;
use App\Models\Regency;
use App\Models\Geofence;
use App\Models\Subdistrict;
use Illuminate\Http\Request;
use App\Http\Requests\CustomerRequest;
use App\Models\UrbanVillage;
use Maatwebsite\Excel\Facades\Excel;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items = Customer::all();
        return view('pages.customers.index')->with([
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
        // Ambil data provinsi dari tabel provinces
        // $provinces = Province::all();

        // Kirim data provinsi ke view create customer
        return view('pages.customers.create');
    }

    // public function getRegencies($province_id)
    // {
    //     $regencies = Regency::where('province_id', $province_id)->get();
    //     //dd($regencies);
    //     return response()->json($regencies);
    // }

    // public function getDistrict($regency_id)
    // {
    //     $districts = Subdistrict::where('regency_id', $regency_id)->get();
    //     //dd($districts);
    //     return response()->json($districts);
    // }

    // public function getVillage($district_id)
    // {
    //     $villages = UrbanVillage::where('subdistrict_id', $district_id)->get();
    //     //dd($districts);
    //     return response()->json($villages);
    // }

    // public function getPostalCode($village_id)
    // {
    //     $villages = UrbanVillage::where('id', $village_id)->get();
    //     //dd($districts);
    //     return response()->json($villages);
    // }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CustomerRequest $request)
    {
        // Ambil data dari form request
        $data = $request->all();

        // Simpan data customer 
        Customer::create($data);
   
        session()->flash('pesan', 'Data berhasil disimpan.');
        return redirect()->route('customers.index');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $item = Customer::findOrFail($id);

        return view('pages.customers.view')->with([
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
        $item = Customer::findOrFail($id);

        return view('pages.customers.edit')->with([
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
    public function update(CustomerRequest $request, $id)
    {
        $data = $request->all();

        $item = Customer::findOrFail($id);
        $item->update($data);

        session()->flash('pesan', 'ID ' . $id . ' berhasil diupdate');
        return redirect()->route('customers.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Temukan dan hapus customer berdasarkan ID
        $customer = Customer::findOrFail($id);
        $customer->delete();

        // Hapus data di tabel geofences berdasarkan customer_id
        Geofence::where('customer_id', $id)->delete();

        // Setelah selesai, kembalikan ke halaman index
        session()->flash('pesan', 'ID ' . $id . ' berhasil dihapus beserta data di geofence');
        return redirect()->route('customers.index');
    }

    public function export() 
    {
        $fileName = 'Data_Customer_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
    
        return Excel::download(new ExportCustomer, $fileName);
    }
}
