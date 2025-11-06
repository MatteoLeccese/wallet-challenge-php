<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TopUpWalletRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'document' => 'required|string|min:5|max:50',
            'phone' => 'required|string|min:10|max:30',
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
            'document.required' => 'The document is required.',
            'document.string' => 'The document must be a string.',
            'document.min' => 'The minimum length for the names is 3 characters.',
            'document.max' => 'The maximum length of the document is 50 characters.',
            'phone.required' => 'The phone is required.',
            'phone.string' => 'The phone must be a string.',
            'phone.min' => 'The minimum length of the phone is 10 characters.',
            'phone.max' => 'The maximum length of the phone is 30 characters.',
            'amount.required' => 'The amount is required.',
            'amount.numeric' => 'The amount must be numeric.',
            'amount.min' => 'The minimum amount is 1.',
        ];
    }
}
