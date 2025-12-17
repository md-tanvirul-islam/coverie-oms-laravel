<?php

namespace Database\Seeders;

use App\Enums\SystemDefinedRole;
use App\Models\Team;
use App\Models\User;
use Exception;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\QueryException;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

use function Symfony\Component\Clock\now;

class AdminUser extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            $name =  env('ADMIN_NAME', 'Admin');
            $email = env('ADMIN_EMAIL');
            $password = env('ADMIN_PASSWORD');

            if (!$email || !$password) {
                return;
            }

            if (User::where('email', $email)->exists()) {
                return;
            }

            DB::beginTransaction();
            $team = Team::updateOrCreate(
                [
                    'name' => 'Team of Ameer'
                ],
                [
                    'status' => 1
                ]
            );

            $role = Role::updateOrCreate(
                [
                    'name' => SystemDefinedRole::ADMIN,
                    'team_id' => $team->id
                ]
            );

            $user = User::updateOrCreate([
                'email' => $email,
            ], [
                'team_id' => $team->id,
                'name' => $name,
                'password' => bcrypt($password),
                'email_verified_at' => now(),
            ]);

            setPermissionsTeamId($team->id);
            
            $user->assignRole($role);

            DB::commit();

            $this->command->info("Admin User created. \nEmail: {$email} \nPassword: {$password} ");
        } catch (Exception | QueryException $e) {
            DB::rollBack();
            $this->command->error($e->getMessage());
        }
    }
}
