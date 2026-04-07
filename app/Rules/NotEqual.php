<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class NotEqual implements ValidationRule
{
    /**
     * @var array<int, string>
     */
    protected array $disallowed = [
        'hi',
        'hello',
        'test',
        'asdf',
        '123',
        'message',
    ];

    /**
     * Run the validation rule.
     *
     * @param  Closure(string): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail('Provide some context I can use to help you.');
        }

        // Strip HTML and normalize whitespace
        $text = trim(preg_replace('/\s+/u', ' ', strip_tags($value) ?? ''));

        if ($text === '') {
            $fail('Provide some context I can use to help you.');
        }

        $normalized = trim(mb_strtolower($text));

        // Too short to be useful
        if (mb_strlen($normalized) < 30) {
            $fail('Provide more context I can use to help you.');
        }

        // Exact matches of common junk
        if (in_array($normalized, $this->disallowed, true)) {
            $fail('Provide some context I can use to help you.');
        }

        // All characters the same: "aaaaaa", "111111", etc.
        if (preg_match('/^(.)\1+$/u', $normalized)) {
            $fail('Provide some context I can use to help you.');
        }

        // Just numbers or just punctuation
        if (preg_match('/^\p{N}+$/u', $normalized) || preg_match('/^\p{P}+$/u', $normalized)) {
            $fail('Provide some context I can use to help you.');
        }
    }
}
