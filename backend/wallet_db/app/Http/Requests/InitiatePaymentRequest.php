<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InitiatePaymentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'toDocument' => 'required|string|min:5|max:50',
            'toPhone' => 'required|string|min:10|max:30',
            'amount' => 'required|numeric|min:1',
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
            'toDocument.required' => 'The toDocument is required.',
            'toDocument.string' => 'The toDocument must be a string.',
            'toDocument.min' => 'The minimum length for the names is 3 characters.',
            'toDocument.max' => 'The maximum length of the toDocument is 50 characters.',
            'toPhone.required' => 'The toPhone is required.',
            'toPhone.string' => 'The toPhone must be a string.',
            'toPhone.min' => 'The minimum length of the toPhone is 10 characters.',
            'toPhone.max' => 'The maximum length of the toPhone is 30 characters.',
            'amount.required' => 'The amount is required.',
            'amount.numeric' => 'The amount must a number.',
            'amount.min' => 'The minimum amount to send is 1.',
        ];
    }
}
