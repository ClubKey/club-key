<script lang="ts" setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, usePage } from '@inertiajs/vue3';
import Button from '../components/ui/button/Button.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

const page = usePage();
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl">
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                <div
                    v-if="!page.props.currentClub && page.props.auth.user.role == 'super_admin'"
                    class="flex h-full flex-col items-center justify-center"
                >
                    <h1 class="text-2xl font-bold">Create your first club</h1>
                    <Button :as="Link" :href="route('clubs.create')" class="mt-4">Create Club</Button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
