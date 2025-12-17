<?php

namespace App\Http\Controllers;

use App\DataTables\RolesDataTable;
use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Services\RoleService;
use App\Models\Role;

class RoleController extends Controller
{
    public function __construct(private RoleService $service) {}

    public function index(RolesDataTable $dataTable)
    {
        return $dataTable->render('roles.index');
    }

    public function create()
    {
        return view('roles.create');
    }

    public function store(StoreRoleRequest $request)
    {
        $this->service->create($request->validated());

        return redirect()
            ->route('roles.index')
            ->with('success', 'Role created successfully.');
    }

    public function edit(Role $role)
    {
        return view('roles.edit', compact('role'));
    }

    public function update(UpdateRoleRequest $request, Role $role)
    {
        $this->service->update($role, $request->validated());

        return redirect()
            ->route('roles.index')
            ->with('success', 'Role updated.');
    }

    public function destroy(Role $role)
    {
        $this->service->delete($role);

        return back()->with('success', 'Role deleted.');
    }
}
