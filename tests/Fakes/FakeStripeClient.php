<?php

declare(strict_types=1);

namespace Tests\Fakes;

use Illuminate\Support\Str;
use Stripe\Collection;
use Stripe\Customer;
use Stripe\StripeObject;
use Stripe\Subscription;

final class FakeStripeClient
{
    public FakeStripeCustomerClient $customers;

    public FakeStripeSubscriptionClient $subscriptions;

    public FakeStripePaymentMethodClient $paymentMethods;

    public function __construct(array $config = [])
    {
        $this->customers = new FakeStripeCustomerClient();
        $this->subscriptions = new FakeStripeSubscriptionClient();
        $this->paymentMethods = new FakeStripePaymentMethodClient();
    }
}

final class FakeStripeCustomerClient
{
    public function retrieve(string $customerId, array $options = []): Customer
    {
        return Customer::constructFrom([
            'id' => $customerId,
            'invoice_settings' => StripeObject::constructFrom([
                'default_payment_method' => 'pm_card_visa',
            ]),
        ]);
    }
}

final class FakeStripeSubscriptionClient
{
    public function create(array $payload): Subscription
    {
        $items = [];

        foreach ($payload['items'] as $index => $item) {
            $items[] = StripeObject::constructFrom([
                'id' => 'si_fake_'.($index + 1),
                'price' => StripeObject::constructFrom([
                    'id' => $item['price'],
                    'product' => 'prod_fake_'.($index + 1),
                ]),
                'quantity' => $item['quantity'] ?? null,
            ]);
        }

        return Subscription::constructFrom([
            'id' => 'sub_fake_'.Str::uuid()->toString(),
            'status' => 'active',
            'items' => Collection::constructFrom([
                'data' => $items,
            ]),
        ]);
    }
}

final class FakeStripePaymentMethodClient
{
    public function retrieve(string $paymentMethodId): FakeStripePaymentMethod
    {
        return new FakeStripePaymentMethod($paymentMethodId);
    }
}

final class FakeStripePaymentMethod
{
    public string $id;

    public ?string $customer = null;

    public string $type = 'card';

    public object $card;

    public function __construct(string $id)
    {
        $this->id = $id;
        $this->card = (object) [
            'brand' => 'visa',
            'last4' => '4242',
        ];
    }

    public function attach(array $parameters): self
    {
        $this->customer = $parameters['customer'] ?? $this->customer;

        return $this;
    }
}
