<?php

namespace App\Http\Requests\Api\V1\Auth;

use App\Services\RespondActive;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class SocialLoginUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'          => ['required', 'string', 'max:190'],
            'email'         => ['nullable', 'email', 'max:100'],
            'phone'         => ['nullable', 'max:100'],
            'objective'     => ['required', 'in:facebook,google,twitter,apple'],
            'provider_id'   => ['required', 'max:500'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(RespondActive::clientError(
            RespondActive::stringifyErrors($validator->errors())
        ));
    }
}
