<script lang="ts" setup>
import Heading from '@/components/Heading.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Club } from '@/types';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import { ArrowLeft } from 'lucide-vue-next';

const props = defineProps<{
    club: Club;
}>();

const page = usePage();
const showManualEntry = ref(false);

const fileForm = useForm({
    file: null as File | null,
    source: 'import'
});

const manualForm = useForm({
    subscriptions: [
        {
            contact_type: 'email',
            contact_value: '',
            topic: 'promotions',
            user_id: null as number | null
        }
    ] as Array<{
        contact_type: string;
        contact_value: string;
        topic: string;
        user_id: number | null;
    }>,
    source: 'import'
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: props.club.name,
        href: `/clubs/${props.club.slug}/edit`
    },
    {
        title: 'Import Subscriptions',
        href: route('subscribers.import')
    }
];

const submitFile = () => {
    fileForm.post(route('subscribers.import.store'), {
        preserveScroll: true,
        onSuccess: () => {
            fileForm.reset();
        }
    });
};

const submitManual = () => {
    manualForm.post(route('subscribers.import.store'), {
        preserveScroll: true,
        onSuccess: () => {
            manualForm.reset();
            manualForm.subscriptions = [
                {
                    contact_type: 'email',
                    contact_value: '',
                    topic: 'promotions',
                    user_id: null
                }
            ];
        }
    });
};

const addManualEntry = () => {
    manualForm.subscriptions.push({
        contact_type: 'email',
        contact_value: '',
        topic: 'promotions',
        user_id: null
    });
};

const removeManualEntry = (index: number) => {
    manualForm.subscriptions.splice(index, 1);
};
</script>

<template>
    <AppLayout :breadcrumbs>
        <Head :title="`Import Subscriptions - ${club.name}`" />
        <div class="flex flex-col items-start gap-4">
            <Button as-child>
                <Link :href="route('subscribers.index')">
                    <ArrowLeft />
                    Subscribers
                </Link>
            </Button>
            <Heading :title="`Import Subscriptions - ${club.name}`" />

        </div>

        <Card class="p-6">
            <HeadingSmall title="CSV File Upload" />
            <p class="mb-4 text-sm text-neutral-600">
                Upload a CSV file with columns: contact_type, contact_value, topic, user_id (optional). Supported
                contact types: email, sms.
                Topics: promotions, notifications.
            </p>

            <form class="space-y-4" @submit.prevent="submitFile">
                <div class="grid gap-2">
                    <Label for="file">CSV File</Label>
                    <Input
                        id="file"
                        accept=".csv,.txt"
                        type="file"
                        @change="(e: Event) => (fileForm.file = (e.target as HTMLInputElement).files?.[0] || null)"
                    />
                    <InputError :message="fileForm.errors.file" class="mt-2" />
                </div>

                <Button :disabled="fileForm.processing" type="submit">Upload CSV</Button>
            </form>
        </Card>

        <Card class="p-6">
            <div class="mb-4 flex items-center justify-between">
                <HeadingSmall title="Manual Entry" />
                <Button variant="outline" @click="showManualEntry = !showManualEntry">
                    {{ showManualEntry ? 'Hide' : 'Show' }} Manual Entry
                </Button>
            </div>

            <form v-if="showManualEntry" class="space-y-6" @submit.prevent="submitManual">
                <div v-for="(subscription, index) in manualForm.subscriptions" :key="index"
                     class="space-y-4 rounded-lg border p-4">
                    <div class="flex items-center justify-between">
                        <h4 class="font-medium">Entry {{ index + 1 }}</h4>
                        <Button
                            v-if="manualForm.subscriptions.length > 1"
                            size="sm"
                            type="button"
                            variant="destructive"
                            @click="removeManualEntry(index)"
                        >
                            Remove
                        </Button>
                    </div>

                    <div class="grid gap-2">
                        <Label :for="`contact_type_${index}`">Contact Type</Label>
                        <select
                            :id="`contact_type_${index}`"
                            v-model="subscription.contact_type"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background"
                        >
                            <option value="email">Email</option>
                            <option value="sms">SMS</option>
                        </select>
                        <InputError :message="manualForm.errors[`subscriptions.${index}.contact_type`]"
                                    class="mt-2" />
                    </div>

                    <div class="grid gap-2">
                        <Label :for="`contact_value_${index}`">Contact Value</Label>
                        <Input
                            :id="`contact_value_${index}`"
                            v-model="subscription.contact_value"
                            :placeholder="subscription.contact_type === 'email' ? 'email@example.com' : '+1234567890'"
                            required
                        />
                        <InputError :message="manualForm.errors[`subscriptions.${index}.contact_value`]"
                                    class="mt-2" />
                    </div>

                    <div class="grid gap-2">
                        <Label :for="`topic_${index}`">Topic</Label>
                        <select
                            :id="`topic_${index}`"
                            v-model="subscription.topic"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background"
                        >
                            <option value="promotions">Promotions</option>
                            <option value="notifications">Notifications</option>
                        </select>
                        <InputError :message="manualForm.errors[`subscriptions.${index}.topic`]" class="mt-2" />
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <Button type="button" variant="outline" @click="addManualEntry">Add Another Entry</Button>
                    <Button :disabled="manualForm.processing" type="submit">Submit</Button>
                </div>
            </form>
        </Card>

        <div v-if="page.props.flash.success" class="rounded-lg bg-green-50 p-4 text-green-800">
            {{ page.props.flash.success }}
        </div>

        <div
            v-if="page.props.flash.errors && Array.isArray(page.props.flash.errors) && page.props.flash.errors.length > 0"
            class="rounded-lg bg-red-50 p-4"
        >
            <h4 class="font-semibold text-red-800">Import Errors:</h4>
            <ul class="mt-2 list-inside list-disc text-red-700">
                <li v-for="(error, index) in page.props.flash.errors" :key="index">Row {{ error.row }}:
                    {{ error.error }}
                </li>
            </ul>
        </div>
    </AppLayout>
</template>
