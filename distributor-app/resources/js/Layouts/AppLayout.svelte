<script>
    import { router, usePage } from '@inertiajs/svelte';
    import Icon from '../Components/Icon.svelte';
    import Logo from '../Components/Logo.svelte';

    let { children } = $props();

    const page = usePage();
    const currentUrl = $derived(page.url);
    const user = $derived(page.props.auth?.user);
    const role = $derived(user?.role ?? 'customer');

    // Sidebar auto-collapse: expand ≥ 1536 (2xl), collapse < 1536
    // Manual toggle → stored in localStorage, overrides breakpoint default
    function getBreakpointDefault() {
        return window.innerWidth < 1536;
    }

    let userToggled = $state(localStorage.getItem('sentrax_sidebar_toggled') === 'true');
    let collapsed = $state(
        userToggled
            ? localStorage.getItem('sentrax_sidebar_collapsed') === 'true'
            : getBreakpointDefault()
    );

    $effect(() => {
        function handleResize() {
            if (!userToggled) {
                collapsed = getBreakpointDefault();
            }
        }
        window.addEventListener('resize', handleResize);
        return () => window.removeEventListener('resize', handleResize);
    });

    let userMenuOpen = $state(false);
    let topMenuOpen = $state(false);
    let expandedMenus = $state({});
    let hoveredMenu = $state(null);
    let menuPosition = $state({ top: 0, left: 0 });

    // Auto-expand menu if child is active
    $effect(() => {
        for (const item of nav) {
            if (item.children) {
                const hasActiveChild = item.children.some(c => isActive(c.href));
                if (hasActiveChild) {
                    expandedMenus[item.label] = true;
                }
            }
        }
    });

    $effect(() => {
        if (!userMenuOpen) return;
        function close() {
            userMenuOpen = false;
        }
        document.addEventListener('click', close);
        return () => document.removeEventListener('click', close);
    });

    $effect(() => {
        if (!topMenuOpen) return;
        function close() {
            topMenuOpen = false;
        }
        document.addEventListener('click', close);
        return () => document.removeEventListener('click', close);
    });

    // Close portal dropdown on click outside
    $effect(() => {
        if (hoveredMenu === null) return;
        function handleClick(e) {
            if (!e.target.closest('.sidebar-dropdown') && !e.target.closest('.portal-dropdown')) {
                hoveredMenu = null;
            }
        }
        document.addEventListener('click', handleClick);
        return () => document.removeEventListener('click', handleClick);
    });

    const navByRole = {
        owner: [
            { label: 'Dashboard', href: '/owner', icon: 'home' },
            { label: 'Order', href: '/admin/orders', icon: 'cart' },
            { label: 'Produk', icon: 'box', children: [
                { label: 'Daftar Produk', href: '/admin/products' },
                { label: 'Kategori', href: '/admin/products/categories' },
                { label: 'Daftar Harga', href: '/admin/products?tab=prices' },
            ]},
            { label: 'Area', href: '/admin/areas', icon: 'map' },
            { label: 'Pabrik', href: '/admin/plants', icon: 'factory' },
            { label: 'Ongkir', href: '/admin/shipping-rates', icon: 'truck' },
            { label: 'Customer', href: '/admin/customers', icon: 'building' },
            { label: 'User', href: '/admin/users-manage', icon: 'users' },
            { label: 'Laporan', href: '/admin/reports', icon: 'chart' },
        ],
        admin: [
            { label: 'Dashboard', href: '/admin', icon: 'home' },
            { label: 'Order', href: '/admin/orders', icon: 'cart' },
            { label: 'Produk', icon: 'box', children: [
                { label: 'Daftar Produk', href: '/admin/products' },
                { label: 'Kategori', href: '/admin/products/categories' },
                { label: 'Daftar Harga', href: '/admin/products?tab=prices' },
            ]},
            { label: 'Area', href: '/admin/areas', icon: 'map' },
            { label: 'Pabrik', href: '/admin/plants', icon: 'factory' },
            { label: 'Ongkir', href: '/admin/shipping-rates', icon: 'truck' },
            { label: 'Customer', href: '/admin/customers', icon: 'building' },
            { label: 'User', href: '/admin/users-manage', icon: 'users' },
            { label: 'Laporan', href: '/admin/reports', icon: 'chart' },
        ],
        sales: [
            { label: 'Dashboard', href: '/sales', icon: 'home' },
        ],
        customer: [
            { label: 'Dashboard', href: '/customer', icon: 'home' },
            { label: 'Order', href: '/customer/order', icon: 'cart' },
            { label: 'Referal', href: '/customer/referal', icon: 'referral' },
        ],
    };

    const nav = $derived(navByRole[role] ?? navByRole.customer);

    function toggle() {
        collapsed = !collapsed;
        userToggled = true;
        localStorage.setItem('sentrax_sidebar_toggled', 'true');
        localStorage.setItem('sentrax_sidebar_collapsed', String(collapsed));
    }

    // Check if any child or parent URL is active
    function isChildActive(item) {
        if (!item.children) return false;
        return item.children.some(c => {
            if (!c.href) return false;
            if (currentUrl === c.href) return true;
            if (c.href === '/admin/products' && currentUrl.startsWith('/admin/products')) return true;
            return false;
        });
    }

    function isActive(href) {
        if (!href) return false;
        return currentUrl === href;
    }

    function initials(name) {
        if (!name) return '?';
        const parts = name.trim().split(/\s+/);
        return ((parts[0]?.[0] ?? '') + (parts[1]?.[0] ?? '')).toUpperCase();
    }

    function logout() {
        router.post('/logout');
    }
</script>

<div class="mx-auto min-h-screen max-w-[1920px]">
    <!-- Mobile top bar -->
    <header class="sticky top-0 z-30 flex items-center justify-between border-b border-slate-200 bg-white px-4 py-3 lg:hidden">
        <div class="flex items-center gap-2.5">
            <Logo size="sm" tone="dark" />
        </div>
        <div class="relative">
            <button
                onclick={(e) => { e.stopPropagation(); topMenuOpen = !topMenuOpen; }}
                class="flex items-center rounded-full transition hover:opacity-80"
            >
                {#if user?.avatar}
                    <img
                        src={user.avatar}
                        alt={user?.name}
                        class="h-9 w-9 rounded-full object-cover ring-2 ring-slate-200"
                    />
                {:else}
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary-100 text-sm font-bold text-primary-700 ring-2 ring-slate-200">
                        {initials(user?.name)}
                    </div>
                {/if}
                <!-- Nama: tablet+ only -->
                <span class="ml-2 hidden max-w-[120px] truncate text-sm font-medium text-slate-700 md:block lg:hidden">{user?.name}</span>
            </button>
            {#if topMenuOpen}
                <div class="absolute right-0 top-full mt-1 w-48 overflow-hidden rounded-lg bg-white shadow-lg ring-1 ring-slate-200">
                    <div class="border-b border-slate-100 px-3 py-2.5">
                        <p class="truncate text-sm font-medium text-slate-800">{user?.name}</p>
                        <p class="truncate text-xs text-slate-500">{user?.email}</p>
                    </div>
                    <button
                        onclick={logout}
                        class="flex w-full items-center gap-2.5 px-3 py-2.5 text-left text-sm font-medium text-red-600 transition hover:bg-red-50"
                    >
                        <Icon name="logout" class="h-4 w-4" />
                        Logout
                    </button>
                </div>
            {/if}
        </div>
    </header>

    <!-- Desktop sidebar: fixed, stays on scroll -->
    <aside class="fixed inset-y-0 left-0 z-30 hidden h-screen flex-col bg-gradient-to-b from-primary-700 via-primary-800 to-primary-900 text-white transition-[width] duration-200 lg:flex {collapsed ? 'w-15' : 'w-56'}">
        <div class="flex h-16 items-center justify-center">
            <button onclick={toggle} class="flex items-center gap-2.5 rounded-lg transition hover:bg-white/10 {collapsed ? 'p-1.5' : 'px-3 py-1.5'}" title={collapsed ? 'Perluas menu' : 'Lipat menu'}>
                <Logo tone="light" size="md" showText={!collapsed} />
            </button>
        </div>

        <nav class="sidebar-dropdown flex flex-1 flex-col overflow-y-auto {collapsed ? 'items-center justify-center gap-3.5 py-3' : 'justify-start space-y-3.5 px-3 pt-4 pb-2'} relative">
            {#each nav as item (item.label)}
                {#if item.children}
                    <!-- Parent with children -->
                    <div class="relative">
                        <button
                            onclick={(e) => {
                                e.stopPropagation();
                                if (collapsed) {
                                    hoveredMenu = hoveredMenu === item ? null : item;
                                    if (hoveredMenu) {
                                        const rect = e.currentTarget.getBoundingClientRect();
                                        menuPosition = { top: rect.top, left: rect.right + 4 };
                                    }
                                } else {
                                    expandedMenus[item.label] = !expandedMenus[item.label];
                                }
                            }}
                            title={item.label}
                            class="flex items-center rounded-lg transition {collapsed ? 'h-11 w-11 justify-center' : 'w-full gap-3 px-3 py-2'} {isChildActive(item) || hoveredMenu === item || (!collapsed && expandedMenus[item.label]) ? 'bg-white/15 font-semibold text-white' : 'text-white/70 hover:bg-white/10 hover:text-white'}"
                        >
                            <Icon name={item.icon} class="shrink-0 {collapsed ? 'h-6 w-6' : 'h-5 w-5'}" />
                            {#if !collapsed}
                                <span class="flex-1 text-left text-sm">{item.label}</span>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4 transition {!collapsed && expandedMenus[item.label] ? 'rotate-180' : ''}"><path d="m6 9 6 6 6-6"/></svg>
                            {/if}
                        </button>
                        {#if !collapsed && expandedMenus[item.label]}
                            <div class="ml-4 mt-1 space-y-0.5 border-l border-white/20 pl-3">
                                {#each item.children as child (child.label)}
                                    <a href={child.href} class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition {isActive(child.href) ? 'bg-white/15 font-semibold text-white' : 'text-white/60 hover:bg-white/10 hover:text-white'}">
                                        <span class="h-1.5 w-1.5 rounded-full {isActive(child.href) ? 'bg-white' : 'bg-white/40'}"></span>
                                        {child.label}
                                    </a>
                                {/each}
                            </div>
                        {/if}
                    </div>
                {:else}
                    <!-- Simple link -->
                    <a
                        href={item.href}
                        title={collapsed ? item.label : undefined}
                        class="flex items-center rounded-lg transition {collapsed ? 'h-11 w-11 justify-center' : 'w-full gap-3 px-3 py-2'} {isActive(item.href) ? 'bg-white/15 font-semibold text-white' : 'text-white/70 hover:bg-white/10 hover:text-white'}"
                    >
                        <Icon name={item.icon} class="shrink-0 {collapsed ? 'h-6 w-6' : 'h-5 w-5'}" />
                        {#if !collapsed}
                            <span class="flex-1 text-left text-sm">{item.label}</span>
                        {/if}
                    </a>
                {/if}
            {/each}
        </nav>

        <div class="border-t border-white/10 px-3 py-3">
            <div class="relative {collapsed ? 'flex justify-center' : ''}">
                <button onclick={(e) => { e.stopPropagation(); userMenuOpen = !userMenuOpen; }} class="flex w-full items-center gap-2.5 rounded-lg p-1.5 transition hover:bg-white/10 {collapsed ? 'justify-center' : ''}" title={user?.name}>
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white/10 text-xs font-bold text-white">{initials(user?.name)}</div>
                    {#if !collapsed}
                        <span class="min-w-0 flex-1 truncate text-left text-sm font-medium">{user?.name}</span>
                    {/if}
                </button>
                {#if userMenuOpen}
                    <div class="absolute bottom-full mb-2 overflow-hidden rounded-lg bg-white shadow-lg {collapsed ? 'left-0 w-40' : 'left-0 w-full'}">
                        <button onclick={logout} class="flex w-full items-center gap-2.5 px-3 py-2.5 text-sm font-medium text-red-600 transition hover:bg-red-50">
                            <Icon name="logout" class="h-4 w-4" />
                            Logout
                        </button>
                    </div>
                {/if}
            </div>
        </div>

        <!-- Portal dropdown for collapsed state -->
        {#if collapsed && hoveredMenu}
            <div
                style="position: fixed; top: {menuPosition.top}px; left: {menuPosition.left}px; z-index: 9999;"
                class="portal-dropdown w-44 overflow-hidden rounded-lg bg-white shadow-lg"
            >
                {#each hoveredMenu.children as child (child.label)}
                    <a href={child.href} class="flex items-center gap-2 px-3 py-2.5 text-sm {isActive(child.href) ? 'bg-primary-50 font-semibold text-primary-700' : 'text-slate-600 hover:bg-slate-50'}">
                        <span class="h-1.5 w-1.5 rounded-full flex-shrink-0 {isActive(child.href) ? 'bg-primary-600' : 'bg-slate-300'}"></span>
                        {child.label}
                    </a>
                {/each}
            </div>
        {/if}
    </aside>

    <!-- Main content -->
    <div class="transition-[padding] duration-200 {collapsed ? 'lg:pl-15' : 'lg:pl-56'}">
        <main class="mx-auto w-full max-w-[1600px] px-4 py-5 pb-24 lg:px-8 lg:py-8 lg:pb-8">
            {@render children?.()}
        </main>
    </div>

    <!-- Mobile bottom nav -->
    <nav class="fixed inset-x-0 bottom-0 z-30 border-t border-slate-200 bg-white lg:hidden">
        <div class="flex justify-around">
            {#each nav as item (item.label)}
                <a href={item.href} class="flex flex-col items-center gap-1 py-2.5 text-[10px] transition {isActive(item.href) ? 'font-semibold text-primary-700' : 'text-slate-500 hover:text-primary-700'}">
                    <Icon name={item.icon} class="h-5 w-5" />
                    <span class="leading-none">{item.label}</span>
                </a>
            {/each}
        </div>
    </nav>
</div>
