<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
            ],
            'password_confirmation' => ['required', 'string'],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->has('role_id')) {
            $this->merge([
                'role_id' => (int) $this->role_id,
            ]);
        }
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Name is required',
            'name.min' => 'Name must be at least 2 characters',
            'email.required' => 'Email is required',
            'email.email' => 'Please enter a valid email',
            'email.unique' => 'This email is already registered',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 8 characters',
            'password.confirmed' => 'Passwords must match',
            'role_id.required' => 'Please select a role',
            'role_id.exists' => 'The selected role is invalid',
        ];
    }
}
