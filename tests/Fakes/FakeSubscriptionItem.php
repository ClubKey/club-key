<?php

declare(strict_types=1);

namespace Tests\Fakes;

use Laravel\Cashier\SubscriptionItem as CashierSubscriptionItem;

final class FakeSubscriptionItem extends CashierSubscriptionItem
{
    public static array $updatedQuantities = [];

    protected $table = 'subscription_items';

    public function updateQuantity($quantity)
    {
        static::$updatedQuantities[$this->id] = $quantity;

        return $this;
    }
}
