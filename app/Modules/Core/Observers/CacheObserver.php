<?php

namespace App\Modules\Core\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class CacheObserver
{
    private function clearCache(Model $model)
    {
        Cache::tags(class_basename($model))->flush();
    }

    public function updated(Model $model)
    {
        $this->clearCache($model);
    }

    public function created(Model $model)
    {
        $this->clearCache($model);
    }

    public function deleted(Model $model)
    {
        $this->clearCache($model);
    }

    public function forceDeleted(Model $model)
    {
        // ...
    }
}
