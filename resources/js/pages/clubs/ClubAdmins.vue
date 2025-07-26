<script lang="ts" setup>
import FormInputDescription from '@/components/FormInputDescription.vue';
import Heading from '@/components/Heading.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import ClubLayout from '@/layouts/club/ClubLayout.vue';
import { AdminInvitation, type BreadcrumbItem, Club, User } from '@/types';
import { useForm } from '@inertiajs/vue3';

const props = defineProps<{
    club: Club;
    admins: User[];
    adminInvitations: AdminInvitation[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: props.club.name,
        href: `/clubs/${props.club.slug}/edit`,
    },
    {
        title: 'Admins',
        href: `/clubs/${props.club.slug}/admins`,
    },
];

const form = useForm({
    email: '',
    role: 'admin',
});

const submit = () => {
    form.post(route('clubs.admin-invitations.store', props.club), {
        preserveScroll: true,
        onSuccess: () => {},
        onError: () => {
            //scroll to the top of the page on error
            window.scrollTo({
                top: 0,
                behavior: 'smooth',
            });
        },
    });
};
</script>

<template>
    <AppLayout :breadcrumbs>
        <ClubLayout :club="club">
            <Heading :title="`${club.name} Admins`" />
            <Card class="w-full max-w-2xl p-6">
                <HeadingSmall title="Invite an admin" />

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
                            <p v-show="form.recentlySuccessful" class="text-sm text-neutral-600">Saved.</p>
                        </Transition>
                    </div>
                </form>
            </Card>
        </ClubLayout>
    </AppLayout>
</template>
