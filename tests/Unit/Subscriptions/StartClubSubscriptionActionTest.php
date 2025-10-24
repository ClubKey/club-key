<?php

declare(strict_types=1);

use App\Actions\Subscriptions\StartClubSubscriptionAction;
use App\Actions\Subscriptions\UpdateClubSubscriptionAction;
use App\Models\Club;
use App\Models\User;
use Laravel\Cashier\Cashier;
use Stripe\StripeClient;
use Tests\Fakes\FakeStripeClient;
use Tests\Fakes\FakeSubscription;
use Tests\Fakes\FakeSubscriptionItem;

use function PHPUnit\Framework\assertSame;

uses(Tests\TestCase::class);
uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function (): void {
    Cashier::useSubscriptionModel(FakeSubscription::class);
    Cashier::useSubscriptionItemModel(FakeSubscriptionItem::class);
    app()->bind(StripeClient::class, fn () => new FakeStripeClient());
    FakeSubscription::$recordedAdditions = [];
    FakeSubscription::$recordedRemovals = [];
    FakeSubscriptionItem::$updatedQuantities = [];
});

afterEach(function (): void {
    Cashier::useSubscriptionModel(\Laravel\Cashier\Subscription::class);
    Cashier::useSubscriptionItemModel(\Laravel\Cashier\SubscriptionItem::class);
});

it('throws when the club subscription price is not configured', function (): void {
    config(['subscriptions.club.price' => null]);

    $action = new StartClubSubscriptionAction(new UpdateClubSubscriptionAction());

    $club = Club::factory()->create();
    $user = User::factory()->create();

    $this->expectException(\RuntimeException::class);
    $this->expectExceptionMessage('Club subscription price is not configured.');

    $action->handle($user, $club, 'pm_card_visa');
});

it('creates the subscription and updates the club metadata', function (): void {
    config(['subscriptions.club.price' => 'price_basic']);

    $club = Club::factory()->create();
    $user = User::factory()->create([
        'stripe_id' => 'cus_test_user',
        'pm_type' => 'card',
        'pm_last_four' => '4242',
    ]);

    $action = new StartClubSubscriptionAction(new UpdateClubSubscriptionAction());

    $action->handle($user, $club, 'pm_card_visa');

    $club->refresh();

    assertSame(sprintf('club-%d', $club->id), $club->subscription_name);
    expect($club->stripe_subscription_id)->not->toBeNull();
    expect($user->subscription($club->subscription_name))->not->toBeNull();
});
