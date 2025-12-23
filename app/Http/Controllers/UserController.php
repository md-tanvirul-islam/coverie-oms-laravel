<?php

namespace App\Http\Controllers;

use App\DataTables\UsersDataTable;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use App\Services\UserService;
use Exception;
use Illuminate\Database\QueryException;
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

        try {
            $this->service->createUserAndAssignRoleTeamStore($data);

            return redirect()
                ->route('users.index')
                ->with('success', 'User created successfully.');
        } catch (Exception | QueryException $ex) {
            log_exception($ex, "User creation failed");

            return redirect()
                ->route('users.index')
                ->with('error', 'Failed! Try again later');
        }
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();
        try {
            $user = $this->service->updateUserAndAssignRoleTeamStore($user, $data);

            $user->employee->update([
                'name' => $data['name']
            ]);

            return redirect()
                ->route('users.index')
                ->with('success', 'User update successfully.');
        } catch (Exception | QueryException $ex) {
            log_exception($ex, "User update failed");

            return redirect()
                ->route('users.index')
                ->with('error', 'Failed! Try again later.');
        }
    }

    public function destroy(User $user)
    {
        $this->service->delete($user);

        return back()->with('success', 'User deleted.');
    }
}
