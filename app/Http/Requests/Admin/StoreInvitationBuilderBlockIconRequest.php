<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreInvitationBuilderBlockIconRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'icon' => ['required', 'file', 'max:2048'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $file = $this->file('icon');

            if (! $file) {
                return;
            }

            $mime = strtolower((string) $file->getMimeType());
            $extension = strtolower((string) $file->getClientOriginalExtension());
            $allowedMimes = ['image/png', 'image/jpeg', 'image/webp', 'image/gif', 'image/svg+xml'];
            $allowedExtensions = ['png', 'jpg', 'jpeg', 'webp', 'gif', 'svg'];

            if (! in_array($extension, $allowedExtensions, true)) {
                $validator->errors()->add('icon', __('admin.ib-block-icon-invalid-extension'));
            }

            if (! in_array($mime, $allowedMimes, true)) {
                $validator->errors()->add('icon', __('admin.ib-block-icon-invalid-mime'));
            }
        });
    }
}
