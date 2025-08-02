<template>
    <AppLayout :breadcrumbs>
        <Head :title="`Edit Reward Level - ${club.name}`" />
        <div class="grid gap-6 p-6">
            <div class="flex flex-col justify-between md:flex-row md:items-center">
                <Heading :title="`Edit Reward Level`" description="Update reward level details" />
                <div class="flex flex-wrap items-center gap-3">
                    <Button as-child variant="outline">
                        <Link :href="route('reward-levels.index')">Back to Reward Levels</Link>
                    </Button>
                    <Button as-child>
                        <Link :href="route('reward-levels.rewards.create', rewardLevel.id)">
                            <Plus class="mr-2 h-4 w-4" />
                            Add Reward
                        </Link>
                    </Button>
                </div>
            </div>

            <div class="grid gap-8 lg:grid-cols-2">
                <!-- Reward Level Form -->
                <Card>
                    <CardHeader>
                        <CardTitle>Reward Level Details</CardTitle>
                        <CardDescription>Update the details for this reward level.</CardDescription>
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

                            <div class="space-y-2">
                                <Label for="icon_name">Icon Name (Optional)</Label>
                                <Input
                                    id="icon_name"
                                    v-model="form.icon_name"
                                    :error="form.errors.icon_name"
                                    placeholder="e.g., trophy, star, crown"
                                    type="text"
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
                                    {{ form.processing ? 'Updating...' : 'Update Reward Level' }}
                                </Button>
                                <Button as-child type="button" variant="outline">
                                    <Link :href="route('reward-levels.index')">Cancel</Link>
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>

                <!-- Rewards Management -->
                <Card>
                    <CardHeader>
                        <CardTitle>Rewards</CardTitle>
                        <CardDescription>Manage rewards for this level.</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div v-if="!rewardLevel.rewards || rewardLevel.rewards.length === 0" class="py-8 text-center">
                            <Gift class="mx-auto mb-4 h-12 w-12 text-muted-foreground" />
                            <HeadingSmall title="No rewards yet" />
                            <p class="mt-2 text-sm text-muted-foreground">Add rewards that members can earn at this level.</p>
                            <Button as-child class="mt-4">
                                <Link :href="route('reward-levels.rewards.create', rewardLevel.id)">
                                    <Plus class="mr-2 h-4 w-4" />
                                    Add Reward
                                </Link>
                            </Button>
                        </div>

                        <div v-else-if="rewardLevel.rewards" class="space-y-4">
                            <div
                                v-for="reward in rewardLevel.rewards"
                                :key="reward.id"
                                class="flex items-center justify-between rounded-lg border p-4"
                            >
                                <div class="flex-1">
                                    <p class="font-medium">{{ reward.description }}</p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <Button as-child size="sm" variant="outline">
                                        <Link :href="route('reward-levels.rewards.edit', [rewardLevel.id, reward.id])">
                                            <Edit class="h-4 w-4" />
                                        </Link>
                                    </Button>
                                    <Button class="text-destructive" size="sm" variant="outline" @click="deleteReward(reward)">
                                        <Trash2 class="h-4 w-4" />
                                    </Button>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>

        <!-- Delete Confirmation Dialog -->
        <AlertDialog :open="showDeleteDialog" @update:open="showDeleteDialog = false">
            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogTitle>Delete Reward</AlertDialogTitle>
                    <AlertDialogDescription>
                        Are you sure you want to delete "{{ rewardToDelete?.description }}"? This action cannot be undone.
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel>Cancel</AlertDialogCancel>
                    <AlertDialogAction class="bg-red-600 hover:bg-red-700" @click="confirmDeleteReward">Delete </AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    </AppLayout>
</template>

<script lang="ts" setup>
import Heading from '@/components/Heading.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from '@/components/ui/alert-dialog';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Club, Reward, RewardLevel } from '@/types';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { Edit, Gift, Plus, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps<{
    club: Club;
    rewardLevel: RewardLevel;
}>();

const form = useForm({
    name: props.rewardLevel.name,
    icon_name: props.rewardLevel.icon_name || '',
    min_visits_per_month: props.rewardLevel.min_visits_per_month.toString(),
    color: props.rewardLevel.color || '#6b7280',
    description: props.rewardLevel.description || '',
    percentage_discount: props.rewardLevel.percentage_discount.toString(),
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
];

const rewardToDelete = ref<Reward | null>(null);
const showDeleteDialog = ref(false);

const submit = () => {
    form.put(route('reward-levels.update', props.rewardLevel.id));
};

const deleteReward = (reward: Reward) => {
    rewardToDelete.value = reward;
    showDeleteDialog.value = true;
};

const confirmDeleteReward = () => {
    if (rewardToDelete.value) {
        router.delete(route('reward-levels.rewards.destroy', [props.rewardLevel.id, rewardToDelete.value.id]));
    }
    showDeleteDialog.value = false;
    rewardToDelete.value = null;
};
</script>
