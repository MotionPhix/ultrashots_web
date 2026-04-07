<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class BulkUpdateDownloadsRequest extends FormRequest
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
            'download_ids' => ['required', 'array', 'min:1'],
            'download_ids.*' => ['integer', 'exists:downloads,id'],
            'changes' => ['required', 'array', 'min:1'],
            'changes.is_featured' => ['sometimes', 'boolean'],
            'changes.is_public' => ['sometimes', 'boolean'],
            'changes.category' => ['sometimes', 'nullable', 'string', 'max:255'],
            'changes.published_at' => ['sometimes', 'nullable', 'date'],
        ];
    }
}
