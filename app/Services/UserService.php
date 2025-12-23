<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function create(array $data)
    {
        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->team_id = $data['team_id'];
        $user->password = Hash::make($data['password']);
        $user->save();

        return $user;
    }

    public function update(User $user, array $data)
    {
        $user->name  = $data['name'];
        $user->email = $data['email'];

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return $user;
    }

    public function delete(User $user)
    {
        return $user->delete();
    }

    public function createUserAndAssignRoleTeamStore(array $data)
    {
        $user = $this->create($data);

        $roles = Role::whereIn('id', $data['role_ids'])->get();

        $user->syncRoles($roles);

        $storePivotData = collect($data['store_ids'])->mapWithKeys(function ($store_id) use ($data) {
            return [
                $store_id => [
                    'team_id'   => $data['team_id'],
                    'full_data' => $data['store_full_data'][$store_id] ?? 0,
                ],
            ];
        })->toArray();

        $user->stores()->attach($storePivotData);

        return $user;
    }

    public function updateUserAndAssignRoleTeamStore(User $user, array $data)
    {
        $this->update($user, $data);

        $roles = Role::whereIn('id', $data['role_ids'])->get();

        $user->syncRoles($roles);

        $storePivotData = collect($data['store_ids'])->mapWithKeys(function ($store_id) use ($data) {
            return [
                $store_id => [
                    'team_id'   => $data['team_id'],
                    'full_data' => $data['store_full_data'][$store_id] ?? 0,
                ],
            ];
        })->toArray();

        $user->stores()->sync($storePivotData);

        return $user;
    }

    public function dropdown()
    {
        return User::join('employees', 'users.id', '=', 'employees.user_id')
            ->select(['employees.code', 'users.id as user_id', 'users.name', DB::raw("CONCAT(users.name, '(', employees.code, ')') AS name_code")])
            ->pluck('name_code', 'user_id')->toArray();
    }
}
