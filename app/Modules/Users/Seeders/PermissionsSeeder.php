<?php

namespace App\Modules\Users\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $controllers = [];
        foreach (Route::getRoutes()->getRoutes() as $route) {
            $action = $route->getAction();
            if (array_key_exists('controller', $action)) $controllers[] = explode('@', $action['controller'])[0];
        }
        $controllers = collect($controllers)->unique();

        foreach ($controllers as $controller) {
            if (property_exists($controller, 'permissions')) {
                foreach ($controller::$permissions as $permission) {
                    Permission::firstOrCreate(['name' => $permission['name'], 'description' => $permission['description']]);
                };
            }
        }
    }
}
