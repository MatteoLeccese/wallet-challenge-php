<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'document' => 'required|string|max:50',
            'names' => 'required|string|min:3|max:150',
            'email' => 'required|email|max:150',
            'phone' => 'required|string|min:10|max:30',
            'password' => 'required|string|min:6',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
    */
    public function messages(): array
    {
        return [
            'document.required' => 'The document is required.',
            'document.string' => 'The document must be a string.',
            'document.max' => 'The maximum length of the document is 50 characters.',
            'names.required' => 'The name is required.',
            'names.string' => 'The name must be a string.',
            'names.min' => 'The minimum length for the names is 3 characters.',
            'names.max' => 'The maximum length of the names is 150 characters.',
            'email.required' => 'The email is required.',
            'email.email' => '',
            'email.max' => 'The maximum length of the email is 150 characters.',
            'phone.required' => 'The phone is required.',
            'phone.string' => 'The phone must be a string.',
            'phone.min' => 'The minimum length of the phone is 10 characters.',
            'phone.max' => 'The maximum length of the phone is 30 characters.',
            'password.required' => 'The password is required.',
            'password.string' => 'The password must be a string.',
            'password.min' => 'The minimum length for the password is 6 characters.',
        ];
    }
}
