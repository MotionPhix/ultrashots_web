<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreProjectReactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'in:like,recommended,nice,feedback'],
            'feedback' => ['nullable', 'string', 'max:1000'],
            'visitor_name' => ['nullable', 'string', 'max:255'],
            'visitor_email' => ['nullable', 'email:rfc', 'max:255'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'feedback' => $this->filled('feedback') ? trim((string) $this->input('feedback')) : null,
            'visitor_name' => $this->filled('visitor_name') ? trim((string) $this->input('visitor_name')) : null,
            'visitor_email' => $this->filled('visitor_email') ? trim((string) $this->input('visitor_email')) : null,
        ]);
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'type.required' => 'Choose a reaction before submitting.',
            'type.in' => 'Choose a supported reaction type.',
            'visitor_email.email' => 'Enter a valid email address or leave it blank.',
        ];
    }
}
