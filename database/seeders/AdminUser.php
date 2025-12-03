<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use function Symfony\Component\Clock\now;

class AdminUser extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if(!env('ADMIN_EMAIL') || !env('ADMIN_PASSWORD')) {
            return;
        }

        if(User::where('email', env('ADMIN_EMAIL'))->exists()) {
            return;
        }

        User::factory()->create([
            'name' => env('ADMIN_NAME', 'Admin') ,
            'email' => env('ADMIN_EMAIL'),
            'password' => bcrypt(env('ADMIN_PASSWORD')),
            'email_verified_at' => now(),
        ]);
    }
}
