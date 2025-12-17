<?php

namespace App\Services;

use App\Enums\SystemDefinedRole;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class TeamService
{
    public function list($data = [], $is_query_only = false, $is_paginated = true)
    {
        $order = "asc";

        $query = Team::query();

        $query = $query->with([]);

        //filter options
        if (isset($data['name'])) {
            $query->where('name', $data['name']);
        }

        if (array_key_exists('status', $data)) {
            $query->where('status', $data['status']);
        }

        if (isset($data['created_by'])) {
            $query->where('created_by', $data['created_by']);
        }

        if (isset($data['updated_by'])) {
            $query->where('updated_by', $data['updated_by']);
        }

        if ($is_query_only === true) {
            return $query;
        }

        if ($is_paginated === true) {
            $item_per_page = isset($data['item_per_page']) ? $data['item_per_page'] : config('constants.pagination.per_page');
            $teams = $query->paginate($item_per_page)->appends($data);
            $teams->pagination_summary = get_pagination_summary($teams);
        } else {
            $teams = $query->get();
        }

        return $teams;
    }

    public function create(array $data)
    {
        return Team::create($data);
    }

    public function update(Team $model, array $data)
    {
        return $model->update($data);
    }

    public function delete(Team $model)
    {
        return $model->delete();
    }

    public function find($id)
    {
        return Team::findOrFail($id);
    }

    public function onboardWithMinimumRoles(array $data): User
    {
        return DB::transaction(function () use ($data) {

            $name     = $data['name'];
            $email    = $data['email'];
            $password = $data['password'];

            if (User::where('email', $email)->exists()) {
                return User::where('email', $email)->first();
            }

            $team = Team::updateOrCreate(
                ['name' => "{$name} Team"],
                ['status' => 1]
            );

            app(PermissionRegistrar::class)
                ->setPermissionsTeamId($team->id);

            $ownerRole = Role::updateOrCreate([
                'name'    => SystemDefinedRole::OWNER,
                'team_id' => $team->id,
            ]);

            Role::updateOrCreate([
                'name'    => SystemDefinedRole::MODERATOR,
                'team_id' => $team->id,
            ]);

            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'team_id'           => $team->id,
                    'name'              => $name,
                    'password'          => Hash::make($password),
                    'email_verified_at' => now(),
                ]
            );

            $user->assignRole($ownerRole);

            return $user;
        });
    }
}
