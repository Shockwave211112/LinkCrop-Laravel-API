<?php

namespace App\Modules\Core\Providers;

use App\Modules\Auth\Models\ConfirmLinks;
use App\Modules\Auth\Models\RestoreLinks;
use App\Modules\Core\Observers\CacheObserver;
use App\Modules\Links\Models\Group;
use App\Modules\Links\Models\Link;
use App\Modules\Links\Models\LinkGroup;
use App\Modules\Links\Models\LinkStatistic;
use App\Modules\Users\Models\User;
use Illuminate\Support\ServiceProvider;


class GlobalObserverProvider extends ServiceProvider
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
        $models = [
            User::class,
            Link::class,
            LinkGroup::class,
            ConfirmLinks::class,
            RestoreLinks::class,
            Group::class,
            LinkStatistic::class,
        ];

        foreach ($models as $model) {
            $model::observe(CacheObserver::class);
        }
    }
}
