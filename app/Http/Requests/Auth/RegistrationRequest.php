<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Models\User;
use App\Rules\Phone;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

final class RegistrationRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'phone' => ['required', 'string', 'max:255', new Phone],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->uncompromised()],
            'accept_terms' => ['accepted'],
            'accept_marketing' => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'accept_terms' => 'In order to use this application you must accept the terms and conditions.',
            'accept_marketing' => 'You must consent to receiving occasional emails to use this application.',
        ];
    }
}
