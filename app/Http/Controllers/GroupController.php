<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\Customer;
use App\Http\Requests\GroupRequest;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = Group::join('customers', 'groups.customer_id', '=', 'customers.id')
              ->select('groups.*', 'customers.name as customer_name')
              ->get();

        return view('pages.group.index', [
            'items' => $items
        ]);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = Customer::where('status', 1)->get();
        
        return view('pages.group.create')->with([
            'customers' => $customers
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(GroupRequest $request)
    {
        $data = $request->all();
        //dd($data);
        Group::create($data);

        session()->flash('pesan', 'Data berhasil di simpan.');
        return redirect()->route('groups.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $item = Group::findOrFail($id);

        return view('pages.group.view')->with([
            'item' => $item
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $item = Group::findOrFail($id);
        $customers = Customer::where('status', 1)->get();

        return view('pages.group.edit')->with([
            'item' => $item,
            'customers' => $customers
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(GroupRequest $request, string $id)
    {
        $data = $request->all();

        $item = Group::findOrFail($id);
        $item->update($data);

        session()->flash('pesan', 'ID ' . $id . ' berhasil di update.');

        return redirect()->route('groups.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = Group::findOrFail($id);
        $item->delete();

        session()->flash('pesan', 'ID ' .$id. ' berhasil dihapus.');

        return redirect()->route('groups.index');
    }
}
