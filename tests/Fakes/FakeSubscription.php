<?php

declare(strict_types=1);

namespace Tests\Fakes;

use Laravel\Cashier\Subscription as CashierSubscription;
use Laravel\Cashier\Cashier;

final class FakeSubscription extends CashierSubscription
{
    public static array $recordedAdditions = [];

    public static array $recordedRemovals = [];

    protected $table = 'subscriptions';

    public function items()
    {
        return $this->hasMany(Cashier::$subscriptionItemModel, 'subscription_id');
    }

    public function addPrice($price, $quantity = 1, array $options = [])
    {
        $options['quantity'] = $options['quantity'] ?? $quantity;

        static::$recordedAdditions[$this->id][] = ['price' => $price, 'options' => $options];

        return $this;
    }

    public function removePrice($price)
    {
        static::$recordedRemovals[$this->id][] = $price;

        return $this;
    }
}
