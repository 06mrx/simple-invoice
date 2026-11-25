export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
    roles?: { id: number; name: string }[];
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    name: string;
    auth: {
        user: User;
        permissions: string[];
        roles: string[];
    };
    flash?: {
        success?: string;
        error?: string;
        info?: string;
    };
};
