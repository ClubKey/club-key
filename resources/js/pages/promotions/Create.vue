<script lang="ts" setup>
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { Form, Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <AppLayout>
        <Head title="Create" />
        <Heading
            class="max-w-2xl"
            description="Promotions are short updates you can send to your club members. You can choose to send them out via email, or just as an in-app banner they'll see when the check in."
            title="Create a promotion"
        />
        <Card class="max-w-2xl">
            <CardHeader>
                <CardTitle>Promotion Details</CardTitle>
                <CardDescription>Enter the details for your promotion.</CardDescription>
            </CardHeader>
            <CardContent>
                <Form #default="{ errors, processing }" :action="route('promotions.store')" class="space-y-6" method="post">
                    <div class="space-y-2">
                        <Label for="name">Promotion Name</Label>
                        <Input id="name" :error="errors.name" name="name" placeholder="e.g., Fall Seasonal Promo" required type="text" />
                        <p v-if="errors.name" class="text-sm text-destructive">
                            {{ errors.name }}
                        </p>
                    </div>
                    <div class="space-y-2">
                        <Label for="link">Link (Optional)</Label>
                        <p class="text-sm text-muted-foreground">
                            Add an external link to more details about this promotion if you want. If this is provided, it will be added to the promo
                            banner on your page.
                        </p>
                        <Input id="link" :error="errors.link" name="link" placeholder="https://myawesomepromo.example" type="text" />
                        <p v-if="errors.link" class="text-sm text-destructive">
                            {{ errors.link }}
                        </p>
                    </div>
                    <div class="space-y-2">
                        <Label for="description">Description</Label>
                        <p class="text-sm text-muted-foreground">
                            Here you can add all the details of your promotion. We recommend making it short, as it appears in a banner on your page,
                            and will be easier to read that way.
                        </p>
                        <Textarea id="description" name="description" placeholder="Describe what this promotion offers..." required />
                        <p v-if="errors.description" class="text-sm text-destructive">
                            {{ errors.description }}
                        </p>
                    </div>
                    <div class="flex gap-4">
                        <div class="space-y-2">
                            <Label for="start_date">Start Date</Label>
                            <Input name="start_date" required type="date" />
                            <p v-if="errors.start_date" class="text-sm text-destructive">{{ errors.description }}</p>
                        </div>
                        <div class="space-y-2">
                            <Label for="end_date">End Date (Optional)</Label>
                            <Input name="end_date" type="date" />
                            <p v-if="errors.end_date" class="text-sm text-destructive">
                                {{ errors.end_date }}
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <Button :disabled="processing" type="submit">
                            {{ processing ? 'Creating...' : 'Create Promotion' }}
                        </Button>
                        <Button as-child type="button" variant="outline">
                            <Link :href="route('promotions.index')">Cancel</Link>
                        </Button>
                    </div>
                </Form>
            </CardContent>
        </Card>
    </AppLayout>
</template>
