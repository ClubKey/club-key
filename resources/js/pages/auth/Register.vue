<script lang="ts" setup>
import InputError from '@/components/InputError.vue';
import Terms from '@/components/legal/Terms.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Form, Head } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { ref } from 'vue';

const step = ref(1);

function next() {
    step.value = 2;
}

function back() {
    step.value = 1;
}
</script>

<template>
    <AuthBase description="Enter your details below to start getting club your rewards!" title="Create an account">
        <Head title="Register" />

        <Form #default="{ errors, hasErrors, processing }" :action="route('register')" class="flex flex-col gap-6" method="post">
            <!-- Section 1: Basic info -->
            <div v-show="step === 1 || hasErrors" class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="name">Name*</Label>
                    <Input id="name" :tabindex="1" autocomplete="name" name="name" placeholder="Full name" required type="text" />
                    <InputError :message="errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="email">Email address*</Label>
                    <Input id="email" :tabindex="2" autocomplete="email" name="email" placeholder="email@example.com" required type="email" />
                    <InputError :message="errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label for="phone">Phone*</Label>
                    <Input id="phone" :tabindex="3" autocomplete="tel" name="phone" placeholder="509 123 4567" required type="tel" />
                    <InputError :message="errors.phone" />
                </div>

                <Button v-if="!hasErrors" class="mt-2 w-full" type="button" @click="next">Next</Button>
            </div>

            <!-- Section 2: Security & consent -->
            <div v-show="step === 2 || hasErrors" class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="password">Password*</Label>
                    <Input id="password" :tabindex="4" autocomplete="new-password" name="password" placeholder="Password" required type="password" />
                    <InputError :message="errors.password" />
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation">Confirm password*</Label>
                    <Input
                        id="password_confirmation"
                        :tabindex="5"
                        autocomplete="new-password"
                        name="password_confirmation"
                        placeholder="Confirm password"
                        required
                        type="password"
                    />
                    <InputError :message="errors.password_confirmation" />
                </div>

                <div class="grid gap-2">
                    <div class="flex items-center gap-4">
                        <Label class="gap-1" for="accept_terms"
                            >Accept our applications<Terms><Button class="px-0 underline" variant="link">terms?*</Button></Terms></Label
                        >
                        <Checkbox id="accept_terms" name="accept_terms"></Checkbox>
                    </div>
                    <InputError :message="errors.accept_terms" />
                </div>
                <div class="grid gap-2">
                    <div class="flex items-start gap-4">
                        <Label for="accept_marketing">Okay with getting emails or texts with offers and reminders?*</Label>
                        <Checkbox id="accept_marketing" name="accept_marketing"></Checkbox>
                    </div>

                    <p class="text-sm text-muted-foreground">This is required to join a club and receive rewards.</p>
                    <InputError :message="errors.accept_marketing" />
                </div>
                <div class="grid gap-2">
                    <Button v-if="!hasErrors" type="button" variant="outline" @click="back">Back</Button>
                    <Button :disabled="processing" class="mt-2 w-full" tabindex="6" type="submit">
                        <LoaderCircle v-if="processing" class="h-4 w-4 animate-spin" />
                        Create account
                    </Button>
                </div>
            </div>

            <p class="text-center text-sm text-muted-foreground">*All fields are required.</p>

            <div class="text-center text-sm text-muted-foreground">
                Already have an account?
                <TextLink :href="route('login')" :tabindex="7" class="underline underline-offset-4">Log in</TextLink>
            </div>
        </Form>
    </AuthBase>
</template>
