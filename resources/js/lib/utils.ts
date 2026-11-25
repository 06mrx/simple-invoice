export { cn } from 'cn';

export function formatDate(value?: string | null, options?: Intl.DateTimeFormatOptions) {
    if (!value) return '-';
    const normalized = value.includes('T') ? value : value.replace(' ', 'T');
    const date = new Date(normalized.length <= 10 ? `${normalized}T00:00:00` : normalized);
    if (Number.isNaN(date.getTime())) return value;
    return date.toLocaleDateString('id-ID', options ?? {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
}
