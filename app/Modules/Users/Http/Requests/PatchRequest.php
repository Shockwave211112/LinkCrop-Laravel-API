<?php

namespace App\Modules\Users\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *      schema="PatchUserRequest",
 *      @OA\Property(
 *          property="email",
 *          type="string",
 *          example="example@mail.com"
 *      ),
 *     @OA\Property(
 *          property="password",
 *          type="string",
 *          example="password"
 *      ),
 *     @OA\Property(
 *          property="name",
 *          type="string",
 *          example="Example"
 *      ),
 *     @OA\Property(
 *          property="role_id",
 *          type="array",
 *          @OA\Items(
 *              type="integer",
 *              example={"1", "2", "3"}
 *          )
 *      ),
 * )
 */
class PatchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name'  => ['string', 'max:254'],
            'email' => ['email', 'max:254', 'unique:users,email,' . request('id')],
            'password' => ['string', 'max:254'],
            'role_id' => ['array', 'exists:roles,id'],
        ];
    }
}
