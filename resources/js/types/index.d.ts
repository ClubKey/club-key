import type { LucideIcon } from 'lucide-vue-next';
import type { Config } from 'ziggy-js';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface DataTableSortOption {
    value: string;
    label: string;
    direction: 'asc' | 'desc';
}

export type DataTableFilterType = 'single' | 'multi';

export interface DataTableFilterOption {
    label: string;
    value: string;
}

export interface DataTableFilterConfig {
    label: string;
    type: DataTableFilterType;
    options: DataTableFilterOption[];
}

export interface DataTableState {
    search: string;
    sort: {
        key: string | null;
        direction: 'asc' | 'desc';
    };
    filters: Record<string, unknown>;
}

export interface DataTableMeta {
    sorts: DataTableSortOption[];
    filters: Record<string, DataTableFilterConfig>;
    state: DataTableState;
}

export interface NavItem {
    title: string;
    href: string;
    icon?: LucideIcon;
    isActive?: boolean;
}

export type AppPageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    currentClub: Club | null;
    clubs: Club[];
    ziggy: Config & { location: string };
    sidebarOpen: boolean;
    flash: {
        success?: string | null;
        error?: string | null;
        warning?: string | null;
        info?: string | null;
    };
};
export type AppRole = 'super_admin' | 'club_admin' | 'user';

export type ClubRole = 'admin' | 'member';

export interface User {
    id: number;
    name: string;
    email: string;
    phone?: string;
    avatar?: string;
    current_club_id?: number;
    currentClub?: Club;
    club_role?: ClubRole;
    role?: AppRole;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

export interface ClubUser {
    id: number;
    user_id: number;
    user: User;
    club_id: number;
    club: Club;
    role: ClubRole;
    reward_level_id?: number;
    visits_past_30_days: number;
    total_visits: number;
    visits_last_week: number;
    next_reward_level_id?: number;
    reward_level?: RewardLevel;
    next_reward_level?: RewardLevel;
    created_at: string;
    updated_at: string;
}

export interface Club {
    id: number;
    owner_id: number;
    name: string;
    visit_qr_code_path: string;
    slug: string;
    description?: string;
    logo_path?: string;
    email?: string;
    phone?: string;
    website?: string;
    street_address?: string;
    city?: string;
    state?: string;
    zip?: string;
    facebook?: string;
    instagram?: string;
    x?: string;
    youtube?: string;
    tiktok?: string;
    linkedin?: string;
    pinterest?: string;
    snapchat?: string;
    created_at: string;
    updated_at: string;
    owner?: User;
}

export interface AdminOverview {
    numberOfMembers: number;
    numberOfMembersToday: number;
    numberOfVisitsThisWeek: number;
    mostPopularLocationName: string | null;
}

export interface AdminInvitation {
    id: number;
    email: string;
    club_id: number;
    club: Club;
    inviter_id: number;
    inviter?: User;
    role: ClubRole;
    expires_at: string;
    created_at: string;
    updated_at: string;
}

export interface ClubLocation {
    id: number;
    club_id: number;
    club?: Club;
    name: string;
    visit_qr_code_path: string;
    visit_tap_link?: string;
    street_address: string;
    city: string;
    state: string;
    zip: string;
    phone?: string;
    created_at: string;
    updated_at: string;
}

export interface Reward {
    id: number;
    description: string;
    order: number;
    reward_level_id: number;
    rewardLevel: RewardLevel;
}

export interface RewardLevel {
    id: number;
    name: string;
    icon_name: string | null;
    min_visits_per_month: number;
    color: string | null;
    description: string | null;
    percentage_discount: number;
    order: number;
    rewards?: Reward[];
}

export type BreadcrumbItemType = BreadcrumbItem;
