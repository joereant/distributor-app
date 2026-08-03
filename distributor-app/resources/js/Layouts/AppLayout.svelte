<script>
    import { Link, router, usePage } from '@inertiajs/svelte';
    import Icon from '../Components/Icon.svelte';
    import Logo from '../Components/Logo.svelte';

    let { children } = $props();

    const page = usePage();
    const currentUrl = $derived(page.url);
    const user = $derived(page.props.auth?.user);
    const role = $derived(user?.role ?? 'customer');

    let mobileDrawerOpen = $state(false);

    // Sidebar auto-collapse: expand ≥ 1280 (xl), collapse < 1280
    // Manual toggle → stored in localStorage, overrides breakpoint default
    function getBreakpointDefault() {
        return window.innerWidth < 1280;
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
    let activeBottomPopover = $state(null);
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

    // Close bottom nav popover on click outside
    $effect(() => {
        if (activeBottomPopover === null) return;
        function handleClick(e) {
            if (!e.target.closest('.bottom-nav-popover')) {
                activeBottomPopover = null;
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
                { label: 'Daftar Produk', href: '/admin/products', icon: 'box' },
                { label: 'Kategori', href: '/admin/products/categories', icon: 'category' },
                { label: 'Daftar Harga', href: '/admin/products/prices', icon: 'tag' },
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
                { label: 'Daftar Produk', href: '/admin/products', icon: 'box' },
                { label: 'Kategori', href: '/admin/products/categories', icon: 'category' },
                { label: 'Daftar Harga', href: '/admin/products/prices', icon: 'tag' },
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

    // Items to show in mobile bottom nav bar (max 4 + 1 "Menu" button)
    const mobileBottomItems = $derived(nav.slice(0, 4));

    function getItemHref(item) {
        if (item.href) return item.href;
        if (item.children && item.children.length > 0) return item.children[0].href;
        return '#';
    }

    function toggle() {
        collapsed = !collapsed;
        userToggled = true;
        localStorage.setItem('sentrax_sidebar_toggled', 'true');
        localStorage.setItem('sentrax_sidebar_collapsed', String(collapsed));
    }

    function getAllNavHrefs() {
        const hrefs = [];
        for (const item of nav) {
            if (item.href) hrefs.push(item.href.split('?')[0]);
            if (item.children) {
                for (const child of item.children) {
                    if (child.href) hrefs.push(child.href.split('?')[0]);
                }
            }
        }
        return hrefs;
    }

    function isActive(href) {
        if (!href) return false;
        const [currentPath, currentQuery] = (currentUrl || '').split('?');
        const [targetPath, targetQuery] = href.split('?');

        // 1. Target with query params (e.g. /admin/products?tab=prices)
        if (targetQuery) {
            return currentPath === targetPath && (currentQuery || '').includes(targetQuery);
        }

        // 2. Current URL has tab parameter, don't active base route
        if (currentQuery && currentQuery.includes('tab=')) {
            return false;
        }

        // 3. Exact path match
        if (currentPath === targetPath) {
            return true;
        }

        // 4. Base root routes exact match
        const baseRoutes = ['/', '/admin', '/sales', '/customer', '/owner'];
        if (baseRoutes.includes(targetPath)) {
            return false;
        }

        // 5. Nested child route match (e.g. /admin/products/create)
        if (currentPath.startsWith(targetPath + '/')) {
            const allHrefs = getAllNavHrefs();
            const hasBetterMatch = allHrefs.some(otherHref =>
                otherHref !== targetPath &&
                otherHref.length > targetPath.length &&
                (currentPath === otherHref || currentPath.startsWith(otherHref + '/'))
            );

            if (hasBetterMatch) {
                return false;
            }

            return true;
        }

        return false;
    }

    function isChildActive(item) {
        if (!item.children) return false;
        return item.children.some(c => isActive(c.href));
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
        <div class="flex items-center gap-3">
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

    <!-- Mobile Drawer Overlay -->
    {#if mobileDrawerOpen}
        <div
            class="fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-sm transition-opacity lg:hidden"
            onclick={() => (mobileDrawerOpen = false)}
            aria-hidden="true"
        ></div>

        <aside class="fixed inset-y-0 left-0 z-50 flex w-72 flex-col bg-slate-900 text-white shadow-2xl transition-transform duration-300 lg:hidden">
            <div class="flex h-16 items-center justify-between border-b border-white/10 px-4">
                <Logo tone="light" size="md" showText={true} />
                <button
                    onclick={() => (mobileDrawerOpen = false)}
                    class="rounded-lg p-1.5 text-slate-400 hover:bg-white/10 hover:text-white"
                    aria-label="Tutup menu"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <nav class="flex-1 overflow-y-auto space-y-1 px-3 py-4">
                {#each nav as item (item.label)}
                    {#if item.children}
                        <div class="space-y-1 transition-all duration-200 {expandedMenus[item.label] || isChildActive(item) ? 'bg-white rounded-xl p-1 shadow-md text-slate-800' : ''}">
                            <button
                                onclick={() => (expandedMenus[item.label] = !expandedMenus[item.label])}
                                class="flex w-full items-center justify-between rounded-lg px-3 py-2.5 text-sm font-medium transition {expandedMenus[item.label] || isChildActive(item) ? 'text-slate-800 font-semibold hover:bg-slate-100' : 'text-slate-300 hover:bg-white/10 hover:text-white'}"
                            >
                                <div class="flex items-center gap-3">
                                    <Icon name={item.icon} class="h-5 w-5 shrink-0 {expandedMenus[item.label] || isChildActive(item) ? 'text-primary-700' : ''}" />
                                    <span>{item.label}</span>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4 transition {expandedMenus[item.label] ? 'rotate-180' : ''} {expandedMenus[item.label] || isChildActive(item) ? 'text-slate-500' : ''}"><path d="m6 9 6 6 6-6"/></svg>
                            </button>
                            {#if expandedMenus[item.label]}
                                <div class="mt-1 space-y-0.5 w-full">
                                    {#each item.children as child (child.label)}
                                        <Link
                                            href={child.href}
                                            onclick={() => (mobileDrawerOpen = false)}
                                            class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition {isActive(child.href) ? 'bg-primary-600 font-semibold text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 font-medium'}"
                                        >
                                            <Icon name={child.icon ?? 'box'} class="h-4 w-4 shrink-0 {isActive(child.href) ? 'text-white' : 'text-slate-400'}" />
                                            <span>{child.label}</span>
                                        </Link>
                                    {/each}
                                </div>
                            {/if}
                        </div>
                    {:else}
                        <Link
                            href={item.href}
                            onclick={() => (mobileDrawerOpen = false)}
                            class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {isActive(item.href) ? 'bg-white font-semibold text-primary-700 shadow-sm' : 'text-slate-300 hover:bg-white/10 hover:text-white'}"
                        >
                            <Icon name={item.icon} class="h-5 w-5 shrink-0 {isActive(item.href) ? 'text-primary-700' : ''}" />
                            <span>{item.label}</span>
                        </Link>
                    {/if}
                {/each}
            </nav>

            <div class="border-t border-white/10 p-3">
                <button
                    onclick={logout}
                    class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2.5 text-sm font-medium text-red-400 hover:bg-red-500/10"
                >
                    <Icon name="logout" class="h-4 w-4" />
                    Logout
                </button>
            </div>
        </aside>
    {/if}

    <!-- Desktop sidebar: fixed, stays on scroll -->
    <aside class="fixed inset-y-0 left-0 z-30 hidden h-screen flex-col bg-gradient-to-b from-primary-700 via-primary-800 to-primary-900 text-white transition-[width] duration-200 lg:flex {collapsed ? 'w-16' : 'w-56'}">
        <div class="flex h-16 items-center justify-center">
            <button onclick={toggle} class="flex items-center gap-2.5 rounded-lg transition hover:bg-white/10 {collapsed ? 'p-1.5' : 'px-3 py-1.5'}" title={collapsed ? 'Perluas menu' : 'Lipat menu'}>
                <Logo tone="light" size="md" showText={!collapsed} />
            </button>
        </div>

        <nav class="sidebar-dropdown flex flex-1 flex-col overflow-y-auto {collapsed ? 'items-center justify-start space-y-3.5 py-4' : 'justify-start space-y-3.5 px-3 pt-4 pb-2'} relative">
            {#each nav as item (item.label)}
                {#if item.children}
                    <!-- Parent with children -->
                    <div class="relative w-full flex flex-col transition-all duration-200 {collapsed ? 'items-center' : (!collapsed && (expandedMenus[item.label] || isChildActive(item)) ? 'bg-white rounded-xl p-1 shadow-md text-slate-800' : '')}">
                        <button
                            onclick={(e) => {
                                e.stopPropagation();
                                if (collapsed) {
                                    hoveredMenu = hoveredMenu === item ? null : item;
                                    if (hoveredMenu) {
                                        const rect = e.currentTarget.getBoundingClientRect();
                                        menuPosition = { top: rect.top, left: rect.right + 8 };
                                    }
                                } else {
                                    expandedMenus[item.label] = !expandedMenus[item.label];
                                }
                            }}
                            title={item.label}
                            class="flex items-center rounded-lg transition {collapsed ? 'h-11 w-11 justify-center' : 'w-full gap-3 px-3 py-2'} {collapsed && isChildActive(item) ? 'bg-white font-semibold text-primary-700 shadow-sm' : (!collapsed && (expandedMenus[item.label] || isChildActive(item))) ? 'text-slate-800 font-semibold hover:bg-slate-100' : 'text-white/70 hover:bg-white/10 hover:text-white'}"
                        >
                            <Icon name={item.icon} class="shrink-0 {collapsed ? 'h-6 w-6' : 'h-5 w-5'} {(!collapsed && (expandedMenus[item.label] || isChildActive(item))) || (collapsed && isChildActive(item)) ? 'text-primary-700' : ''}" />
                            {#if !collapsed}
                                <span class="flex-1 text-left text-sm">{item.label}</span>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4 transition {!collapsed && expandedMenus[item.label] ? 'rotate-180' : ''} {expandedMenus[item.label] || isChildActive(item) ? 'text-slate-500' : ''}"><path d="m6 9 6 6 6-6"/></svg>
                            {/if}
                        </button>
                        {#if !collapsed && expandedMenus[item.label]}
                            <div class="mt-1 space-y-0.5 w-full">
                                {#each item.children as child (child.label)}
                                    <Link href={child.href} class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition {isActive(child.href) ? 'bg-primary-600 font-semibold text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 font-medium'}">
                                        <Icon name={child.icon ?? 'box'} class="h-4 w-4 shrink-0 {isActive(child.href) ? 'text-white' : 'text-slate-400'}" />
                                        <span>{child.label}</span>
                                    </Link>
                                {/each}
                            </div>
                        {/if}
                    </div>
                {:else}
                    <!-- Simple link -->
                    <div class="w-full flex justify-center">
                        <Link
                            href={item.href}
                            title={collapsed ? item.label : undefined}
                            class="flex items-center rounded-lg transition {collapsed ? 'h-11 w-11 justify-center' : 'w-full gap-3 px-3 py-2'} {isActive(item.href) ? 'bg-white font-semibold text-primary-700 shadow-sm' : 'text-white/70 hover:bg-white/10 hover:text-white'}"
                        >
                            <Icon name={item.icon} class="shrink-0 {collapsed ? 'h-6 w-6' : 'h-5 w-5'} {isActive(item.href) ? 'text-primary-700' : ''}" />
                            {#if !collapsed}
                                <span class="flex-1 text-left text-sm">{item.label}</span>
                            {/if}
                        </Link>
                    </div>
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
                    <div class="absolute overflow-hidden rounded-xl bg-white p-1.5 shadow-2xl ring-1 ring-slate-900/10 border border-slate-100 z-50 {collapsed ? 'left-full ml-3 bottom-0 w-44' : 'bottom-full mb-2 left-0 w-full'}">
                        <button onclick={logout} class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-50">
                            <Icon name="logout" class="h-4 w-4 text-red-500 shrink-0" />
                            <span>Logout</span>
                        </button>
                    </div>
                {/if}
            </div>
        </div>

        <!-- Portal dropdown for collapsed state -->
        {#if collapsed && hoveredMenu}
            <div
                style="position: fixed; top: {menuPosition.top}px; left: {menuPosition.left}px; z-index: 9999;"
                class="portal-dropdown w-48 overflow-hidden rounded-xl bg-white p-1.5 shadow-xl ring-1 ring-slate-900/10 border border-slate-100 text-slate-800"
            >
                <div class="space-y-0.5">
                    {#each hoveredMenu.children as child (child.label)}
                        <Link
                            href={child.href}
                            onclick={() => (hoveredMenu = null)}
                            class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm transition {isActive(child.href) ? 'bg-primary-700 font-semibold text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 font-medium'}"
                        >
                            <Icon name={child.icon ?? 'box'} class="h-4 w-4 shrink-0 {isActive(child.href) ? 'text-white' : 'text-slate-400'}" />
                            <span>{child.label}</span>
                        </Link>
                    {/each}
                </div>
            </div>
        {/if}
    </aside>

    <!-- Main content -->
    <div class="transition-[padding] duration-200 {collapsed ? 'lg:pl-16' : 'lg:pl-56'}">
        <main class="mx-auto w-full max-w-[1600px] px-4 py-5 pb-24 lg:px-8 lg:py-8 lg:pb-8">
            {@render children?.()}
        </main>
    </div>

    <!-- Mobile bottom nav -->
    <nav class="fixed inset-x-0 bottom-0 z-30 border-t border-slate-200 bg-white lg:hidden">
        <div class="flex justify-around items-center">
            {#each mobileBottomItems as item (item.label)}
                {#if item.children}
                    <div class="relative flex flex-col items-center bottom-nav-popover">
                        <button
                            onclick={(e) => {
                                e.stopPropagation();
                                activeBottomPopover = activeBottomPopover === item.label ? null : item.label;
                            }}
                            class="flex flex-col items-center gap-1 py-2 text-[10px] transition {isActive(getItemHref(item)) || isChildActive(item) ? 'font-semibold text-primary-700' : 'text-slate-500 hover:text-primary-700'}"
                        >
                            <Icon name={item.icon} class="h-5 w-5" />
                            <span class="leading-none">{item.label}</span>
                        </button>

                        {#if activeBottomPopover === item.label}
                            <div class="absolute bottom-full mb-3 left-1/2 -translate-x-1/2 w-48 overflow-hidden rounded-xl bg-white p-1.5 shadow-2xl ring-1 ring-slate-900/10 border border-slate-100 z-50">
                                <div class="space-y-0.5">
                                    {#each item.children as child (child.label)}
                                        <Link
                                            href={child.href}
                                            onclick={() => (activeBottomPopover = null)}
                                            class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-xs transition {isActive(child.href) ? 'bg-primary-700 font-semibold text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 font-medium'}"
                                        >
                                            <Icon name={child.icon ?? 'box'} class="h-4 w-4 shrink-0 {isActive(child.href) ? 'text-white' : 'text-slate-400'}" />
                                            <span>{child.label}</span>
                                        </Link>
                                    {/each}
                                </div>
                            </div>
                        {/if}
                    </div>
                {:else}
                    <Link
                        href={item.href}
                        class="flex flex-col items-center gap-1 py-2 text-[10px] transition {isActive(item.href) ? 'font-semibold text-primary-700' : 'text-slate-500 hover:text-primary-700'}"
                    >
                        <Icon name={item.icon} class="h-5 w-5" />
                        <span class="leading-none">{item.label}</span>
                    </Link>
                {/if}
            {/each}

            {#if nav.length > 4}
                <button
                    onclick={() => (mobileDrawerOpen = true)}
                    class="flex flex-col items-center gap-1 py-2 text-[10px] text-slate-500 hover:text-primary-700 transition"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                    </svg>
                    <span class="leading-none">Lainnya</span>
                </button>
            {/if}
        </div>
    </nav>
</div>

