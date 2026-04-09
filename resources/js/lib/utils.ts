import type { InertiaLinkProps } from '@inertiajs/vue3';
import type { ClassValue } from 'clsx';
import { clsx } from 'clsx';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export function toUrl(href: NonNullable<InertiaLinkProps['href']>) {
    return typeof href === 'string' ? href : href?.url;
}

function toPath(value: string): string {
    if (value.startsWith('http://') || value.startsWith('https://')) {
        return new URL(value).pathname;
    }

    return value.startsWith('/') ? value : `/${value}`;
}

export function urlIsActive(
    href: NonNullable<InertiaLinkProps['href']>,
    currentUrl: string,
): boolean {
    const targetPath = toPath(toUrl(href));
    const currentPath = toPath(currentUrl);

    if (targetPath === '/') {
        return currentPath === '/';
    }

    return currentPath === targetPath || currentPath.startsWith(`${targetPath}/`);
}
