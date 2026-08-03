<script>
    import { router, usePage } from '@inertiajs/svelte';
    import AppLayout from '../../../Layouts/AppLayout.svelte';

    const page = usePage();
    const rawCategories = $derived(page.props.categories ?? []);
    const categories = $derived(Array.isArray(rawCategories) ? rawCategories : (rawCategories.data ?? []));
    const filters = $derived(page.props.filters ?? {});
    const flash = $derived(page.props.flash?.message);
    const errors = $derived(page.props.errors ?? {});

    let search = $state(filters.search ?? '');
    let showCreate = $state(false);
    let createName = $state('');
    let createActive = $state(true);

    function applyFilter() {
        router.get('/admin/products/categories', { search: search || undefined });
    }

    function submitCreate() {
        if (!createName.trim()) return;
        router.post('/admin/products/categories', { name: createName.trim(), is_active: createActive }, {
            onSuccess: () => {
                showCreate = false;
                createName = '';
                createActive = true;
            }
        });
    }
</script>

<AppLayout>
    <div class="w-full">
        <header class="mb-6 flex flex-col justify-between gap-1 sm:flex-row sm:items-end">
            <div>
                <h2 class="text-xl font-bold text-slate-800">Kategori</h2>
                <p class="mt-0.5 text-sm text-slate-500">Kelola kategori produk.</p>
            </div>
            <button onclick={() => showCreate = true} class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-700">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="h-4 w-4"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                Tambah Kategori
            </button>
        </header>

        {#if flash}
            <div class="mb-4 rounded-xl bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 ring-1 ring-emerald-100">{flash}</div>
        {/if}
        {#if errors.delete}
            <div class="mb-4 rounded-xl bg-red-50 px-4 py-3 text-sm font-medium text-red-700 ring-1 ring-red-100">{errors.delete}</div>
        {/if}

        <!-- Create Modal -->
        {#if showCreate}
            <div class="fixed inset-0 z-50">
                <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-[2px]" onclick={() => showCreate = false}></div>
                <div class="absolute inset-0 flex items-center justify-center p-4">
                    <div class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-2xl">
                        <h3 class="text-lg font-bold text-slate-800">Tambah Kategori</h3>
                        <p class="mt-1 text-sm text-slate-500">Masukkan nama kategori baru.</p>
                        <form onsubmit={(e) => { e.preventDefault(); submitCreate(); }} class="mt-5 space-y-4">
                            <div>
                                <label for="cat-name" class="mb-1 block text-sm font-medium text-slate-700">Nama Kategori *</label>
                                <input id="cat-name" type="text" bind:value={createName} required autofocus class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100 {errors.name ? 'border-red-400' : ''}" placeholder="Contoh: Semen" />
                                {#if errors.name}<p class="mt-1 text-xs text-red-500">{errors.name}</p>{/if}
                            </div>
                            <label class="flex cursor-pointer items-center gap-3">
                                <input type="checkbox" bind:checked={createActive} class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500" />
                                <span class="text-sm text-slate-600">Aktif</span>
                            </label>
                            <div class="flex justify-end gap-2">
                                <button type="button" onclick={() => showCreate = false} class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">Batal</button>
                                <button type="submit" class="rounded-xl bg-primary-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-primary-700">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        {/if}

        <!-- Search -->
        <div class="mb-4 flex items-center gap-3">
            <div class="relative flex-1">
                <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                </span>
                <input bind:value={search} type="search" placeholder="Cari kategori..." onkeydown={(e) => e.key === 'Enter' && applyFilter()} class="w-full rounded-xl border border-slate-200 bg-white py-2 pl-9 pr-3 text-sm text-slate-800 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100" />
            </div>
            <button onclick={applyFilter} class="rounded-xl bg-slate-100 px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-200">Cari</button>
        </div>

        <!-- List -->
        <div class="space-y-3">
            {#each categories as cat (cat.id)}
                <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200 {!cat.is_active ? 'opacity-60' : ''}">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-semibold text-slate-800">{cat.name}</p>
                            <p class="mt-0.5 text-xs text-slate-400">{cat.products_count} produk</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[11px] font-medium ring-1 {cat.is_active ? 'bg-emerald-50 text-emerald-700 ring-emerald-100' : 'bg-slate-100 text-slate-500 ring-slate-200'}">
                                {cat.is_active ? 'Aktif' : 'Nonaktif'}
                            </span>
                            <form onsubmit={(e) => { e.preventDefault(); router.put(`/admin/products/categories/${cat.id}`, { name: cat.name, is_active: !cat.is_active }); }} class="inline">
                                <button type="submit" class="text-xs font-medium text-slate-500 transition hover:text-slate-700">
                                    {cat.is_active ? 'Nonaktifkan' : 'Aktifkan'}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            {/each}
            {#if categories.length === 0}
                <div class="rounded-2xl bg-white p-10 text-center shadow-sm ring-1 ring-slate-200">
                    <p class="text-sm text-slate-400">Belum ada kategori.</p>
                </div>
            {/if}
        </div>
    </div>
</AppLayout>
