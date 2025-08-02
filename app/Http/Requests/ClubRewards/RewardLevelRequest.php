<?php

declare(strict_types=1);

namespace App\Http\Requests\ClubRewards;

use Illuminate\Foundation\Http\FormRequest;

final class RewardLevelRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'icon_name' => ['nullable', 'string', 'max:255'],
            'min_visits_per_month' => ['required', 'integer', 'min:0'],
            'color' => ['nullable', 'string', 'max:7', 'regex:/^#[0-9A-F]{6}$/i'],
            'description' => ['nullable', 'string', 'max:1000'],
            'percentage_discount' => ['required', 'integer', 'min:0', 'max:100'],
            'order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'name',
            'icon_name' => 'icon name',
            'min_visits_per_month' => 'minimum visits per month',
            'color' => 'color',
            'description' => 'description',
            'percentage_discount' => 'percentage discount',
            'order' => 'order',
        ];
    }
}
