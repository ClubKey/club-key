<script lang="ts" setup>
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Club, type ClubLocation } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    club: Club;
    location: ClubLocation;
}>();

const form = useForm({
    name: props.location.name,
    street_address: props.location.street_address,
    city: props.location.city,
    state: props.location.state,
    zip: props.location.zip,
    phone: props.location.phone || '',
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Locations',
        href: route('locations.index'),
    },
    {
        title: 'Edit Location',
        href: route('locations.edit', props.location.id),
    },
];

const submit = () => {
    form.put(route('locations.update', props.location.id));
};
</script>

<template>
    <AppLayout :breadcrumbs>
        <Head :title="`Edit Location - ${club.name}`" />
        <Heading :title="`Edit Location`" description="Update location details" />

        <Card class="mt-8 max-w-2xl">
            <CardHeader>
                <CardTitle>Location Details</CardTitle>
                <CardDescription> Update the details for this club location.</CardDescription>
            </CardHeader>
            <CardContent>
                <form class="space-y-6" @submit.prevent="submit">
                    <div class="space-y-2">
                        <Label for="name">Location Name</Label>
                        <Input id="name" v-model="form.name" :error="form.errors.name" placeholder="e.g., Downtown Branch" type="text" />
                        <p v-if="form.errors.name" class="text-sm text-destructive">
                            {{ form.errors.name }}
                        </p>
                    </div>

                    <div class="space-y-2">
                        <Label for="street_address">Street Address</Label>
                        <Input
                            id="street_address"
                            v-model="form.street_address"
                            :error="form.errors.street_address"
                            placeholder="e.g., 123 Main St"
                            type="text"
                        />
                        <p v-if="form.errors.street_address" class="text-sm text-destructive">
                            {{ form.errors.street_address }}
                        </p>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div class="space-y-2">
                            <Label for="city">City</Label>
                            <Input id="city" v-model="form.city" :error="form.errors.city" placeholder="City" type="text" />
                            <p v-if="form.errors.city" class="text-sm text-destructive">
                                {{ form.errors.city }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="state">State</Label>
                            <Input id="state" v-model="form.state" :error="form.errors.state" placeholder="State" type="text" />
                            <p v-if="form.errors.state" class="text-sm text-destructive">
                                {{ form.errors.state }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="zip">ZIP Code</Label>
                            <Input id="zip" v-model="form.zip" :error="form.errors.zip" placeholder="ZIP" type="text" />
                            <p v-if="form.errors.zip" class="text-sm text-destructive">
                                {{ form.errors.zip }}
                            </p>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label for="phone">Phone Number (Optional)</Label>
                        <Input id="phone" v-model="form.phone" :error="form.errors.phone" placeholder="e.g., (555) 123-4567" type="tel" />
                        <p v-if="form.errors.phone" class="text-sm text-destructive">
                            {{ form.errors.phone }}
                        </p>
                    </div>

                    <div class="flex gap-4">
                        <Button :disabled="form.processing" type="submit">
                            {{ form.processing ? 'Updating...' : 'Update Location' }}
                        </Button>
                        <Button as-child type="button" variant="outline">
                            <Link :href="route('locations.index')"> Cancel</Link>
                        </Button>
                    </div>
                </form>
            </CardContent>
        </Card>
    </AppLayout>
</template>
