<script lang="ts" setup>
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger
} from '@/components/ui/dialog';
import AppLayout from '@/layouts/AppLayout.vue';
import { Promotion } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import { Pencil, Trash } from 'lucide-vue-next';

const props = defineProps<{ promotion: Promotion }>();

const deleteForm = useForm({});
const isDeleteDialogOpen = ref(false);

const title = computed(() => `${props.promotion.name} Promotion`);

const isActiveLabel = computed(() => (props.promotion.is_active ? 'Active' : 'Inactive'));
const isActiveVariant = computed(() => (props.promotion.is_active ? 'default' : 'outline'));

const isPublished = computed(() => !!props.promotion.published_at);

const formatDateTime = (value: string | null, fallback = 'Not set') => {
    if (!value) return fallback;
    return new Date(value).toLocaleString();
};

const handleDelete = (event: Event) => {
    event.preventDefault();
    deleteForm.delete(route('promotions.destroy', props.promotion.id), {
        onSuccess: () => {
            isDeleteDialogOpen.value = false;
        }
    });
};

const closeDeleteDialog = () => {
    deleteForm.clearErrors();
    deleteForm.reset();
};

watch(isDeleteDialogOpen, (isOpen) => {
    if (!isOpen) closeDeleteDialog();
});
</script>

<template>
    <AppLayout>
        <Head :title="title" />

        <!-- Top heading and actions -->
        <div class="flex flex-col gap-4 flex-row md:items-center md:justify-between">
            <Heading :title="promotion.name" description="Review your promotion details." />
            <div class="flex gap-2 ml-auto">
                <Button as-child size="sm" variant="outline">
                    <Link :href="route('promotions.edit', promotion.id)">
                        <Pencil />
                        <span
                            class="sr-only lg:not-sr-only">Edit Promotion</span>
                    </Link>
                </Button>

                <!-- Delete Dialog -->
                <Dialog v-model:open="isDeleteDialogOpen">
                    <DialogTrigger as-child>
                        <Button :disabled="deleteForm.processing" size="sm" type="button" variant="destructive">
                            <Trash />
                            <span
                                class="sr-only lg:not-sr-only">{{ deleteForm.processing ? 'Deleting...' : 'Delete Promotion'
                                }}</span>
                        </Button>
                    </DialogTrigger>

                    <DialogContent>
                        <form class="space-y-6" @submit="handleDelete">
                            <DialogHeader class="space-y-3">
                                <DialogTitle>Delete promotion</DialogTitle>
                                <DialogDescription>
                                    Are you sure you want to delete this promotion? This action cannot be undone.
                                </DialogDescription>
                            </DialogHeader>

                            <div
                                class="rounded-lg border border-red-100 bg-red-50 p-4 dark:border-red-200/10 dark:bg-red-700/10">
                                <div class="space-y-1 text-red-600 dark:text-red-100">
                                    <p class="font-medium">Warning</p>
                                    <p class="text-sm">
                                        Deleting this promotion will permanently remove it from your club.
                                    </p>
                                </div>
                            </div>

                            <DialogFooter class="gap-2">
                                <DialogClose as-child>
                                    <Button variant="secondary" @click="closeDeleteDialog">Cancel</Button>
                                </DialogClose>

                                <Button :disabled="deleteForm.processing" type="submit" variant="destructive">
                                    {{ deleteForm.processing ? 'Deleting...' : 'Delete Promotion' }}
                                </Button>
                            </DialogFooter>
                        </form>
                    </DialogContent>
                </Dialog>
            </div>
        </div>

        <!-- Summary Card -->
        <div>
            <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between pb-4">
                <h1 class="text-xl font-semibold">Promotion Summary</h1>

                <div class="flex flex-wrap gap-2">
                    <Badge :variant="isActiveVariant">{{ isActiveLabel }}</Badge>
                    <Badge v-if="isPublished" variant="secondary">Published</Badge>
                </div>
            </div>

            <div class="space-y-6">
                <!-- Description -->
                <section class="space-y-2">
                    <h3 class="text-sm font-medium text-muted-foreground">Description</h3>
                    <p class="text-base leading-relaxed text-foreground">
                        {{ promotion.description }}
                    </p>
                </section>

                <!-- Dates -->
                <dl class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-1">
                        <dt class="text-sm font-medium text-muted-foreground">Publishes At</dt>
                        <dd class="text-base">{{ formatDateTime(promotion.publish_at, 'Not scheduled') }}</dd>
                    </div>

                    <div v-if="isPublished" class="space-y-1">
                        <dt class="text-sm font-medium text-muted-foreground">Published At</dt>
                        <dd class="text-base">{{ formatDateTime(promotion.published_at) }}</dd>
                    </div>

                    <div class="space-y-1">
                        <dt class="text-sm font-medium text-muted-foreground">Starts At</dt>
                        <dd class="text-base">{{ formatDateTime(promotion.start_at) }}</dd>
                    </div>

                    <div class="space-y-1">
                        <dt class="text-sm font-medium text-muted-foreground">Ends At</dt>
                        <dd class="text-base">{{ formatDateTime(promotion.end_at, 'No end date') }}</dd>
                    </div>
                </dl>

                <!-- Link -->
                <div v-if="promotion.link" class="space-y-2">
                    <h3 class="text-sm font-medium text-muted-foreground">Link</h3>
                    <Link
                        :href="promotion.link"
                        class="text-primary hover:underline"
                        rel="noopener noreferrer"
                        target="_blank"
                    >
                        {{ promotion.link }}
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
