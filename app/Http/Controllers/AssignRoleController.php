<?php

namespace App\Http\Controllers;

//use App\Models\Role;
//use Spatie\Permission\Contracts\Role;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use App\Models\User;

class AssignRoleController extends Controller
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
            $items = User::where('status', 1)
                ->get();
        } else {
            $items = User::where('customer_id', $customer_id)
                ->where('status', 1)
                ->get();
        }
        
        return view('pages.assign_roles.index')->with([
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
        $item = User::findOrFail($id);

        return view('pages.assign_roles.view')->with([
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
        $roles = Role::all();

        return view('pages.assign_roles.edit')->with([
            'item' => $item,
            'roles' => $roles
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $userId)
    {
        // Validasi permintaan jika diperlukan

        // Temukan pengguna berdasarkan ID
        $user = User::findOrFail($userId);

        // Dapatkan role_id dari permintaan
        $roleId = $request->input('role_id');
        //dd($roleId); exit;

        // Temukan peran berdasarkan ID
        $role = Role::findOrFail($roleId);

        // Cek apakah pengguna sudah memiliki peran tersebut
        if ($user->hasRole($role->name)) {
            session()->flash('pesan', 'Pengguna sudah memiliki peran ' . $role->name);
        } else {
            // Assign peran ke pengguna
            $user->syncRoles([$role->name]);

            // Berikan umpan balik kepada pengguna jika peran berhasil ditetapkan
            session()->flash('pesan', 'Peran ' . $role->name . ' berhasil ditetapkan kepada pengguna dengan ID ' . $user->id);
        }

        // Berikan umpan balik kepada pengguna jika peran berhasil ditetapkan
        session()->flash('pesan', 'Peran ' . $role->name . ' berhasil ditetapkan kepada pengguna dengan ID ' . $user->id);

        return redirect()->route('assign_roles.index');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($userId)
    {
        // Hapus semua role pengguna berdasarkan ID
        $user = User::find($userId);
        if ($user) {
            $user->roles()->detach();
            // Berikan pesan atau lakukan tindakan lain sesuai kebutuhan
            session()->flash('pesan', 'Role successfully deleted.');
        } else {
            // Berikan pesan jika pengguna tidak ditemukan
            session()->flash('pesan', 'User not found.');
        }

        // Redirect atau lakukan tindakan lainnya setelah penghapusan
        return redirect()->route('assign_roles.index');
    }


}
