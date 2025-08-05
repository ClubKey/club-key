<script lang="ts" setup>
import InvitationList from '@/components/admin-invitations/InvitationList.vue';
import InviteAdminForm from '@/components/admin-invitations/InviteAdminForm.vue';
import Member from '@/components/clubs/Member.vue';
import Heading from '@/components/Heading.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Card } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import ClubLayout from '@/layouts/club/ClubLayout.vue';
import { AdminInvitation, type BreadcrumbItem, Club, User } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';

const props = defineProps<{
    club: Club;
    admins: User[];
    adminInvitations: AdminInvitation[];
}>();

const page = usePage();

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
        <Head :title="`${club.name} Admins`" />
        <ClubLayout :club="club">
            <Heading :title="`${club.name} Admins`" />
            <Card v-if="page.props.auth.user.id === club.owner_id" class="p-6">
                <HeadingSmall title="Invite an admin" />

                <InviteAdminForm :club="club" />
            </Card>

            <Card class="p-6">
                <HeadingSmall title="Admin Invitations" />

                <InvitationList :admin-invitations="adminInvitations" :club="club" />
            </Card>

            <Card class="p-6">
                <HeadingSmall title="Current Admins" />
                <div class="flex flex-col gap-4">
                    <div v-for="user in admins" :key="user.id">
                        <Member :club :user />
                    </div>
                </div>
            </Card>
        </ClubLayout>
    </AppLayout>
</template>
