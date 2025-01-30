<?php

namespace App\Modules\Links\Services;

use App\Modules\Core\CRUDRepository;
use App\Modules\Core\Exceptions\AuthException;
use App\Modules\Core\Exceptions\DataBaseException;
use App\Modules\Core\Traits\CRUDTrait;
use App\Modules\Links\Models\Group;
use App\Modules\Users\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class GroupService
{
    use CRUDTrait;

    public function __construct()
    {
        $this->repository = new CRUDRepository(new Group());
    }

    /**
     * Проверка существования группы.
     *
     * @param int $id
     * @return Group
     * @throws DataBaseException
     */
    public function exists(int $id): Group
    {
        $group = Group::find($id);

        if (!$group) {
            throw new DataBaseException(__('errors.groups.not_found'), 404);
        }

        return $group;
    }

    /**
     * Проверяет наличие связи между группой и пользователем
     *
     * @param User $user
     * @param int $id
     * @return void
     * @throws AuthException
     */
    public function hasAccess(User $user, int $id)
    {
        if (!$user->hasExactRoles(User::ADMIN) && !in_array($id, $user->groups->pluck('id')->toArray())) {
            throw new AuthException(__('errors.groups.permissions'), 403);
        }
    }

    /**
     * @param User $user
     * @return void
     * @throws DataBaseException
     */
    public function canStore(User $user)
    {
        if ($user->groups->sum('count') >= config('constants.env.groups_per_user')) {
            throw new DataBaseException(__('errors.groups.max_count'), 403);
        }
    }

    /**
     * @param User $user
     * @return void
     * @throws AuthException
     */
    public function canChangeCount(User $user)
    {
        if (!$user->hasExactRoles(User::ADMIN)) {
            throw new AuthException(__('errors.groups.count_edit'), 403);
        }
    }

    /**
     * @param User $user
     * @return void
     * @throws AuthException
     */
    public function checkIfLast(User $user)
    {
        if (!$user->hasExactRoles(User::ADMIN)) {
            {
                $userGroups = $user->groups->pluck('id')->toArray();
                if (count($userGroups) == 1) {
                    throw new AuthException(__('errors.groups.delete_last'), 403);
                }
            }
        }
    }

    /**
     * @return JsonResponse
     */
    public function getAll()
    {
        $user = auth()->user();

        return response()->json(['data' => Cache::tags(['User:' . $user->id, 'Group'])
            ->remember(
                $user->id . '-Groups-All',
                now()->addMinutes(180),
                fn () => $user->groups()->get(['groups.id', 'groups.name', 'groups.description'])->toArray()
            )]);
    }

    /**
     * @param Group $group
     * @return void
     * @throws DataBaseException
     */
    public function checkLinksWithOnlyThisGroup(Group $group): void
    {
        foreach ($group->links as $link) {
            if ($link->groups->count() == 1) {
                throw new DataBaseException(__('errors.groups.delete_last_parent'), 403);
            }
        }
    }

    /**
     * @return void
     */
    public function flushLinksCache(): void
    {
        Cache::tags(['User:' . auth()->user()->id])->flush();
    }

    /**
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function adminIndex()
    {
        $query = Group::query();

        if (request('search') && request('searchBy')) {
            $query->where(request('searchBy'), 'ILIKE', '%' . request('search') . '%');
        }

        if (request('orderBy')) {
            $orderDir = request('dir') ?? 'asc';
            $query->orderBy(request('orderBy'), $orderDir);
        }

        $perPage = request('perPage') ?? 10;

        $cacheKey = $this->repository->generateCacheKey();

        $query->with(['users', 'links']);

        if (!request('search') && !request('searchBy')) {
            return Cache::tags(['Group', 'pagination', 'admin'])
                ->remember(
                    'Admin-Groups-All-' . $cacheKey,
                    now()->addMinutes(180),
                    fn () => $query->paginate($perPage)
                );
        } else {
            return $query->paginate($perPage);
        }
    }
}
