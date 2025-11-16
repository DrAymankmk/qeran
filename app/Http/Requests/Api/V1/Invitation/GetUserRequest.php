<?php

namespace App\Http\Requests\Api\V1\Invitation;

use App\Helpers\Constant;
use App\Services\RespondActive;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class GetUserRequest extends FormRequest
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
            'seen' => ['nullable',Rule::in([
                Constant::SEEN_STATUS['not in the app'],
                Constant::SEEN_STATUS['in app'],
                Constant::SEEN_STATUS['seen'],
                Constant::SEEN_STATUS['scanned'],
                Constant::SEEN_STATUS['all not attended'],
                Constant::SEEN_STATUS['delivered'],
                Constant::SEEN_STATUS['Sent'],
                Constant::SEEN_STATUS['accepted'],
                Constant::SEEN_STATUS['declined'],
                Constant::SEEN_STATUS['did not attend'],
            ])],

        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(RespondActive::clientError(
            RespondActive::stringifyErrors($validator->errors())
        ));
    }
}
