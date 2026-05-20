<script lang="ts" setup>
import Heading from '@/components/Heading.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from '@/components/ui/alert-dialog';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import ClubLayout from '@/layouts/club/ClubLayout.vue';
import { type BreadcrumbItem, Club } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { Plug, Unplug } from 'lucide-vue-next';
import { ref } from 'vue';

interface SquareIntegrationStatus {
    configured: boolean;
    redirect_uri: string;
    connected: boolean;
    merchant_id?: string | null;
    connected_at?: string | null;
    access_token_expires_at?: string | null;
    scopes: string[];
}

const props = defineProps<{
    club: Club;
    square: SquareIntegrationStatus;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: props.club.name,
        href: `/clubs/${props.club.slug}/edit`,
    },
    {
        title: 'Integrations',
        href: `/clubs/${props.club.slug}/integrations/square`,
    },
];

const showDisconnectDialog = ref(false);

function confirmDisconnectSquare(): void {
    router.delete(route('clubs.integrations.square.disconnect', props.club.slug));
    showDisconnectDialog.value = false;
}

function formatDate(value: string | null | undefined): string {
    if (!value) {
        return '—';
    }

    return new Date(value).toLocaleString();
}
</script>

<template>
    <AppLayout :breadcrumbs>
        <Head :title="`Integrations - ${club.name}`" />
        <ClubLayout :club="club">
            <Heading
                description="Connect third-party services to your club."
                title="Integrations"
            />

            <Card class="w-full max-w-2xl p-6">
                <div class="flex items-start justify-between gap-4">
                    <div class="space-y-1">
                        <HeadingSmall title="Square" />
                        <p class="text-muted-foreground text-sm">
                            Connect your Square seller account so Club Key can access your Square
                            data on your behalf.
                        </p>
                    </div>
                    <Plug class="text-muted-foreground size-5 shrink-0" />
                </div>

                <div v-if="!square.configured" class="mt-4 rounded-md border border-amber-200 bg-amber-50 p-3 text-sm text-amber-900 dark:border-amber-900 dark:bg-amber-950 dark:text-amber-100">
                    Square OAuth is not configured for this application yet. An administrator must
                    add Square application credentials to the server environment.
                </div>

                <div v-else class="mt-4 space-y-3 text-sm">
                    <p>
                        <span class="font-medium">Status:</span>
                        {{ square.connected ? 'Connected' : 'Not connected' }}
                    </p>
                    <template v-if="square.connected">
                        <p>
                            <span class="font-medium">Merchant ID:</span>
                            {{ square.merchant_id }}
                        </p>
                        <p>
                            <span class="font-medium">Connected at:</span>
                            {{ formatDate(square.connected_at) }}
                        </p>
                        <p v-if="square.access_token_expires_at">
                            <span class="font-medium">Access token expires:</span>
                            {{ formatDate(square.access_token_expires_at) }}
                        </p>
                        <p v-if="square.scopes.length > 0">
                            <span class="font-medium">Scopes:</span>
                            {{ square.scopes.join(', ') }}
                        </p>
                    </template>
                </div>

                <div class="mt-6 flex flex-wrap gap-3">
                    <Button v-if="square.configured && !square.connected" as-child>
                        <a :href="`/clubs/${club.slug}/integrations/square/connect`">
                            <Plug class="mr-2 size-4" />
                            Connect Square
                        </a>
                    </Button>

                    <Button
                        v-if="square.connected"
                        variant="destructive"
                        @click="showDisconnectDialog = true"
                    >
                        <Unplug class="mr-2 size-4" />
                        Disconnect Square
                    </Button>
                </div>
            </Card>

            <AlertDialog :open="showDisconnectDialog" @update:open="showDisconnectDialog = $event">
                <AlertDialogContent>
                    <AlertDialogHeader>
                        <AlertDialogTitle>Disconnect Square</AlertDialogTitle>
                        <AlertDialogDescription>
                            Are you sure you want to disconnect your Square account from this club? Club Key
                            will no longer be able to access your Square data on your behalf until you
                            connect again.
                        </AlertDialogDescription>
                    </AlertDialogHeader>
                    <AlertDialogFooter>
                        <AlertDialogCancel>Cancel</AlertDialogCancel>
                        <AlertDialogAction
                            class="bg-destructive text-destructive-foreground hover:bg-destructive/90"
                            @click="confirmDisconnectSquare"
                        >
                            Disconnect
                        </AlertDialogAction>
                    </AlertDialogFooter>
                </AlertDialogContent>
            </AlertDialog>
        </ClubLayout>
    </AppLayout>
</template>
