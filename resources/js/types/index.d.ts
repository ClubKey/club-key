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

export type AppPageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    currentClub: Club | null;
    clubs: Club[];
    ziggy: Config & { location: string };
    sidebarOpen: boolean;
};
export type AppRole = 'super_admin' | 'club_admin' | 'user';

export type ClubRole = 'admin' | 'member';

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    current_club_id?: number;
    currentClub?: Club;
    club_role?: ClubRole;
    role?: AppRole;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

export interface Club {
    id: number;
    owner_id: number;
    name: string;
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
    street_address: string;
    city: string;
    state: string;
    zip: string;
    phone?: string;
    created_at: string;
    updated_at: string;
}

export type BreadcrumbItemType = BreadcrumbItem;
