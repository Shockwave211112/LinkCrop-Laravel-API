<?php

namespace App\Modules\Users\Seeders;

use App\Modules\Links\Models\Group;
use App\Modules\Users\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'admin',
            'email' => config('constants.env.admin_email'),
            'password' => config('constants.env.admin_pwd'),
        ]);

        $admin->assignRole(User::ADMIN);

        $baseUser = User::factory()->create([
            'name' => 'user',
            'email' => config('constants.env.basic_user_email'),
            'password' => config('constants.env.basic_user_pwd'),
        ]);

        $baseUser->assignRole(User::BASIC_USER);
    }
}
