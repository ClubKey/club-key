<?php

declare(strict_types=1);

use App\DTOs\PromotionDto;

it('normalizes checkbox submissions into booleans and nulls', function (): void {
    $dto = PromotionDto::fromArray([
        'name' => 'Checkbox DTO',
        'description' => 'Casts checkbox payloads correctly.',
        'publish_now' => '0',
        'publish_at' => '',
        'start_at' => '',
        'end_at' => '',
        'is_active' => '1',
        'link' => '',
    ]);

    expect($dto->publishNow)->toBeFalse();
    expect($dto->publishAt)->toBeNull();
    expect($dto->startAt)->toBeNull();
    expect($dto->endAt)->toBeNull();
    expect($dto->isActive)->toBeTrue();
    expect($dto->link)->toBeNull();
});
