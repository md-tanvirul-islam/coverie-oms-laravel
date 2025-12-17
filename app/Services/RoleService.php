<?php

namespace App\Services;

use Illuminate\Support\Facades\Hash;
use App\Models\Role;

class RoleService
{
    public function create(array $data)
    {
        return Role::create([
            'name'      => $data['name'],
        ]);
    }

    public function update(Role $role, array $data)
    {
        if (isset($data['name'])) {
            $role->name  = $data['name'];
        }

        return $role->save();
    }

    public function delete(Role $role)
    {
        return $role->delete();
    }
}
