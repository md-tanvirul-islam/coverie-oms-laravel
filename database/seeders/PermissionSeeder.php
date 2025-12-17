<?php

namespace Database\Seeders;

use App\Enums\SystemPermission;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            DB::beginTransaction();

            foreach (SystemPermission::options() as $permission) {
                $permission = Permission::updateOrCreate(
                    [
                        'name' => $permission
                    ]
                );
            }

            DB::commit();

            $this->command->info("Permission has seeded.");
        } catch (Exception | QueryException $e) {
            DB::rollBack();
            $this->command->error($e->getMessage());
        }
    }
}
