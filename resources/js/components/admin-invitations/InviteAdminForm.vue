<script lang="ts" setup>
import FormInputDescription from '@/components/FormInputDescription.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { type Club } from '@/types';
import { useForm } from '@inertiajs/vue3';

const props = defineProps<{
    club: Club;
}>();

const form = useForm({
    email: '',
    role: 'admin',
});

const submit = () => {
    form.post(route('clubs.admin-invitations.store', props.club), {
        preserveScroll: true,
    });
};
</script>

<template>
    <form class="space-y-6" @submit.prevent="submit">
        <InputError v-if="form.hasErrors" class="mb-4" message="There are errors in your form. Please fix them before submitting." />

        <div class="grid gap-2">
            <Label for="email">User Email</Label>
            <FormInputDescription
                >Enter the email of the person you would like to invite to manage your club. This will be where the invitation is sent.
            </FormInputDescription>
            <Input id="email" v-model="form.email" autocomplete="email" class="mt-1 block w-full" placeholder="User email" required />
            <InputError :message="form.errors.email" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <Button :disabled="form.processing">Invite</Button>
            <Transition
                enter-active-class="transition ease-in-out"
                enter-from-class="opacity-0"
                leave-active-class="transition ease-in-out"
                leave-to-class="opacity-0"
            >
                <p v-show="form.recentlySuccessful" class="text-sm text-neutral-600">Invitation sent!</p>
            </Transition>
        </div>
    </form>
</template>
