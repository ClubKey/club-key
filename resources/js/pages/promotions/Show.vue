<script lang="ts" setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Promotion } from '@/types';
import { Head } from '@inertiajs/vue3';

defineProps<{
    promotion: Promotion;
}>();
</script>

<template>
    <AppLayout>
        <Head title="Show" />
    </AppLayout>
</template>
