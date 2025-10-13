<script lang="ts" setup>
import ClubUserCard from '@/components/club-users/ClubUserCard.vue';
import Heading from '@/components/Heading.vue';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Club, ClubUser, Paginator } from '@/types';
import { Form, Head, InfiniteScroll } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps<{
    club: Club;
    clubUsers: Paginator<ClubUser>;
    search: string;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Members',
        href: route('members.index'),
    },
];

const formRef = ref();

const handleSubmit = () => {
    setTimeout(() => {
        formRef.value.submit();
    }, 1000);
};
</script>

<template>
    <AppLayout :breadcrumbs>
        <Head :title="`${club.name} Members`" />
        <div class="flex items-center justify-between">
            <Heading :title="`${club.name} Members`" description="View your club members." />
        </div>

        <Form
            ref="formRef"
            :action="route('members.index')"
            :options="{
                preserveScroll: true,
                preserveState: true,
            }"
            class="lg:mr-auto lg:min-w-72"
            method="get"
        >
            <Input :default-value="search" name="search" placeholder="Search members..." type="search" @input="handleSubmit" />
            <button class="hidden" type="submit">Search</button>
        </Form>

        <div v-if="clubUsers.data.length === 0" class="mt-4">
            <div class="flex items-center justify-center py-12">
                <h3 class="text-lg font-semibold">Couldn't find any members. Adjust your search?</h3>
            </div>
        </div>

        <InfiniteScroll :buffer="100" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4" data="clubUsers">
            <ClubUserCard v-for="clubUser in clubUsers.data" :key="clubUser.id" :club-user />
        </InfiniteScroll>
    </AppLayout>
</template>
