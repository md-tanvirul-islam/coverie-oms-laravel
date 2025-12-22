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

        $user = $this->service->createUserAndAssignRoleTeamStore($data);

        if (! $user) {
            return redirect()
                ->route('users.index')
                ->with('error', 'Failed! Try again later');
        }

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

        $user = $this->service->updateUserAndAssignRoleTeamStore($user, $data);

        $user->employee->update([
            'name' => $data['name']
        ]);

        if (! $user) {
            return redirect()
                ->route('users.index')
                ->with('error', 'Failed! Try again later');
        }

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
