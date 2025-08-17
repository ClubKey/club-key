<script lang="ts" setup>
import Heading from '@/components/Heading.vue';
import { Avatar, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import ClubLayout from '@/layouts/club/ClubLayout.vue';
import { type BreadcrumbItem, Club } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Download } from 'lucide-vue-next';

const props = defineProps<{
    club: Club;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: props.club.name,
        href: `/clubs/${props.club.slug}/edit`,
    },
    {
        title: 'Check-in Code',
        href: `/clubs/${props.club.slug}/check-in-code`,
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
                :title="`${club.name} Check-in Code`"
                description="If you don't want users to check into specific locations, you can use this qr code on your membership flyer for users to check in to your club. This will allow them to earn rewards without needing to be at a specific location."
            />
            <Card class="w-full max-w-2xl">
                <CardContent class="flex flex-col gap-4">
                    <Button as-child class="ml-auto">
                        <a :download="`${club.name} Visit Code`" :href="club.visit_qr_code_path" target="_blank">
                            <Download />
                            Download</a
                        >
                    </Button>

                    <img :src="club.visit_qr_code_path" alt="Club Check-in QR Code" class="w-full" />
                </CardContent>
            </Card>
        </ClubLayout>
    </AppLayout>
</template>
