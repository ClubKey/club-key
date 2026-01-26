<script lang="ts" setup>
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Club } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps<{
    club: Club;
}>();

const form = useForm({
    name: '',
    min_visits_per_month: '',
    color: '#6b7280',
    description: '',
    percentage_discount: '',
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Reward Levels',
        href: route('reward-levels.index'),
    },
    {
        title: 'Create Reward Level',
        href: route('reward-levels.create'),
    },
];

const submit = () => {
    form.post(route('reward-levels.store'));
};
</script>

<template>
    <AppLayout :breadcrumbs>
        <Head :title="`Create Reward Level - ${club.name}`" />
        <Heading :title="`Create Reward Level`" description="Add a new reward level for your club" />

        <Card class="max-w-2xl">
            <CardHeader>
                <CardTitle>Reward Level Details</CardTitle>
                <CardDescription>Enter the details for your new reward level.</CardDescription>
            </CardHeader>
            <CardContent>
                <form class="space-y-6" @submit.prevent="submit">
                    <div class="space-y-2">
                        <Label for="name">Level Name</Label>
                        <Input id="name" v-model="form.name" :error="form.errors.name" placeholder="e.g., Bronze, Silver, Gold" type="text" />
                        <p v-if="form.errors.name" class="text-sm text-destructive">
                            {{ form.errors.name }}
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <Label for="min_visits_per_month">Minimum Visits per Month</Label>
                            <Input
                                id="min_visits_per_month"
                                v-model="form.min_visits_per_month"
                                :error="form.errors.min_visits_per_month"
                                placeholder="5"
                                type="number"
                            />
                            <p v-if="form.errors.min_visits_per_month" class="text-sm text-destructive">
                                {{ form.errors.min_visits_per_month }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="percentage_discount">Percentage Discount</Label>
                            <Input
                                id="percentage_discount"
                                v-model="form.percentage_discount"
                                :error="form.errors.percentage_discount"
                                placeholder="10"
                                type="number"
                            />
                            <p v-if="form.errors.percentage_discount" class="text-sm text-destructive">
                                {{ form.errors.percentage_discount }}
                            </p>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label for="color">Color (Optional)</Label>
                        <Input id="color" v-model="form.color" :error="form.errors.color" type="color" />
                        <p v-if="form.errors.color" class="text-sm text-destructive">
                            {{ form.errors.color }}
                        </p>
                    </div>

                    <div class="space-y-2">
                        <Label for="description">Description (Optional)</Label>
                        <Textarea
                            id="description"
                            v-model="form.description"
                            :error="form.errors.description"
                            placeholder="Describe what this reward level offers..."
                        />
                        <p v-if="form.errors.description" class="text-sm text-destructive">
                            {{ form.errors.description }}
                        </p>
                    </div>

                    <div class="flex gap-4">
                        <Button :disabled="form.processing" type="submit">
                            {{ form.processing ? 'Creating...' : 'Create Reward Level' }}
                        </Button>
                        <Button as-child type="button" variant="outline">
                            <Link :href="route('reward-levels.index')">Cancel</Link>
                        </Button>
                    </div>
                </form>
            </CardContent>
        </Card>
    </AppLayout>
</template>
