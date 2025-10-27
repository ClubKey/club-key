<?php

declare(strict_types=1);

namespace App\Http\Requests\Webhooks;

use Illuminate\Foundation\Http\FormRequest;

final class VonageDeliveryStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'messageId' => ['required_without:message-id', 'string'],
            'message-id' => ['required_without:messageId', 'string'],
            'status' => ['required', 'string'],
            'msisdn' => ['nullable', 'string'],
            'to' => ['nullable', 'string'],
            'err-code' => ['nullable', 'string'],
            'error' => ['nullable', 'string'],
            'error-text' => ['nullable', 'string'],
            'timestamp' => ['nullable', 'string'],
            'message-timestamp' => ['nullable', 'string'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        return $this->all();
    }
}
