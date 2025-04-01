<?php

namespace App\Modules\Users\Seeders;

use App\Modules\Users\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Role::firstOrCreate([
            'id' => 1,
            'name' => User::ADMIN,
            'guard_name' => 'web',
            'description' => 'No restrictions',
            'is_default' => 0
        ]);

        $user = Role::firstOrCreate([
            'id' => 2,
            'name' => User::BASIC_USER,
            'guard_name' => 'web',
            'description' => 'Restrictions on creating groups and links',
            'is_default' => 1
        ]);

        $user->givePermissionTo('user--info');
    }
}
