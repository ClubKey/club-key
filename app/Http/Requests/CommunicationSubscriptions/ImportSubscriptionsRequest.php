<?php

declare(strict_types=1);

namespace App\Http\Requests\CommunicationSubscriptions;

use App\Enums\ContactType;
use App\Enums\Topic;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

final class ImportSubscriptionsRequest extends FormRequest
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
            'file' => ['sometimes', 'file', 'mimes:csv,txt', 'max:10240'],
            'subscriptions' => ['sometimes', 'array'],
            'subscriptions.*.contact_type' => ['required', 'string', 'in:'.implode(',', ContactType::values())],
            'subscriptions.*.contact_value' => ['required', 'string'],
            'subscriptions.*.topic' => ['required', 'string', 'in:'.implode(',', Topic::values())],
            'subscriptions.*.user_id' => ['sometimes', 'nullable', 'integer', 'exists:users,id'],
            'source' => ['sometimes', 'string', 'max:255'],
        ];
    }
}
