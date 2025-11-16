<?php

namespace App\Http\Requests\Api\V1\Auth;

use App\Helpers\Constant;
use App\Services\RespondActive;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class SetFirebaseTokenRequest extends FormRequest
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
            'platform'       => [
                'required', Rule::in([
                    Constant::USER_PLATFORM['Android'],
                    Constant::USER_PLATFORM['Ios'],
                ])
            ],
            'device_id'      => ['required_if:platform,'.Constant::USER_PLATFORM['Android'], 'string', 'max:150'],
            'firebase_token' => ['required', 'string', 'max:200']
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(RespondActive::clientError(
            RespondActive::stringifyErrors($validator->errors())
        ));
    }
}
