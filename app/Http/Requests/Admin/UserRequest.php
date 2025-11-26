<?php

namespace App\Http\Requests\Admin;

use App\Services\RespondActive;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserRequest extends FormRequest
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
            'name' => ['required', 'max:150'],
            'email' => ['nullable', 'max:160'],
            'image' => ['nullable', 'mimes:jpeg,jpg,png,gif', 'max:20000'],
            'img' => ['nullable', 'mimes:jpeg,jpg,png,gif', 'max:20000'],
            'password' => ['nullable', 'min:6'],
        ];
    }

}
