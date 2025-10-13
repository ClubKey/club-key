<script lang="ts" setup>
import { Card, CardContent, CardTitle } from '@/components/ui/card';
import RewardCard from '@/components/user-rewards/RewardCard.vue';
import RewardCardRewards from '@/components/user-rewards/RewardCardRewards.vue';
import RewardProgress from '@/components/user-rewards/RewardProgress.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { AdminOverview, Auth, type BreadcrumbItem, Club, ClubUser, UserOverview } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import Button from '../components/ui/button/Button.vue';
import { useDate } from '@/composables/useDate';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

const { formatDate } = useDate();

defineProps<{
    currentClub: Club;
    auth: Auth;
    clubs: Club[];
    clubUser: ClubUser;
    adminOverview: AdminOverview | null;
    userOverview: UserOverview | null;
}>();
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl">
            <div v-if="adminOverview" class="grid gap-6 py-6 md:grid-cols-2 lg:grid-cols-3">
                <Card>
                    <CardContent class="flex flex-col gap-4">
                        <div class="text-7xl">{{ adminOverview.numberOfMembersToday }}</div>
                        <CardTitle>New members today</CardTitle>
                    </CardContent>
                </Card>
                <Card>
                    <CardContent class="flex flex-col gap-4">
                        <div class="text-7xl">{{ adminOverview.numberOfMembers }}</div>
                        <CardTitle>Total members</CardTitle>
                    </CardContent>
                </Card>
                <Card>
                    <CardContent class="flex flex-col gap-4">
                        <div class="text-7xl">{{ adminOverview.numberOfVisitsThisWeek }}</div>
                        <CardTitle>Visits this week</CardTitle>
                    </CardContent>
                </Card>
                <Card v-if="adminOverview.mostPopularLocationName">
                    <CardContent class="flex flex-col gap-4">
                        <div class="text-7xl">{{ adminOverview.mostPopularLocationName }}</div>
                        <CardTitle>Most popular location</CardTitle>
                    </CardContent>
                </Card>
            </div>
            <div v-else-if="clubUser && currentClub" class="flex flex-col gap-6 py-6">
                <div v-if="userOverview" class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    <Card>
                        <CardContent class="flex flex-col gap-2">
                            <div class="text-4xl font-semibold">
                                {{ clubUser.reward_level?.name ?? 'Member' }}
                            </div>
                            <CardTitle>Your reward tier</CardTitle>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardContent class="flex flex-col gap-2">
                            <div class="text-4xl font-semibold">{{ userOverview.visitsPastWeek }}</div>
                            <CardTitle>Visits in the past 7 days</CardTitle>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardContent class="flex flex-col gap-2">
                            <div class="text-4xl font-semibold">{{ userOverview.visitsThisMonth }}</div>
                            <CardTitle>Visits this month</CardTitle>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardContent class="flex flex-col gap-2">
                            <div class="text-4xl font-semibold">{{ userOverview.lifetimeVisits }}</div>
                            <CardTitle>Total visits</CardTitle>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardContent class="flex flex-col gap-2">
                            <div class="text-4xl font-semibold">
                                <template v-if="userOverview.visitsUntilNextReward !== null">
                                    {{ userOverview.visitsUntilNextReward }}
                                </template>
                                <template v-else>🎉</template>
                            </div>
                            <CardTitle v-if="userOverview.visitsUntilNextReward !== null">
                                Visits until next reward
                            </CardTitle>
                            <CardTitle v-else>You're at the top tier!</CardTitle>
                            <p v-if="userOverview.nextRewardLevelName" class="text-sm text-muted-foreground">
                                Next up: {{ userOverview.nextRewardLevelName }}
                            </p>
                        </CardContent>
                    </Card>
                    <Card class="md:col-span-2 xl:col-span-1">
                        <CardContent class="flex flex-col gap-2">
                            <div class="text-4xl font-semibold">
                                {{ userOverview.lastVisitedAt ? formatDate(userOverview.lastVisitedAt) : 'Not yet visited' }}
                            </div>
                            <CardTitle>Last visit</CardTitle>
                        </CardContent>
                    </Card>
                </div>

                <div class="grid gap-6 xl:grid-cols-2">
                    <RewardProgress :club-user class="w-full xl:col-span-2" />
                    <Card>
                        <CardContent class="flex flex-col gap-4">
                            <h2 class="text-xl font-bold">Your reward card</h2>
                            <RewardCard :club-user />
                        </CardContent>
                    </Card>
                    <RewardCardRewards :reward-level="clubUser.reward_level" class="max-w-none" />
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
