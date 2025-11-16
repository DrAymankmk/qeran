<?php

namespace App\Http\Requests\Api\V1\Auth;

use App\Helpers\Constant;
use App\Services\RespondActive;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
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
            'name' => ['required', 'max:150'],
            'phone' => ['required', 'max:150'
                ,'phone:INTERNATIONAL,EG,SA'],
            'country_code' => ['required'],
            'email' => ['nullable', 'max:160'],
            'image'         => ['nullable', 'mimes:jpeg,jpg,png,gif', 'max:20000'],
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
