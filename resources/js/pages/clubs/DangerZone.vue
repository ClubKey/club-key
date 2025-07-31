<script lang="ts" setup>
import DeleteClub from '@/components/clubs/DeleteClub.vue';
import Heading from '@/components/Heading.vue';
import { Avatar, AvatarImage } from '@/components/ui/avatar';
import AppLayout from '@/layouts/AppLayout.vue';
import ClubLayout from '@/layouts/club/ClubLayout.vue';
import { type BreadcrumbItem, Club } from '@/types';
import { Head } from '@inertiajs/vue3';

const props = defineProps<{
    club: Club;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: props.club.name,
        href: `/clubs/${props.club.slug}/edit`,
    },
    {
        title: 'Danger Zone',
        href: `/clubs/${props.club.slug}/danger-zone`,
    },
];
</script>

<template>
    <AppLayout :breadcrumbs>
        <Head :title="`Danger Zone - ${club.name}`" />
        <ClubLayout :club="club">
            <Avatar v-if="club.logo_path" class="mb-6 h-20 w-20 overflow-hidden rounded-lg">
                <AvatarImage :src="club.logo_path" alt="Club Logo" />
            </Avatar>
            <Heading :title="`Delete ${club.name}`" />

            <DeleteClub :club="club" />
        </ClubLayout>
    </AppLayout>
</template>
