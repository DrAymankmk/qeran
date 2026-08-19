<?php

namespace App\Http\Requests\Api\V1\Invitation;

use App\Helpers\Constant;
use App\Services\RespondActive;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class CheckInvitationRequest extends FormRequest
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
            'code' => ['required', 'string', 'max:255'],
            'invitation_id' => ['required', Rule::exists('invitations','id')],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (! $this->filled('code')) {
            foreach (['scan_code', 'qr_code', 'qr', 'scanned_code'] as $key) {
                if ($this->filled($key)) {
                    $this->merge(['code' => (string) $this->input($key)]);

                    break;
                }
            }
        }
    }

    protected function failedValidation(Validator $validator): void
    {
        \Illuminate\Support\Facades\Log::warning('checkInvitation validation failed', [
            'errors' => $validator->errors()->toArray(),
            'payload' => $this->except(['password']),
            'has_legacy_user_id' => $this->filled('user_id'),
        ]);

        throw new HttpResponseException(RespondActive::clientError(
            RespondActive::stringifyErrors($validator->errors())
        ));
    }
}
