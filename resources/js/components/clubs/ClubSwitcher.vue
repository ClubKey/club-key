<script lang="ts" setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import { ArrowRightLeftIcon, ChevronsUpDown, PlusIcon, Settings } from 'lucide-vue-next';
import { computed } from 'vue';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '../ui/dropdown-menu';
import { SidebarMenuButton } from '../ui/sidebar';
import ClubInfo from './ClubInfo.vue';

const page = usePage();
const user = page.props.auth.user;

const clubs = computed(() => page.props.clubs.filter((club) => club.id !== currentClub.value?.id));

const currentClub = computed(() => page.props.currentClub);

const hasOtherClubs = computed(() => clubs.value.length > 0);

const handleSwitchClub = () => {
    router.flushAll();
};
</script>

<template>
    <DropdownMenu v-if="currentClub || hasOtherClubs" class="min-w-auto">
        <DropdownMenuTrigger>
            <SidebarMenuButton class="data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground" size="lg">
                <ClubInfo :club="currentClub" :show-slug="true" />
                <ChevronsUpDown class="ml-auto size-4" />
            </SidebarMenuButton>
        </DropdownMenuTrigger>
        <DropdownMenuContent :side-offset="4" align="end" class="w-(--reka-dropdown-menu-trigger-width) min-w-56 rounded-lg">
            <DropdownMenuLabel class="p-0 font-normal">
                <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
                    <ClubInfo :club="currentClub" :show-slug="true" />
                </div>
            </DropdownMenuLabel>
            <DropdownMenuGroup v-if="currentClub && user.club_role === 'admin'">
                <DropdownMenuSeparator />
                <DropdownMenuItem :as-child="true">
                    <Link :href="route('clubs.edit', currentClub.slug)" as="button" class="flex w-full items-center" prefetch>
                        <Settings class="mr-2 h-4 w-4" />
                        Club Settings
                    </Link>
                </DropdownMenuItem>
            </DropdownMenuGroup>
            <DropdownMenuGroup v-if="hasOtherClubs">
                <DropdownMenuSeparator />
                <DropdownMenuLabel class="p-2 font-normal">
                    <div class="flex w-full items-center gap-4">
                        <ArrowRightLeftIcon class="h-4 w-4 text-current" />
                        Switch clubs
                    </div>
                </DropdownMenuLabel>
                <DropdownMenuItem v-for="club in clubs" :key="club.id" :as-child="true">
                    <Link
                        :href="route('clubs.switch', club.slug)"
                        :preserve-state="false"
                        as="button"
                        class="block w-full"
                        method="post"
                        @click="handleSwitchClub"
                    >
                        <ClubInfo :club="club" />
                    </Link>
                </DropdownMenuItem>
            </DropdownMenuGroup>
            <DropdownMenuGroup v-if="page.props.auth.user.role === 'super_admin' || page.props.auth.user.role === 'club_admin'">
                <DropdownMenuSeparator />
                <DropdownMenuItem :as-child="true">
                    <Link :href="route('clubs.create')" as="button" class="block w-full">
                        <PlusIcon class="h-4 w-4 text-current" />
                        New Club
                    </Link>
                </DropdownMenuItem>
            </DropdownMenuGroup>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
