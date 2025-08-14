<script lang="ts" setup>
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { getInitials } from '@/composables/useInitials';
import { ClubUser } from '@/types';
import { computed } from 'vue';

const props = defineProps<{
    clubUser: ClubUser;
}>();

const bgColor = computed(() => {
    return props.clubUser.reward_level?.color ?? 'black';
});

function getContrastColor(hex: string): string {
    // Normalize hex
    hex = hex.replace('#', '');

    // Support short hex (#abc → #aabbcc)
    if (hex.length === 3) {
        hex = hex
            .split('')
            .map((c) => c + c)
            .join('');
    }

    const r = parseInt(hex.substr(0, 2), 16);
    const g = parseInt(hex.substr(2, 2), 16);
    const b = parseInt(hex.substr(4, 2), 16);

    // Calculate luminance (per ITU-R BT.709)
    const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;

    return luminance > 0.5 ? '#000000' : '#FFFFFF';
}

const textColor = computed(() => getContrastColor(bgColor.value));

const cardTitle = computed(() => {
    if (props.clubUser.reward_level) {
        return `${props.clubUser.reward_level.name} Member`;
    }

    return `${props.clubUser.club.name} Member`;
});
</script>

<template>
    <div
        :style="`background-color:${bgColor}; color:${textColor}`"
        class="flex aspect-4/2 w-full max-w-lg flex-col justify-end gap-12 rounded-lg border p-6 shadow"
    >
        <Avatar class="size-20 overflow-hidden rounded-full">
            <AvatarImage v-if="clubUser.club.logo_path" :alt="clubUser.club.name" :src="clubUser.club.logo_path!" />
            <AvatarFallback class="rounded-lg text-black dark:text-white">
                {{ getInitials(clubUser.club.name) }}
            </AvatarFallback>
        </Avatar>

        <div>
            <p class="text-xs">Loyalty Card</p>
            <h2 class="text-xl font-semibold">{{ cardTitle }}</h2>
            <p>{{ clubUser.user.name }}</p>
        </div>
    </div>
</template>
