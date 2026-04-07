<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ReorderDownloadsRequest extends FormRequest
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
            'downloads' => ['required', 'array', 'min:1'],
            'downloads.*.id' => ['required', 'integer', 'exists:downloads,id'],
            'downloads.*.sort_order' => ['required', 'integer', 'min:0'],
        ];
    }
}
