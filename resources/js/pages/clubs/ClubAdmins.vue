<script lang="ts" setup>
import InviteAdminForm from '@/components/admin-invitations/InviteAdminForm.vue';
import Heading from '@/components/Heading.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Card } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import ClubLayout from '@/layouts/club/ClubLayout.vue';
import { AdminInvitation, type BreadcrumbItem, Club, User } from '@/types';

const props = defineProps<{
    club: Club;
    admins: User[];
    adminInvitations: AdminInvitation[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: props.club.name,
        href: `/clubs/${props.club.slug}/edit`,
    },
    {
        title: 'Admins',
        href: `/clubs/${props.club.slug}/admins`,
    },
];
</script>

<template>
    <AppLayout :breadcrumbs>
        <ClubLayout :club="club">
            <Heading :title="`${club.name} Admins`" />
            <Card class="w-full max-w-2xl p-6">
                <HeadingSmall title="Invite an admin" />

                <InviteAdminForm :club="club" />
            </Card>
        </ClubLayout>
    </AppLayout>
</template>
