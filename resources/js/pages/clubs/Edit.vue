<script lang="ts" setup>
import UpdateClubForm from '@/components/clubs/UpdateClubForm.vue';
import Heading from '@/components/Heading.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Avatar, AvatarImage } from '@/components/ui/avatar';
import { Card } from '@/components/ui/card';
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
];
</script>

<template>
    <AppLayout :breadcrumbs>
        <Head :title="`Edit ${club.name}`" />
        <ClubLayout :club="club">
            <Avatar v-if="club.logo_path" class="mb-6 h-20 w-20 overflow-hidden rounded-lg">
                <AvatarImage :src="club.logo_path" alt="Club Logo" />
            </Avatar>
            <Heading
                :title="`Edit ${club.name}`"
                description="Edit your club information below, like your logo that appears on member reward cards, slug that users visit to join your club and more."
            />
            <Card class="w-full max-w-2xl p-6">
                <HeadingSmall title="Details" />
                <UpdateClubForm :club="club" />
            </Card>
        </ClubLayout>
    </AppLayout>
</template>
