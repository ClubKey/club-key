<script lang="ts" setup>
import { Progress } from '@/components/ui/progress';
import { ClubUser } from '@/types';
import { computed, ref, watchEffect } from 'vue';

const props = defineProps<{
    clubUser: ClubUser;
}>();

const progress = ref(0);

const currentProgress = computed(() => {
    if (props.clubUser.next_reward_level) {
        return (props.clubUser.visits_past_30_days / props.clubUser.next_reward_level.min_visits_per_month) * 100;
    }
    return (props.clubUser.visits_past_30_days / (props.clubUser.reward_level?.min_visits_per_month ?? 0)) * 100;
});

watchEffect((cleanupFn) => {
    const timer = setTimeout(() => {
        progress.value = Math.min(currentProgress.value, 100);
    }, 500);

    cleanupFn(() => clearTimeout(timer));
});
</script>

<template>
    <div class="flex flex-col items-center gap-2 text-center">
        <h2 class="text-2xl font-bold">Your progress to more rewards:</h2>
        <Progress v-model="progress" />
        <div class="flex w-full flex-wrap items-center justify-between gap-2">
            <h3 v-if="clubUser.reward_level" class="font-semibold">
                {{
                    `${clubUser.reward_level?.name} (Current Reward Level) ${clubUser.visits_past_30_days}/${clubUser.reward_level?.min_visits_per_month}`
                }}
            </h3>
            <h3 v-if="clubUser.next_reward_level" class="ml-auto font-semibold">
                {{ `${clubUser.next_reward_level?.name} ${clubUser.visits_past_30_days}/${clubUser.next_reward_level?.min_visits_per_month}` }}
            </h3>
        </div>
    </div>
</template>
