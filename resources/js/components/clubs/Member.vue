<script lang="ts" setup>
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { getInitials } from '@/composables/useInitials';
import { Club, User } from '@/types';
import { usePage } from '@inertiajs/vue3';

defineProps<{
    user: User;
    club: Club;
}>();

const authUser = usePage().props.auth.user;
</script>

<template>
    <div v-if="user" class="flex items-center gap-2">
        <Avatar class="h-10 w-10 overflow-hidden rounded-lg">
            <AvatarImage v-if="user.avatar" :alt="user.name" :src="user.avatar!" />
            <AvatarFallback class="rounded-lg text-black dark:text-white">
                {{ getInitials(user.name) }}
            </AvatarFallback>
        </Avatar>
        <div class="flex flex-col">
            <p class="text-sm font-medium">{{ user.name }} {{ user.id === authUser.id ? '(You)' : '' }}</p>
            <p class="text-xs font-bold capitalize">
                {{ club.owner_id === user.id ? 'Club Owner' : user.club_role }}
            </p>
            <p class="text-sm text-neutral-500">{{ user.email }}</p>
        </div>
    </div>
</template>
