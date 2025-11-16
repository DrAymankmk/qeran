<?php

namespace App\Http\Requests\Api\V1\Auth;

use App\Services\RespondActive;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ChangePasswordRequest extends FormRequest
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
            'old_password'  => ['nullable', 'string', 'max:190'],
            'phone' => ['required', 'max:150', Rule::exists('users'),'phone:INTERNATIONAL,EG,SA'],

            'password'      => [
                'required',
                'string',
                Password::min(6),
//                    ->mixedCase()
//                    ->numbers(),
//                'confirmed',
                'max:190'
            ],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(RespondActive::clientError(
            RespondActive::stringifyErrors($validator->errors())
        ));
    }
}
