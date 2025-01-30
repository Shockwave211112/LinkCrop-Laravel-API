<?php

namespace App\Modules\Users\Services;

use App\Modules\Core\CRUDRepository;
use App\Modules\Core\Traits\CRUDTrait;
use App\Modules\Users\Models\User;
use Illuminate\Http\JsonResponse;

class UserService
{
    use CRUDTrait;

    public function __construct()
    {
        $this->repository = new CRUDRepository(new User());
    }

    /**
     * @param User $user
     * @return JsonResponse
     */
    public function getInfo(User $user)
    {
        return response()->json([
            'entity' => $user->attributesToArray(),
            'roles' => $user->roles->pluck('name'),
            'groups' => $user->groups,
            'links' => $user->links
        ]);
    }
}
