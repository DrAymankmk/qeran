<?php

namespace App\Http\Requests\Api\V1\Auth;

use App\Models\User;
use App\Services\RespondActive;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
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
            'phone' => [
                'required_without:old_password',
                'nullable',
                'max:150',
                'phone:INTERNATIONAL,EG,SA',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if ($this->filled('old_password') || $value === null || $value === '') {
                        return;
                    }
                    if (! User::findByPhone((string) $value, $this->input('country_code'))) {
                        $fail(__('messages.otp_reset_user_not_found'));
                    }
                },
            ],
            'country_code' => ['nullable', 'max:6'],

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
