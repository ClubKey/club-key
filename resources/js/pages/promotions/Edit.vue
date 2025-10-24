<script lang="ts" setup>
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { CardDescription, CardFooter, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Badge } from '@/components/ui/badge';
import AppLayout from '@/layouts/AppLayout.vue';
import { Promotion } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, watch } from 'vue';

const props = defineProps<{ promotion: Promotion }>();

const formatDateTimeLocal = (value: string | null) => {
    if (!value) return '';
    const date = new Date(value);
    const pad = (n: number) => `${n}`.padStart(2, '0');
    return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(date.getMinutes())}`;
};

const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;

const form = useForm({
    _method: 'put',
    name: props.promotion.name,
    description: props.promotion.description,
    link: props.promotion.link ?? '',
    publish_now: props.promotion.publish_at === null,
    publish_at: formatDateTimeLocal(props.promotion.publish_at),
    start_at: formatDateTimeLocal(props.promotion.start_at),
    end_at: formatDateTimeLocal(props.promotion.end_at),
    is_active: props.promotion.is_active,
    timezone
});

const headingTitle = computed(() => `Edit ${form.name}`);
const isPublished = computed(() => !!props.promotion.published_at);

watch(() => form.publish_now, value => {
    if (value) form.publish_at = '';
});

const submit = () => {
    form.transform(data => ({
        ...data,
        publish_at: data.publish_now ? null : data.publish_at || null,
        start_at: data.start_at || null,
        end_at: data.end_at || null,
        link: data.link || null
    }));
    form.post(route('promotions.update', props.promotion.id), {
        preserveScroll: true,
        onFinish: () => form.transform(data => data)
    });
};
</script>

<template>
    <AppLayout>
        <Head :title="headingTitle" />

        <Heading :title="headingTitle" description="Update your promotion’s details and scheduling." />

        <form class="grid lg:grid-cols-12 gap-6 max-w-screen-xl py-8" @submit.prevent="submit">
            <div class="space-y-6 lg:col-span-7">
                <div class="space-y-6">
                    <div class="space-y-2">
                        <Label for="name">Promotion Name</Label>
                        <Input id="name" v-model="form.name" autocomplete="off" required />
                        <p v-if="form.errors.name" class="text-sm text-destructive">{{ form.errors.name }}</p>
                    </div>

                    <div class="space-y-2">
                        <Label for="link">Link (Optional)</Label>
                        <p class="text-sm text-muted-foreground">
                            Provide a link with more details about this promotion.
                        </p>
                        <Input id="link" v-model="form.link" placeholder="https://example.com" />
                        <p v-if="form.errors.link" class="text-sm text-destructive">{{ form.errors.link }}</p>
                    </div>

                    <div class="space-y-2">
                        <Label for="description">Description</Label>
                        <Textarea
                            id="description"
                            v-model="form.description"
                            placeholder="Describe what this promotion offers..."
                            required
                        />
                        <p v-if="form.errors.description" class="text-sm text-destructive">
                            {{ form.errors.description }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: Publishing & Activation -->
            <div class="space-y-6 lg:col-span-5">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <CardTitle>Publish & Activation</CardTitle>
                        <CardDescription>
                            Control when this promotion goes live and its active state.
                        </CardDescription>
                    </div>
                    <Badge v-if="isPublished" variant="default">Published</Badge>
                </div>

                <div class="space-y-6">
                    <div class="space-y-2">
                        <Label for="start_at">Start Date (Optional)</Label>
                        <p class="text-sm text-muted-foreground">This is when the banner will become visible in the
                            app
                            on your club's page.</p>
                        <Input id="start_at" v-model="form.start_at" class="sm:w-auto" type="datetime-local" />
                        <p v-if="form.errors.start_at" class="text-sm text-destructive">{{ form.errors.start_at
                            }}</p>
                    </div>

                    <div class="space-y-2">
                        <Label for="end_at">End Date (Optional)</Label>
                        <p class="text-sm text-muted-foreground">
                            If you want the promotion banner to end after a certain date, you can specify it here.
                            If not provided, the banner will stay visible until you publish another promotion.
                        </p>
                        <Input id="end_at" v-model="form.end_at" class="sm:w-auto" type="datetime-local" />
                        <p v-if="form.errors.end_at" class="text-sm text-destructive">{{ form.errors.end_at }}</p>
                    </div>

                    <!-- Hide publish controls if already published -->
                    <div v-if="!isPublished" class="space-y-2">
                        <Label class="flex items-center gap-2">
                            <Checkbox id="publish_now" v-model:checked="form.publish_now" />
                            Publish Now
                        </Label>
                        <p class="text-sm text-muted-foreground">
                            Choose when you want to publish this promotion. If you choose to publish it now, it will
                            be
                            visible immediately.
                        </p>
                        <Input
                            v-if="!form.publish_now"
                            id="publish_at"
                            v-model="form.publish_at"
                            class="sm:w-auto"
                            type="datetime-local"
                        />
                        <p v-if="form.errors.publish_at" class="text-sm text-destructive">
                            {{ form.errors.publish_at }}
                        </p>
                    </div>

                    <div class="space-y-2">
                        <Label class="flex items-center gap-2">
                            <Checkbox id="is_active" v-model="form.is_active" />
                            <span>{{ form.is_active ? 'Active' : 'Activate?' }}</span>
                        </Label>
                    </div>
                </div>
                <CardFooter class="flex justify-end gap-4 mt-auto">
                    <Button :disabled="form.processing || !form.isDirty" type="submit">
                        {{ form.processing ? 'Updating...' : 'Update Promotion' }}
                    </Button>
                    <Button as-child type="button" variant="outline">
                        <Link :href="route('promotions.show', promotion.id)">Cancel</Link>
                    </Button>
                </CardFooter>
            </div>
        </form>
    </AppLayout>
</template>
