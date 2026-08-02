<script>
    import { router, usePage } from '@inertiajs/svelte';
    import Icon from '../Components/Icon.svelte';

    let { children } = $props();

    const page = usePage();
    const user = $derived(page.props.auth?.user);
    const role = $derived(user?.role ?? 'customer');

    let collapsed = $state(localStorage.getItem('da_sidebar_collapsed') !== 'false');

    const navByRole = {
        owner: [
            { label: 'Dashboard', href: '/owner', icon: 'home' },
            { label: 'User', href: null, icon: 'users' },
            { label: 'Order', href: null, icon: 'cart' },
            { label: 'Laporan', href: null, icon: 'chart' },
        ],
        admin: [
            { label: 'Dashboard', href: '/admin', icon: 'home' },
            { label: 'User', href: null, icon: 'users' },
            { label: 'Order', href: null, icon: 'cart' },
            { label: 'Laporan', href: null, icon: 'chart' },
        ],
        sales: [
            { label: 'Dashboard', href: '/sales', icon: 'home' },
            { label: 'Transaksi', href: null, icon: 'monitor' },
            { label: 'Referal', href: null, icon: 'referral' },
        ],
        customer: [
            { label: 'Dashboard', href: '/customer', icon: 'home' },
            { label: 'Order', href: null, icon: 'cart' },
            { label: 'Riwayat', href: null, icon: 'history' },
        ],
    };

    const nav = $derived(navByRole[role] ?? navByRole.customer);

    function toggle() {
        collapsed = !collapsed;
        localStorage.setItem('da_sidebar_collapsed', String(collapsed));
    }

    function isActive(href) {
        return href && (page.url === href || page.url.startsWith(`${href}/`));
    }

    function logout() {
        router.post('/logout');
    }
</script>

<div class="min-h-screen">
    <!-- Mobile top bar -->
    <header class="sticky top-0 z-30 flex items-center justify-between border-b border-slate-200 bg-white px-4 py-3 lg:hidden">
        <div class="flex items-center gap-2.5">
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary-700 font-extrabold text-white shadow">DA</div>
            <span class="font-bold text-slate-800">DistributorApp</span>
        </div>
        <div class="flex items-center gap-3">
            <span class="max-w-[140px] truncate text-sm text-slate-600">{user?.name}</span>
            <button onclick={logout} class="text-sm font-medium text-red-600 hover:text-red-700">Keluar</button>
        </div>
    </header>

    <!-- Desktop sidebar -->
    <aside class="fixed inset-y-0 left-0 z-30 hidden flex-col bg-gradient-to-b from-primary-700 via-primary-800 to-primary-900 text-white transition-[width] duration-200 lg:flex {collapsed ? 'w-20' : 'w-64'}">
        <div class="flex h-16 items-center {collapsed ? 'justify-center px-0' : 'justify-between px-3'}">
            <button onclick={toggle} class="flex items-center gap-2.5 rounded-lg transition hover:bg-white/10 {collapsed ? 'p-1.5' : 'px-2 py-1.5'}" title={collapsed ? 'Perluas menu' : 'Lipat menu'}>
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white font-extrabold text-primary-700 shadow">DA</div>
                {#if !collapsed}
                    <div class="text-left">
                        <p class="text-sm font-bold leading-tight">DistributorApp</p>
                        <p class="text-[11px] text-white/50">Panel {role}</p>
                    </div>
                {/if}
            </button>
        </div>

        <nav class="flex flex-1 flex-col overflow-y-auto {collapsed ? 'justify-center gap-8 py-2' : 'justify-start space-y-1 px-3 py-2'}">
            {#each nav as item (item.label)}
                {#if item.href}
                    <a
                        href={item.href}
                        title={collapsed ? item.label : undefined}
                        class="flex w-full items-center rounded-lg transition {collapsed ? 'justify-center px-0 py-1' : 'gap-3 px-3 py-2'} {isActive(item.href) ? 'bg-white/15 font-semibold text-white' : 'text-white/70 hover:bg-white/10 hover:text-white'}"
                    >
                        <Icon name={item.icon} class="shrink-0 {collapsed ? 'h-7 w-7' : 'h-5 w-5'}" />
                        {#if !collapsed}
                            <span class="flex-1 text-left text-sm">{item.label}</span>
                            <span class="rounded-full bg-white/10 px-2 py-0.5 text-[10px] font-normal text-white/50">Segera</span>
                        {/if}
                    </a>
                {:else}
                    <button type="button" disabled title={item.label} class="flex w-full cursor-not-allowed items-center rounded-lg text-white/40 {collapsed ? 'justify-center px-0 py-1' : 'gap-3 px-3 py-2'}">
                        <Icon name={item.icon} class="shrink-0 {collapsed ? 'h-7 w-7' : 'h-5 w-5'}" />
                        {#if !collapsed}
                            <span class="flex-1 text-left text-sm">{item.label}</span>
                            <span class="rounded-full bg-white/10 px-2 py-0.5 text-[10px] font-normal text-white/50">Segera</span>
                        {/if}
                    </button>
                {/if}
            {/each}
        </nav>

        <div class="border-t border-white/10 px-3 py-3 {collapsed ? 'text-center' : ''}">
            {#if collapsed}
                <button onclick={logout} class="rounded-lg p-2 text-white/60 transition hover:bg-white/10 hover:text-white" title="Keluar">
                    <Icon name="logout" class="h-6 w-6" />
                </button>
            {:else}
                <p class="truncate text-sm font-medium">{user?.name}</p>
                <button onclick={logout} class="mt-1 text-xs text-white/60 hover:text-white">Keluar</button>
            {/if}
        </div>
    </aside>

    <!-- Main -->
    <div class="transition-[padding] duration-200 {collapsed ? 'lg:pl-20' : 'lg:pl-64'}">
        <main class="px-4 py-5 pb-24 lg:px-8 lg:py-8 lg:pb-8">
            {@render children?.()}
        </main>
    </div>

    <!-- Mobile bottom nav -->
    <nav class="fixed inset-x-0 bottom-0 z-30 border-t border-slate-200 bg-white lg:hidden">
        <div class="flex justify-around">
            {#each nav as item (item.label)}
                {#if item.href}
                    <a href={item.href} class="flex flex-col items-center gap-1 py-2.5 text-[10px] transition {isActive(item.href) ? 'font-semibold text-primary-700' : 'text-slate-500 hover:text-primary-700'}">
                        <Icon name={item.icon} class="h-5 w-5" />
                        <span class="leading-none">{item.label}</span>
                    </a>
                {:else}
                    <button type="button" disabled class="flex flex-col items-center gap-1 py-2.5 text-[10px] text-slate-400">
                        <Icon name={item.icon} class="h-5 w-5" />
                        <span class="leading-none">{item.label}</span>
                    </button>
                {/if}
            {/each}
        </div>
    </nav>
</div>
