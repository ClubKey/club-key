<script lang="ts" setup>
import DeleteRewardLevel from '@/components/club-rewards/DeleteRewardLevel.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { RewardLevel } from '@/types';
import { Link } from '@inertiajs/vue3';
import { Edit, Trophy } from 'lucide-vue-next';
import { computed } from 'vue';

const props = defineProps<{
    rewardLevel: RewardLevel;
    canEdit: boolean;
}>();
const getIconComponent = (iconName: string | null) => {
    if (!iconName) return Trophy;
    // You can add more icon mappings here if needed
    return Trophy;
};

const description = computed(() => {
    const text = props.rewardLevel.description;
    const maxLength = 100;
    if (!text) {
        return '';
    }

    if (text.length > maxLength) {
        return text.slice(0, maxLength) + '...';
    }

    return text;
});
</script>

<template>
    <Card class="group">
        <CardHeader>
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center space-x-3">
                        <div
                            v-if="rewardLevel.icon_name"
                            :style="{ backgroundColor: rewardLevel.color || '#6b7280' }"
                            class="flex h-8 w-8 items-center justify-center rounded-full"
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
            <div v-if="rewardLevel.description" class="mb-4 text-sm text-wrap text-muted-foreground">
                {{ description }}
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
                <Button v-if="canEdit" as-child class="flex-1" size="sm" variant="outline">
                    <Link :href="route('reward-levels.edit', rewardLevel.id)">
                        <Edit />
                        Manage
                    </Link>
                </Button>
                <DeleteRewardLevel v-if="canEdit" :reward-level="rewardLevel" />
            </div>
        </CardContent>
    </Card>
</template>
