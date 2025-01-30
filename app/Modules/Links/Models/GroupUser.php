<?php

namespace App\Modules\Links\Models;

use App\Modules\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class GroupUser extends Model
{
    use HasFactory;

    public $timestamps = false;
}
