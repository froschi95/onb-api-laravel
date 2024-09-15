<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'name' => 'required|min:5|max:150',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|max:50',
            'phone_number' => 'required|digits:10',
        ];
    }

    /**
     * Get the validation error messages that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'please enter your name',
            'name.min' => 'Name must be at least 5 chars long',
            'name.max' => 'Name must be at most 150 chars long',
            'email.required' => 'Please enter your email',
            'email.unique' => 'Email is already in use. Please enter a different email',
            'password.required' => 'Please enter your password',
            'password.min' => 'Password must be at least 8 chars long',
            'password.max' => 'Password must be at most 50 chars long',
            'phone_number.required' => 'Please enter your phone number',
            'phone_number.min' => 'Phone number must contain 10 digits',
        ];
    }
}
