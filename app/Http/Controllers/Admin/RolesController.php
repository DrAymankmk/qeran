<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::where('guard_name', 'admin')->with('permissions')->orderBy('created_at', 'desc')->paginate(15);
        return view('pages.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permissions = Permission::where('guard_name', 'admin')->orderBy('name')->get();
        $permissionGroups = $this->groupPermissions($permissions);
        return view('pages.roles.create', compact('permissionGroups', 'permissions'));
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
            'name' => ['required', 'string', 'max:255', 'unique:roles,name,NULL,id,guard_name,admin'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id']
        ], [
            'name.required' => __('admin.name-required'),
            'name.unique' => __('admin.role-name-already-exists'),
            'permissions.array' => __('admin.permissions-must-be-array'),
        ]);

        DB::beginTransaction();
        try {
            $role = Role::create([
                'name' => $request->name,
                'guard_name' => 'admin'
            ]);

            if ($request->has('permissions')) {
                $role->syncPermissions($request->permissions);
            }

            DB::commit();
            return redirect()->route('roles.index')->with('success', __('admin.created-successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
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
        $role = Role::where('guard_name', 'admin')->with('permissions')->findOrFail($id);
        return view('pages.roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role = Role::where('guard_name', 'admin')->with('permissions')->findOrFail($id);
        $permissions = Permission::where('guard_name', 'admin')->orderBy('name')->get();
        $permissionGroups = $this->groupPermissions($permissions);
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        
        return view('pages.roles.edit', compact('role', 'permissionGroups', 'permissions', 'rolePermissions'));
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
        $role = Role::where('guard_name', 'admin')->findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name,' . $id . ',id,guard_name,admin'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id']
        ], [
            'name.required' => __('admin.name-required'),
            'name.unique' => __('admin.role-name-already-exists'),
            'permissions.array' => __('admin.permissions-must-be-array'),
        ]);

        DB::beginTransaction();
        try {
            $role->update([
                'name' => $request->name
            ]);

            if ($request->has('permissions')) {
                $role->syncPermissions($request->permissions);
            } else {
                $role->syncPermissions([]);
            }

            DB::commit();
            return redirect()->route('roles.index')->with('success', __('admin.updated-successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
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
        $role = Role::where('guard_name', 'admin')->findOrFail($id);

        // Check if role is assigned to any admin
        if ($role->users()->count() > 0) {
            return redirect()->route('roles.index')->with('error', __('admin.cannot-delete-role-with-admins'));
        }

        $role->delete();

        return redirect()->route('roles.index')->with('success', __('admin.deleted-successfully'));
    }

    /**
     * Group permissions by module
     *
     * @param  \Illuminate\Support\Collection  $permissions
     * @return array
     */
    private function groupPermissions($permissions)
    {
        $groups = [];
        
        foreach ($permissions as $permission) {
            // Extract module name from permission name (e.g., 'view-dashboard' -> 'dashboard')
            $parts = explode('-', $permission->name);
            if (count($parts) >= 2) {
                // Get all parts after the first one as module name
                $module = implode('-', array_slice($parts, 1));
            } else {
                $module = 'other';
            }
            
            if (!isset($groups[$module])) {
                $groups[$module] = [];
            }
            
            $groups[$module][] = $permission;
        }
        
        // Sort groups by module name
        ksort($groups);
        
        return $groups;
    }
}

