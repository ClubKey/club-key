<script lang="ts" setup>
import AppContent from '@/components/AppContent.vue';
import AppShell from '@/components/AppShell.vue';
import AppSidebar from '@/components/AppSidebar.vue';
import AppSidebarHeader from '@/components/AppSidebarHeader.vue';
import SessionBanner from '@/components/SessionBanner.vue';
import type { BreadcrumbItemType } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <AppShell variant="sidebar">
        <AppSidebar />
        <AppContent class="overflow-x-hidden" variant="sidebar">
            <AppSidebarHeader :breadcrumbs="breadcrumbs" />
            <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl p-4 md:p-6">
                <slot />
            </div>
        </AppContent>
        <SessionBanner />
    </AppShell>
</template>
