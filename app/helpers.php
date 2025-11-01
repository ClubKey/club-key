<?php

declare(strict_types=1);

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

if (! function_exists('format_phone_number')) {
    /**
     * Formats a phone number into E.164 format for Vonage.
     *
     * Example:
     *   (202) 555-0198   → +12025550198
     *   +44 20 7946 0958 → +442079460958
     *
     * @param  string|null  $defaultRegion  The default country (e.g. 'US')
     */
    function format_phone_number(?string $phoneNumber, ?string $defaultRegion = 'US'): string
    {
        if (empty($phoneNumber)) {
            return '';
        }

        $phoneUtil = PhoneNumberUtil::getInstance();

        try {
            // Parse the phone number, assuming the given region if no country code is present
            $number = $phoneUtil->parse($phoneNumber, $defaultRegion);

            // Return the number in E.164 format (e.g. +15551234567)
            return $phoneUtil->format($number, PhoneNumberFormat::E164);
        } catch (NumberParseException) {
            // Fall back to a basic cleanup if parsing fails
            $digits = preg_replace('/\D+/', '', $phoneNumber);
            if (mb_strlen($digits) === 10) {
                return '+1'.$digits;
            }
            if (mb_strlen($digits) > 10 && ! str_starts_with($digits, '+')) {
                return '+'.$digits;
            }

            return $digits ?: '';
        }
    }
}
