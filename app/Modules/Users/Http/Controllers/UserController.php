<?php

namespace App\Modules\Users\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Exceptions\DataBaseException;
use App\Modules\Core\Traits\PermissionsTrait;
use App\Modules\Users\Http\Requests\PatchRequest;
use App\Modules\Users\Http\Requests\StoreRequest;
use App\Modules\Users\Http\Requests\PutRequest;
use App\Modules\Users\Models\User;
use App\Modules\Users\Services\UserService;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    use PermissionsTrait;

    public static $permissions = [
        ['name' => 'user--list', 'description' => 'Viewing a list of users'],
        ['name' => 'user--create', 'description' => 'Creating a new user'],
        ['name' => 'user--put', 'description' => 'Editing user data'],
        ['name' => 'user--patch', 'description' => 'Partial editing user data'],
        ['name' => 'user--delete', 'description' => 'Deleting a user'],
        ['name' => 'user--show', 'description' => 'User information by ID'],
        ['name' => 'user--info', 'description' => 'Information about the authorized user'],
    ];

    public function __construct()
    {
        $this->__constructPermissions();
    }

    /**
     * @OA\Get(
     *     path="/user",
     *     summary="Get paginated list of users.",
     *     description="Only for admins.",
     *     tags={"User"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *          response=200, description="Paginated list.",
     *          @OA\JsonContent(ref="#/components/schemas/UsersListPaginated")
     *      ),
     *     @OA\Response(response=401, description="Unauthenticated."),
     *     @OA\Response(response=403, description="Permissions error.")
     * )
     * @PermissionGuard user--list
     * @param UserService $service
     * @return mixed
     */
    public function index(UserService $service)
    {
        return $service->index();
    }

    /**
     * @OA\Post(
     *     path="/user",
     *     summary="Create new user manually.",
     *     description="Only for admins. Remark: When creating a user, a default group is generated for its links.",
     *     tags={"User"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/StoreUserRequest")
     *             )
     *      ),
     *     @OA\Response(
     *          response=200, description="Paginated list.",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Info message."),
     *              @OA\Property(property="entity", ref="#/components/schemas/User"))
     *      ),
     *     @OA\Response(response=401, description="Unauthenticated."),
     *     @OA\Response(response=403, description="Permissions error."),
     *     @OA\Response(response=422, description="Validation error."),
     * )
     * @PermissionGuard user--create
     * @param StoreRequest $request
     * @param UserService $service
     * @return JsonResponse
     * @throws \App\Modules\Core\Exceptions\DataBaseException
     */
    public function store(StoreRequest $request, UserService $service)
    {
        $data = $request->validated();

        $response = $service->store($data);
        if ($response) User::where('id', $response->getData()->entity->id)->first()->assignRole(User::BASIC_USER);

        return $response;
    }

    /**
     * @OA\Get(
     *     path="/user/{id}",
     *     summary="Show user by id.",
     *     description="Only for admins.",
     *     tags={"User"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *          description="ID of user.",
     *          in="path",
     *          name="id",
     *          required=true,
     *          example="1"
     *      ),
     *     @OA\Response(
     *          response=200, description="User object.",
     *          @OA\JsonContent(
     *              @OA\Property(property="entity", ref="#/components/schemas/User"))
     *      ),
     *     @OA\Response(response=401, description="Unauthenticated."),
     *     @OA\Response(response=403, description="Permissions error."),
     *     @OA\Response(response=404, description="User not found."),
     * )
     * @PermissionGuard user--show
     * @param int $id
     * @param UserService $service
     * @return JsonResponse
     * @throws DataBaseException
     */
    public function show(int $id, UserService $service)
    {
        return $service->show($id);
    }

    /**
     * @OA\Get(
     *     path="/user/info",
     *     summary="Show info of loginned user.",
     *     tags={"User"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *          response=200, description="Info about current user.",
     *          @OA\JsonContent(
     *              @OA\Property(property="entity", ref="#/components/schemas/User"),
     *              @OA\Property(property="roles", type="array",
     *                  @OA\Items(
     *                  type="string",
     *                  example="admin"
     *                  )
     *              ),
     *              @OA\Property(
     *                  property="groups",
     *                  type="array",
     *                  @OA\Items(ref="#/components/schemas/Group")
     *              ),
     *              @OA\Property(
     *                  property="links",
     *                  type="array",
     *                  @OA\Items(ref="#/components/schemas/Link")
     *              ),
     *          ),
     *      ),
     *     @OA\Response(response=401, description="Unauthenticated."),
     *     @OA\Response(response=403, description="Permissions error.")
     * )
     * @PermissionGuard user--info
     * @param UserService $service
     * @return JsonResponse
     */
    public function getInfo(UserService $service)
    {
        return $service->getInfo(auth()->user());
    }

    /**
     * @OA\Put(
     *     path="/user/{id}",
     *     summary="Update all user data.",
     *     description="Only for admins.",
     *     tags={"User"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *          description="ID of user.",
     *          in="path",
     *          name="id",
     *          required=true,
     *          example="1"
     *      ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/PutUserRequest")
     *             )
     *      ),
     *     @OA\Response(
     *          response=200, description="User updated.",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Info message."),
     *              @OA\Property(property="entity", ref="#/components/schemas/User"),
     *              @OA\Property(property="old_entity", ref="#/components/schemas/User"))
     *      ),
     *     @OA\Response(response=401, description="Unauthenticated."),
     *     @OA\Response(response=403, description="Permissions error."),
     *     @OA\Response(response=404, description="User not found."),
     *     @OA\Response(response=422, description="Validation error."),
     * )
     * @PermissionGuard user--put
     * @param int $id
     * @param PutRequest $request
     * @param UserService $service
     * @return JsonResponse
     */
    public function put(int $id, PutRequest $request, UserService $service)
    {
        $data = $request->validated();

        if (isset($data['role_id'])) {
            $relations = [
                'roles' => $data['role_id']
            ];
            unset($data['role_id']);
        }
        else $relations = [];

        return $service->put($id, $data, $relations);
    }

    /**
     * @OA\Patch(
     *     path="/user/{id}",
     *     summary="Partly update user data.",
     *     description="Only for admins.",
     *     tags={"User"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *          description="ID of user.",
     *          in="path",
     *          name="id",
     *          required=true,
     *          example="1"
     *      ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/PatchUserRequest")
     *             )
     *      ),
     *     @OA\Response(
     *          response=200, description="User updated.",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Info message."),
     *              @OA\Property(property="entity", ref="#/components/schemas/User"),
     *              @OA\Property(property="old_entity", ref="#/components/schemas/User"))
     *      ),
     *     @OA\Response(response=401, description="Unauthenticated."),
     *     @OA\Response(response=403, description="Permissions error."),
     *     @OA\Response(response=404, description="User not found."),
     *     @OA\Response(response=422, description="Validation error."),
     * )
     * @PermissionGuard user--patch
     * @param int $id
     * @param PatchRequest $request
     * @param UserService $service
     * @return JsonResponse
     */
    public function patch(int $id, PatchRequest $request, UserService $service)
    {
        $data = $request->validated();

        if (isset($data['role_id'])) {
            $relations = [
                'roles' => $data['role_id']
            ];
            unset($data['role_id']);
        }
        else $relations = [];

        return $service->patch($id, $data, $relations);
    }

    /**
     * @OA\Delete(
     *     path="/user/{id}",
     *     summary="Delete user.",
     *     description="Only for admins.",
     *     tags={"User"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *          description="ID of user.",
     *          in="path",
     *          name="id",
     *          required=true,
     *          example="1"
     *      ),
     *     @OA\Response(
     *          response=200, description="User deleted.",
     *          @OA\JsonContent(ref="#/components/schemas/MessageResponse")
     *      ),
     *     @OA\Response(response=401, description="Unauthenticated."),
     *     @OA\Response(response=403, description="Permissions error."),
     *     @OA\Response(response=404, description="User not found."),
     * )
     * @PermissionGuard user--delete
     * @param int $id
     * @param UserService $service
     * @return JsonResponse
     * @throws DataBaseException
     */
    public function delete(int $id, UserService $service)
    {
        return $service->delete($id);
    }
}
