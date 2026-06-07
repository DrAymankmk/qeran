<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreInvitationBuilderThemeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoryKeys = array_keys(config('invitation_builder.theme_categories', []));

        return [
            'name_ar' => ['required', 'string', 'max:120'],
            'name_en' => ['nullable', 'string', 'max:120'],
            'category' => ['nullable', 'string', Rule::in($categoryKeys)],
            'media_type' => ['required', 'string', Rule::in(['video', 'gif', 'image'])],
            'media' => ['required', 'file'],
            'primary_color' => ['nullable', 'string', 'regex:/^#?[0-9A-Fa-f]{6}$/'],
            'secondary_color' => ['nullable', 'string', 'regex:/^#?[0-9A-Fa-f]{6}$/'],
            'background_color' => ['nullable', 'string', 'regex:/^#?[0-9A-Fa-f]{6}$/'],
            'text_color' => ['nullable', 'string', 'regex:/^#?[0-9A-Fa-f]{6}$/'],
            'preview_color' => ['nullable', 'string', 'regex:/^#?[0-9A-Fa-f]{6}$/'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $file = $this->file('media');
            $mediaType = (string) $this->input('media_type');

            if (! $file || $mediaType === '') {
                return;
            }

            $mime = strtolower((string) $file->getMimeType());
            $extension = strtolower((string) $file->getClientOriginalExtension());

            $rules = [
                'video' => [
                    'mimes' => ['mp4', 'webm'],
                    'mimetypes' => ['video/mp4', 'video/webm'],
                    'max_kb' => 51200,
                ],
                'gif' => [
                    'mimes' => ['gif'],
                    'mimetypes' => ['image/gif'],
                    'max_kb' => 15360,
                ],
                'image' => [
                    'mimes' => ['jpg', 'jpeg', 'png', 'webp'],
                    'mimetypes' => ['image/jpeg', 'image/png', 'image/webp'],
                    'max_kb' => 10240,
                ],
            ];

            $rule = $rules[$mediaType] ?? null;

            if (! $rule) {
                return;
            }

            if ($file->getSize() > $rule['max_kb'] * 1024) {
                $validator->errors()->add('media', __('admin.ib-theme-upload-size-exceeded', [
                    'max' => (int) ($rule['max_kb'] / 1024),
                ]));
            }

            if (! in_array($extension, $rule['mimes'], true)) {
                $validator->errors()->add('media', __('admin.ib-theme-upload-invalid-extension'));

                return;
            }

            if (! in_array($mime, $rule['mimetypes'], true)) {
                $validator->errors()->add('media', __('admin.ib-theme-upload-invalid-mime'));
            }
        });
    }
}
