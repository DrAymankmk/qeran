<?php

namespace App\Http\Requests\Api\V1\Auth;

use App\Models\User;
use App\Services\RespondActive;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AuthenticationRequest extends FormRequest
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
            'phone' => [
                'required',
                'max:150',
                'phone:INTERNATIONAL,EG,SA',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (! User::findByPhone((string) $value, $this->input('country_code'))) {
                        $fail(__('Wrong Info!'));
                    }
                },
            ],
            'country_code' => ['required', 'max:6'],
            'password'  => ['required'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(RespondActive::clientError(
            RespondActive::stringifyErrors($validator->errors())
        ));
    }
}
