<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class PermissionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $permissions = Permission::where('guard_name', 'admin')->orderBy('name')->paginate(15);
        return view('pages.permissions.index', compact('permissions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.permissions.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name,NULL,id,guard_name,admin'],
        ], [
            'name.required' => __('admin.name-required'),
            'name.unique' => __('admin.permission-name-already-exists'),
        ]);

        try {
            Permission::create([
                'name' => $request->name,
                'guard_name' => 'admin'
            ]);

            return redirect()->route('permissions.index')->with('success', __('admin.created-successfully'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('admin.error-occurred'))->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $permission = Permission::where('guard_name', 'admin')->with('roles')->findOrFail($id);
        return view('pages.permissions.show', compact('permission'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $permission = Permission::where('guard_name', 'admin')->findOrFail($id);
        return view('pages.permissions.edit', compact('permission'));
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
        $permission = Permission::where('guard_name', 'admin')->findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name,' . $id . ',id,guard_name,admin'],
        ], [
            'name.required' => __('admin.name-required'),
            'name.unique' => __('admin.permission-name-already-exists'),
        ]);

        try {
            $permission->update([
                'name' => $request->name
            ]);

            return redirect()->route('permissions.index')->with('success', __('admin.updated-successfully'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('admin.error-occurred'))->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $permission = Permission::where('guard_name', 'admin')->findOrFail($id);

        // Check if permission is assigned to any role
        if ($permission->roles()->count() > 0) {
            return redirect()->route('permissions.index')->with('error', __('admin.cannot-delete-permission-with-roles'));
        }

        $permission->delete();

        return redirect()->route('permissions.index')->with('success', __('admin.deleted-successfully'));
    }
}























































