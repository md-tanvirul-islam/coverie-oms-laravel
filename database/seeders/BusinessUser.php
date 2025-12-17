<?php

namespace Database\Seeders;

use App\Services\TeamService;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BusinessUser extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            $name =  env('OWNER_NAME', 'Business Owner');
            $email = env('OWNER_EMAIL');
            $password = env('OWNER_PASSWORD');

            if (!$email || !$password) {
                return;
            }

            app(TeamService::class)
                ->onboardWithMinimumRoles([
                    'name'     => $name,
                    'email'    => $email,
                    'password' => $password,
                ]);

            $this->command->info("Admin User created. \nEmail: {$email} \nPassword: {$password} ");
        } catch (Exception | QueryException $e) {
            DB::rollBack();
            $this->command->error($e->getMessage());
        }
    }
}
