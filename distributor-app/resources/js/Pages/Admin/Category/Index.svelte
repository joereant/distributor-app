<script>
    import { router, usePage } from '@inertiajs/svelte';
    import AppLayout from '../../../Layouts/AppLayout.svelte';
    import SidePanel from '../../../Components/SidePanel.svelte';

    const page = usePage();
    const rawCategories = $derived(page.props.categories ?? []);
    const categories = $derived(Array.isArray(rawCategories) ? rawCategories : (rawCategories.data ?? []));
    const filters = $derived(page.props.filters ?? {});
    const flash = $derived(page.props.flash?.message);
    const errors = $derived(page.props.errors ?? {});

    let search = $state(filters.search ?? '');
    let selectedStatus = $state('');

    // SidePanel state for Create & Edit/Show
    let sidePanel = $state({
        open: false,
        mode: 'create', // 'create' | 'edit'
        category: null,
    });

    let formName = $state('');
    let formActive = $state(true);

    function openCreate() {
        formName = '';
        formActive = true;
        sidePanel = { open: true, mode: 'create', category: null };
    }

    function openEdit(cat) {
        formName = cat.name;
        formActive = cat.is_active;
        sidePanel = { open: true, mode: 'edit', category: cat };
    }

    function closePanel() {
        sidePanel = { ...sidePanel, open: false };
    }

    function submitForm() {
        if (!formName.trim()) return;

        if (sidePanel.mode === 'create') {
            router.post('/admin/products/categories', {
                name: formName.trim(),
                is_active: formActive
            }, {
                onSuccess: () => closePanel()
            });
        } else if (sidePanel.mode === 'edit' && sidePanel.category) {
            router.put(`/admin/products/categories/${sidePanel.category.id}`, {
                name: formName.trim(),
                is_active: formActive
            }, {
                onSuccess: () => closePanel()
            });
        }
    }

    function deleteCategory(cat) {
        if (cat.products_count > 0) {
            alert(`Kategori '${cat.name}' tidak dapat dihapus karena masih terikat pada ${cat.products_count} produk.`);
            return;
        }

        if (confirm(`Apakah Anda yakin ingin menghapus kategori '${cat.name}'?`)) {
            router.delete(`/admin/products/categories/${cat.id}`, {
                onSuccess: () => closePanel()
            });
        }
    }

    function toggleActive(e, cat) {
        e.stopPropagation(); // prevent card click
        router.put(`/admin/products/categories/${cat.id}`, {
            name: cat.name,
            is_active: !cat.is_active
        });
    }

    // Metrics
    const totalCategoriesCount = $derived(categories.length);
    const activeCategoriesCount = $derived(categories.filter(c => c.is_active).length);
    const totalBoundProducts = $derived(categories.reduce((acc, c) => acc + (c.products_count ?? 0), 0));

    // Filtered categories
    const filteredCategories = $derived(() => {
        return categories.filter(c => {
            const matchSearch = c.name.toLowerCase().includes(search.toLowerCase());
            const matchStatus = selectedStatus === '' ? true : selectedStatus === 'active' ? c.is_active : !c.is_active;
            return matchSearch && matchStatus;
        });
    });
</script>

<AppLayout>
    <div class="space-y-6">
        <!-- Flash & Errors -->
        {#if flash}
            <div class="flex items-center gap-3 rounded-2xl bg-emerald-50/90 p-4 text-sm font-medium text-emerald-800 shadow-xs ring-1 ring-emerald-200/60 backdrop-blur-xs">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-emerald-500 text-white shadow-xs">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="h-4 w-4"><path d="M20 6 9 17l-5-5"/></svg>
                </span>
                <p class="flex-1">{typeof flash === 'object' ? flash.text : flash}</p>
            </div>
        {/if}
        {#if errors.delete}
            <div class="flex items-center gap-3 rounded-2xl bg-rose-50/90 p-4 text-sm font-medium text-rose-800 shadow-xs ring-1 ring-rose-200/60 backdrop-blur-xs">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-rose-500 text-white shadow-xs">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="h-4 w-4"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                </span>
                <p class="flex-1">{errors.delete}</p>
            </div>
        {/if}

        <!-- 1. Executive Header & Action Bar -->
        <header class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900">Kategori Produk</h1>
                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-600 ring-1 ring-slate-200/80">{totalCategoriesCount} grup</span>
                </div>
                <p class="mt-1 text-sm text-slate-500">Kelola grup klasifikasi produk untuk mempermudah navigasi customer dan laporan penjualan.</p>
            </div>
            <button
                onclick={openCreate}
                class="inline-flex items-center gap-2 rounded-xl bg-teal-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:bg-teal-700 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 active:scale-[0.98]"
            >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" class="h-4 w-4"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                Tambah Kategori Baru
            </button>
        </header>

        <!-- 2. Executive Summary Metric Strip -->
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-3 sm:gap-4">
            <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-slate-500">Total Kategori</span>
                    <span class="rounded-lg bg-teal-50 p-2 text-teal-600">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/></svg>
                    </span>
                </div>
                <p class="mt-2 text-2xl font-bold tracking-tight text-slate-900">{totalCategoriesCount}</p>
                <span class="text-[11px] font-medium text-slate-400">Grup klasifikasi aktif</span>
            </div>

            <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-slate-500">Status Aktif</span>
                    <span class="rounded-lg bg-emerald-50 p-2 text-emerald-600">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    </span>
                </div>
                <p class="mt-2 text-2xl font-bold tracking-tight text-slate-900">{activeCategoriesCount}</p>
                <span class="text-[11px] font-medium text-emerald-600">Tampil di katalog online</span>
            </div>

            <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-slate-500">Total Produk Terikat</span>
                    <span class="rounded-lg bg-indigo-50 p-2 text-indigo-600">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/></svg>
                    </span>
                </div>
                <p class="mt-2 text-2xl font-bold tracking-tight text-slate-900">{totalBoundProducts}</p>
                <span class="text-[11px] font-medium text-slate-400">Total varian di seluruh kategori</span>
            </div>
        </div>

        <!-- 3. Toolbar Control Center (Search + Status Filter) -->
        <div class="flex flex-col gap-3 rounded-2xl border border-slate-200/80 bg-white p-3.5 shadow-xs sm:flex-row sm:items-center sm:justify-between">
            <div class="relative flex-1">
                <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                </span>
                <input
                    bind:value={search}
                    type="search"
                    placeholder="Cari nama kategori..."
                    class="w-full rounded-xl border border-slate-200 bg-slate-50/50 py-2 pl-10 pr-4 text-sm text-slate-800 transition placeholder:text-slate-400 focus:border-teal-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-100"
                />
            </div>

            <div class="flex items-center gap-2">
                <button
                    onclick={() => selectedStatus = ''}
                    class="rounded-xl px-3 py-1.5 text-xs font-semibold transition {selectedStatus === '' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'}"
                >
                    Semua ({categories.length})
                </button>
                <button
                    onclick={() => selectedStatus = 'active'}
                    class="rounded-xl px-3 py-1.5 text-xs font-semibold transition {selectedStatus === 'active' ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'}"
                >
                    Aktif ({activeCategoriesCount})
                </button>
                <button
                    onclick={() => selectedStatus = 'inactive'}
                    class="rounded-xl px-3 py-1.5 text-xs font-semibold transition {selectedStatus === 'inactive' ? 'bg-rose-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'}"
                >
                    Nonaktif ({categories.length - activeCategoriesCount})
                </button>
            </div>
        </div>

        <!-- 4. Data Cards Grid Pro (Click Card to Show/Edit) -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-6">
            {#each filteredCategories() as cat (cat.id)}
                <div
                    onclick={() => openEdit(cat)}
                    role="button"
                    tabindex="0"
                    onkeydown={(e) => e.key === 'Enter' && openEdit(cat)}
                    class="group relative flex cursor-pointer flex-col justify-between overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-4.5 shadow-2xs transition-all duration-200 hover:-translate-y-1 hover:border-teal-500/50 hover:shadow-lg hover:shadow-teal-500/5 active:translate-y-0 {!cat.is_active ? 'bg-slate-50/60 opacity-70' : ''}"
                >
                    <!-- Top Accent Bar on Hover -->
                    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-teal-500 to-emerald-500 opacity-0 transition-opacity duration-200 group-hover:opacity-100"></div>

                    <div>
                        <!-- Header: Icon & Category Name -->
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-teal-50 to-teal-100/70 text-teal-700 ring-1 ring-teal-200/60 transition-all duration-200 group-hover:from-teal-600 group-hover:to-emerald-600 group-hover:text-white group-hover:shadow-xs">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5"><rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/></svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <h3 class="truncate text-sm font-bold tracking-tight text-slate-900 transition-colors group-hover:text-teal-700" title={cat.name}>
                                    {cat.name}
                                </h3>
                                <p class="mt-0.5 truncate font-mono text-[11px] text-slate-400">
                                    #{cat.slug ?? cat.name.toLowerCase().replace(/\s+/g, '-')}
                                </p>
                            </div>
                        </div>

                        <!-- Mid Section: Product Count Box (Di Tengah) -->
                        <div class="mt-4 flex items-center justify-between rounded-xl bg-slate-50 px-3 py-2 transition-colors group-hover:bg-teal-50/50">
                            <span class="text-xs text-slate-500 font-medium">Produk Terikat</span>
                            <span class="font-bold text-xs text-slate-900 group-hover:text-teal-800">{cat.products_count ?? 0} item</span>
                        </div>
                    </div>

                    <!-- Footer: Edit Detail on Left + Interactive Status Toggle Button on Right (Sudut Kanan Bawah) -->
                    <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-3">
                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-400 transition-colors group-hover:text-teal-600">
                            Edit Detail
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="h-3 w-3 transition-transform group-hover:translate-x-0.5"><path d="m9 18 6-6-6-6"/></svg>
                        </span>

                        <!-- Interactive Status Toggle Button (Bentuk Toggle dengan Teks Aktif/Nonaktif) -->
                        <button
                            type="button"
                            onclick={(e) => toggleActive(e, cat)}
                            title={cat.is_active ? 'Klik untuk nonaktifkan' : 'Klik untuk aktifkan'}
                            class="inline-flex cursor-pointer items-center gap-1.5 rounded-full px-3 py-1 text-[11px] font-bold shadow-2xs transition-all hover:scale-105 active:scale-95 ring-1 {cat.is_active ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/30 hover:bg-emerald-100' : 'bg-slate-100 text-slate-500 ring-slate-300 hover:bg-slate-200'}"
                        >
                            <span class="h-1.5 w-1.5 rounded-full {cat.is_active ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400'}"></span>
                            {cat.is_active ? 'Aktif' : 'Nonaktif'}
                        </button>
                    </div>
                </div>
            {/each}
        </div>

        <!-- Illustrative Empty State -->
        {#if filteredCategories().length === 0}
            <div class="flex flex-col items-center justify-center rounded-2xl border border-slate-200/80 bg-white py-16 text-center shadow-xs">
                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-teal-50 text-teal-600 ring-8 ring-teal-50/50">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-8 w-8"><rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/></svg>
                </div>
                <h3 class="mt-4 text-base font-bold text-slate-900">Tidak ada kategori ditemukan</h3>
                <p class="mt-1 max-w-sm text-sm text-slate-500">Tidak ada kategori produk yang sesuai dengan pencarian atau filter yang dipilih.</p>
                <div class="mt-5">
                    <button onclick={openCreate} class="rounded-xl bg-teal-600 px-4 py-2 text-sm font-semibold text-white shadow-xs transition hover:bg-teal-700">
                        + Tambah Kategori Baru
                    </button>
                </div>
            </div>
        {/if}
    </div>

    <!-- 5. Contextual SidePanel Pro for Show & Edit & Create -->
    <SidePanel
        open={sidePanel.open}
        title={sidePanel.mode === 'create' ? 'Tambah Kategori Baru' : 'Detail & Edit Kategori'}
        subtitle={sidePanel.mode === 'edit' ? sidePanel.category?.name : 'Buat grup kategori baru untuk mengelompokkan katalog produk'}
        size="md"
        onclose={closePanel}
    >
        <form onsubmit={(e) => { e.preventDefault(); submitForm(); }} class="space-y-5">
            {#if sidePanel.mode === 'edit' && sidePanel.category}
                <!-- Category Detail Card in SidePanel -->
                <div class="rounded-2xl border border-slate-200/80 bg-slate-50 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-xs font-medium text-slate-500">ID Kategori</span>
                            <p class="font-mono text-sm font-bold text-slate-800">#{sidePanel.category.id}</p>
                        </div>
                        <div>
                            <span class="text-xs font-medium text-slate-500">URL Slug</span>
                            <p class="font-mono text-xs font-semibold text-teal-700 bg-teal-50 px-2 py-0.5 rounded-md border border-teal-200">
                                /{sidePanel.category.slug ?? sidePanel.category.name.toLowerCase().replace(/\s+/g, '-')}
                            </p>
                        </div>
                        <div>
                            <span class="text-xs font-medium text-slate-500">Produk Terikat</span>
                            <p class="font-bold text-slate-900 text-right">{sidePanel.category.products_count ?? 0} item</p>
                        </div>
                    </div>
                </div>
            {/if}

            <div>
                <label for="cat-name-input" class="mb-1.5 block text-sm font-bold text-slate-800">Nama Kategori <span class="text-rose-500">*</span></label>
                <input
                    id="cat-name-input"
                    type="text"
                    bind:value={formName}
                    required
                    autofocus
                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 shadow-xs transition focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-100 {errors.name ? 'border-rose-400' : ''}"
                    placeholder="Contoh: Semen Portland Composite (PCC)"
                />
                {#if errors.name}<p class="mt-1 text-xs text-rose-500">{errors.name}</p>{/if}
            </div>

            <div class="rounded-xl border border-slate-200/80 bg-slate-50 p-4">
                <label class="flex cursor-pointer items-center justify-between">
                    <div>
                        <span class="text-sm font-bold text-slate-800">Status Aktif</span>
                        <p class="text-xs text-slate-500">Tampilkan kategori ini pada katalog online customer</p>
                    </div>
                    <input type="checkbox" bind:checked={formActive} class="h-5 w-5 rounded border-slate-300 text-teal-600 focus:ring-teal-500" />
                </label>
            </div>

            <div class="flex items-center justify-between border-t border-slate-100 pt-4">
                {#if sidePanel.mode === 'edit' && sidePanel.category}
                    <button
                        type="button"
                        onclick={() => deleteCategory(sidePanel.category)}
                        class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-2.5 text-sm font-semibold text-rose-700 transition hover:bg-rose-100 active:scale-95"
                    >
                        Hapus
                    </button>
                {:else}
                    <div></div>
                {/if}

                <div class="flex items-center gap-2">
                    <button type="button" onclick={closePanel} class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                        Batal
                    </button>
                    <button type="submit" class="rounded-xl bg-teal-600 px-5 py-2.5 text-sm font-semibold text-white shadow-xs transition hover:bg-teal-700 active:scale-95">
                        {sidePanel.mode === 'create' ? 'Simpan Kategori' : 'Perbarui Kategori'}
                    </button>
                </div>
            </div>
        </form>
    </SidePanel>
</AppLayout>


