<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConfirmPaymentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'sessionId' => 'required|integer',
            'token' => 'required|string|size:6',
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
            'sessionId.required' => 'The sessionId is required.',
            'sessionId.integer' => 'The sessionId must a number.',
            'token.required' => 'The token is required.',
            'token.string' => 'The token must be a string.',
            'token.size' => 'The length of the token must be 6 characters exactly.',
        ];
    }
}
