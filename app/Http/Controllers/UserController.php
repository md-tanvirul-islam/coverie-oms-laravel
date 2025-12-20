<?php

namespace App\Http\Controllers;

use App\DataTables\UsersDataTable;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(private UserService $service) {}

    public function index(UsersDataTable $dataTable)
    {
        return $dataTable->render('users.index');
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();

        $data['team_id'] = getPermissionsTeamId();

        $user = $this->service->create($data);

        $roles = Role::whereIn('id', $data['role_ids'])->get();

        $user->syncRoles($roles);

        $storePivotData = collect($data['store_ids'])->mapWithKeys(function ($store_id) use ($data) {
            return [
                $store_id => [
                    'team_id'   => $data['team_id'],
                    'full_data' => $data['full_data'] ?? null,
                ],
            ];
        })->toArray();

        $user->stores()->sync($storePivotData);

        return redirect()
            ->route('users.index')
            ->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();

        $this->service->update($user, $data);

        $roles = Role::whereIn('id', $data['role_ids'])->get();

        $user->syncRoles($roles);

        $storePivotData = collect($data['store_ids'])->mapWithKeys(function ($store_id) use ($data) {
            return [
                $store_id => [
                    'team_id'   => $data['team_id'],
                    'full_data' => $data['full_data'] ?? null,
                ],
            ];
        })->toArray();

        $user->stores()->sync($storePivotData);

        $user->syncRoles($roles);

        return redirect()
            ->route('users.index')
            ->with('success', 'User updated.');
    }

    public function destroy(User $user)
    {
        $this->service->delete($user);

        return back()->with('success', 'User deleted.');
    }
}
