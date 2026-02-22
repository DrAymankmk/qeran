<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class InvitationRequest extends FormRequest
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
        $maxKb = config('app.max_invitation_upload_kb', 102400);

        return [
            'file' => [
                'nullable',
                'file',
                'max:'.$maxKb,
                'mimes:mp4,webm,ogg,mov,avi,jpeg,jpg,png,gif',
                'mimetypes:video/mp4,video/webm,video/ogg,video/quicktime,video/x-msvideo,image/jpeg,image/png,image/gif',
            ],
        ];
    }
    public function messages()
    {
        return[
            'ar.name.required'=>__('admin.name-required'),
            'ar.name.string'=>__('admin.string'),
        ];
    }
}
