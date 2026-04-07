<?php

namespace App\Http\Requests;

use App\Rules\NotEqual;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreContactQueryRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:100',
                // At least 2 words, letters + basic name chars
                'regex:/^(?=.{3,100}$)([A-Za-z\'\-]+\s+[A-Za-z\'\-]+(?:\s+[A-Za-z\'\-]+)*)$/',
            ],
            'email' => [
                'required',
                'email:rfc',
                'max:255',
            ],
            'phone' => 'required|phone:INTERNATIONAL,MW',
            'message' => [
                'required',
                'string',
                'min:10',
                'max:2000',
                new NotEqual,
            ],
            'company' => [
                'nullable',
                'string',
                'max:150',
            ],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Enter your full name.',
            'name.regex' => 'Enter your full name with at least a first and last name.',
            'name.min' => 'Your name must be at least :min characters.',
            'name.max' => 'Your name may not be greater than :max characters.',

            'email.required' => 'Enter your email address.',
            'email.email' => 'You have entered an invalid email address.',
            'email.max' => 'Your email address is too long.',

            'phone.required' => 'Provide your phone number.',
            'phone.phone' => 'Enter a valid phone number including the country code.',

            'company.max' => 'Your company name may not be greater than :max characters.',

            'message.required' => 'Enter your message so I can help accordingly.',
            'message.min' => 'Share a bit more detail so I can help.',
            'message.max' => 'Your message is too long. Please shorten it.',
        ];
    }
}
