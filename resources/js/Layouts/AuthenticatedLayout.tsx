import { Button } from '@/Components/ui/button';
import {
    CommandDialog,
    CommandEmpty,
    CommandGroup,
    CommandInput,
    CommandItem,
    CommandList,
} from '@/Components/ui/command';
import { cn } from '@/lib/utils';
import { Link, router, usePage } from '@inertiajs/react';
import {
    Baby,
    Building2,
    ClipboardList,
    FileText,
    KeyRound,
    LayoutDashboard,
    LogOut,
    Moon,
    PanelLeft,
    ScrollText,
    Search,
    Settings2,
    ShieldCheck,
    Sun,
    User as UserIcon,
    Users,
    type LucideIcon,
} from 'lucide-react';
import {
    PropsWithChildren,
    ReactNode,
    useEffect,
    useRef,
    useState,
} from 'react';
import { createPortal } from 'react-dom';

type NavItem = {
    label: string;
    href: string;
    icon: LucideIcon;
    show: boolean;
    active: boolean;
};

export default function Authenticated({
    header,
    children,
}: PropsWithChildren<{ header?: ReactNode }>) {
    const page = usePage<{
        auth: {
            user: { id: number; name: string; email: string };
            permissions: string[];
            roles: string[];
        };
        name: string;
    }>();
    const user = page.props.auth.user;
    const permissions = page.props.auth.permissions ?? [];
    const has = (permission: string) => permissions.includes(permission);

    const [sidebarOpen, setSidebarOpen] = useState(false);
    const [collapsed, setCollapsed] = useState(
        () => localStorage.getItem('sidebar-collapsed') === 'true',
    );
    const [dark, setDark] = useState(
        () => localStorage.getItem('theme') === 'dark',
    );
    const [commandOpen, setCommandOpen] = useState(false);
    const [accountOpen, setAccountOpen] = useState(false);
    const [accountPos, setAccountPos] = useState({ top: 0, right: 0 });
    const accountRef = useRef<HTMLDivElement>(null);
    const accountMenuRef = useRef<HTMLDivElement>(null);
    const url = page.url;

    useEffect(() => {
        localStorage.setItem('sidebar-collapsed', String(collapsed));
    }, [collapsed]);

    useEffect(() => {
        localStorage.setItem('theme', dark ? 'dark' : 'light');
        document.documentElement.classList.toggle('dark', dark);
    }, [dark]);

    useEffect(() => setSidebarOpen(false), [url]);

    useEffect(() => {
        const onKeyDown = (event: KeyboardEvent) => {
            if (
                (event.metaKey || event.ctrlKey) &&
                event.key.toLowerCase() === 'k'
            ) {
                event.preventDefault();
                setCommandOpen(true);
            }
            if (event.key === 'Escape') setCommandOpen(false);
        };
        window.addEventListener('keydown', onKeyDown);
        return () => window.removeEventListener('keydown', onKeyDown);
    }, []);

    useEffect(() => {
        if (!accountOpen) return;

        const onPointerDown = (event: MouseEvent) => {
            const target = event.target as Node;
            if (accountRef.current?.contains(target)) return;
            if (accountMenuRef.current?.contains(target)) return;
            setAccountOpen(false);
        };
        const onKey = (event: KeyboardEvent) => {
            if (event.key === 'Escape') setAccountOpen(false);
        };

        document.addEventListener('mousedown', onPointerDown);
        document.addEventListener('keydown', onKey);

        return () => {
            document.removeEventListener('mousedown', onPointerDown);
            document.removeEventListener('keydown', onKey);
        };
    }, [accountOpen]);

    const toggleAccount = () => {
        const rect = accountRef.current?.getBoundingClientRect();
        if (rect) {
            setAccountPos({
                top: rect.bottom + 8,
                right: window.innerWidth - rect.right,
            });
        }
        setAccountOpen((value) => !value);
    };

    const mainItems: NavItem[] = [
        {
            label: 'Dashboard',
            href: route('dashboard'),
            icon: LayoutDashboard,
            show: true,
            active: route().current('dashboard'),
        },
        {
            label: 'Bayi',
            href: route('bayi.index'),
            icon: Baby,
            show: has('view-bayi'),
            active: route().current('bayi.*'),
        },
    ];

    const adminItems: NavItem[] = [
        {
            label: 'Users',
            href: route('admin.users.index'),
            icon: Users,
            show: has('view-user'),
            active: route().current('admin.users.*'),
        },
        {
            label: 'Roles',
            href: route('admin.roles.index'),
            icon: ShieldCheck,
            show: has('view-role'),
            active: route().current('admin.roles.*'),
        },
        {
            label: 'Permissions',
            href: route('admin.permissions.index'),
            icon: KeyRound,
            show: has('view-permission'),
            active: route().current('admin.permissions.*'),
        },
        {
            label: 'Audit',
            href: route('admin.audit.index'),
            icon: ScrollText,
            show: has('view-audit'),
            active: route().current('admin.audit.*'),
        },
    ].filter((item) => item.show);

    const invoiceItems: NavItem[] = [
        {
            label: 'Invoice',
            href: route('invoices.index'),
            icon: FileText,
            show: has('view-invoice'),
            active: route().current('invoices.*'),
        },
        {
            label: 'Customers',
            href: route('customers.index'),
            icon: Building2,
            show: has('view-customer'),
            active: route().current('customers.*'),
        },
        {
            label: 'PO',
            href: route('purchase-orders.index'),
            icon: ClipboardList,
            show: has('view-purchaseorder'),
            active: route().current('purchase-orders.*'),
        },
        {
            label: 'Setting Invoice',
            href: route('invoice-settings.edit'),
            icon: Settings2,
            show: has('view-invoicesetting'),
            active: route().current('invoice-settings.*'),
        },
    ].filter((item) => item.show);

    const commandItems = [
        ...mainItems.filter((item) => item.show),
        ...adminItems,
        ...invoiceItems,
        {
            label: 'Profile',
            href: route('profile.edit'),
            icon: UserIcon,
            show: true,
            active: route().current('profile.edit'),
        },
    ];

    const renderNav = (items: NavItem[]) =>
        items
            .filter((item) => item.show)
            .map((item) => {
                const Icon = item.icon;
                return (
                    <Link
                        key={item.label}
                        href={item.href}
                        title={item.label}
                        className={cn(
                            'flex items-center gap-3 rounded-md py-2 text-sm font-medium transition-colors',
                            collapsed ? 'justify-center px-2' : 'px-3',
                            item.active
                                ? 'bg-sidebar-accent text-sidebar-accent-foreground'
                                : 'text-sidebar-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground',
                        )}
                    >
                        <Icon className="h-4 w-4 shrink-0" />
                        {!collapsed && (
                            <span className="truncate">{item.label}</span>
                        )}
                    </Link>
                );
            });

    const toggleSidebar = () => {
        if (window.innerWidth < 768) setSidebarOpen((value) => !value);
        else setCollapsed((value) => !value);
    };

    return (
        <div className="flex min-h-screen w-full">
            {sidebarOpen && (
                <div
                    className="fixed inset-0 z-40 bg-black/50 md:hidden"
                    onClick={() => setSidebarOpen(false)}
                />
            )}

            <aside
                className={cn(
                    'border-sidebar-border bg-sidebar text-sidebar-foreground fixed inset-y-0 left-0 z-50 flex w-64 flex-col border-r transition-all duration-200 ease-in-out md:relative',
                    collapsed ? 'md:w-16' : 'md:w-64',
                    sidebarOpen
                        ? 'translate-x-0'
                        : '-translate-x-full md:translate-x-0',
                )}
            >
                <div
                    className={cn(
                        'border-sidebar-border flex h-14 items-center border-b',
                        collapsed ? 'justify-center px-2' : 'px-3',
                    )}
                >
                    <Link
                        href={route('dashboard')}
                        className="flex items-center gap-2 overflow-hidden font-semibold"
                    >
                        <div className="bg-primary text-primary-foreground flex h-9 w-9 shrink-0 items-center justify-center overflow-hidden rounded-md">
                            <img
                                src="/images/favicon/android-chrome-192x192.png"
                                alt={page.props.name}
                                className="h-6 w-6 object-contain"
                            />
                        </div>
                        {!collapsed && (
                            <span className="truncate">{page.props.name}</span>
                        )}
                    </Link>
                </div>

                <nav className="flex-1 overflow-y-auto px-2 py-4">
                    <div className="space-y-1">
                        {!collapsed && (
                            <p className="text-muted-foreground px-2 text-xs font-semibold uppercase">
                                Menu
                            </p>
                        )}
                        {renderNav(mainItems)}
                    </div>

                    {adminItems.length > 0 && (
                        <div className="mt-6 space-y-1">
                            {!collapsed && (
                                <p className="text-muted-foreground px-2 text-xs font-semibold uppercase">
                                    Administration
                                </p>
                            )}
                            {renderNav(adminItems)}
                        </div>
                    )}

                    {invoiceItems.length > 0 && (
                        <div className="mt-6 space-y-1">
                            {!collapsed && (
                                <p className="text-muted-foreground px-2 text-xs font-semibold uppercase">
                                    Invoice
                                </p>
                            )}
                            {renderNav(invoiceItems)}
                        </div>
                    )}
                </nav>

                <div className="border-sidebar-border border-t p-2">
                    <Link
                        href={route('logout')}
                        method="post"
                        as="button"
                        title="Logout"
                        className={cn(
                            'text-sidebar-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground flex w-full items-center gap-3 rounded-md py-2 text-sm font-medium transition-colors',
                            collapsed ? 'justify-center px-2' : 'px-3',
                        )}
                    >
                        <LogOut className="h-4 w-4 shrink-0" />
                        {!collapsed && <span>Logout</span>}
                    </Link>
                </div>
            </aside>

            <div className="bg-sidebar flex flex-1 flex-col overflow-hidden p-2">
                <div className="bg-background flex flex-1 flex-col overflow-hidden rounded-2xl border shadow-md">
                    <header className="bg-background flex h-14 items-center gap-3 border-b px-4 md:px-6">
                        <Button
                            variant="outline"
                            size="icon"
                            onClick={toggleSidebar}
                            aria-label="Toggle sidebar"
                        >
                            <PanelLeft className="h-4 w-4" />
                        </Button>

                        <div className="flex-1 text-lg font-semibold">
                            {header ?? 'Dashboard'}
                        </div>

                        <Button
                            variant="outline"
                            onClick={() => setCommandOpen(true)}
                            className="text-muted-foreground hidden h-9 w-72 items-center gap-2 px-3 md:inline-flex"
                        >
                            <Search className="h-4 w-4" />
                            <span className="flex-1 text-left">Search...</span>
                            <kbd className="bg-muted text-muted-foreground pointer-events-none inline-flex h-5 items-center gap-1 rounded border px-1.5 font-mono text-[10px] font-medium select-none">
                                <span className="text-xs">⌘</span>K
                            </kbd>
                        </Button>

                        <Button
                            variant="outline"
                            size="icon"
                            onClick={() => setDark((value) => !value)}
                            aria-label="Toggle theme"
                        >
                            {dark ? (
                                <Moon className="h-4 w-4" />
                            ) : (
                                <Sun className="h-4 w-4" />
                            )}
                        </Button>

                        <div ref={accountRef}>
                            <button
                                type="button"
                                onClick={toggleAccount}
                                aria-label="Account menu"
                                aria-expanded={accountOpen}
                                className="border-input bg-background hover:bg-accent hover:text-accent-foreground focus-visible:ring-ring inline-flex h-9 w-9 items-center justify-center rounded-md border text-sm font-medium transition-colors outline-none focus-visible:ring-2"
                            >
                                <UserIcon className="h-4 w-4" />
                            </button>
                        </div>

                        {accountOpen &&
                            createPortal(
                                <div className="fixed inset-0 z-50">
                                    <div
                                        ref={accountMenuRef}
                                        className="bg-popover text-popover-foreground absolute w-48 rounded-lg border p-1 shadow-md"
                                        style={{
                                            top: accountPos.top,
                                            right: accountPos.right,
                                        }}
                                    >
                                        <div className="flex flex-col space-y-1 px-2 py-1.5">
                                            <p className="text-sm leading-none font-medium">
                                                {user.name}
                                            </p>
                                            <p className="text-muted-foreground text-xs leading-none">
                                                {user.email}
                                            </p>
                                        </div>
                                        <div className="bg-border my-1 h-px" />
                                        <button
                                            type="button"
                                            onClick={() => {
                                                router.visit(
                                                    route('profile.edit'),
                                                );
                                                setAccountOpen(false);
                                            }}
                                            className="hover:bg-accent hover:text-accent-foreground block w-full rounded-md px-2 py-1.5 text-left text-sm"
                                        >
                                            Profile
                                        </button>
                                        <button
                                            type="button"
                                            onClick={() => {
                                                router.post(route('logout'));
                                                setAccountOpen(false);
                                            }}
                                            className="hover:bg-accent hover:text-accent-foreground flex w-full items-center rounded-md px-2 py-1.5 text-left text-sm"
                                        >
                                            <LogOut className="mr-2 h-4 w-4" />
                                            Log Out
                                        </button>
                                    </div>
                                </div>,
                                document.body,
                            )}
                    </header>

                    <main className="flex-1 overflow-y-auto p-4 md:p-6">
                        {children}
                    </main>
                </div>
            </div>

            <CommandDialog open={commandOpen} onOpenChange={setCommandOpen}>
                <CommandInput placeholder="Type a command or search..." />
                <CommandList>
                    <CommandEmpty>No results found.</CommandEmpty>
                    <CommandGroup heading="Navigation">
                        {commandItems.map((item) => {
                            const Icon = item.icon;
                            return (
                                <CommandItem
                                    key={item.label}
                                    value={item.label}
                                    onSelect={() => {
                                        setCommandOpen(false);
                                        router.visit(item.href);
                                    }}
                                >
                                    <Icon className="mr-2 h-4 w-4" />
                                    {item.label}
                                </CommandItem>
                            );
                        })}
                    </CommandGroup>
                </CommandList>
            </CommandDialog>
        </div>
    );
}
