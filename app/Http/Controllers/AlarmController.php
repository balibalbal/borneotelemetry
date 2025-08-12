<?php

namespace App\Http\Controllers;
use App\Models\Alarm;

use Illuminate\Http\Request;

class AlarmController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customer_id = auth()->user()->customer_id;
        
        if ($customer_id == 1) {
            $items = Alarm::orderBy('id', 'desc')->get();
        } else {
            $items = Alarm::where('customer_id', $customer_id)->get();
        }
        return view('pages.alarm.index')->with([
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
}
