<script lang="ts" setup>
import DeleteLocation from '@/components/locations/DeleteLocation.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { ClubLocation } from '@/types';
import { Link } from '@inertiajs/vue3';
import { Download, MapPin, Phone, SquarePen } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const props = defineProps<{
    location: ClubLocation;
    canEdit: boolean;
}>();

const copyText = ref('Copy Check-In Tap Link');

const linkToGoogleAddress = computed(() => `https://www.google.com/maps/search/?api=1&query=${props.location.full_address}`);

function formatAddress(location: ClubLocation) {
    return `${location.street_address}, ${location.city}, ${location.state} ${location.zip}`;
}

function copyVisitLink() {
    if (!props.location.visit_tap_link) return;
    navigator.clipboard.writeText(props.location.visit_tap_link);
    copyText.value = 'Copied!';
    setTimeout(() => {
        copyText.value = 'Copy Visit Tap Link';
    }, 5000);
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
            <div v-if="canEdit" class="flex flex-col items-start gap-2">
                <Button v-if="location.visit_tap_link" size="sm" variant="outline" @click="copyVisitLink">{{ copyText }} </Button>

                <Button as-child size="sm">
                    <a :href="location.visit_qr_code_path" :title="`${location.name} Visit Code`" download target="_blank">
                        <Download />
                        Download Check In Code</a
                    >
                </Button>
            </div>
        </CardContent>
        <CardFooter class="mt-auto border-t">
            <div v-if="canEdit" class="flex w-full gap-2">
                <Button as-child class="flex-1" variant="outline">
                    <Link :href="route('locations.edit', location.id)">
                        <SquarePen />
                        Edit
                    </Link>
                </Button>
                <DeleteLocation :location="location" />
            </div>
            <a
                v-else
                :href="linkToGoogleAddress"
                class="flex w-full items-center justify-center gap-2 transition duration-200 ease-in-out hover:underline"
                target="_blank"
            >
                <MapPin class="h-4 w-4" />
                Get Directions
            </a>
        </CardFooter>
    </Card>
</template>
