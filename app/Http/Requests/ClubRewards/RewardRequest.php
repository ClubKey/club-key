<?php

declare(strict_types=1);

namespace App\Http\Requests\ClubRewards;

use Illuminate\Foundation\Http\FormRequest;

final class RewardRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'description' => ['required', 'string', 'max:1000'],
            'order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
