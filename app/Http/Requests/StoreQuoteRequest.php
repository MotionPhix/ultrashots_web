<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rules\File;

class StoreQuoteRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'company' => ['nullable', 'string', 'max:255'],
            'project_type' => ['required', 'string', 'in:web_design,web_development,mobile_app,branding,ui_ux,e_commerce,api_development'],
            'budget_range' => ['nullable', 'string', 'in:under_2k,2k_5k,5k_10k,10k_plus'],
            'timeline' => ['required', 'string', 'in:urgent,tight,standard,flexible'],
            'description' => ['required', 'string', 'min:20', 'max:2000'],
            'goals' => ['nullable', 'string', 'max:1000'],
            'target_audience' => ['nullable', 'string', 'max:255'],
            'additional_info' => ['nullable', 'string', 'max:1000'],
            'reference_files' => ['nullable', 'array'],
            'reference_files.*' => [
                File::types(['jpeg', 'jpg', 'png', 'gif', 'webp', 'pdf'])
                    ->max(10 * 1024),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Enter your name before requesting a quote.',
            'email.required' => 'Enter your email address so we can send the quote details.',
            'project_type.required' => 'Choose the kind of project you want quoted.',
            'timeline.required' => 'Choose the delivery timeline for your project.',
            'description.required' => 'Describe the project so we can generate a quote.',
            'description.min' => 'Share a little more detail so the quote is meaningful.',
        ];
    }

    /**
     * @return array<int, UploadedFile>
     */
    public function referenceFiles(): array
    {
        return array_values(array_filter($this->file('reference_files', [])));
    }
}
