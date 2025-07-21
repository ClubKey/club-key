<script lang="ts" setup>
import { Head } from '@inertiajs/vue3';

import AppearanceTabs from '@/components/AppearanceTabs.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { type BreadcrumbItem } from '@/types';

import AppLayout from '@/layouts/AppLayout.vue';
import SettingsSettingsLayout from '@/layouts/settings/SettingsLayout.vue';

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Appearance settings',
        href: '/settings/appearance',
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Appearance settings" />

        <SettingsSettingsLayout>
            <div class="space-y-6">
                <HeadingSmall description="Update your account's appearance settings" title="Appearance settings" />
                <AppearanceTabs />
            </div>
        </SettingsSettingsLayout>
    </AppLayout>
</template>
