<script lang="ts" setup>
import { useForm } from '@inertiajs/vue3';

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
import { type ClubLocation } from '@/types';
import { Trash2 } from 'lucide-vue-next';

const form = useForm({});

const props = defineProps<{
    location: ClubLocation;
}>();

const deleteLocation = (e: Event) => {
    e.preventDefault();

    form.delete(route('locations.destroy', props.location));
};

const closeModal = () => {
    form.clearErrors();
    form.reset();
};
</script>

<template>
    <Dialog>
        <DialogTrigger as-child>
            <Button size="icon" variant="destructive"><Trash2 /></Button>
        </DialogTrigger>
        <DialogContent>
            <form class="space-y-6" @submit="deleteLocation">
                <DialogHeader class="space-y-3">
                    <DialogTitle>Are you sure you want to delete this location?</DialogTitle>
                    <DialogDescription>
                        Once this location is deleted, it will be permanently removed from your club. This action cannot be undone.
                    </DialogDescription>
                </DialogHeader>

                <div class="rounded-lg border border-red-100 bg-red-50 p-4 dark:border-red-200/10 dark:bg-red-700/10">
                    <div class="relative space-y-0.5 text-red-600 dark:text-red-100">
                        <p class="font-medium">Warning</p>
                        <p class="text-sm">This action cannot be undone.</p>
                    </div>
                </div>

                <DialogFooter class="gap-2">
                    <DialogClose as-child>
                        <Button variant="secondary" @click="closeModal"> Cancel </Button>
                    </DialogClose>

                    <Button :disabled="form.processing" type="submit" variant="destructive"> Delete location </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
