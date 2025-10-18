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
import { Form, Head, Link } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

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

const name = ref(props.promotion.name);
const description = ref(props.promotion.description);
const link = ref(props.promotion.link ?? '');
const publishNow = ref(props.promotion.publish_at === null);
const publishAt = ref(formatDateTimeLocal(props.promotion.publish_at));
const startAt = ref(formatDateTimeLocal(props.promotion.start_at));
const endAt = ref(formatDateTimeLocal(props.promotion.end_at));
const isActive = ref(props.promotion.is_active);

const headingTitle = computed(() => `Edit ${props.promotion.name}`);

watch(publishNow, (value) => {
    if (value) {
        publishAt.value = '';
    }
});

if (publishNow.value) {
    publishAt.value = '';
}
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
                <Form
                    #default="{ errors, processing }"
                    :action="route('promotions.update', promotion.id)"
                    class="space-y-6"
                    method="post"
                >
                    <input type="hidden" name="_method" value="put" />
                    <div class="space-y-2">
                        <Label for="name">Promotion Name</Label>
                        <Input id="name" v-model="name" :error="errors.name" name="name" required type="text" />
                        <p v-if="errors.name" class="text-sm text-destructive">
                            {{ errors.name }}
                        </p>
                    </div>

                    <div class="space-y-2">
                        <Label for="link">Link (Optional)</Label>
                        <p class="text-sm text-muted-foreground">
                            Add an external link to more details about this promotion if you want.
                        </p>
                        <Input
                            id="link"
                            v-model="link"
                            :error="errors.link"
                            name="link"
                            placeholder="https://example.com"
                            type="text"
                        />
                        <p v-if="errors.link" class="text-sm text-destructive">
                            {{ errors.link }}
                        </p>
                    </div>

                    <div class="space-y-2">
                        <Label for="description">Description</Label>
                        <Textarea
                            id="description"
                            v-model="description"
                            :error="errors.description"
                            name="description"
                            placeholder="Describe what this promotion offers..."
                            required
                        />
                        <p v-if="errors.description" class="text-sm text-destructive">
                            {{ errors.description }}
                        </p>
                    </div>

                    <div class="space-y-2">
                        <Label for="start_at">Start At</Label>
                        <p class="text-sm text-muted-foreground">
                            This is when the banner will become visible in the app on your club's page.
                        </p>
                        <Input
                            id="start_at"
                            v-model="startAt"
                            :error="errors.start_at"
                            class="w-auto"
                            name="start_at"
                            type="datetime-local"
                        />
                        <p v-if="errors.start_at" class="text-sm text-destructive">
                            {{ errors.start_at }}
                        </p>
                    </div>

                    <div class="space-y-2">
                        <Label for="end_at">End At (Optional)</Label>
                        <p class="text-sm text-muted-foreground">
                            If you want the promotion banner to end after a certain date, you can specify it here.
                        </p>
                        <Input
                            id="end_at"
                            v-model="endAt"
                            :error="errors.end_at"
                            class="w-auto"
                            name="end_at"
                            type="datetime-local"
                        />
                        <p v-if="errors.end_at" class="text-sm text-destructive">
                            {{ errors.end_at }}
                        </p>
                    </div>

                    <div class="space-y-2">
                        <h3 class="text-lg font-semibold">Publish</h3>
                        <p class="text-sm text-muted-foreground">
                            Choose when you want to publish this promotion.
                        </p>
                        <Label class="flex items-center gap-2">
                            <input type="hidden" name="publish_now" :value="publishNow ? 1 : 0" />
                            <Checkbox id="publish_now" v-model:checked="publishNow" />
                            Publish Now?
                        </Label>
                        <Input
                            v-if="!publishNow"
                            id="publish_at"
                            v-model="publishAt"
                            :error="errors.publish_at"
                            class="w-auto"
                            name="publish_at"
                            type="datetime-local"
                        />
                        <p v-if="errors.publish_at" class="text-sm text-destructive">
                            {{ errors.publish_at }}
                        </p>
                    </div>

                    <div class="space-y-2">
                        <Label class="flex items-center gap-2">
                            <input type="hidden" name="is_active" :value="isActive ? 1 : 0" />
                            <Checkbox id="is_active" v-model:checked="isActive" />
                            Active promotion?
                        </Label>
                    </div>

                    <div class="flex gap-4">
                        <Button :disabled="processing" type="submit">
                            {{ processing ? 'Updating...' : 'Update Promotion' }}
                        </Button>
                        <Button as-child type="button" variant="outline">
                            <Link :href="route('promotions.show', promotion.id)">Cancel</Link>
                        </Button>
                    </div>
                </Form>
            </CardContent>
        </Card>
    </AppLayout>
</template>
