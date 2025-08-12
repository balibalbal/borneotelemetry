<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Customer;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
            $items = User::all();
        
        // Cek apakah user yang sedang login memiliki akses 0
        // if (auth()->user()->user_type == 0) {
        //     // Ambil semua pengguna jika user_type == 0
        //     $items = User::all();
        // } else {
        //     // Ambil pengguna dengan akses tidak sama dengan 0
        //     $items = User::where('user_type', '!=', 0)->get();
        // }

        return view('pages.users.index')->with([
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
        $customers = Customer::all();
        
        return view('pages.users.create')->with([
            'customers' => $customers
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        $data = $request->all();
        //dd($data);
        // Encrypt the password using Hash
        $data['password'] = Hash::make($request->input('password'));
        //dd($data);
        User::create($data);

        session()->flash('pesan', 'Data berhasil di simpan.');
        return redirect()->route('users.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $item = User::findOrFail($id);

        return view('pages.users.view')->with([
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
        $item = User::findOrFail($id);
        $customers = Customer::all();

        return view('pages.users.edit')->with([
            'item' => $item,
            'customers' => $customers,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, $id)
    {
        $data = $request->all();
        $data['password'] = Hash::make($request->input('password'));

        $item = User::findOrFail($id);
        $item->update($data);

        session()->flash('pesan', 'ID ' . $id . ' berhasil di update.');

        return redirect()->route('users.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $item = User::findOrFail($id);
        $item->delete();

        session()->flash('pesan', 'ID ' .$id. ' berhasil dihapus.');

        return redirect()->route('users.index');
    }
}
