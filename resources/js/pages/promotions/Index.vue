<script lang="ts" setup>
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import DataTable from '@/components/ui/DataTable.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Paginator, Promotion } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Gift, Plus } from 'lucide-vue-next';
import { h } from 'vue';

defineProps<{
    promotions: Paginator<Promotion>;
}>();

const columns = [
    {
        header: 'Name',
        accessorKey: 'name',
    },
    {
        header: 'Active',
        accessorFn: (row: Promotion) => (row.is_active ? 'Yes' : 'No'),
    },
    {
        header: 'Publishes At',
        accessorFn: (row: Promotion) => (row.publish_at ? new Date(row.publish_at).toLocaleString() : 'Not scheduled'),
    },
    {
        header: 'Ends At',
        accessorFn: (row: Promotion) => (row.end_at ? new Date(row.end_at).toLocaleDateString() : 'No end date'),
    },
    {
        header: 'Actions',
        enableSorting: false,
        cell: ({ row }: { row: { original: Promotion } }) =>
            h('div', { class: 'flex items-center gap-3' }, [
                h(
                    Link,
                    {
                        href: route('promotions.show', row.original.id),
                        class: 'text-sm font-medium text-primary hover:underline',
                    },
                    () => 'View',
                ),
                h(
                    Link,
                    {
                        href: route('promotions.edit', row.original.id),
                        class: 'text-sm font-medium text-muted-foreground hover:text-primary',
                    },
                    () => 'Edit',
                ),
            ]),
    },
];
</script>

<template>
    <AppLayout>
        <Head title="Promotions" />
        <div class="mb-4 flex items-center justify-between gap-4">
            <Heading description="View your clubs current and past promotions" title="Promotions" />
            <Button as="child"><Link :href="route('promotions.create')">Create Promotion</Link></Button>
        </div>
        <DataTable v-if="promotions.total" :columns="columns" :data="promotions" />
        <Card v-else>
            <CardContent class="flex flex-col items-center justify-center py-12">
                <Gift class="mb-4 h-12 w-12 text-muted-foreground" />
                <h3 class="mb-2 text-lg font-semibold">No promotions yet</h3>
                <p class="mb-4 text-center text-muted-foreground">Send your club your first promotion.</p>
                <Button as-child>
                    <Link :href="route('promotions.create')">
                        <Plus />
                        Add Promotion
                    </Link>
                </Button>
            </CardContent>
        </Card>
    </AppLayout>
</template>
