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
        $data = array_filter($data, fn($value) => !is_null($value));
        
        return $role->update($data);
    }

    public function delete(Role $role)
    {
        return $role->delete();
    }
}
