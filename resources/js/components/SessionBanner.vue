<script lang="ts" setup>
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { usePage } from '@inertiajs/vue3';
import { AlertCircle, Info, Rocket, TriangleAlert, X } from 'lucide-vue-next';
import { computed } from 'vue';

const page = usePage();

const messageType = computed(() => {
    if (page.props.flash.success) return 'success';
    if (page.props.flash.error) return 'error';
    if (page.props.flash.warning) return 'warning';
    if (page.props.flash.info) return 'info';
    return '';
});

const shouldShow = computed(() => {
    return page.props.flash.success || page.props.flash.error || page.props.flash.warning || page.props.flash.info;
});

const hideMessage = () => {
    // Clear the flash message from the session
    // This prevents the message from showing again on page refresh
    if (page.props.flash) {
        page.props.flash.success = null;
        page.props.flash.error = null;
        page.props.flash.warning = null;
        page.props.flash.info = null;
    }
};

const message = computed(() => {
    return page.props.flash.success || page.props.flash.error || page.props.flash.warning || page.props.flash.info;
});

const title = computed(() => {
    switch (messageType.value) {
        case 'success':
            return 'Success';
        case 'error':
            return 'Error';
        case 'warning':
            return 'Warning';
        case 'info':
            return 'Info';
        default:
            return '';
    }
});
</script>

<template>
    <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="transform translate-y-2 opacity-0"
        enter-to-class="transform translate-y-0 opacity-100"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="transform translate-y-0 opacity-100"
        leave-to-class="transform translate-y-2 opacity-0"
    >
        <Alert v-if="shouldShow" :variant="messageType === 'error' ? 'destructive' : 'default'" class="fixed right-0 bottom-0 m-4 max-w-xl md:m-8">
            <Rocket v-if="messageType === 'success'" class="size-4" />
            <Info v-if="messageType === 'info'" class="size-4" />
            <AlertCircle v-if="messageType === 'error'" class="size-4" />
            <TriangleAlert v-else-if="messageType === 'warning'" class="size-4" />
            <AlertTitle>{{ title }}</AlertTitle>
            <AlertDescription>
                {{ message }}
            </AlertDescription>

            <Button class="absolute top-0 right-0 m-2" size="icon" variant="outline" @click="hideMessage">
                <X name="x" />
            </Button>
        </Alert>
    </Transition>
</template>
