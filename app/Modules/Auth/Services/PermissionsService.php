<?php

namespace App\Modules\Auth\Services;

use App\Modules\Core\Exceptions\DataBaseException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * @OA\Schema(
 *      schema="Permission",
 *      @OA\Property(
 *          property="id",
 *          type="integer",
 *          example="1"
 *      ),
 *      @OA\Property(
 *          property="name",
 *          type="string",
 *          example="permission--example"
 *      ),
 *      @OA\Property(
 *          property="guard_name",
 *          type="string",
 *          example="web"
 *      ),
 *      @OA\Property(
 *          property="description",
 *          type="string",
 *          example="Permission example description."
 *      ),
 *      @OA\Property(
 *          property="created_at",
 *          type="datetime",
 *          example="2023-09-27T06:16:30.000000Z"
 *      ),
 *      @OA\Property(
 *          property="updated_at",
 *          type="datetime",
 *          example="2023-09-27T06:16:30.000000Z"
 *      ),
 * )
 * @OA\Schema(
 *      schema="PermissionsList",
 *      type="array",
 *      @OA\Items(ref="#/components/schemas/Permission"),
 * )
 */
class PermissionsService
{
    public function index()
    {
        $permissions = Permission::all();

        if(!$permissions) {
            throw new DataBaseException(__('errors.unknown'), 422);
        }

        return $permissions;
    }

    public function show(int $id)
    {
        $permission = Permission::find($id);

        if(!$permission) {
            throw new DataBaseException(__('errors.permissions.not_found'), 404);
        }

        return $permission;
    }

    public function sync(array $data)
    {
        $role = Role::where('id', $data['role_id'])->first();

        $role->syncPermissions($data['permissions']);

        return response()->json([
            'message' => __('messages.permissions.updated'),
        ]);
    }
}
