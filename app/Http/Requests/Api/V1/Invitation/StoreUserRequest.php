<?php

namespace App\Http\Requests\Api\V1\Invitation;

use App\Helpers\Constant;
use App\Services\RespondActive;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
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
            'users' => ['required', 'array', 'min:1'],
            'users.*.name.*' => ['nullable'],
            'users.*.phone.*' => ['required','max:150'],
            'users.*.country_code.*' => ['nullable', 'max:6'],
            'users.*.invitation_count.*' => ['required','min:1'],

        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(RespondActive::clientError(
            RespondActive::stringifyErrors($validator->errors())
        ));
    }
    public function totalInvitationCount()
    {
        return collect($this->users)->sum('invitation_count');
    }
}
