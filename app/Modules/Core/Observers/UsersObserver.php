<?php

namespace App\Modules\Core\Observers;

use App\Modules\Links\Models\Group;
use Illuminate\Database\Eloquent\Model;

class UsersObserver
{
    public function updated(Model $model)
    {
        // ...
    }

    public function created(Model $model)
    {
        $linkGroup = Group::create([
            'name' => $model->id . ' ' . $model->name . ' Default',
            'description' => 'Default group for user - ' . $model->name,
        ]);
        $linkGroup->users()->attach($model->id);
    }

    public function deleted(Model $model)
    {
        // ...
    }

    public function forceDeleted(Model $model)
    {
        // ...
    }
}
