<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();
const appName = page.props.name as string;
</script>

<template>
    <div class="flex min-h-dvh flex-col bg-neutral-50 dark:bg-neutral-900">
        <header class="border-b bg-white px-6 py-4 dark:bg-neutral-800">
            <Link :href="route('home')" class="flex items-center gap-2 text-lg font-semibold">
                <AppLogoIcon class="size-8 fill-current" />
                <span>{{ appName }}</span>
            </Link>
        </header>
        <main class="flex flex-1 items-center justify-center p-6">
            <slot />
        </main>
    </div>
</template>
