<?php

namespace App\Http\Requests\Promotions;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

final class PromotionRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:1000'],
            'publish_now' => ['nullable', 'boolean'],
            'publish_at' => ['nullable', 'date'],
            'start_at' => ['nullable', 'date'],
            'end_at' => ['nullable', 'date'],
            'is_active' => ['nullable', 'boolean'],
            'link' => ['nullable', 'url'],
        ];
    }
}
