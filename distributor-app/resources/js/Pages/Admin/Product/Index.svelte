<script>
    import { router, usePage } from '@inertiajs/svelte';
    import AppLayout from '../../../Layouts/AppLayout.svelte';
    import SidePanel from '../../../Components/SidePanel.svelte';
    import ProductForm from '../../../Components/ProductForm.svelte';

    const page = usePage();
    const flash = $derived(page.props.flash?.message);
    const tab = $derived(page.props.tab ?? 'products');

    // ── Tab: Products ──────────────────────────────
    const products = $derived(page.props.products?.data ?? []);
    const productCategories = $derived(page.props.categories ?? []);
    const productFilters = $derived(page.props.filters ?? {});
    const productsMeta = $derived(page.props.products);

    let search = $state(productFilters.search ?? '');
    let selectedCategory = $state(productFilters.category ?? '');

    // ── Tab: Prices ───────────────────────────────
    const plants = $derived(page.props.plants ?? []);
    const selectedPlant = $derived(page.props.selected_plant);
    const areas = $derived(page.props.areas ?? []);
    const groupedProducts = $derived(page.props.grouped_products ?? {});
    const priceMap = $derived(page.props.prices ?? {});

    // ── Tab: Kategori ──────────────────────────────
    let catSearch = $state('');

    function openCategoryCreate() {
        const name = prompt('Nama Kategori baru:');
        if (name?.trim()) {
            router.post('/admin/products/categories', { name: name.trim() });
        }
    }

    function toggleCategory(id) {
        router.post(`/admin/products/categories/${id}/toggle`);
    }

    function deleteCategory(id) {
        if (confirm('Hapus kategori ini?')) {
            router.delete(`/admin/products/categories/${id}`);
        }
    }

    function applyCatFilter() {
        router.get('/admin/products', { tab: 'categories', search: catSearch || undefined });
    }

    // Price form state: { [product_id]: { [area_id]: value } }
    let priceForm = $state({});

    function getPrice(productId, areaId) {
        const key = `${productId}_${areaId}`;
        const p = priceMap[key];
        return p ? String(p.price ?? '') : '';
    }

    function initPriceForm() {
        const form = {};
        for (const [catName, prods] of Object.entries(groupedProducts)) {
            for (const p of prods) {
                form[p.id] = {};
                for (const a of areas) {
                    form[p.id][a.id] = getPrice(p.id, a.id);
                }
            }
        }
        priceForm = form;
    }

    // Re-init when tab or data changes
    $effect(() => {
        if (tab === 'prices' && Object.keys(groupedProducts).length > 0) {
            initPriceForm();
        }
    });

    // ── Side Panel ───────────────────────────────
    let sidePanel = $state({ open: false, mode: 'create', product: null });
    // mode: 'create' | 'edit'

    function openCreate() {
        sidePanel = { open: true, mode: 'create', product: null };
    }

    function openEdit(product) {
        sidePanel = { open: true, mode: 'edit', product };
    }

    function closePanel() {
        sidePanel = { ...sidePanel, open: false };
    }

    // ── Helpers ──────────────────────────────────
    function formatRp(value) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value ?? 0);
    }

    function setTab(t) {
        router.get('/admin/products', { tab: t });
    }

    function applyProductFilter() {
        router.get('/admin/products', { tab: 'products', search: search || undefined, category: selectedCategory || undefined });
    }

    function setPlant(id) {
        router.get('/admin/products', { tab: 'prices', plant: id });
    }

    function savePrices() {
        router.post('/admin/products/prices', {
            plant_id: selectedPlant?.id,
            prices: priceForm,
        }, {
            onSuccess: () => initPriceForm(),
        });
    }

    const packagingLabel = { zak: 'Zak', ton_bag: 'Ton Bag', bulk: 'Curah' };
    const packagingStyle = {
        zak: 'bg-primary-50 text-primary-700 ring-primary-100',
        ton_bag: 'bg-amber-50 text-amber-700 ring-amber-100',
        bulk: 'bg-slate-100 text-slate-600 ring-slate-200',
    };

    // All categories for form (edit mode needs inactive too)
    const allCategories = $derived(
        tab === 'prices'
            ? page.props.categories ?? []
            : (page.props.categories ?? [])
    );

    // Kategori tab: unwrap paginated response
    const catList = $derived(
        Array.isArray(page.props.categories)
            ? page.props.categories
            : (page.props.categories?.data ?? [])
    );
</script>

<AppLayout>
    <div>
        <!-- Flash -->
        {#if flash}
            <div class="mb-4 rounded-xl bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 ring-1 ring-emerald-100">{flash}</div>
        {/if}

        <!-- Page Header -->
        <header class="mb-5 flex flex-col justify-between gap-1 sm:flex-row sm:items-end">
            <div>
                <h2 class="text-xl font-bold text-slate-800">Produk</h2>
                <p class="mt-0.5 text-sm text-slate-500">Kelola produk & daftar harga.</p>
            </div>
            {#if tab === 'products'}
                <button onclick={openCreate} class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-700">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="h-4 w-4"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                    Tambah Produk
                </button>
            {/if}
        </header>

        <!-- Tabs -->
        <div class="mb-5 flex gap-1 rounded-xl bg-slate-100 p-1 w-fit">
            <button onclick={() => setTab('products')} class="flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition {tab === 'products' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-700'}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><path d="M3 8l9-5 9 5v8l-9 5-9-5z"/><path d="M3 8l9 5 9-5"/><path d="M12 13v9"/></svg>
                Daftar Produk
            </button>
            <button onclick={() => setTab('prices')} class="flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition {tab === 'prices' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-700'}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><path d="M12 2H2v10l9.29 9.29c.94.94 2.48.94 3.42 0l6.58-6.58c.94-.94.94-2.48 0-3.42L12 2z"/><path d="M7 7h.01"/></svg>
                Daftar Harga
            </button>
            <button onclick={() => setTab('categories')} class="flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition {tab === 'categories' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-700'}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><path d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                Kategori
            </button>
        </div>

        <!-- ── TAB: Products ──────────────────────── -->
        {#if tab === 'products'}
            <!-- Filters -->
            <div class="mb-4 flex flex-wrap items-center gap-3">
                <div class="relative min-w-[200px] flex-1">
                    <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    </span>
                    <input bind:value={search} type="search" placeholder="Cari nama atau kode..." onkeydown={(e) => e.key === 'Enter' && applyProductFilter()} class="w-full rounded-xl border border-slate-200 bg-white py-2 pl-9 pr-3 text-sm text-slate-800 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100" />
                </div>
                <select bind:value={selectedCategory} onchange={applyProductFilter} class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100">
                    <option value="">Semua Kategori</option>
                    {#each productCategories as c (c.id)}<option value={c.id}>{c.name}</option>{/each}
                </select>
                <button onclick={applyProductFilter} class="rounded-xl bg-slate-100 px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-200">Filter</button>
                {#if productFilters.search || productFilters.category}
                    <button onclick={() => { search = ''; selectedCategory = ''; router.get('/admin/products', { tab: 'products' }); }} class="text-sm font-medium text-primary-600 transition hover:text-primary-700">Reset</button>
                {/if}
            </div>

            <!-- Table -->
            <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-100 text-left text-xs uppercase tracking-wide text-slate-400">
                                <th class="px-5 py-3.5 font-semibold">Produk</th>
                                <th class="px-5 py-3.5 font-semibold">Kategori</th>
                                <th class="px-5 py-3.5 font-semibold">Kemasan</th>
                                <th class="px-5 py-3.5 text-right font-semibold">Harga Jual</th>
                                <th class="px-5 py-3.5 text-right font-semibold">Harga Beli</th>
                                <th class="px-5 py-3.5 text-right font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            {#each products as p (p.id)}
                                <tr class="transition hover:bg-slate-50/60 {!p.is_active ? 'opacity-50' : ''}">
                                    <td class="px-5 py-3.5">
                                        <p class="font-semibold text-slate-800">{p.name}</p>
                                        <p class="text-xs text-slate-400">{p.code ?? '—'}</p>
                                    </td>
                                    <td class="px-5 py-3.5 text-sm text-slate-600">{p.category?.name ?? '—'}</td>
                                    <td class="px-5 py-3.5">
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-medium ring-1 {packagingStyle[p.packaging_type] ?? packagingStyle.zak}">
                                            {packagingLabel[p.packaging_type] ?? p.packaging_type}
                                        </span>
                                        <p class="mt-0.5 text-xs text-slate-400">/{p.unit}</p>
                                    </td>
                                    <td class="px-5 py-3.5 text-right font-medium text-slate-800">{formatRp(p.price)}</td>
                                    <td class="px-5 py-3.5 text-right font-medium text-slate-500">{formatRp(p.harga_beli)}</td>
                                    <td class="px-5 py-3.5 text-right">
                                        <button onclick={() => openEdit(p)} class="inline-flex rounded-lg px-3 py-1.5 text-xs font-semibold text-primary-600 transition hover:bg-primary-50">Edit</button>
                                    </td>
                                </tr>
                            {/each}
                        </tbody>
                    </table>
                </div>
                {#if products.length === 0}
                    <p class="px-5 py-12 text-center text-sm text-slate-400">Belum ada produk. <button onclick={openCreate} class="font-medium text-primary-600 hover:text-primary-700">Tambah produk pertama</button></p>
                {/if}
            </div>

            <!-- Pagination -->
            {#if productsMeta?.last_page > 1}
                <div class="mt-4 flex items-center justify-between">
                    <p class="text-xs text-slate-500">Menampilkan {productsMeta.from}-{productsMeta.to} dari {productsMeta.total}</p>
                    <div class="flex gap-1">
                        {#each productsMeta.links as link (link.label)}
                            <button onclick={() => link.url && router.get(link.url)} class="rounded-lg px-3 py-1.5 text-xs font-medium transition {link.active ? 'bg-primary-600 text-white' : 'text-slate-600 hover:bg-slate-100'}" disabled={!link.url}>{@html link.label}</button>
                        {/each}
                    </div>
                </div>
            {/if}

        <!-- ── TAB: Prices ──────────────────────── -->
        {:else if tab === 'prices'}
            <!-- Plant selector -->
            <div class="mb-4 flex flex-wrap items-center gap-3">
                <div class="flex items-center gap-2">
                    <span class="text-sm text-slate-500">Plant:</span>
                    {#each plants as p (p.id)}
                        <button onclick={() => setPlant(p.id)} class="rounded-lg border px-3.5 py-1.5 text-sm font-medium transition {selectedPlant?.id === p.id ? 'border-primary-500 bg-primary-50 text-primary-700 ring-1 ring-primary-200' : 'border-slate-200 text-slate-600 hover:bg-slate-50'}">
                            {p.name}
                            {#if p.location}<span class="text-xs text-slate-400">({p.location})</span>{/if}
                        </button>
                    {/each}
                </div>
                {#if Object.keys(groupedProducts).length > 0}
                    <button onclick={savePrices} class="ml-auto rounded-xl bg-primary-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-primary-700">Simpan Harga</button>
                {/if}
            </div>

            <!-- Price Matrix -->
            {#if Object.keys(groupedProducts).length === 0}
                <div class="rounded-2xl bg-white py-16 text-center shadow-sm ring-1 ring-slate-200">
                    <p class="text-sm text-slate-400">Belum ada produk aktif.</p>
                </div>
            {:else}
                <div class="overflow-x-auto rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50 text-slate-500">
                                <th class="sticky left-0 z-10 min-w-[220px] bg-slate-50 px-4 py-3 text-left font-semibold">Produk</th>
                                {#each areas as a (a.id)}
                                    <th class="min-w-[110px] px-3 py-3 text-center font-semibold">
                                        <div>{a.name}</div>
                                        <div class="font-normal text-slate-400">{a.code}</div>
                                    </th>
                                {/each}
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            {#each Object.entries(groupedProducts) as [catName, prods] (catName)}
                                <!-- Category header -->
                                <tr class="bg-slate-50">
                                    <td colspan={areas.length + 1} class="px-4 py-2 text-xs font-semibold uppercase tracking-wide text-slate-400">
                                        {catName}
                                    </td>
                                </tr>
                                {#each prods as p (p.id)}
                                    <tr class="hover:bg-slate-50/40">
                                        <td class="sticky left-0 z-10 min-w-[220px] bg-white px-4 py-2.5">
                                            <p class="font-medium text-slate-800">{p.name}</p>
                                            <p class="text-slate-400">{packagingLabel[p.packaging_type]} · {p.unit}</p>
                                        </td>
                                        {#each areas as a (a.id)}
                                            <td class="px-2 py-2 text-center">
                                                <input
                                                type="number"
                                                min="0"
                                                step="100"
                                                value={priceForm[p.id]?.[a.id] ?? ''}
                                                oninput={(e) => {
                                                    priceForm[p.id][a.id] = e.target.value;
                                                    priceForm = { ...priceForm };
                                                }}
                                                class="w-full rounded-lg border border-slate-200 bg-white px-2 py-1.5 text-center text-sm text-slate-800 transition focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-100"
                                                placeholder="—"
                                            />
                                            </td>
                                        {/each}
                                    </tr>
                                {/each}
                            {/each}
                        </tbody>
                    </table>
                </div>
                <p class="mt-2 text-xs text-slate-400">Klik sel untuk edit harga per produk per area. Tekan Enter untuk simpan per cell, atau klik "Simpan Harga" di atas.</p>
            {/if}

        <!-- ── TAB: Kategori ──────────────────────── -->
        {:else if tab === 'categories'}
            <!-- Filter & Add -->
            <div class="mb-4 flex flex-wrap items-center gap-3">
                <div class="relative min-w-[200px] flex-1">
                    <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    </span>
                    <input bind:value={catSearch} type="search" placeholder="Cari kategori..." onkeydown={(e) => e.key === 'Enter' && applyCatFilter()} class="w-full rounded-xl border border-slate-200 bg-white py-2 pl-9 pr-3 text-sm text-slate-800 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100" />
                </div>
                <button onclick={applyCatFilter} class="rounded-xl bg-slate-100 px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-200">Cari</button>
                <button onclick={openCategoryCreate} class="ml-auto inline-flex items-center gap-2 rounded-xl bg-primary-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-700">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="h-4 w-4"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                    Tambah Kategori
                </button>
            </div>

            <!-- List -->
            <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
                {#if catList.length === 0}
                    <p class="px-5 py-12 text-center text-sm text-slate-400">Belum ada kategori.</p>
                {:else}
                    <ul class="divide-y divide-slate-100">
                        {#each catList as c (c.id)}
                            <li class="flex items-center gap-4 px-5 py-3.5 {!c.is_active ? 'opacity-50' : ''}">
                                <!-- Icon -->
                                <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><path d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                                </div>
                                <!-- Info -->
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-slate-800 truncate">{c.name}</p>
                                    <p class="text-xs text-slate-400">
                                        {c.products_count ?? 0} produk
                                        {#if !c.is_active} · <span class="text-amber-600 font-medium">Nonaktif</span>{/if}
                                    </p>
                                </div>
                                <!-- Actions -->
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <button onclick={() => toggleCategory(c.id)} class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-medium transition {c.is_active ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' : 'bg-amber-50 text-amber-700 hover:bg-amber-100'}">
                                        {#if c.is_active}
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-3.5 w-3.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                            Aktif
                                        {:else}
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-3.5 w-3.5"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                                            Nonaktif
                                        {/if}
                                    </button>
                                    {#if (c.products_count ?? 0) === 0}
                                        <button onclick={() => deleteCategory(c.id)} class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-medium bg-red-50 text-red-600 transition hover:bg-red-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-3.5 w-3.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                                            Hapus
                                        </button>
                                    {/if}
                                </div>
                            </li>
                        {/each}
                    </ul>
                {/if}
            </div>
        {/if}
    </div>

    <!-- ── Side Panel ─────────────────────────── -->
    <SidePanel
        open={sidePanel.open}
        title={sidePanel.mode === 'create' ? 'Tambah Produk' : 'Edit Produk'}
        subtitle={sidePanel.mode === 'edit' ? sidePanel.product?.name : ''}
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
