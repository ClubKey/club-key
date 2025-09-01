<script lang="ts" setup>
import { useForm } from '@inertiajs/vue3';
import { Trash2 } from 'lucide-vue-next';

// Components
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { type RewardLevel } from '@/types';

const form = useForm({});

const props = defineProps<{
    rewardLevel: RewardLevel;
}>();

const deleteRewardLevel = (e: Event) => {
    e.preventDefault();

    form.delete(route('reward-levels.destroy', props.rewardLevel));
};

const closeModal = () => {
    form.clearErrors();
    form.reset();
};
</script>

<template>
    <Dialog>
        <DialogTrigger as-child>
            <Button size="sm" variant="destructive">
                <Trash2 />
            </Button>
        </DialogTrigger>
        <DialogContent>
            <form class="space-y-6" @submit="deleteRewardLevel">
                <DialogHeader class="space-y-3">
                    <DialogTitle>Are you sure you want to delete this reward level?</DialogTitle>
                    <DialogDescription>
                        Once this reward level is deleted, it will be permanently removed from your club along with all its associated rewards. This
                        action cannot be undone.
                    </DialogDescription>
                </DialogHeader>

                <div class="rounded-lg border border-red-100 bg-red-50 p-4 dark:border-red-200/10 dark:bg-red-700/10">
                    <div class="relative space-y-0.5 text-red-600 dark:text-red-100">
                        <p class="font-medium">Warning</p>
                        <p class="text-sm">This action cannot be undone and will also delete all associated rewards.</p>
                    </div>
                </div>

                <DialogFooter class="gap-2">
                    <DialogClose as-child>
                        <Button variant="secondary" @click="closeModal">Cancel</Button>
                    </DialogClose>

                    <Button :disabled="form.processing" type="submit" variant="destructive">Delete reward level</Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
