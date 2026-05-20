<script lang="ts" setup>
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { Club } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
    club: Club;
}>();

const sidebarNavItems = computed(() => {
    const base = [
        {
            title: 'Details',
            href: `/clubs/${props.club.slug}/edit`,
        },
        {
            title: 'Admins',
            href: `/clubs/${props.club.slug}/admins`,
        },
        {
            title: 'Check In Qr Code',
            href: `/clubs/${props.club.slug}/check-in-code`,
        },
        {
            title: 'Integrations',
            href: `/clubs/${props.club.slug}/integrations/square`,
        },
    ];

    if (props.club.owner_id === usePage().props.auth.user.id) {
        base.push({
            title: 'Danger Zone',
            href: `/clubs/${props.club.slug}/danger-zone`,
        });
    }

    return base;
});

const page = usePage();

const currentPath = page.props.ziggy?.location ? new URL(page.props.ziggy.location).pathname : '';
</script>

<template>
    <div class="flex flex-col gap-6">
        <Heading :title="props.club.name" description="Manage your club" />

        <div class="flex flex-col gap-y-6 lg:flex-row lg:gap-x-12 lg:gap-y-0">
            <aside class="w-full max-w-xl lg:w-48">
                <nav class="flex flex-col space-y-1 space-x-0">
                    <Button
                        v-for="item in sidebarNavItems"
                        :key="item.href"
                        :class="['w-full justify-start', { 'bg-muted': currentPath === item.href }]"
                        as-child
                        variant="ghost"
                    >
                        <Link :href="item.href">
                            {{ item.title }}
                        </Link>
                    </Button>
                </nav>
            </aside>

            <Separator class="lg:hidden" />

            <div class="flex max-w-2xl flex-1 flex-col gap-6">
                <slot />
            </div>
        </div>
    </div>
</template>
