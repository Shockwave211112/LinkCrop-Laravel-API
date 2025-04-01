<?php

namespace Database\Seeders;

use App\Modules\Users\Seeders\PermissionsSeeder;
use App\Modules\Users\Seeders\RoleSeeder;
use App\Modules\Users\Seeders\UserSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionsSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
        ]);
    }
}
