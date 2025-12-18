<?php

namespace App\Http\Controllers;

use App\DataTables\RolesDataTable;
use App\Enums\SystemPermission;
use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Services\RoleService;
use App\Models\Role;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function __construct(private RoleService $service) {}

    public function index(RolesDataTable $dataTable)
    {
        return $dataTable->render('roles.index');
    }

    public function create()
    {
        $permission_groups = $this->permissionByGroup();

        return view('roles.create', compact('permission_groups'));
    }

    public function store(StoreRoleRequest $request)
    {
        try {
            DB::beginTransaction();

            $role = $this->service->create($request->validated());

            $role->syncPermissions($request->permissions ?? []);

            DB::commit();

            return redirect()
                ->route('roles.index')
                ->with('success', 'Role created successfully.');
        } catch (Exception | QueryException $ex) {
            log_exception($ex, 'Create Role Error');

            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Failed to create Role. Try again later');
        }
    }

    public function edit(Role $role)
    {
        $permission_groups = $this->permissionByGroup();

        $assigned_permissions = $role->permissions()->pluck('name')->toArray();

        return view('roles.edit', compact('role', 'permission_groups', 'assigned_permissions'));
    }

    public function update(UpdateRoleRequest $request, Role $role)
    {

        try {
            DB::beginTransaction();

            $this->service->update($role, $request->validated());;

            $role->syncPermissions($request->permissions ?? []);

            DB::commit();

            return redirect()
                ->route('roles.index')
                ->with('success', 'Role updated.');
        } catch (Exception | QueryException $ex) {
            log_exception($ex, 'Update Role Error');

            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Failed to update Role. Try again later');
        }
    }

    public function destroy(Role $role)
    {
        $this->service->delete($role);

        return back()->with('success', 'Role deleted.');
    }

    private function permissionByGroup()
    {
        $permissions = Permission::query()
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return $permissions
            ->groupBy(function ($permission) {
                return $this->resolvePermissionGroup($permission->name);
            });
    }

    private function resolvePermissionGroup(string $permission): string
    {
        return match (true) {
            str_starts_with($permission, 'user.') => 'User',
            str_starts_with($permission, 'role.') => 'Role',
            str_starts_with($permission, 'store.') => 'Store',
            str_starts_with($permission, 'order.') => 'Order',
            str_starts_with($permission, 'courier_paid_invoice.') => 'Courier Paid Invoice',
            str_starts_with($permission, 'report.') => 'Report',
            default => 'Other',
        };
    }
}
