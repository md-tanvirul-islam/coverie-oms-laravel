<?php

namespace App\Services;

use App\Models\User;
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
}
