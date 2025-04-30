<?php

namespace App\Http\Requests;
use Illuminate\Validation\Rule;

class LoginRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email:rfc,dns',
                'max:255',
                Rule::exists('users', 'email'), 
            ],
            'password' => [
                'required',
                'string',
                'min:8',
            ],
        ];
    }
}
