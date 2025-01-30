<?php

namespace App\Modules\Core\Providers;

use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $modules = config('modules.modules');
        $path = config('modules.path');
        $base_namespace = config('modules.base_namespace');

        foreach ($modules as $module) {
            $this->loadMigrationsFrom($path . '/' . $module . '/Migrations');

            $this->loadRoutesFrom($path . '/' . $module . '/Routes.php');
        }


    }
}
