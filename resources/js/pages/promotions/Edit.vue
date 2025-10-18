<script lang="ts" setup>
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { Promotion } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, watch } from 'vue';

const props = defineProps<{
    promotion: Promotion;
}>();

const formatDateTimeLocal = (value: string | null) => {
    if (!value) {
        return '';
    }

    const date = new Date(value);

    const pad = (num: number) => `${num}`.padStart(2, '0');

    return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(
        date.getMinutes()
    )}`;
};

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
});

const headingTitle = computed(() => `Edit ${form.name}`);

watch(
    () => form.publish_now,
    (value) => {
        if (value) {
            form.publish_at = '';
        }
    }
);

if (form.publish_now) {
    form.publish_at = '';
}

const submit = () => {
    form.transform((data) => ({
        ...data,
        publish_at: data.publish_now ? null : data.publish_at || null,
        start_at: data.start_at || null,
        end_at: data.end_at || null,
        link: data.link || null,
    }));

    form.post(route('promotions.update', props.promotion.id), {
        preserveScroll: true,
        onFinish: () => {
            form.transform((data) => data);
        },
    });
};
</script>

<template>
    <AppLayout>
        <Head :title="headingTitle" />
        <Heading
            class="max-w-2xl"
            :title="headingTitle"
            description="Update the details for your promotion."
        />
        <Card class="max-w-2xl">
            <CardHeader>
                <CardTitle>Promotion Details</CardTitle>
                <CardDescription>Update the details for this promotion.</CardDescription>
            </CardHeader>
            <CardContent>
                <form class="space-y-6" @submit.prevent="submit">
                    <div class="space-y-2">
                        <Label for="name">Promotion Name</Label>
                        <Input
                            id="name"
                            v-model="form.name"
                            :aria-invalid="Boolean(form.errors.name)"
                            autocomplete="off"
                            name="name"
                            required
                            type="text"
                        />
                        <p v-if="form.errors.name" class="text-sm text-destructive">
                            {{ form.errors.name }}
                        </p>
                    </div>

                    <div class="space-y-2">
                        <Label for="link">Link (Optional)</Label>
                        <p class="text-sm text-muted-foreground">
                            Add an external link to more details about this promotion if you want.
                        </p>
                        <Input
                            id="link"
                            v-model="form.link"
                            :aria-invalid="Boolean(form.errors.link)"
                            name="link"
                            placeholder="https://example.com"
                            type="text"
                        />
                        <p v-if="form.errors.link" class="text-sm text-destructive">
                            {{ form.errors.link }}
                        </p>
                    </div>

                    <div class="space-y-2">
                        <Label for="description">Description</Label>
                        <Textarea
                            id="description"
                            v-model="form.description"
                            :aria-invalid="Boolean(form.errors.description)"
                            name="description"
                            placeholder="Describe what this promotion offers..."
                            required
                        />
                        <p v-if="form.errors.description" class="text-sm text-destructive">
                            {{ form.errors.description }}
                        </p>
                    </div>

                    <div class="space-y-2">
                        <Label for="start_at">Start At</Label>
                        <p class="text-sm text-muted-foreground">
                            This is when the banner will become visible in the app on your club's page.
                        </p>
                        <Input
                            id="start_at"
                            v-model="form.start_at"
                            :aria-invalid="Boolean(form.errors.start_at)"
                            class="w-auto"
                            name="start_at"
                            type="datetime-local"
                        />
                        <p v-if="form.errors.start_at" class="text-sm text-destructive">
                            {{ form.errors.start_at }}
                        </p>
                    </div>

                    <div class="space-y-2">
                        <Label for="end_at">End At (Optional)</Label>
                        <p class="text-sm text-muted-foreground">
                            If you want the promotion banner to end after a certain date, you can specify it here.
                        </p>
                        <Input
                            id="end_at"
                            v-model="form.end_at"
                            :aria-invalid="Boolean(form.errors.end_at)"
                            class="w-auto"
                            name="end_at"
                            type="datetime-local"
                        />
                        <p v-if="form.errors.end_at" class="text-sm text-destructive">
                            {{ form.errors.end_at }}
                        </p>
                    </div>

                    <div class="space-y-2">
                        <h3 class="text-lg font-semibold">Publish</h3>
                        <p class="text-sm text-muted-foreground">
                            Choose when you want to publish this promotion.
                        </p>
                        <Label class="flex items-center gap-2">
                            <Checkbox id="publish_now" v-model:checked="form.publish_now" />
                            Publish Now?
                        </Label>
                        <Input
                            v-if="!form.publish_now"
                            id="publish_at"
                            v-model="form.publish_at"
                            :aria-invalid="Boolean(form.errors.publish_at)"
                            class="w-auto"
                            name="publish_at"
                            type="datetime-local"
                        />
                        <p v-if="form.errors.publish_at" class="text-sm text-destructive">
                            {{ form.errors.publish_at }}
                        </p>
                    </div>

                    <div class="space-y-2">
                        <Label class="flex items-center gap-2">
                            <Checkbox id="is_active" v-model:checked="form.is_active" />
                            Active promotion?
                        </Label>
                    </div>

                    <div class="flex gap-4">
                        <Button :disabled="form.processing" type="submit">
                            {{ form.processing ? 'Updating...' : 'Update Promotion' }}
                        </Button>
                        <Button as-child type="button" variant="outline">
                            <Link :href="route('promotions.show', promotion.id)">Cancel</Link>
                        </Button>
                    </div>
                </form>
            </CardContent>
        </Card>
    </AppLayout>
</template>
