<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 40" :class="className" v-bind="$attrs">
        <!-- Background circle -->
        <circle cx="20" cy="20" r="19" fill="none" stroke="currentColor" stroke-width="2" opacity="0.2" />

        <!-- Main star/trophy -->
        <path fill="currentColor" d="M20 4l2.5 7.7h8.1l-6.6 4.8 2.5 7.7-6.5-4.7-6.5 4.7 2.5-7.7-6.6-4.8h8.1L20 4z" />

        <!-- Community/people icons -->
        <circle cx="12" cy="28" r="2" fill="currentColor" opacity="0.8" />
        <circle cx="20" cy="32" r="2" fill="currentColor" opacity="0.8" />
        <circle cx="28" cy="28" r="2" fill="currentColor" opacity="0.8" />

        <!-- Connection lines between people -->
        <path stroke="currentColor" stroke-width="1.5" stroke-linecap="round" opacity="0.6" d="M14 28.5l6 3.5M26 28.5l-6 3.5" />

        <!-- Reward points/sparkles -->
        <circle cx="8" cy="12" r="1" fill="currentColor" opacity="0.7" />
        <circle cx="32" cy="12" r="1" fill="currentColor" opacity="0.7" />
        <circle cx="6" cy="20" r="0.8" fill="currentColor" opacity="0.6" />
        <circle cx="34" cy="20" r="0.8" fill="currentColor" opacity="0.6" />
    </svg>
</template>
