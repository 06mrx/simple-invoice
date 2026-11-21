import { usePage } from '@inertiajs/react';

export function usePermissions() {
    const auth = usePage().props.auth as {
        permissions?: string[];
        roles?: string[];
    };

    const permissions = auth?.permissions ?? [];
    const roles = auth?.roles ?? [];

    return {
        can: (permission: string) => permissions.includes(permission),
        canAny: (...items: string[]) =>
            items.some((permission) => permissions.includes(permission)),
        hasRole: (role: string) => roles.includes(role),
    };
}
