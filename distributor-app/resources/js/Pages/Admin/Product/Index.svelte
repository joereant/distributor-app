<script>
    import { router, usePage } from '@inertiajs/svelte';
    import AppLayout from '../../../Layouts/AppLayout.svelte';
    import SidePanel from '../../../Components/SidePanel.svelte';
    import ProductForm from '../../../Components/ProductForm.svelte';

    const page = usePage();
    const flash = $derived(page.props.flash?.message);

    // ── Products ──────────────────────────────
    const products = $derived(page.props.products?.data ?? []);
    const productCategories = $derived(page.props.categories ?? []);
    const productFilters = $derived(page.props.filters ?? {});
    const productsMeta = $derived(page.props.products);

    let search = $state(productFilters.search ?? '');
    let selectedCategory = $state(productFilters.category ?? '');
    let selectedPackaging = $state(productFilters.packaging ?? '');

    // ── Side Panel ───────────────────────────────
    let sidePanel = $state({ open: false, mode: 'create', product: null });

    function openCreate() {
        sidePanel = { open: true, mode: 'create', product: null };
    }

    function openEdit(product) {
        sidePanel = { open: true, mode: 'edit', product };
    }

    function closePanel() {
        sidePanel = { ...sidePanel, open: false };
    }

    // ── Helpers & Metrics ──────────────────────────────────
    function formatRp(value) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value ?? 0);
    }

    function calcMargin(price, hargaBeli) {
        if (!hargaBeli || hargaBeli <= 0) return 0;
        return (((price - hargaBeli) / hargaBeli) * 100).toFixed(1);
    }

    function applyProductFilter() {
        router.get('/admin/products', { 
            search: search || undefined, 
            category: selectedCategory || undefined,
            packaging: selectedPackaging || undefined 
        });
    }

    // Summary Metrics
    const totalCount = $derived(productsMeta?.total ?? products.length);
    const activeCount = $derived(products.filter(p => p.is_active).length);
    const avgMarginPct = $derived(() => {
        if (products.length === 0) return 0;
        const total = products.reduce((acc, p) => acc + parseFloat(calcMargin(p.price, p.harga_beli)), 0);
        return (total / products.length).toFixed(1);
    });

    const packagingLabel = { zak: 'Zak', ton_bag: 'Ton Bag', bulk: 'Curah' };
    const packagingStyle = {
        zak: 'bg-teal-50 text-teal-700 ring-teal-200/80',
        ton_bag: 'bg-amber-50 text-amber-700 ring-amber-200/80',
        bulk: 'bg-slate-100 text-slate-700 ring-slate-200/80',
    };

    let showFlash = $state(true);

    $effect(() => {
        if (flash) {
            showFlash = true;
            const timer = setTimeout(() => {
                showFlash = false;
            }, 3500);
            return () => clearTimeout(timer);
        }
    });
</script>

<AppLayout>
    <div class="space-y-6">
        <!-- Flash Message -->
        {#if flash && showFlash}
            <div class="flex items-center justify-between gap-3 rounded-2xl bg-emerald-50/95 p-4 text-sm font-medium text-emerald-800 shadow-md ring-1 ring-emerald-200/80 backdrop-blur-xs transition-all duration-300 animate-in fade-in slide-in-from-top-2">
                <div class="flex items-center gap-3">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-emerald-500 text-white shadow-xs">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="h-4 w-4"><path d="M20 6 9 17l-5-5"/></svg>
                    </span>
                    <p class="flex-1">{flash}</p>
                </div>
                <button
                    onclick={() => showFlash = false}
                    class="rounded-lg p-1 text-emerald-600 hover:bg-emerald-100 hover:text-emerald-900 transition-colors"
                    title="Tutup notifikasi"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
        {/if}

        <!-- 1. Executive Header & Action Bar -->
        <header class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900">Katalog Produk</h1>
                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-600 ring-1 ring-slate-200/80">{totalCount} item</span>
                </div>
                <p class="mt-1 text-sm text-slate-500">Kelola spesifikasi produk, harga acuan, dan margin penjualan distributor SentraX.</p>
            </div>
            <div class="flex items-center gap-3">
                <button onclick={openCreate} class="inline-flex items-center gap-2 rounded-xl bg-teal-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:bg-teal-700 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 active:scale-[0.98]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" class="h-4 w-4"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                    Tambah Produk Baru
                </button>
            </div>
        </header>

        <!-- 2. Executive Summary Metric Strip -->
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 sm:gap-4">
            <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-slate-500">Total Katalog</span>
                    <span class="rounded-lg bg-teal-50 p-2 text-teal-600">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
                    </span>
                </div>
                <p class="mt-2 text-2xl font-bold tracking-tight text-slate-900">{totalCount}</p>
                <span class="text-[11px] font-medium text-slate-400">Varian produk terdaftar</span>
            </div>

            <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-slate-500">Status Aktif</span>
                    <span class="rounded-lg bg-emerald-50 p-2 text-emerald-600">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    </span>
                </div>
                <p class="mt-2 text-2xl font-bold tracking-tight text-slate-900">{activeCount}</p>
                <span class="text-[11px] font-medium text-emerald-600">Siap dipesan customer</span>
            </div>

            <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-slate-500">Kategori</span>
                    <span class="rounded-lg bg-indigo-50 p-2 text-indigo-600">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/></svg>
                    </span>
                </div>
                <p class="mt-2 text-2xl font-bold tracking-tight text-slate-900">{productCategories.length}</p>
                <span class="text-[11px] font-medium text-slate-400">Grup klasifikasi produk</span>
            </div>

            <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-slate-500">Rata-rata Margin</span>
                    <span class="rounded-lg bg-amber-50 p-2 text-amber-600">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg>
                    </span>
                </div>
                <p class="mt-2 text-2xl font-bold tracking-tight text-emerald-600">+{avgMarginPct()}%</p>
                <span class="text-[11px] font-medium text-slate-400">Estimasi profit kotor</span>
            </div>
        </div>

        <!-- 3. Toolbar Control Center (Search + Quick Filter Pills) -->
        <div class="flex flex-col gap-3 rounded-2xl border border-slate-200/80 bg-white p-3.5 shadow-xs sm:flex-row sm:items-center sm:justify-between">
            <div class="relative flex-1">
                <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                </span>
                <input
                    bind:value={search}
                    type="search"
                    placeholder="Cari nama produk, SKU/kode barang..."
                    onkeydown={(e) => e.key === 'Enter' && applyProductFilter()}
                    class="w-full rounded-xl border border-slate-200 bg-slate-50/50 py-2 pl-10 pr-12 text-sm text-slate-800 transition placeholder:text-slate-400 focus:border-teal-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-100"
                />
                <span class="pointer-events-none absolute right-3 top-1/2 hidden -translate-y-1/2 rounded-md border border-slate-200 bg-white px-1.5 py-0.5 text-[10px] font-semibold text-slate-400 sm:inline-block">↵ Enter</span>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <!-- Filter Kategori -->
                <select
                    bind:value={selectedCategory}
                    onchange={applyProductFilter}
                    class="rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-2 text-sm text-slate-700 transition focus:border-teal-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-100"
                >
                    <option value="">Semua Kategori</option>
                    {#each productCategories as c (c.id)}
                        <option value={c.id}>{c.name}</option>
                    {/each}
                </select>

                <!-- Filter Kemasan -->
                <select
                    bind:value={selectedPackaging}
                    onchange={applyProductFilter}
                    class="rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-2 text-sm text-slate-700 transition focus:border-teal-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-100"
                >
                    <option value="">Semua Kemasan</option>
                    <option value="zak">Zak (Kantong)</option>
                    <option value="ton_bag">Ton Bag</option>
                    <option value="bulk">Curah (Bulk)</option>
                </select>

                <button onclick={applyProductFilter} class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800 active:scale-95">
                    Filter
                </button>

                {#if productFilters.search || productFilters.category || productFilters.packaging}
                    <button
                        onclick={() => { search = ''; selectedCategory = ''; selectedPackaging = ''; router.get('/admin/products'); }}
                        class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-50 hover:text-slate-900"
                    >
                        Reset Filter
                    </button>
                {/if}
            </div>
        </div>

        <!-- 4. Data Table Grid Pro -->
        <div class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/60 text-xs font-semibold uppercase tracking-wider text-slate-500">
                            <th class="px-6 py-4">Item & Detail</th>
                            <th class="px-5 py-4">Kategori</th>
                            <th class="px-5 py-4">Kemasan & Satuan</th>
                            <th class="px-5 py-4 text-right">Harga Beli (Modal)</th>
                            <th class="px-5 py-4 text-right">Harga Jual Acuan</th>
                            <th class="px-5 py-4 text-center">Margin (%)</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        {#each products as p (p.id)}
                            {@const margin = calcMargin(p.price, p.harga_beli)}
                            <tr class="group transition-all hover:bg-teal-50/20 {!p.is_active ? 'opacity-55 bg-slate-50/40' : ''}">
                                <!-- Product Name & Code -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-slate-100 to-slate-200 font-bold text-slate-700 ring-1 ring-slate-200/60 group-hover:from-teal-100 group-hover:to-teal-200 group-hover:text-teal-800">
                                            {p.name.charAt(0)}
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <p class="font-bold text-slate-900 group-hover:text-teal-700">{p.name}</p>
                                                {#if !p.is_active}
                                                    <span class="rounded-md bg-rose-50 px-1.5 py-0.5 text-[10px] font-semibold text-rose-600 ring-1 ring-rose-200/60">Nonaktif</span>
                                                {/if}
                                            </div>
                                            <p class="mt-0.5 font-mono text-xs text-slate-400">{p.code ?? 'SKU-UNSET'}</p>
                                        </div>
                                    </div>
                                </td>

                                <!-- Category -->
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">
                                        {p.category?.name ?? 'Tanpa Kategori'}
                                    </span>
                                </td>

                                <!-- Packaging & Unit -->
                                <td class="px-5 py-4">
                                    <div class="flex flex-col items-start gap-0.5">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 {packagingStyle[p.packaging_type] ?? packagingStyle.zak}">
                                            {packagingLabel[p.packaging_type] ?? p.packaging_type}
                                        </span>
                                        <span class="text-[11px] text-slate-400">per {p.unit}</span>
                                    </div>
                                </td>

                                <!-- Cost Price -->
                                <td class="px-5 py-4 text-right font-medium text-slate-500">
                                    {formatRp(p.harga_beli)}
                                </td>

                                <!-- Selling Price -->
                                <td class="px-5 py-4 text-right font-bold text-slate-900">
                                    {formatRp(p.price)}
                                </td>

                                <!-- Margin Percentage Badge -->
                                <td class="px-5 py-4 text-center">
                                    {#if margin > 0}
                                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-bold text-emerald-700 ring-1 ring-emerald-600/20">
                                            +{margin}%
                                        </span>
                                    {:else if margin == 0}
                                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600 ring-1 ring-slate-200">
                                            0%
                                        </span>
                                    {:else}
                                        <span class="inline-flex items-center rounded-full bg-rose-50 px-2.5 py-0.5 text-xs font-bold text-rose-700 ring-1 ring-rose-600/20">
                                            {margin}%
                                        </span>
                                    {/if}
                                </td>

                                <!-- Action Buttons -->
                                <td class="px-6 py-4 text-right">
                                    <button
                                        onclick={() => openEdit(p)}
                                        class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-2xs transition hover:border-teal-500 hover:bg-teal-50 hover:text-teal-700"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-3.5 w-3.5"><path d="M12 20h9"/><path d="16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                        Edit
                                    </button>
                                </td>
                            </tr>
                        {/each}
                    </tbody>
                </table>
            </div>

            <!-- Empty State Illustrative -->
            {#if products.length === 0}
                <div class="flex flex-col items-center justify-center py-16 text-center">
                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-teal-50 text-teal-600 ring-8 ring-teal-50/50">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-8 w-8"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
                    </div>
                    <h3 class="mt-4 text-base font-bold text-slate-900">Belum ada data produk</h3>
                    <p class="mt-1 max-w-sm text-sm text-slate-500">Tidak ada produk yang cocok dengan pencarian/filter aktif. Silakan reset filter atau tambahkan produk baru.</p>
                    <div class="mt-5 flex items-center gap-3">
                        <button onclick={openCreate} class="rounded-xl bg-teal-600 px-4 py-2 text-sm font-semibold text-white shadow-xs transition hover:bg-teal-700">
                            + Tambah Produk Pertama
                        </button>
                    </div>
                </div>
            {/if}

            <!-- 5. Pro Pagination Bar -->
            {#if productsMeta?.last_page > 1}
                <div class="flex flex-col justify-between gap-3 border-t border-slate-100 bg-slate-50/50 px-6 py-3.5 sm:flex-row sm:items-center">
                    <p class="text-xs font-medium text-slate-500">
                        Menampilkan <span class="font-bold text-slate-800">{productsMeta.from}</span>–<span class="font-bold text-slate-800">{productsMeta.to}</span> dari <span class="font-bold text-slate-800">{productsMeta.total}</span> item produk
                    </p>
                    <div class="flex items-center gap-1">
                        {#each productsMeta.links as link (link.label)}
                            <button
                                onclick={() => link.url && router.get(link.url)}
                                class="rounded-lg px-3 py-1.5 text-xs font-semibold transition-all {link.active ? 'bg-teal-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-200/60'}"
                                disabled={!link.url}
                            >
                                {@html link.label}
                            </button>
                        {/each}
                    </div>
                </div>
            {/if}
        </div>
    </div>

    <!-- ── Contextual Side Panel ─────────────────────────── -->
    <SidePanel
        open={sidePanel.open}
        title={sidePanel.mode === 'create' ? 'Tambah Produk Baru' : 'Edit Detail Produk'}
        subtitle={sidePanel.mode === 'edit' ? sidePanel.product?.name : 'Masukkan rincian spesifikasi & harga barang'}
        size="md"
        onclose={closePanel}
    >
        <ProductForm
            product={sidePanel.product}
            categories={productCategories}
            onclose={closePanel}
        />
    </SidePanel>
</AppLayout>

