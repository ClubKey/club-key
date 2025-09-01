<script lang="ts" setup>
import DeleteLocation from '@/components/locations/DeleteLocation.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { ClubLocation } from '@/types';
import { Link } from '@inertiajs/vue3';
import { Download, MapPin, Phone, SquarePen } from 'lucide-vue-next';

defineProps<{
    location: ClubLocation;
    canEdit: boolean;
}>();

function formatAddress(location: ClubLocation) {
    return `${location.street_address}, ${location.city}, ${location.state} ${location.zip}`;
}
</script>

<template>
    <Card>
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

            <Button v-if="canEdit" as-child class="ml-auto" size="sm">
                <a :href="location.visit_qr_code_path" :title="`${location.name} Visit Code`" download target="_blank">
                    <Download />
                    Download Check In Code</a
                >
            </Button>
        </CardContent>
        <CardFooter class="border-t">
            <div class="flex w-full gap-2">
                <Button v-if="canEdit" as-child class="flex-1" variant="outline">
                    <Link :href="route('locations.edit', location.id)"><SquarePen /> Edit</Link>
                </Button>
                <DeleteLocation v-if="canEdit" :location="location" />
            </div>
        </CardFooter>
    </Card>
</template>
