<script lang="ts" setup>
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Club } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';

interface Props {
    club: Club;
    adminCount: number;
    subscriptionPrice: number;
    priceId: string;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Clubs',
        href: '/clubs',
    },
    {
        title: props.club.name,
        href: `/clubs/${props.club.slug}`,
    },
    {
        title: 'Subscribe',
        href: `/clubs/${props.club.slug}/subscription/create`,
    },
];

const form = useForm({
    payment_method: '',
    price_id: props.priceId,
});

const submit = () => {
    form.post(`/clubs/${props.club.slug}/subscription`, {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
    });
};

const formatPrice = (cents: number): string => {
    return `$${(cents / 100).toFixed(2)}`;
};
</script>

<template>
    <Head description="Subscribe to enable all features for your club." title="Subscribe" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <Heading
            class="mb-6"
            description="Subscribe to enable all features for your club."
            title="Subscribe to Club"
        />

        <Card class="max-w-2xl p-6">
            <div class="mb-6">
                <h3 class="text-lg font-semibold mb-2">Subscription Details</h3>
                <div class="space-y-2 text-sm text-muted-foreground">
                    <p>
                        <strong>Base Price:</strong> $50.00/month (includes up to 3 admins)
                    </p>
                    <p><strong>Current Admin Count:</strong> {{ adminCount }}</p>
                    <p v-if="adminCount > 3">
                        <strong>Extra Admins:</strong> {{ adminCount - 3 }} × $10.00/month
                    </p>
                    <p class="text-lg font-semibold text-foreground mt-4">
                        <strong>Total Price:</strong> {{ formatPrice(subscriptionPrice) }}/month
                    </p>
                </div>
            </div>

            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <Label for="payment_method">Payment Method ID</Label>
                    <Input
                        id="payment_method"
                        v-model="form.payment_method"
                        type="text"
                        placeholder="pm_xxxxxxxxxxxxxxx"
                        required
                    />
                    <p class="text-sm text-muted-foreground mt-1">
                        <strong>Note:</strong> This is a simplified implementation for demonstration. 
                        In production, you should integrate Stripe Elements or Stripe Checkout 
                        for secure payment method collection instead of manually entering IDs.
                    </p>
                    <p class="text-sm text-muted-foreground mt-1">
                        For testing: Enter your Stripe payment method ID. You can obtain this from your Stripe
                        dashboard or by setting up a payment form with Stripe Elements.
                    </p>
                    <p v-if="form.errors.payment_method" class="text-sm text-destructive mt-1">
                        {{ form.errors.payment_method }}
                    </p>
                </div>

                <div class="flex gap-4 pt-4">
                    <Button type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Processing...' : 'Subscribe Now' }}
                    </Button>
                    <Button
                        type="button"
                        variant="outline"
                        @click="$inertia.visit(`/clubs/${club.slug}`)"
                    >
                        Cancel
                    </Button>
                </div>
            </form>
        </Card>
    </AppLayout>
</template>
