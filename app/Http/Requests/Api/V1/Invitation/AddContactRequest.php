<?php

namespace App\Http\Requests\Api\V1\Invitation;

use App\Services\RespondActive;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $contacts = $this->input('contacts');

        if (is_string($contacts)) {
            $decoded = json_decode($contacts, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $this->merge(['contacts' => $decoded]);

                return;
            }
        }

        if (! $this->has('contacts')) {
            $decodedBody = json_decode($this->getContent(), true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decodedBody) && isset($decodedBody['contacts'])) {
                $this->merge(['contacts' => $decodedBody['contacts']]);
            }
        }
    }

    public function rules(): array
    {
        return [
            'contacts' => ['required', 'array', 'min:1'],
            'contacts.*.name' => ['required', 'string', 'max:255'],
            'contacts.*.phone' => ['required', 'string', 'max:32'],
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(RespondActive::clientError(
            RespondActive::stringifyErrors($validator->errors())
        ));
    }
}
