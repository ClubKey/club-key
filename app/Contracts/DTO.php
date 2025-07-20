<?php

declare(strict_types=1);

namespace App\Contracts;

interface DTO
{
    /**
     * Create a DTO instance from an array.
     *
     * This method should be able to handle the data structure that is passed to it,
     * and convert it into a DTO instance.
     *
     * The array keys should match the property names of the DTO but be in snake_case, where the DTO properties are in camelCase.
     *
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self;

    /**
     * Convert the DTO to an array.
     *
     * This method should return data in a format suitable for storage directly to a model.
     * Ex: you should be able to use it in a model's `create` or `update` method. $model->update($dto->toArray());
     *
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
