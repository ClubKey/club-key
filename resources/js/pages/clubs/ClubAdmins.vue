<script lang="ts" setup>
import InvitationList from '@/components/admin-invitations/InvitationList.vue';
import InviteAdminForm from '@/components/admin-invitations/InviteAdminForm.vue';
import Heading from '@/components/Heading.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import Avatar from '@/components/ui/avatar/Avatar.vue';
import AvatarFallback from '@/components/ui/avatar/AvatarFallback.vue';
import AvatarImage from '@/components/ui/avatar/AvatarImage.vue';
import { Card } from '@/components/ui/card';
import { getInitials } from '@/composables/useInitials';
import AppLayout from '@/layouts/AppLayout.vue';
import ClubLayout from '@/layouts/club/ClubLayout.vue';
import { AdminInvitation, type BreadcrumbItem, Club, User } from '@/types';
import { page } from '@inertiajs/core/types/page';
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
                        <div class="flex items-center gap-2">
                            <Avatar class="h-10 w-10 overflow-hidden rounded-lg">
                                <AvatarImage v-if="user.avatar" :alt="user.name" :src="user.avatar!" />
                                <AvatarFallback class="rounded-lg text-black dark:text-white">
                                    {{ getInitials(user.name) }}
                                </AvatarFallback>
                            </Avatar>
                            <div class="flex flex-col">
                                <p class="text-sm font-medium">{{ user.name }} {{ user.id === page.props.auth.user.id ? '(You)' : '' }}</p>
                                <p class="text-xs font-bold">{{ user.id === club.owner_id ? 'Club Owner' : 'Admin' }}</p>
                                <p class="text-sm text-neutral-500">{{ user.email }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </Card>
        </ClubLayout>
    </AppLayout>
</template>
