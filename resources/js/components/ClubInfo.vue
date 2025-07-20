<script lang="ts" setup>
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { useInitials } from '@/composables/useInitials';
import type { Club } from '@/types';
import { computed } from 'vue';

interface Props {
    club: Club | null;
    showSlug?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    showSlug: false,
});

const { getInitials } = useInitials();

// Compute whether we should show the avatar image
const showAvatar = computed(() => props.club?.logo_path && props.club?.logo_path !== '');
</script>

<template>
    <Avatar class="h-8 w-8 overflow-hidden rounded-lg">
        <AvatarImage v-if="showAvatar" :alt="club?.name" :src="club?.logo_path!" />
        <AvatarFallback class="rounded-lg text-black dark:text-white">
            {{ getInitials(club?.name!) ?? 'No Current Club' }}
        </AvatarFallback>
    </Avatar>

    <div class="grid flex-1 text-left text-sm leading-tight">
        <span class="truncate font-medium">{{ club?.name ?? 'No Current Club' }}</span>
        <span v-if="showSlug" class="truncate text-xs text-muted-foreground">{{ club?.slug ?? 'No email' }}</span>
    </div>
</template>
