<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreInvitationBuilderBlockMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'media' => ['required', 'file', 'max:51200'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $file = $this->file('media');

            if (! $file) {
                return;
            }

            $mime = strtolower((string) $file->getMimeType());
            $extension = strtolower((string) $file->getClientOriginalExtension());
            $imageExtensions = ['png', 'jpg', 'jpeg', 'webp', 'gif'];
            $videoExtensions = ['mp4', 'webm', 'mov', 'm4v'];
            $allowedExtensions = array_merge($imageExtensions, $videoExtensions);
            $allowedMimes = [
                'image/png',
                'image/jpeg',
                'image/webp',
                'image/gif',
                'video/mp4',
                'video/webm',
                'video/quicktime',
                'video/x-m4v',
                'video/mp4v-es',
            ];

            if (! in_array($extension, $allowedExtensions, true)) {
                $validator->errors()->add('media', __('admin.ib-block-media-invalid-extension'));

                return;
            }

            if (! in_array($mime, $allowedMimes, true) && ! str_starts_with($mime, 'image/') && ! str_starts_with($mime, 'video/')) {
                $validator->errors()->add('media', __('admin.ib-block-media-invalid-mime'));

                return;
            }

            $isVideo = in_array($extension, $videoExtensions, true) || str_starts_with($mime, 'video/');
            $maxKb = $isVideo ? 51200 : 10240;
            if ($file->getSize() > $maxKb * 1024) {
                $validator->errors()->add('media', __('admin.ib-block-media-size-exceeded', [
                    'max' => (int) ($maxKb / 1024),
                ]));
            }
        });
    }
}
