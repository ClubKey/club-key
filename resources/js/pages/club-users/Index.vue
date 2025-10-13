<script lang="ts" setup>
import ClubUserCard from '@/components/club-users/ClubUserCard.vue';
import Heading from '@/components/Heading.vue';
import DataTableToolbar from '@/components/ui/DataTableToolbar.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { useDataTable } from '@/composables/useDataTable';
import { type BreadcrumbItem, type Club, type DataTableMeta, ClubUser } from '@/types';
import { Head } from '@inertiajs/vue3';

const props = defineProps<{ club: Club; clubUsers: ClubUser[]; dataTable: DataTableMeta }>();

const table = useDataTable({
    routeName: 'members.index',
    meta: props.dataTable,
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Members',
        href: route('members.index'),
    },
];
</script>

<template>
    <AppLayout :breadcrumbs>
        <Head :title="`${props.club.name} Members`" />
        <div class="flex flex-col gap-6">
            <Heading :title="`${props.club.name} Members`" description="View your club members." />
            <DataTableToolbar
                :search="table.search.value"
                :sort-key="table.sortKey.value"
                :sorts="table.sorts"
                :filters="table.filtersConfig"
                :filters-state="table.filters"
                :active-filter-count="table.activeFilterCount.value"
                search-placeholder="Search by name, email, or phone"
                @update:search="table.setSearch"
                @sort-change="table.setSort"
                @filter-toggle="({ key, value }) => table.toggleFilter(key, value)"
                @filter-clear="table.clearFilters"
            />
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4">
            <ClubUserCard v-for="clubUser in props.clubUsers" :key="clubUser.id" :club-user="clubUser" />
        </div>
    </AppLayout>
</template>
