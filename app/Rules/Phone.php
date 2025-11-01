<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;

final class Phone implements ValidationRule
{
    public function __construct(
        private readonly ?string $defaultRegion = 'US'
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $phoneUtil = PhoneNumberUtil::getInstance();

        try {
            $number = $phoneUtil->parse($value, $this->defaultRegion);
            if (! $phoneUtil->isValidNumber($number)) {
                $fail('The :attribute must be a valid phone number.');
            }
        } catch (NumberParseException) {
            $fail('The :attribute must be a valid phone number.');
        }
    }
}
