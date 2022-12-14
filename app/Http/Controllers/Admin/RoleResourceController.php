<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use Inertia\Inertia;
use App\Models\Permission;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class RoleResourceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Role::search(request('search'))
        ->query(fn ($query) => $query->orderBy('name'))
        ->paginate(5)
        ->withQueryString()
        ->through(fn ($role) => [
            'id' => $role->id,
            'name' => $role->name,
            'permissions' => $role->permissions->pluck('name')->toArray(),
            'created_at' => $role->created_at->isoFormat('dddd, D MMMM Y'),
            'updated_at' => $role->updated_at->isoFormat('dddd, D MMMM Y'),
        ]);

        return Inertia::render('Dashboard/Roles/Index', [
            'roles' => $data,
            'filters' => request()->only('search'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('Dashboard/Roles/Create', [
            'permissions' => Permission::query()->select(['name','id'])->get(),
        ]);
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
            'name' => 'required|string|max:255',
        ]);

        $role = Role::create([
            'name' => Str::slug($request->name),
            'guard_name' => 'web',
        ]);

        if($request->has('permissions') && count($request->get('permissions')) > 0) {
            $role->syncPermissions($request->get('permissions'));
        }

        return to_route('roles.index')->with('message', 'Role created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  Spatie\Permission\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Spatie\Permission\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role)
    {
        return Inertia::render('Dashboard/Roles/Edit', [
            'role' => $role,
            'rolePermissions' => $role->permissions->pluck('id')->toArray(),
            'allPermissions' => Permission::query()->select(['name','id'])->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Spatie\Permission\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $role->update([
            'name' => Str::slug($request->name),
        ]);

        if($request->has('permissions') && count($request->get('permissions')) > 0) {
            $role->syncPermissions($request->get('permissions'));
        }

        return to_route('roles.index')->with('message', 'Role updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Spatie\Permission\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        if(in_array($role->name, ['super-admin', 'admin'])) {
            return back()->with('error', 'You can not delete "' . $role->name . '" role');
        }

        if($role->delete()){
            return back()->with('message', 'Role deleted successfully');
        }

        return back()->with('error', 'Something went wrong, please try again.');
    }
}