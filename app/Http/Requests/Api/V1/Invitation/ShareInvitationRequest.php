<?php

namespace App\Http\Requests\Api\V1\Invitation;

use App\Services\RespondActive;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ShareInvitationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'contacts' => ['sometimes', 'array', 'min:1'],
            'contacts.*.name' => ['required_with:contacts', 'string', 'max:255'],
            'contacts.*.phone' => ['required_with:contacts', 'string', 'max:32'],
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(RespondActive::clientError(
            RespondActive::stringifyErrors($validator->errors())
        ));
    }
}
