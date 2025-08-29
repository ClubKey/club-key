<script lang="ts" setup>
import { Card, CardContent, CardTitle } from '@/components/ui/card';
import { RewardLevel } from '@/types';
import { Check } from 'lucide-vue-next';

defineProps<{
    rewardLevel?: RewardLevel;
}>();
</script>

<template>
    <Card v-if="rewardLevel" class="w-full max-w-2xl">
        <CardContent class="flex flex-col gap-4">
            <h2 class="text-xl font-semibold">Your rewards</h2>
            <p class="w-full whitespace-pre-wrap">{{ rewardLevel?.description }}</p>
            <div v-if="rewardLevel.rewards?.length" class="mt-4 flex flex-col gap-4">
                <CardTitle>Reward Perks</CardTitle>
                <ul>
                    <li v-for="reward in rewardLevel.rewards" :key="reward.id" class="flex items-center gap-2 text-sm">
                        <Check class="size-3" />{{ reward.description }}
                    </li>
                </ul>
            </div>
        </CardContent>
    </Card>
</template>
