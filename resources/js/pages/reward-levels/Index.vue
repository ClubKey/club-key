<script lang="ts" setup>
import Heading from '@/components/Heading.vue';
import RewardLevelCard from '@/components/reward-levels/RewardLevelCard.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Club, RewardLevel } from '@/types';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { Plus, Trophy } from 'lucide-vue-next';
import { computed } from 'vue';

defineProps<{
    club: Club;
    rewardLevels: RewardLevel[];
}>();

const page = usePage();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Reward Levels',
        href: route('reward-levels.index'),
    },
];

const canCreate = computed(() => {
    return page.props.auth.user.club_role === 'admin';
});
</script>

<template>
    <AppLayout :breadcrumbs>
        <Head :title="`${club.name} Reward Levels`" />
        <div class="flex items-center justify-between">
            <Heading :title="`${club.name} Reward Levels`" description="Manage your club reward levels" />
            <Button v-if="canCreate" as-child class="fixed right-4 bottom-4 md:static">
                <Link :href="route('reward-levels.create')">
                    <Plus />
                    Add Reward Level
                </Link>
            </Button>
        </div>

        <div v-if="rewardLevels.length === 0" class="mt-4">
            <Card>
                <CardContent class="flex flex-col items-center justify-center py-12">
                    <Trophy class="mb-4 h-12 w-12 text-muted-foreground" />
                    <h3 class="mb-2 text-lg font-semibold">No reward levels yet</h3>
                    <p class="mb-4 text-center text-muted-foreground">Get started by creating your first reward level.</p>
                    <Button v-if="canCreate" as-child>
                        <Link :href="route('reward-levels.create')">
                            <Plus />
                            Add Reward Level
                        </Link>
                    </Button>
                </CardContent>
            </Card>
        </div>

        <div v-else class="mt-4 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            <RewardLevelCard v-for="rewardLevel in rewardLevels" :key="rewardLevel.id" :can-edit="canCreate" :reward-level="rewardLevel" />
        </div>
    </AppLayout>
</template>
