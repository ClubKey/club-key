<script lang="ts" setup>
import DataCard from '@/components/dashboard/DataCard.vue';
import { Card, CardContent } from '@/components/ui/card';
import RewardCard from '@/components/user-rewards/RewardCard.vue';
import RewardCardRewards from '@/components/user-rewards/RewardCardRewards.vue';
import RewardProgress from '@/components/user-rewards/RewardProgress.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Auth, type BreadcrumbItem, Club, ClubUser, DashboardDatum } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import Button from '../components/ui/button/Button.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

defineProps<{
    currentClub: Club;
    auth: Auth;
    clubs: Club[];
    clubUser: ClubUser | null;
    dashboardData: DashboardDatum[];
}>();
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl">
            <div v-if="clubUser && clubUser.role === 'admin'" class="grid gap-6 py-6 md:grid-cols-2 lg:grid-cols-3">
                <DataCard v-for="(datum, index) in dashboardData" :key="index" :datum />
            </div>
            <div v-else-if="clubUser && currentClub" class="flex flex-col gap-6 py-6">
                <div class="grid gap-6 xl:grid-cols-2">
                    <RewardProgress :club-user="clubUser!" class="w-full xl:col-span-2" />
                    <Card>
                        <CardContent class="flex flex-col gap-4">
                            <h2 class="text-xl font-bold">Your reward card</h2>
                            <RewardCard :club-user="clubUser!" />
                        </CardContent>
                    </Card>
                    <RewardCardRewards :reward-level="clubUser!.reward_level" class="max-w-none" />
                </div>
                <div v-if="dashboardData.length" class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    <DataCard v-for="(datum, index) in dashboardData" :key="index" :datum />
                </div>
            </div>

            <div v-else-if="!currentClub && auth.user.role == 'super_admin'" class="flex h-full flex-col items-center justify-center">
                <h1 class="text-2xl font-bold">Create your first club</h1>
                <Button :as="Link" :href="route('clubs.create')" class="mt-4">Create Club</Button>
            </div>

            <div v-else-if="!currentClub" class="flex h-full flex-col items-center justify-center gap-2">
                <h1 class="mb-4 text-3xl font-bold">Hey, so great having you!</h1>
                <p>Want to use Club Key for your restaurant or cafe?</p>
                <p>We'd love to serve you.</p>
                <a href="mailto:chris@sol.company"><Button class="mt-4 cursor-pointer">Send us an email</Button></a>
            </div>
        </div>
    </AppLayout>
</template>
