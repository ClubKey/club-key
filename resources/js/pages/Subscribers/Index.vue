<script lang="ts" setup>
import Heading from '@/components/Heading.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { debounce } from '@/lib/utils';
import { type BreadcrumbItem, type Club, type CommunicationSubscription, type Paginator } from '@/types';
import { Form, Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps<{
    club: Club;
    subscriptions: Paginator<CommunicationSubscription>;
    filters: {
        contact_type?: string;
        topic?: string;
        status?: string;
        search?: string;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: props.club.name,
        href: `/clubs/${props.club.slug}/edit`
    },
    {
        title: 'Notification Subscriptions',
        href: route('subscribers.index')
    }
];

const formRef = ref();

const handleSubmit = debounce(() => {
    formRef.value.submit();
}, 300);

const handleFilterChange = (filterName: string, value: string) => {
    const filters = { ...props.filters };
    if (value === '' || value === 'all') {
        delete filters[filterName as keyof typeof filters];
    } else {
        filters[filterName as keyof typeof filters] = value;
    }

    router.get(
        route('subscribers.index'),
        filters,
        {
            preserveScroll: true,
            preserveState: true
        }
    );
};

const formatContactValue = (subscription: CommunicationSubscription): string => {
    return subscription.contact_value;
};

const getStatusBadgeClass = (status: string): string => {
    switch (status) {
        case 'subscribed':
            return 'bg-green-100 text-green-800';
        case 'unsubscribed':
            return 'bg-red-100 text-red-800';
        case 'pending':
            return 'bg-yellow-100 text-yellow-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
};
</script>

<template>
    <AppLayout :breadcrumbs>
        <Head :title="`Notification Subscriptions - ${club.name}`" />
        <div class="flex flex-col items-start gap-4">
            <Button as-child variant="outline">
                <Link :href="route('members.index')">
                    <ArrowLeft />
                    Members
                </Link>
            </Button>
        </div>
        <div class="mb-4 gap-4 flex flex-col md:flex-row  md:items-center justify-between">
            <Heading :title="`Notification Subscriptions - ${club.name}`"
                     description="Manage and view all notification subscriptions for your club." />
            <Button as-child>
                <Link :href="route('subscribers.import')">Import</Link>
            </Button>
        </div>

        <Card class="p-6">
            <HeadingSmall title="Filters" />
            <Form
                ref="formRef"
                :action="route('subscribers.index')"
                :options="{
                        preserveScroll: true,
                        preserveState: true,
                    }"
                class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4"
                method="get"
            >
                <div class="grid gap-2">
                    <Label for="search">Search</Label>
                    <Input
                        id="search"
                        :default-value="filters.search"
                        name="search"
                        placeholder="Search by contact value..."
                        type="search"
                        @input="handleSubmit"
                    />
                </div>

                <div class="grid gap-2">
                    <Label for="contact_type">Contact Type</Label>
                    <Select :default-value="filters.contact_type || 'all'"
                            @update:model-value="(value) => handleFilterChange('contact_type', value)">
                        <SelectTrigger id="contact_type">
                            <SelectValue placeholder="All types" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">All Types</SelectItem>
                            <SelectItem value="email">Email</SelectItem>
                            <SelectItem value="sms">SMS</SelectItem>
                        </SelectContent>
                    </Select>
                </div>

                <div class="grid gap-2">
                    <Label for="topic">Topic</Label>
                    <Select :default-value="filters.topic || 'all'"
                            @update:model-value="(value) => handleFilterChange('topic', value)">
                        <SelectTrigger id="topic">
                            <SelectValue placeholder="All topics" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">All Topics</SelectItem>
                            <SelectItem value="promotions">Promotions</SelectItem>
                            <SelectItem value="notifications">Notifications</SelectItem>
                        </SelectContent>
                    </Select>
                </div>

                <div class="grid gap-2">
                    <Label for="status">Status</Label>
                    <Select :default-value="filters.status || 'all'"
                            @update:model-value="(value) => handleFilterChange('status', value)">
                        <SelectTrigger id="status">
                            <SelectValue placeholder="All statuses" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">All Statuses</SelectItem>
                            <SelectItem value="subscribed">Subscribed</SelectItem>
                            <SelectItem value="unsubscribed">Unsubscribed</SelectItem>
                            <SelectItem value="pending">Pending</SelectItem>
                        </SelectContent>
                    </Select>
                </div>

                <input v-if="filters.contact_type" :value="filters.contact_type" name="contact_type"
                       type="hidden" />
                <input v-if="filters.topic" :value="filters.topic" name="topic" type="hidden" />
                <input v-if="filters.status" :value="filters.status" name="status" type="hidden" />
                <button class="hidden" type="submit">Search</button>
            </Form>
        </Card>

        <Card class="p-6">
            <div class="mb-4 flex items-center justify-between">
                <HeadingSmall :title="`Notification Subscriptions (${subscriptions.total})`" />
            </div>

            <div v-if="subscriptions.data.length === 0" class="py-12 text-center">
                <p class="text-neutral-600">No notification subscriptions found. Try adjusting your filters or import
                    notification subscriptions.</p>
            </div>

            <div v-else class="overflow-x-auto">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Contact</TableHead>
                            <TableHead>Type</TableHead>
                            <TableHead>Topic</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead>User</TableHead>
                            <TableHead>Source</TableHead>
                            <TableHead>Subscribed</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="subscription in subscriptions.data" :key="subscription.id">
                            <TableCell class="font-medium">{{ formatContactValue(subscription) }}</TableCell>
                            <TableCell>
                                <span class="capitalize">{{ subscription.contact_type }}</span>
                            </TableCell>
                            <TableCell>
                                <span class="capitalize">{{ subscription.topic }}</span>
                            </TableCell>
                            <TableCell>
                                    <span
                                        :class="['rounded-full px-2 py-1 text-xs font-semibold', getStatusBadgeClass(subscription.status)]">
                                        {{ subscription.status }}
                                    </span>
                            </TableCell>
                            <TableCell>
                                <span v-if="subscription.user">{{ subscription.user.name }}</span>
                                <span v-else class="text-neutral-500">No user</span>
                            </TableCell>
                            <TableCell>
                                    <span v-if="subscription.source"
                                          class="text-sm text-neutral-600">{{ subscription.source }}</span>
                                <span v-else class="text-neutral-400">—</span>
                            </TableCell>
                            <TableCell>
                                    <span v-if="subscription.subscribed_at" class="text-sm text-neutral-600">
                                        {{ new Date(subscription.subscribed_at).toLocaleDateString() }}
                                    </span>
                                <span v-else class="text-neutral-400">—</span>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <div v-if="subscriptions.last_page > 1" class="mt-4 flex items-center justify-between">
                <div class="text-sm text-neutral-600">
                    Showing {{ subscriptions.from || 0 }} to {{ subscriptions.to || 0 }} of {{ subscriptions.total
                    }} notification subscriptions
                </div>
                <div class="flex gap-2">
                    <Button
                        v-if="subscriptions.current_page > 1"
                        variant="outline"
                        @click="router.get(subscriptions.first_page_url, {}, { preserveScroll: true })"
                    >
                        First
                    </Button>
                    <Button
                        v-if="subscriptions.current_page > 1"
                        variant="outline"
                        @click="router.get(subscriptions.links.find((l: any) => l.label === '&laquo; Previous')?.url || '', {}, { preserveScroll: true })"
                    >
                        Previous
                    </Button>
                    <span class="px-4 py-2 text-sm">
                            Page {{ subscriptions.current_page }} of {{ subscriptions.last_page }}
                        </span>
                    <Button
                        v-if="subscriptions.current_page < subscriptions.last_page"
                        variant="outline"
                        @click="router.get(subscriptions.links.find((l: any) => l.label === 'Next &raquo;')?.url || '', {}, { preserveScroll: true })"
                    >
                        Next
                    </Button>
                    <Button
                        v-if="subscriptions.current_page < subscriptions.last_page"
                        variant="outline"
                        @click="router.get(subscriptions.last_page_url, {}, { preserveScroll: true })"
                    >
                        Last
                    </Button>
                </div>
            </div>
        </Card>
    </AppLayout>
</template>
