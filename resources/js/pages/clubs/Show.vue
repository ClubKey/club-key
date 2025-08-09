<script lang="ts" setup>
import RewardCard from '@/components/RewardCard.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { getInitials } from '@/composables/useInitials';
import ClubLayout from '@/layouts/ClubLayout.vue';
import { Club, ClubUser } from '@/types';
import { Head, Link, usePage } from '@inertiajs/vue3';

defineProps<{
    club: Club;
    clubUser?: ClubUser;
}>();

const user = usePage().props.auth.user;
</script>

<template>
    <ClubLayout :club>
        <Head :description="club.description" :title="club.name" />
        <div class="mx-auto flex max-w-screen-lg flex-col items-center gap-6">
            <Avatar class="size-28 overflow-hidden rounded-full">
                <AvatarImage v-if="club.logo_path" :alt="club.name" :src="club.logo_path!" />
                <AvatarFallback class="rounded-lg text-black dark:text-white">
                    {{ getInitials(club.name) }}
                </AvatarFallback>
            </Avatar>
            <h1 class="text-5xl font-bold">{{ club.name }}</h1>
            <p v-if="club.description" class="max-w-2xl text-center text-accent-foreground">{{ club.description }}</p>
            <RewardCard v-if="clubUser" :club-user />
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
