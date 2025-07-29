<script lang="ts" setup>
import Button from '@/components/ui/button/Button.vue';
import { formatDate } from '@/composables/useDate';
import { AdminInvitation, Club } from '@/types';
import { router } from '@inertiajs/vue3';
import { Mail, Trash } from 'lucide-vue-next';
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from '../ui/alert-dialog';

const props = defineProps<{
    club: Club;
    adminInvitation: AdminInvitation;
}>();

const resendAdminInvitation = () => {
    router.post(
        route('clubs.admin-invitations.resend', {
            club: props.club.slug,
            adminInvitation: props.adminInvitation,
        }),
    );
};

const revokeAdminInvitation = () => {
    router.delete(
        route('clubs.admin-invitations.destroy', {
            club: props.club.slug,
            admin_invitation: props.adminInvitation,
        }),
    );
};
</script>

<template>
    <div>
        <div class="flex items-center justify-between gap-2">
            <div class="flex flex-col">
                <p class="text-sm font-medium">{{ adminInvitation.email }}</p>
                <p class="text-sm text-neutral-500">Expires: {{ formatDate(adminInvitation.expires_at) }}</p>
            </div>
            <div class="flex items-center gap-2">
                <Button aria-label="Resend adminInvitation" variant="default" @click="resendAdminInvitation">
                    <Mail />
                    Resend
                </Button>

                <AlertDialog>
                    <AlertDialogTrigger as-child>
                        <Button aria-label="Revoke adminInvitation" size="icon" variant="destructive">
                            <Trash />
                        </Button>
                    </AlertDialogTrigger>
                    <AlertDialogContent>
                        <AlertDialogHeader>
                            <AlertDialogTitle>Are you absolutely sure?</AlertDialogTitle>
                            <AlertDialogDescription>
                                This action cannot be undone. This will permanently revoke the admin invitation.
                            </AlertDialogDescription>
                        </AlertDialogHeader>
                        <AlertDialogFooter>
                            <AlertDialogCancel>Cancel</AlertDialogCancel>
                            <AlertDialogAction variant="destructive" @click="revokeAdminInvitation">Revoke </AlertDialogAction>
                        </AlertDialogFooter>
                    </AlertDialogContent>
                </AlertDialog>
            </div>
        </div>
    </div>
</template>
