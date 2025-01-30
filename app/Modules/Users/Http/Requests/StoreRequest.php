<?php

namespace App\Modules\Users\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *      schema="StoreUserRequest",
 *      required={"email", "password", "name"},
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
 * )
 */
class StoreRequest extends FormRequest
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
            'name'  => ['required', 'string', 'max:254'],
            'email' => ['required', 'email', 'max:254', 'unique:users'],
            'password' => ['required', 'string', 'max:254', 'min:8']
        ];
    }
}
