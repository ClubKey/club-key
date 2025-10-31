import type { LucideIcon } from 'lucide-vue-next';
import type { Config } from 'ziggy-js';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: string;
    icon?: LucideIcon;
    isActive?: boolean;
}

export interface Paginator<T extends Record<unknown>> {
    current_page: number;
    data: T[];
    first_page_url: string;
    from: number;
    last_page: number;
    last_page_url: string;
    links: any[];
    next_page_url: string | null;
    path: string;
    per_page: number;
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

export interface DashboardDatum {
    value: number | string;
    heading: string;
    subheading?: string | null;
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

export interface Promotion {
    id: number;
    club_id: number;
    club?: Club;
    name: string;
    description: string;
    publish_at: string | null;
    published_at: string | null;
    start_at: string | null;
    end_at: string | null;
    is_active: boolean;
    link: string | null;
    created_at: string;
    updated_at: string;
}

export interface Visit {
    id: number;
    user_id: number;
    club_id: number;
    club_location_id: number | null;
    club_location?: ClubLocation;
    created_at: string;
    updated_at: string;
}

export type BreadcrumbItemType = BreadcrumbItem;

export interface Paginator<T> {
    data: T[];
    perPage: number;
    currentPage: number;
    path: string;
    query: Record<string, string>;
    fragment: string | null;
    pageName: string;
    total: number;
    lastPage: number;
    onEachSide: number;
    options: {
        [key: string]: unknown;
    };
}
