<?php

namespace App\Modules\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *      schema="RolePermissionsRequest",
 *      required={"role_id", "permissions"},
 *      @OA\Property(
 *          property="role_id",
 *          type="integer",
 *          example="1"
 *      ),
 *     @OA\Property(
 *          property="permissions",
 *          type="array",
 *          @OA\Items(
 *              type="integer",
 *              example="1"
 *          )
 *      ),
 * )
 */
class RolePermissionsRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            'role_id' => ['required', 'integer', 'exists:roles,id'],
            'permissions' => ['required', 'array', 'exists:permissions,id']
        ];
    }
}
