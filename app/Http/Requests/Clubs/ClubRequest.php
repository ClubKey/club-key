<?php

declare(strict_types=1);

namespace App\Http\Requests\Clubs;

use App\Models\Club;
use App\Rules\Slug;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

final class ClubRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        if ($this->routeIs('clubs.store')) {
            return $user?->can('create', Club::class) ?? false;
        }

        return $user?->can('update', $this->route('club')) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', new Slug, 'unique:clubs,slug'.($this->club ? ','.$this->club->id : '')],
            'logo' => ['nullable', 'image', 'max:4000'],
            'description' => ['nullable', 'string'],
            'website' => ['nullable', 'url'],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string'],
            'street_address' => ['nullable', 'string'],
            'city' => ['nullable', 'string'],
            'state' => ['nullable', 'string'],
            'zip' => ['nullable', 'string'],
            'facebook' => ['nullable', 'url'],
            'instagram' => ['nullable', 'url'],
            'twitter' => ['nullable', 'url'],
            'youtube' => ['nullable', 'url'],
            'tiktok' => ['nullable', 'url'],
            'linkedin' => ['nullable', 'url'],
            'pinterest' => ['nullable', 'url'],
            'snapchat' => ['nullable', 'url'],
        ];

        if ($this->routeIs('clubs.store')) {
            $rules['payment_method'] = ['required', 'string'];
        }

        return $rules;
    }
}
