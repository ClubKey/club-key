<script lang="ts" setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

// Components
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
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
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Club } from '@/types';

const passwordInput = ref<HTMLInputElement | null>(null);

const form = useForm({
    password: '',
});

const props = defineProps<{
    club: Club;
}>();

const deleteClub = (e: Event) => {
    e.preventDefault();

    form.delete(route('clubs.destroy', props.club));
};

const closeModal = () => {
    form.clearErrors();
    form.reset();
};
</script>

<template>
    <div class="space-y-6">
        <HeadingSmall description="Delete your club and all of its resources" title="Delete club" />
        <div class="space-y-4 rounded-lg border border-red-100 bg-red-50 p-4 dark:border-red-200/10 dark:bg-red-700/10">
            <div class="relative space-y-0.5 text-red-600 dark:text-red-100">
                <p class="font-medium">Warning</p>
                <p class="text-sm">Please proceed with caution, this cannot be undone.</p>
            </div>
            <Dialog>
                <DialogTrigger as-child>
                    <Button variant="destructive">Delete club</Button>
                </DialogTrigger>
                <DialogContent>
                    <form class="space-y-6" @submit="deleteClub">
                        <DialogHeader class="space-y-3">
                            <DialogTitle>Are you sure you want to delete your club?</DialogTitle>
                            <DialogDescription>
                                Once your club is deleted, all of its resources and data will also be permanently deleted. Please enter your password
                                to confirm you would like to permanently delete your club.
                            </DialogDescription>
                        </DialogHeader>

                        <div class="grid gap-2">
                            <Label class="sr-only" for="password">Password</Label>
                            <Input id="password" ref="passwordInput" v-model="form.password" name="password" placeholder="Password" type="password" />
                            <InputError :message="form.errors.password" />
                        </div>

                        <DialogFooter class="gap-2">
                            <DialogClose as-child>
                                <Button variant="secondary" @click="closeModal"> Cancel</Button>
                            </DialogClose>

                            <Button :disabled="form.processing" type="submit" variant="destructive"> Delete club </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>
        </div>
    </div>
</template>
