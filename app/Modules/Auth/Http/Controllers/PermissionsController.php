<?php

namespace App\Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Http\Requests\RolePermissionsRequest;
use App\Modules\Auth\Services\PermissionsService;

class PermissionsController extends Controller
{
    /**
     * @OA\Get(
     *     path="/permissions",
     *     summary="Get list of permissions.",
     *     description="Only for admins.",
     *     tags={"Permissions"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *          response=200, description="List of all permissions.",
     *          @OA\JsonContent(ref="#/components/schemas/PermissionsList")
     *      ),
     *     @OA\Response(response=401, description="Unauthenticated."),
     *     @OA\Response(response=403, description="Permissions error.")
     * )
     * @param PermissionsService $service
     * @return \Illuminate\Database\Eloquent\Collection
     * @throws \App\Modules\Core\Exceptions\DataBaseException
     */
    public function index(PermissionsService $service)
    {
        return $service->index();
    }

    /**
     * @OA\Get(
     *     path="/permissions/{id}",
     *     summary="Show permission by id.",
     *     description="Only for admins.",
     *     tags={"Permissions"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *          description="ID of permission.",
     *          in="path",
     *          name="id",
     *          required=true,
     *          example="1"
     *      ),
     *     @OA\Response(
     *          response=200, description="Permission object.",
     *          @OA\JsonContent(
     *              @OA\Property(property="entity", ref="#/components/schemas/Permission"))
     *      ),
     *     @OA\Response(response=401, description="Unauthenticated."),
     *     @OA\Response(response=403, description="Permissions error."),
     *     @OA\Response(response=404, description="Permission not found."),
     * )
     * @param int $id
     * @param PermissionsService $service
     * @return mixed
     * @throws \App\Modules\Core\Exceptions\DataBaseException
     */
    public function show(int $id, PermissionsService $service)
    {
        return $service->show($id);
    }

    /**
     * @OA\Post(
     *     path="/permissions/sync",
     *     summary="Set permissions for role",
     *     tags={"Permissions"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/RolePermissionsRequest")
     *             )
     *      ),
     *     @OA\Response(
     *          response=200, description="Paginated list.",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Info message."))
     *      ),
     *     @OA\Response(response=401, description="Unauthenticated."),
     *     @OA\Response(response=403, description="Permissions error."),
     *     @OA\Response(response=422, description="Validation error."),
     * )
     * @param RolePermissionsRequest $request
     * @param PermissionsService $service
     * @return \Illuminate\Http\JsonResponse
     */
    public function sync(RolePermissionsRequest $request, PermissionsService $service)
    {
        return $service->sync($request->validated());
    }

}
