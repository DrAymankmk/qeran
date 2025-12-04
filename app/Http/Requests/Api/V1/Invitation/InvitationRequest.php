<?php

namespace App\Http\Requests\Api\V1\Invitation;

use App\Helpers\Constant;
use App\Services\RespondActive;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

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
        // Build user_id rules conditionally based on count
        $userIdRules = [
            'required_if:invitation_step,==,'.Constant::INVITATION_STEP['Invite Users'],
            'array',
            'min:1'
        ];

        // Only add max rule if count is provided and is numeric
        if ($this->has('count') && $this->count !== null && is_numeric($this->count)) {
            $userIdRules[] = 'max:'.(int)$this->count;
        }

        return [
            'invitation_type'      => ['required_if:invitation_step,==,'.Constant::INVITATION_STEP['Upload Invitation'],
                Rule::in([
                    Constant::INVITATION_TYPE['App Design'],
                    Constant::INVITATION_TYPE['Contact Design'],
                    Constant::INVITATION_TYPE['User Design'],
                ]),
            ],
            'invitation_step'=>['required'],
            'category_id'=>['nullable',Rule::exists('categories','id')],
            'description'=>['nullable'],
            'invitation_media_type'=>['required_if:invitation_type,==,'.Constant::INVITATION_TYPE['Contact Design'],
                Rule::in([
                   Constant::FILE_TYPE['Image'],
                   Constant::FILE_TYPE['Video'],
                   Constant::FILE_TYPE['Gif'],
                ])],
            'image'=>['nullable','mimes:jpeg,jpg,png,gif'],
            'video'=>['nullable','mimes:mp4'],
            'audio'=>['nullable'],
            'event_name'=>['required_if:invitation_type,==,'.Constant::INVITATION_TYPE['Contact Design']],
            'package_id'=>['nullable',Rule::exists('packages','id')],
            'price'=>['nullable'],
            'count'=>['nullable','integer','min:1'],
            'user_id'=>$userIdRules,
            'user_id.*.id'    => [
                Rule::exists('users', 'id'),
            ],
            'invitation_count'=>['required_if:invitation_step,==,'.Constant::INVITATION_STEP['Add Admin'],'min:1'],
            'admin_id'=>['required_if:invitation_step,==,'.Constant::INVITATION_STEP['Add Admin'],'array','min:1'],
            'admin_id.*.id'    => [
                Rule::exists('users', 'id'),
            ],
            'guard_id'=>['nullable','array','min:1'],
            'guard_id.*.id'    => [
                Rule::exists('users', 'id'),
            ],
            'extra_guard_id'=>['nullable','array','min:1'],
            'extra_guard_id.*.id'    => [
                Rule::exists('users', 'id'),
            ],
            'host_name'=>['nullable'],

        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(RespondActive::clientError(
            RespondActive::stringifyErrors($validator->errors())
        ));
    }
}