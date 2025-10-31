<script lang="ts" setup>
import ClubUserCard from '@/components/club-users/ClubUserCard.vue';
import Heading from '@/components/Heading.vue';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';
import { debounce } from '@/lib/utils';
import { type BreadcrumbItem, type Club, ClubUser, Paginator } from '@/types';
import { Form, Head, InfiniteScroll, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
    club: Club;
    clubUsers: Paginator<ClubUser>;
    search: string;
    sort?: string;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Members',
        href: route('members.index'),
    },
];

const formRef = ref();

const handleSubmit = debounce(() => {
    formRef.value.submit();
}, 300);

const handleSortChange = (value: string) => {
    router.get(
        route('members.index'),
        { 
            sort: value === 'default' ? undefined : value,
            search: props.search || undefined,
        },
        {
            preserveScroll: true,
            preserveState: true,
        },
    );
};
</script>

<template>
    <AppLayout :breadcrumbs>
        <Head :title="`${club.name} Members`" />
        <div class="mb-4 flex items-center justify-between">
            <Heading :title="`${club.name} Members`" description="View your club members." />
        </div>
        <div class="grid gap-2">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                <Form
                    ref="formRef"
                    :action="route('members.index')"
                    :options="{
                        preserveScroll: true,
                        preserveState: true,
                    }"
                    class="flex-1 lg:max-w-md"
                    method="get"
                >
                    <Input :default-value="search" name="search" placeholder="Search members..." type="search" @input="handleSubmit" />
                    <input v-if="sort" name="sort" type="hidden" :value="sort" />
                    <button class="hidden" type="submit">Search</button>
                </Form>
                
                <Select :default-value="sort" @update:model-value="handleSortChange">
                    <SelectTrigger class="w-full sm:w-[220px]">
                        <SelectValue placeholder="Sort by..." />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="default">Default</SelectItem>
                        <SelectItem value="most_checkins">Most Check-ins</SelectItem>
                        <SelectItem value="recent_visit">Most Recent Visit</SelectItem>
                        <SelectItem value="reward_level">Highest Reward Level</SelectItem>
                    </SelectContent>
                </Select>
            </div>

            <div v-if="clubUsers.data.length === 0" class="mt-4">
                <div class="flex items-center justify-center py-12">
                    <h3 class="text-lg font-semibold">Couldn't find any members. Adjust your search?</h3>
                </div>
            </div>

            <InfiniteScroll :buffer="100" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4" data="clubUsers">
                <ClubUserCard v-for="clubUser in clubUsers.data" :key="clubUser.id" :club-user />
            </InfiniteScroll>
        </div>
    </AppLayout>
</template>
