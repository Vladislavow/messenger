<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use function Symfony\Component\String\b;

class UpdateUserRequest extends FormRequest
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
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'nickname' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
            'birthdate' => 'required|date',
            'bio' => 'string|nullable'
        ];
    }
}
