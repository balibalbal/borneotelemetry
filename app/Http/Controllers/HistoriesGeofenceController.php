<?php

namespace App\Http\Controllers;

use App\Models\HistoriesGeofence;
use Illuminate\Http\Request;

class HistoriesGeofenceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = HistoriesGeofence::orderBy('id', 'desc')->limit(500)->get();

        return view('pages.hso_monitoring.list_geofence')->with([
            'items' => $items
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
        $item = HistoriesGeofence::findOrFail($id);

        return view('pages.hso_monitoring.view_geofence')->with([
            'item' => $item
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $item = HistoriesGeofence::findOrFail($id);

        return view('pages.hso_monitoring.edit_geofence')->with([
            'item' => $item
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id) 
    {
        // Validasi status yang ingin diperbarui
        $request->validate([
            'status_geofence' => 'required|integer',
            'status_kirim' => 'required|integer',
        ]);

        $item = HistoriesGeofence::findOrFail($id);
        $item->status_geofence = $request->input('status_geofence');
        $item->status_kirim = $request->input('status_kirim');
        $item->save(); 

        session()->flash('pesan', 'ID ' . $id . ' berhasil diupdate');
        return redirect()->route('histories_geofence.index');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $hso = HistoriesGeofence::findOrFail($id);
        $hso->delete();

        // Setelah selesai, kembalikan ke halaman index
        session()->flash('pesan', 'ID ' . $id . ' berhasil dihapus');
        return redirect()->route('histories_geofence.index');
    }
}
