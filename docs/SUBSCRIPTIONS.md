# Subscription Management

## Overview

Club Rewards now supports subscription-based billing for clubs using Laravel Cashier (Stripe). Any authenticated user can create a club and set up a subscription.

## Pricing Model

- **Base Price**: $50.00/month (includes up to 3 admins)
- **Extra Admins**: $10.00/month per admin over 3

### Example Pricing

| Admin Count | Price per Month |
|-------------|-----------------|
| 1-3 admins  | $50.00         |
| 4 admins    | $60.00         |
| 5 admins    | $70.00         |
| 10 admins   | $120.00        |

## Environment Setup

Add the following to your `.env` file:

```env
STRIPE_KEY=your_stripe_publishable_key
STRIPE_SECRET=your_stripe_secret_key
STRIPE_WEBHOOK_SECRET=your_webhook_secret
STRIPE_PRICE_CLUB_BASE=price_xxx_base_subscription_id
STRIPE_PRICE_EXTRA_ADMIN=price_xxx_extra_admin_id
```

Create the corresponding products and prices in your Stripe dashboard:
- **Base Subscription**: $50/month recurring (includes 3 admins)
- **Extra Admin**: $10/month per admin (metered or as quantity)

## Configuration

The subscription settings can be configured in `config/subscriptions.php`:

```php
return [
    'prices' => [
        'club_base' => env('STRIPE_PRICE_CLUB_BASE', 'price_club_base'),
        'extra_admin' => env('STRIPE_PRICE_EXTRA_ADMIN', 'price_extra_admin'),
    ],
    'base_price' => 5000, // $50.00 in cents
    'included_admins' => 3,
    'extra_admin_price' => 1000, // $10.00 in cents
];
```

## Key Features

### For All Users
- Any authenticated user can create a club
- Subscription setup is required to activate all club features
- Billing management through Stripe Customer Portal

### Automatic Subscription Updates
- When admins are added to a club, the subscription quantity automatically updates
- When admins are removed, the subscription adjusts accordingly
- Base subscription includes 3 admins; extras are billed at $10/month each

## API Routes

### Subscription Creation
```
GET  /clubs/{club}/subscription/create - Show subscription form
POST /clubs/{club}/subscription        - Create subscription
```

### Billing Portal
```
GET /clubs/{club}/billing-portal - Redirect to Stripe billing portal
```

## Code Structure

### Actions
- `CreateSubscriptionAction` - Handles subscription creation with Stripe
- `UpdateSubscriptionAction` - Updates subscription when admin count changes

### Events & Listeners
- `AdminAddedToClub` - Dispatched when an admin joins
- `AdminRemovedFromClub` - Dispatched when an admin is removed
- `UpdateClubSubscriptionListener` - Listens to both events and updates subscription

### Frontend Components
- `SubscriptionCard.vue` - Displays subscription status and billing link
- `subscriptions/Create.vue` - Subscription creation page with pricing breakdown

## Usage

### Creating a Club with Subscription

1. User creates a club (now allowed for any authenticated user)
2. User is redirected to set up subscription
3. User enters payment method
4. Subscription is created with base price ($50/month)
5. User can manage billing through the billing portal

### Adding Admins

1. Club admin invites a new admin
2. Invited user accepts invitation
3. `AdminAddedToClub` event is dispatched
4. Subscription automatically updates if admin count exceeds 3

## Testing

Set Stripe to test mode and use test payment methods:
- Test card: `4242 4242 4242 4242`
- Any future expiry date
- Any CVC

**Note**: The current implementation requires manually entering a payment method ID. This is for demonstration purposes. In production, integrate Stripe Elements or Stripe Checkout for a complete payment flow.

## Database Schema

### Clubs Table
- `stripe_id` - Stripe customer ID
- `pm_type` - Payment method type
- `pm_last_four` - Last 4 digits of payment method
- `trial_ends_at` - Trial end date (nullable)

### Subscriptions Table
- `club_id` - Foreign key to clubs
- `stripe_id` - Stripe subscription ID
- `stripe_status` - Subscription status
- `stripe_price` - Price ID
- `quantity` - Number of extra admins
- `trial_ends_at` - Trial end date
- `ends_at` - Subscription end date

### Subscription Items Table
- `subscription_id` - Foreign key to subscriptions
- `stripe_id` - Stripe subscription item ID
- `stripe_product` - Product ID
- `stripe_price` - Price ID
- `quantity` - Quantity for this item
