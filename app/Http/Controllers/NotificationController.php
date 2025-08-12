<?php

namespace App\Http\Controllers;

use App\Exports\Notification as ExportsNotification;
use App\Models\Notification;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\NotificationRequest;
use Yajra\DataTables\Facades\DataTables;
use App\Exports\DataExport;


class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $items = Notification::all();
        
        // return view('pages.notification.index')->with([
        //     'items' => $items
        // ]);
        return view('pages.notification.index');
    }

    public function getData()
    {
        $data = Notification::query()->orderBy('id', 'desc');

        return DataTables::of($data)
        ->addColumn('actions', function($item) {
            return '<a href="' . route('notification.show', $item->id) . '" class="btn btn-icon btn-label-primary waves-effect" data-bs-toggle="tooltip" data-bs-placement="top" title="Lihat Data"><i class="mdi mdi-eye-outline"></i></a>
            <button type="button" class="btn btn-icon btn-label-danger waves-effect deleteBtn" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" data-url="' . route('notification.destroy', $item->id) . '"><i class="mdi mdi-delete-circle"></i></button>';

        })
        ->rawColumns(['actions'])
        ->make(true);
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
        $item = Notification::findOrFail($id);

        // Periksa apakah statusnya belum 'dibaca'
        if ($item->status !== 'dibaca') {
            // Jika belum 'dibaca', ubah status menjadi 'dibaca'
            $item->status = 'dibaca';
            $item->save();
        }

        return view('pages.notification.view')->with([
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
        $item = Notification::findOrFail($id);
        $item->delete();

        session()->flash('pesan', 'Pesan dengan ID ' .$id. ' berhasil dihapus.');

        return redirect()->route('notification.index');
    }


    public function unduh(NotificationRequest $request) 
    {
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');

        $data = DB::select("SELECT 
        traccar_id,
        time as tanggal,
        no_pol,
        message as pesan,
        status
        FROM notifications             
        WHERE
            time BETWEEN ? AND ?", [$start_date, $end_date]);

        return Excel::download(new ExportsNotification($data), 'pesan.xlsx');
    }
    
}
