<script lang="ts" setup>
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { type Club, type Subscription } from '@/types';
import { Link } from '@inertiajs/vue3';

interface Props {
    club: Club;
    subscription?: Subscription | null;
}

const props = defineProps<Props>();

const hasActiveSubscription = () => {
    return props.subscription && props.subscription.stripe_status === 'active';
};

const getStatusBadgeClass = () => {
    if (!props.subscription) return 'bg-gray-100 text-gray-800';
    
    switch (props.subscription.stripe_status) {
        case 'active':
            return 'bg-green-100 text-green-800';
        case 'trialing':
            return 'bg-blue-100 text-blue-800';
        case 'past_due':
            return 'bg-yellow-100 text-yellow-800';
        case 'canceled':
        case 'incomplete':
        case 'incomplete_expired':
        case 'unpaid':
            return 'bg-red-100 text-red-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
};

const getStatusText = () => {
    if (!props.subscription) return 'No active subscription';
    return props.subscription.stripe_status.charAt(0).toUpperCase() + props.subscription.stripe_status.slice(1);
};
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle>Subscription Status</CardTitle>
            <CardDescription v-if="!subscription">
                Set up a subscription to unlock all features for your club.
            </CardDescription>
        </CardHeader>
        <CardContent class="space-y-4">
            <div v-if="subscription" class="space-y-2">
                <div class="flex items-center gap-2">
                    <span class="text-sm font-medium">Status:</span>
                    <span
                        :class="getStatusBadgeClass()"
                        class="px-2 py-1 rounded-full text-xs font-semibold"
                    >
                        {{ getStatusText() }}
                    </span>
                </div>
                
                <div v-if="subscription.trial_ends_at" class="text-sm">
                    <span class="font-medium">Trial ends:</span>
                    {{ new Date(subscription.trial_ends_at).toLocaleDateString() }}
                </div>
                
                <div v-if="subscription.ends_at" class="text-sm">
                    <span class="font-medium">Subscription ends:</span>
                    {{ new Date(subscription.ends_at).toLocaleDateString() }}
                </div>
            </div>

            <div class="flex gap-2">
                <Button v-if="!subscription" as-child>
                    <Link :href="`/clubs/${club.slug}/subscription/create`">
                        Start Subscription
                    </Link>
                </Button>
                <Button v-else as-child variant="outline">
                    <Link :href="`/clubs/${club.slug}/billing-portal`" method="get">
                        Manage Billing
                    </Link>
                </Button>
            </div>
        </CardContent>
    </Card>
</template>
