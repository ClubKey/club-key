<script lang="ts" setup>
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { useDate } from '@/composables/useDate';
import { getInitials } from '@/composables/useInitials';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, ClubUser, User } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';

const props = defineProps<{
    clubUser: ClubUser;
    user: User;
}>();

const { formatDate } = useDate();

const authUser = usePage().props.auth.user;

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Members',
        href: route('members.index'),
    },
    {
        title: 'Member Profile',
        href: route('members.show', props.clubUser.id),
    },
];
</script>

<template>
    <AppLayout :breadcrumbs>
        <Head :title="`${clubUser.user?.name} Profile`" />
        <Card class="min-h-[80vh]">
            <CardHeader>
                <div class="flex items-start space-x-4">
                    <Avatar class="h-16 w-16">
                        <AvatarImage v-if="user.avatar" :alt="user.name" :src="user.avatar" />
                        <AvatarFallback class="text-lg">
                            {{ getInitials(user.name) }}
                        </AvatarFallback>
                    </Avatar>
                    <div class="flex-1">
                        <CardTitle class="text-2xl">{{ user.name }} {{ authUser.id === user.id ? '(You)' : '' }} </CardTitle>
                        <CardDescription class="text-base">{{ user.email }}</CardDescription>
                    </div>
                    <Badge :variant="clubUser.role == 'admin' ? 'default' : 'secondary'">
                        {{ clubUser.role === 'admin' ? 'Admin' : 'Member' }}
                    </Badge>
                </div>
            </CardHeader>
            <CardContent class="space-y-6">
                <div class="grid gap-4 md:grid-cols-2">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium">Member Since</h3>
                            <p class="text-sm">{{ formatDate(clubUser.created_at) }}</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium">Account Created</h3>
                            <p class="text-sm">{{ formatDate(user.created_at) }}</p>
                        </div>
                    </div>
                </div>
            </CardContent>
        </Card>
    </AppLayout>
</template>
