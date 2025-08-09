<script lang="ts" setup>
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { Link, usePage } from '@inertiajs/vue3';
import { Eye, LayoutGrid, MapPin, Trophy, User } from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from './AppLogo.vue';

const user = usePage().props.auth.user;

const club = usePage().props.currentClub;

const mainNavItems = computed(() => {
    const items = [
        {
            title: 'Dashboard',
            href: '/dashboard',
            icon: LayoutGrid,
        },
    ];

    if (user.current_club_id) {
        items.push({
            title: 'Locations',
            href: '/locations',
            icon: MapPin,
        });
    }
    if (user.club_role === 'admin') {
        items.push({
            title: 'Reward Levels',
            href: '/reward-levels',
            icon: Trophy,
        });
        items.push({
            title: 'Members',
            href: '/members',
            icon: User,
        });
    }

    return items;
});

const footerNavItems = computed(() => {
    if (club) {
        return [
            {
                title: 'View Club',
                href: `/${club.slug}`,
                icon: Eye,
            },
        ];
    }
    return [];
});
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton as-child size="lg">
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
