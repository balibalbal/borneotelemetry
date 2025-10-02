<?php

namespace App\Http\Controllers;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $user = auth()->user();
        // dd($user);
        return view('pages.setting.index');
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
    public function save(Request $request)
    {
        // Update langsung menggunakan query builder
        User::where('id', auth()->id()) // Ambil user berdasarkan id yang sedang login
            ->update(['settings' => array_merge(auth()->user()->settings ?? [], $request->all())]);

        return response()->json(['success' => true]);
    }

}
