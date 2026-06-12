<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreInvitationBuilderBlockAudioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'audio' => ['required', 'file', 'max:10240'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $file = $this->file('audio');

            if (! $file) {
                return;
            }

            $mime = strtolower((string) $file->getMimeType());
            $extension = strtolower((string) $file->getClientOriginalExtension());
            $allowedMimes = [
                'audio/mpeg',
                'audio/mp3',
                'audio/ogg',
                'audio/wav',
                'audio/x-wav',
                'audio/webm',
                'audio/mp4',
                'audio/x-m4a',
                'video/mp4',
            ];
            $allowedExtensions = ['mp3', 'mpeg', 'ogg', 'oga', 'wav', 'webm', 'm4a', 'mp4'];

            if (! in_array($extension, $allowedExtensions, true)) {
                $validator->errors()->add('audio', __('admin.ib-block-audio-invalid-extension'));
            }

            if (! in_array($mime, $allowedMimes, true)) {
                $validator->errors()->add('audio', __('admin.ib-block-audio-invalid-mime'));
            }
        });
    }
}
