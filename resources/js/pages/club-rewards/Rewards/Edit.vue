<script lang="ts" setup>
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Club, Reward, RewardLevel } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    club: Club;
    reward: Reward;
    rewardLevel: RewardLevel;
}>();

const form = useForm({
    description: props.reward.description,
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
        title: 'Edit Reward',
        href: route('reward-levels.rewards.edit', [props.rewardLevel.id, props.reward.id]),
    },
];

const submit = () => {
    form.put(route('reward-levels.rewards.update', [props.rewardLevel.id, props.reward.id]));
};
</script>

<template>
    <AppLayout :breadcrumbs>
        <Head :title="`Edit Reward - ${club.name}`" />
        <Heading :title="`Edit Reward`" description="Update reward details" />

        <Card class="mt-8 max-w-2xl">
            <CardHeader>
                <CardTitle>Reward Details</CardTitle>
                <CardDescription>Update the details for this reward.</CardDescription>
            </CardHeader>
            <CardContent>
                <form class="space-y-6" @submit.prevent="submit">
                    <div class="space-y-2">
                        <Label for="description">Reward Description</Label>
                        <Textarea
                            id="description"
                            v-model="form.description"
                            :error="form.errors.description"
                            placeholder="e.g., Free coffee, 20% off next purchase, Free dessert"
                        />
                        <p v-if="form.errors.description" class="text-sm text-destructive">
                            {{ form.errors.description }}
                        </p>
                    </div>

                    <div class="flex gap-4">
                        <Button :disabled="form.processing" type="submit">
                            {{ form.processing ? 'Updating...' : 'Update Reward' }}
                        </Button>
                        <Button as-child type="button" variant="outline">
                            <Link :href="route('reward-levels.edit', rewardLevel.id)">Cancel</Link>
                        </Button>
                    </div>
                </form>
            </CardContent>
        </Card>
    </AppLayout>
</template>
