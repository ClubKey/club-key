<script lang="ts" setup>
import CreateClubForm from '@/components/clubs/CreateClubForm.vue';
import Heading from '@/components/Heading.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Card } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Clubs',
        href: '/clubs',
    },
    {
        title: 'Create',
        href: '/clubs/create',
    },
];
</script>

<template>
    <Head description="Create a club to start building your business." title="Create Club" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="px-4 py-6">
            <Head title="Profile settings" />
            <Heading class="mb-6" description="Create a club to start building your business." title="Create Club" />
            <Card class="max-w-9/12 p-6">
                <HeadingSmall title="Details" />

                <CreateClubForm />
            </Card>
        </div>
    </AppLayout>
</template>
