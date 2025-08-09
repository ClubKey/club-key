<script lang="ts" setup>
import AppContent from '@/components/AppContent.vue';
import AppShell from '@/components/AppShell.vue';
import ClubPageHeader from '@/components/ClubPageHeader.vue';
import SessionBanner from '@/components/SessionBanner.vue';
import type { BreadcrumbItemType, Club } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
    club: Club;
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <AppShell class="flex-col">
        <ClubPageHeader :breadcrumbs :club />
        <AppContent>
            <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl p-4 md:p-6">
                <slot />
            </div>
        </AppContent>

        <SessionBanner />
    </AppShell>
</template>
