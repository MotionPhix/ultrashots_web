import { InertiaLinkProps } from '@inertiajs/vue3';
import type { LucideIcon } from 'lucide-vue-next';
import type { Component } from 'vue';

export interface Auth {
    user: User | null;
    is_admin: boolean;
}

export interface FlashMessages {
    success?: string | null;
    error?: string | null;
    warning?: string | null;
    info?: string | null;
    rate_limit?: string | null;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon|Component;
    isActive?: boolean;
}

export type AppPageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    flash: FlashMessages;
    sidebarOpen: boolean;
};

export interface User {
    id: number;
    name?: string;
    full_name?: string;
    first_name?: string;
    last_name?: string;
    job_title?: string;
    location?: string;
    bio?: string;
    socials?: {
        linkedin?: string;
        twitter?: string;
        behance?: string;
    };
    email: string;
    avatar?: string;
    avatar_url?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

export interface Customer {
    id?: number;
    uuid?: string;
    first_name?: string;
    last_name?: string;
    full_name?: string;
    display_name?: string;
    company_name?: string;
    email?: string;
}

export interface ProjectImage {
    id: number;
    name?: string;
    file_name?: string;
    mime_type?: string;
    size?: number;
    url: string;
    thumb_url?: string;
    medium_url?: string;
    large_url?: string;
}

export interface ProjectMedia {
    id: number;
    name?: string;
    original_url: string;
}

export interface Project {
    id: number;
    uuid: string;
    name: string;
    slug?: string;
    description?: string | null;
    short_description?: string | null;
    production_type?: string | null;
    category?: string | null;
    status: 'not_started' | 'in_progress' | 'on_hold' | 'completed' | 'cancelled' | string;
    status_color?: string;
    priority?: 'low' | 'medium' | 'high' | 'critical' | 'urgent' | string;
    priority_color?: string;
    start_date?: string | null;
    end_date?: string | null;
    duration?: number | null;
    progress?: number;
    estimated_hours?: number | string | null;
    actual_hours?: number | string | null;
    hours_variance?: number | null;
    budget?: number | string | null;
    technologies?: string[];
    main_technologies?: string[];
    features?: string[];
    challenges?: string | null;
    solutions?: string | null;
    process_steps?: Array<{ title: string; description: string }> | null;
    results?: Array<{ metric: string; value: string }> | string | null;
    client_feedback?: string | null;
    is_featured: boolean;
    is_public?: boolean;
    is_overdue?: boolean;
    sort_order?: number;
    meta_title?: string | null;
    meta_description?: string | null;
    poster_url?: string | null;
    live_url?: string | null;
    github_url?: string | null;
    figma_url?: string | null;
    behance_url?: string | null;
    dribbble_url?: string | null;
    gallery_images?: ProjectImage[];
    media?: ProjectMedia[];
    customer?: Customer | null;
    created_at?: string;
    updated_at?: string;
}

export interface Logo {
    id: number;
    uuid: string;
    brand?: string | null;
    poster_url?: string | null;
}

export interface Download {
    id: number;
    uuid: string;
    title?: string | null;
    brand?: string | null;
    description?: string | null;
    category?: string | null;
    file_type?: string | null;
    formatted_file_size?: string | null;
    download_count?: number;
    poster_url?: string | null;
    is_featured?: boolean;
    has_file?: boolean;
    created_at?: string | null;
}

export type BreadcrumbItemType = BreadcrumbItem;
