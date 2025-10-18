<script lang="ts" setup>
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { Form, Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const publishNow = ref(false);
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
                    <div class="space-y-2">
                        <Label for="start_at">Start At</Label>
                        <p class="text-sm text-muted-foreground">This is when the banner will become visible in the app on your club's page.</p>
                        <Input class="w-auto" name="start_at" type="datetime-local" />
                        <p v-if="errors.start_at" class="text-sm text-destructive">{{ errors.description }}</p>
                    </div>
                    <div class="space-y-2">
                        <Label for="end_at">End At (Optional)</Label>
                        <p class="text-sm text-muted-foreground">
                            If you want the promotion banner to end after a certain date, you can specify it here. If not provided, the banner will
                            stay visible until you publish another promotion.
                        </p>
                        <Input class="w-auto" name="end_at" type="datetime-local" />
                        <p v-if="errors.end_at" class="text-sm text-destructive">
                            {{ errors.end_at }}
                        </p>
                    </div>
                    <div class="space-y-2">
                        <h3 class="text-lg font-semibold">Publish</h3>
                        <p class="text-sm text-muted-foreground">
                            Choose when you want to publish this promotion. If you choose to publish it now, it will be visible immediately.
                        </p>
                        <Label><Checkbox id="publish" v-model="publishNow" name="publish_now" value="true" />Publish Now?</Label>
                        <Input v-if="!publishNow" class="w-auto" name="publish_at" type="datetime-local" />
                        <p v-if="errors.publish_at" class="text-sm text-destructive">
                            {{ errors.publish_at }}
                        </p>
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
