<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class UploadDownloadFixRequest extends FormRequest
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
                'min:2',
                'max:100',
            ],
            'email' => [
                'required',
                'email:rfc',
                'max:255',
            ],
            'notes' => [
                'required',
                'string',
                'min:10',
                'max:1000',
            ],
            'file' => [
                'required',
                File::types(['zip', 'pdf', 'ai', 'eps', 'psd', 'xd', 'fig', 'jpg', 'jpeg', 'png', 'svg', 'webp'])
                    ->max(20 * 1024),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Enter your name so we know who sent the file.',
            'email.required' => 'Enter your email address so we can follow up.',
            'email.email' => 'Enter a valid email address.',
            'notes.required' => 'Describe what needs to be fixed before uploading the file.',
            'notes.min' => 'Share a little more detail about the issue.',
            'file.required' => 'Attach the file that needs to be fixed.',
        ];
    }
}
