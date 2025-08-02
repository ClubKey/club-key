<script lang="ts" setup>
import DeleteRewardLevel from '@/components/club-rewards/DeleteRewardLevel.vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import AppLayout from '@/layouts/AppLayout.vue';
import { RewardLevel, type BreadcrumbItem, type Club } from '@/types';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { Edit, Plus, Trophy } from 'lucide-vue-next';
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

const getIconComponent = (iconName: string | null) => {
    if (!iconName) return Trophy;
    // You can add more icon mappings here if needed
    return Trophy;
};
</script>

<template>
    <AppLayout :breadcrumbs>
        <Head :title="`${club.name} Reward Levels`" />
        <div class="px-4 py-6">
            <div class="flex items-center justify-between">
                <Heading :title="`${club.name} Reward Levels`" description="Manage your club reward levels" />
                <Button v-if="canCreate" as-child>
                    <Link :href="route('reward-levels.create')">
                        <Plus class="mr-2 h-4 w-4" />
                        Add Reward Level
                    </Link>
                </Button>
            </div>

            <div v-if="rewardLevels.length === 0" class="mt-8">
                <Card>
                    <CardContent class="flex flex-col items-center justify-center py-12">
                        <Trophy class="mb-4 h-12 w-12 text-muted-foreground" />
                        <h3 class="mb-2 text-lg font-semibold">No reward levels yet</h3>
                        <p class="mb-4 text-center text-muted-foreground">Get started by creating your first reward level.</p>
                        <Button v-if="canCreate" as-child>
                            <Link :href="route('reward-levels.create')">
                                <Plus class="mr-2 h-4 w-4" />
                                Add Reward Level
                            </Link>
                        </Button>
                    </CardContent>
                </Card>
            </div>

            <div v-else class="mt-8 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <Card v-for="rewardLevel in rewardLevels" :key="rewardLevel.id" class="group">
                    <CardHeader>
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3">
                                    <div
                                        v-if="rewardLevel.icon_name"
                                        class="flex h-8 w-8 items-center justify-center rounded-full"
                                        :style="{ backgroundColor: rewardLevel.color || '#6b7280' }"
                                    >
                                        <component :is="getIconComponent(rewardLevel.icon_name)" class="h-4 w-4 text-white" />
                                    </div>
                                    <div>
                                        <CardTitle class="text-lg">{{ rewardLevel.name }}</CardTitle>
                                        <CardDescription class="mt-2"> {{ rewardLevel.min_visits_per_month }} visits/month </CardDescription>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div v-if="rewardLevel.description" class="mb-4 text-sm text-muted-foreground">
                            {{ rewardLevel.description }}
                        </div>

                        <div class="mb-4 space-y-2 text-sm">
                            <div class="flex items-center justify-between">
                                <span class="text-muted-foreground">Discount:</span>
                                <span class="font-medium">{{ rewardLevel.percentage_discount }}%</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-muted-foreground">Rewards:</span>
                                <span class="font-medium">{{ rewardLevel.rewards?.length || 0 }}</span>
                            </div>
                        </div>

                        <Separator class="my-4" />

                        <div class="flex gap-2">
                            <Button v-if="canCreate" variant="outline" size="sm" as-child class="flex-1">
                                <Link :href="route('reward-levels.edit', rewardLevel.id)">
                                    <Edit class="mr-2 h-4 w-4" />
                                    Manage
                                </Link>
                            </Button>
                            <DeleteRewardLevel v-if="canCreate" :reward-level="rewardLevel" />
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
