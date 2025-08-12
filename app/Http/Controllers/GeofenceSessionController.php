<?php

namespace App\Http\Controllers;

use App\Models\GeofenceSession;
use Illuminate\Http\Request;

class GeofenceSessionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = GeofenceSession::orderBy('id', 'desc')->limit(500)->get();

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
        $item = GeofenceSession::findOrFail($id);

        return view('pages.hso_monitoring.view_geofence')->with([
            'item' => $item
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $item = GeofenceSession::findOrFail($id);

        return view('pages.hso_monitoring.edit_geofence')->with([
            'item' => $item
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'status_geofence' => 'required|integer',
            'status_kirim' => 'required|integer',
        ]);

        $item = GeofenceSession::findOrFail($id);
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
        $hso = GeofenceSession::findOrFail($id);
        $hso->delete();

        // Setelah selesai, kembalikan ke halaman index
        session()->flash('pesan', 'ID ' . $id . ' berhasil dihapus');
        return redirect()->route('histories_geofence.index');
    }
}
