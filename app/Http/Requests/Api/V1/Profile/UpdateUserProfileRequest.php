<?php

namespace App\Http\Requests\Api\V1\Profile;

use App\Helpers\Constant;
use App\Services\RespondActive;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateUserProfileRequest extends FormRequest
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
            'name'          => ['nullable', 'string', 'max:190'],
            'phone'         => [
                'nullable',
                 Rule::unique('users')
//                 ->where('account_type', Constant::USER_TYPE['User'])
                 ->whereNot('id', auth()->id())
                     ->where('deleted_at',null),'phone:INTERNATIONAL,EG,SA'

            ],
            'country_code'  => ['nullable', 'max:6'],
            'email'         => [
                'nullable',
                Rule::unique('users')
                    ->whereNot('id', auth()->id())
                    ->where('deleted_at',null)
            ],
            'image'         => ['nullable', 'mimes:jpeg,jpg,png,gif', 'max:20000'],
            'description'  => ['nullable', 'max:150'],
            'gender'  => ['nullable', 'in:'.Constant::USER_GENDER['Male'].','.Constant::USER_GENDER['Female']
            ],

        ];
    }

//    protected function prepareForValidation()
//    {
//        if (request()->phone != auth()->user()->phone) {
//            $this->merge([
//                'verified' => 0
//            ]);
//        }
//    }
//
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(RespondActive::clientError(
            RespondActive::stringifyErrors($validator->errors())
        ));
    }
    function messages()
    {
        return [
            'phone.unique'=>__('Phone already exist')
        ];
    }
}
