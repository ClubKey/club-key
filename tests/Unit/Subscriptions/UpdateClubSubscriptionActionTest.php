<?php

declare(strict_types=1);

use App\Actions\Subscriptions\UpdateClubSubscriptionAction;
use App\Enums\ClubRole;
use App\Models\Club;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Cashier\Cashier;
use Tests\Fakes\FakeSubscription;
use Tests\Fakes\FakeSubscriptionItem;

uses(Tests\TestCase::class);
uses(RefreshDatabase::class);

beforeEach(function (): void {
    config([
        'subscriptions.club.price' => 'price_basic',
        'subscriptions.club.additional_admin_price' => 'price_additional',
        'subscriptions.club.included_admins' => 3,
    ]);

    Cashier::useSubscriptionModel(FakeSubscription::class);
    Cashier::useSubscriptionItemModel(FakeSubscriptionItem::class);

    FakeSubscription::$recordedAdditions = [];
    FakeSubscription::$recordedRemovals = [];
    FakeSubscriptionItem::$updatedQuantities = [];
});

afterEach(function (): void {
    Cashier::useSubscriptionModel(\Laravel\Cashier\Subscription::class);
    Cashier::useSubscriptionItemModel(\Laravel\Cashier\SubscriptionItem::class);
});

it('throws when the additional admin price is not configured', function (): void {
    config(['subscriptions.club.additional_admin_price' => null]);

    [$club] = createClubWithAdmins(1);

    $action = new UpdateClubSubscriptionAction();

    $this->expectException(\RuntimeException::class);
    $this->expectExceptionMessage('Additional admin price is not configured.');

    $action->handle($club);
});

it('adds an additional admin price when the included limit is exceeded', function (): void {
    [$club, , $subscription] = createClubWithAdmins(4);

    $action = new UpdateClubSubscriptionAction();

    $action->handle($club);

    $recordedAdditions = FakeSubscription::$recordedAdditions[$subscription->id] ?? [];

    expect(collect($recordedAdditions)->pluck('price'))->toContain('price_additional');
    $firstOptions = collect($recordedAdditions)->first()['options'] ?? [];
    $quantity = $firstOptions['quantity'] ?? null;

    if (is_array($quantity)) {
        $quantity = $quantity['quantity'] ?? null;
    }

    expect($quantity)->toBe(1);
});

it('updates the additional admin quantity when the item already exists', function (): void {
    [$club, , $subscription, $subscriptionItem] = createClubWithAdmins(5, true);

    $action = new UpdateClubSubscriptionAction();

    $action->handle($club);

    expect(FakeSubscriptionItem::$updatedQuantities[$subscriptionItem->id] ?? null)->toBe(2);
});

it('removes the additional admin price when not needed', function (): void {
    [$club, , $subscription, $subscriptionItem] = createClubWithAdmins(2, true);

    $action = new UpdateClubSubscriptionAction();

    $action->handle($club);

    expect(FakeSubscription::$recordedRemovals[$subscription->id] ?? [])->toContain('price_additional');
});

/**
 * @return array{0: Club, 1: User}
 */
function createClubWithAdmins(int $totalAdmins, bool $withAdditionalItem = false): array
{
    $owner = User::factory()->create();
    $club = Club::factory()->create(['owner_id' => $owner->id]);
    $club->members()->attach($owner->id, ['role' => ClubRole::ADMIN->value]);

    for ($i = 1; $i < $totalAdmins; $i++) {
        $admin = User::factory()->create();
        $club->members()->attach($admin->id, ['role' => ClubRole::ADMIN->value]);
    }

    $club->forceFill([
        'subscription_name' => sprintf('club-%d', $club->id),
        'stripe_subscription_id' => 'sub_fake',
    ])->save();

    $subscription = tap(new FakeSubscription(), function (FakeSubscription $subscription) use ($owner, $club): void {
        $subscription->forceFill([
            'user_id' => $owner->id,
            'type' => $club->subscription_name,
            'stripe_id' => 'sub_'.$club->id,
            'stripe_status' => 'active',
            'stripe_price' => 'price_basic',
        ])->save();
    });

    $subscriptionItem = null;

    if ($withAdditionalItem) {
        $subscriptionItem = tap(new FakeSubscriptionItem(), function (FakeSubscriptionItem $item) use ($subscription): void {
            $item->forceFill([
                'subscription_id' => $subscription->id,
                'stripe_id' => 'si_'.$subscription->id,
                'stripe_product' => 'prod_additional',
                'stripe_price' => 'price_additional',
                'quantity' => 1,
            ])->save();
        });
    }

    $club->owner()->associate($owner);
    $club->save();

    return [$club->fresh(), $owner->fresh(), $subscription->fresh(), $subscriptionItem?->fresh()];
}
