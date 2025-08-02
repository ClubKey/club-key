<script lang="ts" setup>
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { RewardLevel, type BreadcrumbItem, type Club } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    club: Club;
    rewardLevel: RewardLevel;
}>();

const form = useForm({
    description: '',
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Reward Levels',
        href: route('reward-levels.index'),
    },
    {
        title: 'Edit Reward Level',
        href: route('reward-levels.edit', props.rewardLevel.id),
    },
    {
        title: 'Create Reward',
        href: route('reward-levels.rewards.create', props.rewardLevel.id),
    },
];

const submit = () => {
    form.post(route('reward-levels.rewards.store', props.rewardLevel.id));
};
</script>

<template>
    <AppLayout :breadcrumbs>
        <Head :title="`Create Reward - ${club.name}`" />
        <div class="px-4 py-6">
            <Heading :title="`Create Reward`" description="Add a new reward for this level" />

            <Card class="mt-8 max-w-2xl">
                <CardHeader>
                    <CardTitle>Reward Details</CardTitle>
                    <CardDescription>Enter the details for your new reward.</CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="space-y-2">
                            <Label for="description">Reward Description</Label>
                            <Textarea
                                id="description"
                                v-model="form.description"
                                placeholder="e.g., Free coffee, 20% off next purchase, Free dessert"
                                :error="form.errors.description"
                            />
                            <p v-if="form.errors.description" class="text-sm text-destructive">
                                {{ form.errors.description }}
                            </p>
                        </div>

                        <div class="flex gap-4">
                            <Button type="submit" :disabled="form.processing">
                                {{ form.processing ? 'Creating...' : 'Create Reward' }}
                            </Button>
                            <Button type="button" variant="outline" as-child>
                                <Link :href="route('reward-levels.edit', rewardLevel.id)">Cancel</Link>
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
