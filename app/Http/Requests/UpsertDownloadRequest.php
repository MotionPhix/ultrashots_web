<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class UpsertDownloadRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'brand' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category' => ['nullable', 'string', 'max:255'],
            'is_featured' => ['boolean'],
            'is_public' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'published_at' => ['nullable', 'date'],
            'poster_image' => [
                'nullable',
                File::image()
                    ->max(5 * 1024)
                    ->types(['jpg', 'jpeg', 'png', 'webp', 'svg']),
            ],
            'download_file' => [
                'nullable',
                File::types(['zip', 'pdf', 'txt', 'csv', 'json', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'ai', 'eps', 'psd', 'fig', 'sketch'])
                    ->max(50 * 1024),
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_featured' => $this->boolean('is_featured'),
            'is_public' => $this->boolean('is_public', true),
        ]);
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Provide a title for this download.',
            'download_file.types' => 'Upload a supported asset file.',
        ];
    }
}
