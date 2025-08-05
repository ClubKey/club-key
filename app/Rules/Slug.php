<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

final class Slug implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! preg_match('/^[a-z0-9]+(-[a-z0-9]+)*$/', $value)) {
            $fail('The :attribute must be a valid slug.');
        }

        switch (mb_strtolower($value)) {
            case 'admin':
            case 'dashboard':
            case 'login':
            case 'register':
            case 'logout':
            case 'password':
            case 'settings':
            case 'members':
            case 'clubs':
            case 'locations':
            case 'rewards':
            case 'reward-levels':
            case 'cards':
            case 'card':
                $fail('The :attribute cannot be one of the reserved slugs.');
                break;
        }
    }
}
