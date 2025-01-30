<?php

namespace App\Modules\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OauthRequest extends FormRequest
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
            'provider' => ['required', 'string', Rule::in(['google', 'facebook'])]
        ];
    }

    /**
     * @return array
     */
    public function all($keys = null)
    {
        $data = parent::all();
        $data['provider'] = $this->route('provider');

        return $data;
    }
}
