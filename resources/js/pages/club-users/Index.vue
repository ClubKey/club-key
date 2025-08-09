<script lang="ts" setup>
import Heading from '@/components/Heading.vue';
import { Card, CardContent, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Club, ClubUser } from '@/types';
import { Head, Link } from '@inertiajs/vue3';

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
            <Link v-for="clubUser in clubUsers" :key="clubUser.id" :href="route('members.show', clubUser.id)">
                <Card class="h-full transition hover:shadow-md">
                    <CardContent class="flex flex-col gap-2">
                        <CardTitle>{{ clubUser.user.name }}</CardTitle>
                        <div class="text-sm text-wrap text-foreground">{{ clubUser.user.email }}</div>
                    </CardContent>
                </Card>
            </Link>
        </div>
    </AppLayout>
</template>
