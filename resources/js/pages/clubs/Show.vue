<script lang="ts" setup>
import PromotionBanner from '@/components/promotions/PromotionBanner.vue';
import SubscriptionCard from '@/components/subscriptions/SubscriptionCard.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import RewardCard from '@/components/user-rewards/RewardCard.vue';
import RewardCardRewards from '@/components/user-rewards/RewardCardRewards.vue';
import RewardProgress from '@/components/user-rewards/RewardProgress.vue';
import VisitCard from '@/components/visits/VisitCard.vue';
import ClubLayout from '@/layouts/ClubLayout.vue';
import { Club, ClubUser, Paginator, Promotion, Subscription, Visit } from '@/types';
import { Head, InfiniteScroll, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
    club: Club;
    clubUser?: ClubUser;
    promotion?: Promotion | null;
    visits?: Paginator<Visit> | null;
    subscription?: Subscription | null;
}>();

const page = usePage();
const isAdmin = computed(() => {
    const user = page.props.auth?.user;
    if (!user || !props.clubUser) return false;
    return props.clubUser.role === 'admin';
});
</script>

<template>
    <ClubLayout :club>
        <Head>
            <title>{{ club.name }}</title>
            <meta :content="club.description" name="description" />
        </Head>
        <div class="mx-auto flex w-full max-w-screen-lg flex-col items-center gap-6 pb-8">
            <PromotionBanner v-if="promotion" :promotion="promotion" class="w-full" />

            <img v-if="club.logo_path" :alt="club.name" :src="club.logo_path!" class="h-32 overflow-hidden rounded-lg" />
            <h1 class="text-5xl font-bold">{{ club.name }}</h1>
            <p v-if="club.description" class="max-w-2xl text-center text-accent-foreground">{{ club.description }}</p>
            <div v-if="clubUser" class="flex w-full flex-col items-center gap-6">
                <!-- Admin-only subscription card -->
                <SubscriptionCard 
                    v-if="isAdmin" 
                    :club="club" 
                    :subscription="subscription" 
                    class="w-full"
                />

                <RewardProgress :club-user class="w-full" />
                <RewardCard :club-user />
                <RewardCardRewards :reward-level="clubUser.reward_level" />

                <div v-if="visits && visits.data.length > 0" class="w-full">
                    <h2 class="mb-4 text-2xl font-bold">Your Visit History</h2>
                    <InfiniteScroll :buffer="100" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3" data="visits">
                        <VisitCard v-for="visit in visits.data" :key="visit.id" :visit />
                    </InfiniteScroll>
                </div>
            </div>
            <Card v-else>
                <CardContent class="flex flex-col items-center gap-2 text-center">
                    <h2 class="text-2xl font-bold">Join the Club!</h2>
                    <p>Become part of {{ club.name }} and access exclusive discounts and rewards.</p>
                    <Button as-child>
                        <Link :href="route('clubs.join', club.slug)">Join {{ club.name }}</Link>
                    </Button>
                </CardContent>
            </Card>
        </div>
    </ClubLayout>
</template>
