<script lang="ts" setup>
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, usePage } from '@inertiajs/vue3';

interface CommunicationSubscription {
    id: number;
    club: {
        id: number;
        name: string;
    };
    contact_type: string;
    contact_value: string;
    topic: string;
    status: string;
    user?: {
        id: number;
        name: string;
        email: string;
    } | null;
}

const props = defineProps<{
    subscription: CommunicationSubscription;
}>();

const page = usePage();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Unsubscribe',
        href: '#',
    },
];

const resubscribeForm = useForm({});

const resubscribe = () => {
    resubscribeForm.post(route('unsubscribe.resubscribe', props.subscription.id), {
        preserveScroll: true,
        onSuccess: () => {
            // Optionally redirect or show success message
        },
    });
};
</script>

<template>
    <AppLayout :breadcrumbs>
        <Head title="Unsubscribe Confirmation" />
        <div class="mx-auto max-w-2xl">
            <Card class="p-6">
                <Heading :title="`Unsubscribed from ${subscription.club.name}`" />

                <div class="mt-4 space-y-4">
                    <p class="text-neutral-600">
                        You have been successfully unsubscribed from <strong>{{ subscription.topic }}</strong> notifications via
                        <strong>{{ subscription.contact_type === 'email' ? 'email' : 'SMS' }}</strong> for
                        <strong>{{ subscription.club.name }}</strong>.
                    </p>

                    <p class="text-sm text-neutral-500">
                        You will no longer receive {{ subscription.topic }} notifications at
                        <strong>{{ subscription.contact_value }}</strong>.
                    </p>

                    <div class="mt-6 flex items-center gap-4">
                        <Button @click="resubscribe" :disabled="resubscribeForm.processing">
                            Resubscribe
                        </Button>
                    </div>

                    <div v-if="page.props.flash.success" class="mt-4 rounded-lg bg-green-50 p-4 text-green-800">
                        {{ page.props.flash.success }}
                    </div>
                </div>
            </Card>
        </div>
    </AppLayout>
</template>
