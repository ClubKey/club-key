<script lang="ts" setup>
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { Reward, RewardLevel, type BreadcrumbItem, type Club } from '@/types';
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
        <div class="px-4 py-6">
            <Heading :title="`Edit Reward`" description="Update reward details" />

            <Card class="mt-8 max-w-2xl">
                <CardHeader>
                    <CardTitle>Reward Details</CardTitle>
                    <CardDescription>Update the details for this reward.</CardDescription>
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
                                {{ form.processing ? 'Updating...' : 'Update Reward' }}
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
