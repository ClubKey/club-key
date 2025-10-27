<?php

declare(strict_types=1);

namespace App\Http\Requests\Webhooks;

use Illuminate\Foundation\Http\FormRequest;

final class VonageInboundRequest extends FormRequest
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
            'msisdn' => ['required', 'string'],
            'to' => ['required', 'string'],
            'text' => ['required', 'string'],
            'messageId' => ['nullable', 'string'],
            'message-timestamp' => ['nullable', 'string'],
            'type' => ['nullable', 'string'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        return $this->only([
            'msisdn',
            'to',
            'text',
            'messageId',
            'message-timestamp',
            'type',
            'keyword',
        ]) + $this->all();
    }
}
