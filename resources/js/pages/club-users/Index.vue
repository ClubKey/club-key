<script lang="ts" setup>
import ClubUserCard from '@/components/club-users/ClubUserCard.vue';
import Heading from '@/components/Heading.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Club, ClubUser } from '@/types';
import { Head } from '@inertiajs/vue3';

defineProps<{
    club: Club;
    clubUsers: ClubUser[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Members',
        href: route('members.index'),
    },
];
</script>

<template>
    <AppLayout :breadcrumbs>
        <Head :title="`${club.name} Members`" />
        <div class="flex items-center justify-between">
            <Heading :title="`${club.name} Members`" description="View your club members." />
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4">
            <ClubUserCard v-for="clubUser in clubUsers" :key="clubUser.id" :club-user />
        </div>
    </AppLayout>
</template>
