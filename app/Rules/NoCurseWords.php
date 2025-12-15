<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class NoCurseWords implements Rule
{
    private array $badWords;

    public function __construct()
    {
        $path = storage_path('app/bad_words.txt');

        $this->badWords = is_file($path)
            ? file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)
            : [];
    }

    public function passes($attribute, $value): bool
    {
        $value = mb_strtolower(trim((string) $value));

        foreach ($this->badWords as $word) {
            $word = mb_strtolower(trim((string) $word));

            if ($word !== '' && str_contains($value, $word)) {
                return false;
            }
        }

        return true;
    }

    public function message(): string
    {
        return 'Deze naam bevat ongepaste taal.';
    }
}
