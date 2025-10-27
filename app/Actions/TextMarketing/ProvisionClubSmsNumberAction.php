<?php

declare(strict_types=1);

namespace App\Actions\TextMarketing;

use App\Models\Club;
use App\Models\ClubSmsSetting;
use App\Services\TextMarketing\TextMarketingService;
use RuntimeException;
use Vonage\Client as VonageClient;

final readonly class ProvisionClubSmsNumberAction
{
    public function __construct(
        private VonageClient $vonageClient,
        private TextMarketingService $service
    ) {}

    public function handle(Club $club, string $planIdentifier, ?string $country = null, ?string $areaCode = null): ClubSmsSetting
    {
        $country ??= (string) config('text-marketing.default_country', 'US');

        $numbersClient = $this->vonageClient->numbers();

        $searchOptions = [
            'features' => ['SMS'],
            'size' => 1,
        ];

        if ($areaCode) {
            $searchOptions['pattern'] = $areaCode;
        }

        $availableNumbers = iterator_to_array($numbersClient->search($country, $searchOptions));

        if ($availableNumbers === []) {
            throw new RuntimeException('No available Vonage numbers found for provisioning.');
        }

        $firstNumber = $availableNumbers[0];
        $msisdn = $this->extractMsisdn($firstNumber);

        if ($msisdn === '') {
            throw new RuntimeException('Vonage did not return a valid number identifier.');
        }

        $numbersClient->buy($country, $msisdn);

        $settings = $this->service->configurePlan($club, $planIdentifier);

        $settings->forceFill([
            'vonage_number' => $msisdn,
            'vonage_number_id' => $this->extractNumberId($firstNumber),
        ])->save();

        return $settings;
    }

    private function extractMsisdn(mixed $number): string
    {
        if (is_object($number) && method_exists($number, 'getMsisdn')) {
            return (string) $number->getMsisdn();
        }

        if (is_array($number)) {
            return (string) ($number['msisdn'] ?? $number['number'] ?? '');
        }

        return (string) $number;
    }

    private function extractNumberId(mixed $number): ?string
    {
        if (is_object($number) && method_exists($number, 'getId')) {
            return (string) $number->getId();
        }

        if (is_array($number)) {
            return isset($number['type']) ? (string) $number['type'] : null;
        }

        return null;
    }
}
