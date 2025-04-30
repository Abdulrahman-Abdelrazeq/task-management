<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rules\Password;

class RegisterRequest extends BaseFormRequest
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
            'name' => [
                'required',
                'string',
                'max:255',
                'min:3',
                'regex:/^[\p{L}\s-]+(?:\s[\p{L}\s-]+)+$/u',
            ],
            'email' => [
                'required',
                'email:rfc,dns',
                'max:255',
                'unique:users,email',
            ],
            'password' => [
                'required',
                Password::min(8)
                    ->mixedCase()
                    ->letters()
                    ->numbers()
                    ->symbols(),
                'confirmed', 
            ],
            'password_confirmation' => 'required',
        ];
    }
}
