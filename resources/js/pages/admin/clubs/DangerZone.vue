<script lang="ts" setup>
import DeleteClub from '@/components/clubs/DeleteClub.vue';
import Heading from '@/components/Heading.vue';
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
            <Heading description="Delete your club and all of its resources" title="Delete club" />

            <DeleteClub :club="club" />
        </ClubLayout>
    </AppLayout>
</template>
