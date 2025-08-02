<script setup lang="ts">
import Icon from '@/components/Icon.vue';
import { usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const page = usePage();
const messageType = ref<'success' | 'error' | 'warning' | 'info'>('success');

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

const bannerClasses = computed(() => {
    const baseClasses = 'fixed bottom-4 right-4 z-50 max-w-sm rounded-lg border p-4 shadow-lg transition-all duration-300 ease-in-out';

    switch (messageType.value) {
        case 'success':
            return `${baseClasses} bg-green-50 border-green-200 text-green-800 dark:bg-green-900/20 dark:border-green-800 dark:text-green-200`;
        case 'error':
            return `${baseClasses} bg-red-50 border-red-200 text-red-800 dark:bg-red-900/20 dark:border-red-800 dark:text-red-200`;
        case 'warning':
            return `${baseClasses} bg-yellow-50 border-yellow-200 text-yellow-800 dark:bg-yellow-900/20 dark:border-yellow-800 dark:text-yellow-200`;
        case 'info':
            return `${baseClasses} bg-blue-50 border-blue-200 text-blue-800 dark:bg-blue-900/20 dark:border-blue-800 dark:text-blue-200`;
        default:
            return baseClasses;
    }
});

const iconClasses = computed(() => {
    const baseClasses = 'h-5 w-5';

    switch (messageType.value) {
        case 'success':
            return `${baseClasses} text-green-600 dark:text-green-400`;
        case 'error':
            return `${baseClasses} text-red-600 dark:text-red-400`;
        case 'warning':
            return `${baseClasses} text-yellow-600 dark:text-yellow-400`;
        case 'info':
            return `${baseClasses} text-blue-600 dark:text-blue-400`;
        default:
            return baseClasses;
    }
});

const getIcon = computed(() => {
    switch (messageType.value) {
        case 'success':
            return 'check-circle';
        case 'error':
            return 'x-circle';
        case 'warning':
            return 'exclamation-triangle';
        case 'info':
            return 'information-circle';
        default:
            return 'information-circle';
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
        <div v-if="shouldShow" :class="bannerClasses">
            <div class="flex items-start gap-3">
                <Icon :name="getIcon" :class="iconClasses" />
                <div class="flex-1 text-sm">
                    {{ message }}
                </div>
                <button
                    @click="hideMessage"
                    class="flex-shrink-0 rounded-md p-1 text-gray-400 hover:text-gray-600 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 focus:outline-none dark:hover:text-gray-300"
                >
                    <Icon name="x" class="h-4 w-4" />
                </button>
            </div>
        </div>
    </Transition>
</template>
