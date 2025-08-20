<script lang="ts" setup>
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Form, Head } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
</script>

<template>
    <AuthBase description="Enter your details below to start getting club your rewards!" title="Create an account">
        <Head title="Register" />

        <Form #default="{ errors, processing }" :action="route('register')" class="flex flex-col gap-6" method="post">
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="name">Name</Label>
                    <Input id="name" :tabindex="1" autocomplete="name" autofocus name="name" placeholder="Full name" required type="text" />
                    <InputError :message="errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="email">Email address</Label>
                    <Input id="email" :tabindex="2" autocomplete="email" name="email" placeholder="email@example.com" required type="email" />
                    <InputError :message="errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label for="phone">Phone</Label>
                    <Input id="phone" :tabindex="2" autocomplete="phone" name="phone" placeholder="509 123 4567" required type="text" />
                    <InputError :message="errors.phone" />
                </div>

                <div class="grid gap-2">
                    <Label for="password">Password</Label>
                    <Input id="password" :tabindex="3" autocomplete="new-password" name="password" placeholder="Password" required type="password" />
                    <InputError :message="errors.password" />
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation">Confirm password</Label>
                    <Input
                        id="password_confirmation"
                        :tabindex="4"
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
                        <Label for="accept_terms">Accept our applications terms of service?*</Label>
                        <Checkbox id="accept_terms" name="accept_terms"></Checkbox>
                    </div>
                    <InputError :message="errors.accept_terms" />
                </div>
                <div class="grid gap-2">
                    <div class="flex items-center gap-4">
                        <Label for="accept_marketing">Okay with getting emails?*</Label>
                        <Checkbox id="accept_marketing" name="accept_marketing"></Checkbox>
                    </div>
                    <InputError :message="errors.accept_marketing" />
                </div>

                <Button :disabled="processing" class="mt-2 w-full" tabindex="5" type="submit">
                    <LoaderCircle v-if="processing" class="h-4 w-4 animate-spin" />
                    Create account
                </Button>
            </div>

            <div class="text-center text-sm text-muted-foreground">
                Already have an account?
                <TextLink :href="route('login')" :tabindex="6" class="underline underline-offset-4">Log in</TextLink>
            </div>
        </Form>
    </AuthBase>
</template>
