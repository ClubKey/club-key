<script lang="ts" setup>
import Member from '@/components/clubs/Member.vue';
import Heading from '@/components/Heading.vue';
import { Card, CardContent } from '@/components/ui/card';
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

        <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4">
            <Link v-for="clubUser in clubUsers" :key="clubUser.id" :href="route('members.show', clubUser.id)">
                <Card class="transition hover:shadow-md">
                    <CardContent>
                        <Member v-if="clubUser.user" :club :user="clubUser.user" />
                    </CardContent>
                </Card>
            </Link>
        </div>
    </AppLayout>
</template>
