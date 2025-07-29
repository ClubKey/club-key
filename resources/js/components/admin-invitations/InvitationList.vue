<script lang="ts" setup>
import { AdminInvitation, Club } from '@/types';
import Invitation from './Invitation.vue';

defineProps<{
    club: Club;
    adminInvitations: AdminInvitation[];
}>();
</script>
<template>
    <div class="flex flex-col gap-4">
        <Invitation v-for="invite in adminInvitations" :key="invite.id" :admin-invitation="invite" :club="club" />
        <div v-if="adminInvitations.length === 0" class="text-muted-foreground">No pending admin invitations found for this club.</div>
    </div>
</template>
