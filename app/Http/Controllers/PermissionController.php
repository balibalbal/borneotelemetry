<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items = Role::with('permissions')->get();
        return view('pages.permission.index')->with([
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $item = Role::findOrFail($id);
        $permissions = Permission::all();

        return view('pages.permission.edit')->with([
            'item' => $item,
            'permissions' => $permissions
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $roleId)
    {
        try {
            // Temukan peran berdasarkan ID
            $role = Role::findOrFail($roleId);

            // Dapatkan izin-izin yang dipilih dari formulir
            $permissionIds = $request->input('role_permissions');

            // Bersihkan izin-izin yang sebelumnya telah terkait dengan peran
            $role->permissions()->detach();

            // Tambahkan izin-izin yang baru dipilih ke dalam peran
            if (!empty($permissionIds)) {
                foreach ($permissionIds as $permissionId) {
                    $permission = Permission::findOrFail($permissionId);
                    $role->givePermissionTo($permission);
                }
            }

            // Berikan umpan balik kepada pengguna
            session()->flash('pesan', 'Permissions successfully updated for role ' . $role->name);
        } catch (\Throwable $th) {
            // Tangani kesalahan jika terjadi
            session()->flash('pesan', 'Failed to update permissions for role ' . $role->name);
        }

        return redirect()->route('permissions.index');
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            // Temukan peran berdasarkan ID
            $role = Role::findOrFail($id);

            // Hapus semua izin yang terkait dengan peran
            $role->revokePermissionTo($role->permissions);

            // Berikan umpan balik kepada pengguna
            session()->flash('pesan', 'All permissions successfully revoked from role ' . $role->name);
        } catch (\Throwable $th) {
            // Tangani kesalahan jika terjadi
            session()->flash('pesan', 'Failed to revoke permissions from role ' . $role->name);
        }

        return redirect()->route('permissions.index');
    }

}
