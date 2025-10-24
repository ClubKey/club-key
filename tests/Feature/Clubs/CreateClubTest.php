<?php

declare(strict_types=1);

use App\Models\Club;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Laravel\Cashier\Cashier;
use Stripe\StripeClient;
use Tests\Fakes\FakeStripeClient;
use Tests\Fakes\FakeSubscription;
use Tests\Fakes\FakeSubscriptionItem;

pest()->group('clubs', 'create-club');

beforeEach(function (): void {
    config([
        'subscriptions.club.price' => 'price_basic',
        'subscriptions.club.additional_admin_price' => 'price_additional',
        'subscriptions.club.included_admins' => 3,
    ]);

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

test('an authenticated user can access the page to create a club', function () {
    $user = User::factory()->asUser()->create();

    $response = $this->actingAs($user)->get('/clubs/create');

    $response->assertOk();
});

test('an authenticated user can create a club and starts a subscription', function () {
    $user = User::factory()->asUser()->create([
        'stripe_id' => 'cus_test_user',
        'pm_type' => 'card',
        'pm_last_four' => '4242',
    ]);

    $response = $this->actingAs($user)->post(route('clubs.store'), [
        'name' => 'Test Club',
        'description' => 'Test Description',
        'logo' => UploadedFile::fake()->image('test.jpg'),
        'slug' => 'test-club',
        'payment_method' => 'pm_card_visa',
    ]);

    $response->assertRedirect('dashboard');

    $this->assertDatabaseHas('clubs', [
        'name' => 'Test Club',
        'description' => 'Test Description',
        'slug' => 'test-club',
    ]);

    $club = Club::where('name', 'Test Club')->first();
    $user->refresh();
    expect($user->currentClub->id)->toBe($club->id);
    expect($club->subscription_name)->toBe(sprintf('club-%d', $club->id));
    expect($club->stripe_subscription_id)->not->toBeNull();
    expect($user->subscription($club->subscription_name))->not->toBeNull();
});

test('someone who is not logged in cannot create a club', function () {
    $response = $this->post('/clubs', [
        'name' => 'Test Club',
        'description' => 'Test Description',
        'logo' => UploadedFile::fake()->image('test.jpg'),
        'slug' => 'test-club',
        'payment_method' => 'pm_card_visa',
    ]);

    $response->assertRedirect(route('login'));
});
