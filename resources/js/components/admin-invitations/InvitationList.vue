<script setup lang="ts">
import { AdminInvitation, Club } from '@/types';
import Invitation from './Invitation.vue';

defineProps<{
    club: Club;
    adminInvitations: AdminInvitation[];
}>();
</script>
<template>
    <div class="flex flex-col gap-4">
        <Invitation v-for="invite in adminInvitations" :key="invite.id" :club="club" :admin-invitation="invite" />
    </div>
</template>
