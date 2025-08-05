<script lang="ts" setup>
import Heading from '@/components/Heading.vue';
import DeleteLocation from '@/components/locations/DeleteLocation.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Club, type ClubLocation } from '@/types';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { MapPin, Phone, Plus } from 'lucide-vue-next';
import { computed } from 'vue';

defineProps<{
    club: Club;
    locations: ClubLocation[];
}>();

const page = usePage();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Locations',
        href: route('locations.index'),
    },
];

const canCreate = computed(() => {
    return page.props.auth.user.club_role === 'admin';
});

const formatAddress = (location: ClubLocation) => {
    return `${location.street_address}, ${location.city}, ${location.state} ${location.zip}`;
};
</script>

<template>
    <AppLayout :breadcrumbs>
        <Head :title="`${club.name} Locations`" />
        <div class="flex flex-col items-start justify-between gap-4 md:flex-row md:items-center">
            <Heading :title="`${club.name} Locations`" description="Manage your club locations" />
            <Button v-if="canCreate" as-child class="fixed right-4 bottom-4 md:static">
                <Link :href="route('locations.create')">
                    <Plus />
                    Add Location
                </Link>
            </Button>
        </div>

        <div v-if="locations.length === 0" class="mt-4">
            <Card>
                <CardContent class="flex flex-col items-center justify-center py-12">
                    <MapPin class="mb-4 h-12 w-12 text-muted-foreground" />
                    <h3 class="mb-2 text-lg font-semibold">No locations yet</h3>
                    <p class="mb-4 text-center text-muted-foreground">Get started by adding your first club location.</p>
                    <Button v-if="canCreate" as-child>
                        <Link :href="route('locations.create')">
                            <Plus />
                            Add Location
                        </Link>
                    </Button>
                </CardContent>
            </Card>
        </div>

        <div v-else class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            <Card v-for="location in locations" :key="location.id" class="group">
                <CardHeader>
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <CardTitle class="text-lg">{{ location.name }}</CardTitle>
                            <CardDescription class="mt-2">
                                <div class="flex items-start gap-2">
                                    <MapPin class="mt-0.5 h-4 w-4 flex-shrink-0" />
                                    <span>{{ formatAddress(location) }}</span>
                                </div>
                            </CardDescription>
                        </div>
                    </div>
                </CardHeader>
                <CardContent>
                    <div v-if="location.phone" class="mb-4 flex items-center gap-2 text-sm text-muted-foreground">
                        <Phone />
                        <span>{{ location.phone }}</span>
                    </div>

                    <Separator class="my-4" />

                    <div class="flex gap-2">
                        <Button v-if="canCreate" as-child class="flex-1" size="sm" variant="outline">
                            <Link :href="route('locations.edit', location.id)"> Edit</Link>
                        </Button>
                        <DeleteLocation v-if="canCreate" :location="location" />
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
