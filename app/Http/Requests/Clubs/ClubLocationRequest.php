<?php

declare(strict_types=1);

namespace App\Http\Requests\Clubs;

use App\Models\ClubLocation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

final class ClubLocationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if ($this->routeIs('locations.store')) {
            return $this->user()->can('create', ClubLocation::class);
        }

        return $this->user()->can('update', $this->route('location'));
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
            'street_address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'state' => ['required', 'string', 'max:255'],
            'zip' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
        ];
    }
}
