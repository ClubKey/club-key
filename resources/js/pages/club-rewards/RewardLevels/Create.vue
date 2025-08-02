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
    icon_name: '',
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
        <div class="px-4 py-6">
            <Heading :title="`Create Reward Level`" description="Add a new reward level for your club" />

            <Card class="mt-8 max-w-2xl">
                <CardHeader>
                    <CardTitle>Reward Level Details</CardTitle>
                    <CardDescription>Enter the details for your new reward level.</CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="space-y-2">
                            <Label for="name">Level Name</Label>
                            <Input id="name" v-model="form.name" type="text" placeholder="e.g., Bronze, Silver, Gold" :error="form.errors.name" />
                            <p v-if="form.errors.name" class="text-sm text-destructive">
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="icon_name">Icon Name (Optional)</Label>
                            <Input
                                id="icon_name"
                                v-model="form.icon_name"
                                type="text"
                                placeholder="e.g., trophy, star, crown"
                                :error="form.errors.icon_name"
                            />
                            <p v-if="form.errors.icon_name" class="text-sm text-destructive">
                                {{ form.errors.icon_name }}
                            </p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <Label for="min_visits_per_month">Minimum Visits per Month</Label>
                                <Input
                                    id="min_visits_per_month"
                                    v-model="form.min_visits_per_month"
                                    type="number"
                                    placeholder="5"
                                    :error="form.errors.min_visits_per_month"
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
                                    type="number"
                                    placeholder="10"
                                    :error="form.errors.percentage_discount"
                                />
                                <p v-if="form.errors.percentage_discount" class="text-sm text-destructive">
                                    {{ form.errors.percentage_discount }}
                                </p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="color">Color (Optional)</Label>
                            <Input id="color" v-model="form.color" type="color" :error="form.errors.color" />
                            <p v-if="form.errors.color" class="text-sm text-destructive">
                                {{ form.errors.color }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="description">Description (Optional)</Label>
                            <Textarea
                                id="description"
                                v-model="form.description"
                                placeholder="Describe what this reward level offers..."
                                :error="form.errors.description"
                            />
                            <p v-if="form.errors.description" class="text-sm text-destructive">
                                {{ form.errors.description }}
                            </p>
                        </div>

                        <div class="flex gap-4">
                            <Button type="submit" :disabled="form.processing">
                                {{ form.processing ? 'Creating...' : 'Create Reward Level' }}
                            </Button>
                            <Button type="button" variant="outline" as-child>
                                <Link :href="route('reward-levels.index')">Cancel</Link>
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
