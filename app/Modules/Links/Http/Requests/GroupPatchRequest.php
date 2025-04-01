<?php

namespace App\Modules\Links\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *      schema="PatchGroupRequest",
 *      @OA\Property(
 *          property="name",
 *          type="string",
 *          example="Example group"
 *      ),
 *     @OA\Property(
 *          property="description",
 *          type="string",
 *          example="Example description"
 *      ),
 *     @OA\Property(
 *          property="count",
 *          type="integer",
 *          example="10"
 *      )
 * )
 */
class GroupPatchRequest extends FormRequest
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
            'description' => ['string', 'max:254'],
            'count' => ['integer']
        ];
    }
}
