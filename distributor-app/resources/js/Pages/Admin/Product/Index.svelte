<script>
    import { router, usePage } from '@inertiajs/svelte';
    import AppLayout from '../../../Layouts/AppLayout.svelte';

    const page = usePage();
    const products = $derived(page.props.products ?? []);
    const categories = $derived(page.props.categories ?? []);
    const filters = $derived(page.props.filters ?? {});
    const flash = $derived(page.props.flash?.message);

    let search = $state(filters.search ?? '');
    let selectedCategory = $state(filters.category ?? '');

    function formatRp(value) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value ?? 0);
    }

    function applyFilter() {
        router.get('/admin/products', {
            search: search || undefined,
            category: selectedCategory || undefined,
        });
    }

    function clearFilter() {
        search = '';
        selectedCategory = '';
        router.get('/admin/products');
    }

    const packagingLabel = {
        zak: 'Zak',
        ton_bag: 'Ton Bag',
        bulk: 'Curah',
    };

    const packagingStyle = {
        zak: 'bg-primary-50 text-primary-700 ring-primary-100',
        ton_bag: 'bg-amber-50 text-amber-700 ring-amber-100',
        bulk: 'bg-slate-100 text-slate-600 ring-slate-200',
    };
</script>

<AppLayout>
    <div class="mx-auto max-w-7xl">
        <header class="mb-6 flex flex-col justify-between gap-1 sm:flex-row sm:items-end">
            <div>
                <h2 class="text-xl font-bold text-slate-800">Produk</h2>
                <p class="mt-0.5 text-sm text-slate-500">Kelola katalog produk & harga per area.</p>
            </div>
            <a href="/admin/products/create" class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-700">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="h-4 w-4"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                Tambah Produk
            </a>
        </header>

        {#if flash}
            <div class="mb-4 rounded-xl bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 ring-1 ring-emerald-100">{flash}</div>
        {/if}

        <!-- Filter -->
        <div class="mb-4 flex flex-wrap items-center gap-3">
            <div class="relative flex-1 min-w-[200px]">
                <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                </span>
                <input bind:value={search} type="search" placeholder="Cari nama atau kode produk..." onkeydown={(e) => e.key === 'Enter' && applyFilter()} class="w-full rounded-xl border border-slate-200 bg-white py-2 pl-9 pr-3 text-sm text-slate-800 shadow-sm transition placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100" />
            </div>
            <select bind:value={selectedCategory} onchange={applyFilter} class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100">
                <option value="">Semua Kategori</option>
                {#each categories as c (c.id)}
                    <option value={c.id}>{c.name}</option>
                {/each}
            </select>
            <button onclick={applyFilter} class="rounded-xl bg-slate-100 px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-200">Filter</button>
            {#if filters.search || filters.category}
                <button onclick={clearFilter} class="text-sm font-medium text-primary-600 transition hover:text-primary-700">Reset</button>
            {/if}
        </div>

        <!-- Table -->
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-left text-xs uppercase tracking-wide text-slate-400">
                            <th class="px-5 py-3 font-semibold">Produk</th>
                            <th class="px-5 py-3 font-semibold">Kategori</th>
                            <th class="px-5 py-3 font-semibold">Kemasan</th>
                            <th class="px-5 py-3 text-right font-semibold">Harga Dasar</th>
                            <th class="px-5 py-3 text-right font-semibold">Harga Beli</th>
                            <th class="px-5 py-3 text-right font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        {#each products.data as p (p.id)}
                            <tr class="transition hover:bg-slate-50/60 {!p.is_active ? 'opacity-50' : ''}">
                                <td class="px-5 py-3.5">
                                    <p class="font-semibold text-slate-800">{p.name}</p>
                                    <p class="text-xs text-slate-400">{p.code ?? '—'}</p>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="text-sm text-slate-600">{p.category?.name ?? '—'}</span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-medium ring-1 {packagingStyle[p.packaging_type] ?? packagingStyle.zak}">
                                        {packagingLabel[p.packaging_type] ?? p.packaging_type}
                                    </span>
                                    <p class="mt-0.5 text-xs text-slate-400">/{p.unit}</p>
                                </td>
                                <td class="px-5 py-3.5 text-right font-medium text-slate-800">{formatRp(p.price)}</td>
                                <td class="px-5 py-3.5 text-right font-medium text-slate-600">{formatRp(p.harga_beli)}</td>
                                <td class="px-5 py-3.5 text-right">
                                    <a href="/admin/products/{p.id}/edit" class="inline-flex rounded-lg px-3 py-1.5 text-xs font-semibold text-primary-600 transition hover:bg-primary-50">Edit</a>
                                </td>
                            </tr>
                        {/each}
                    </tbody>
                </table>
            </div>
            {#if products.data.length === 0}
                <p class="px-5 py-12 text-center text-sm text-slate-400">Belum ada produk. <a href="/admin/products/create" class="font-medium text-primary-600 hover:text-primary-700">Tambah produk pertama</a></p>
            {/if}
        </div>

        <!-- Pagination -->
        {#if products.last_page > 1}
            <div class="mt-4 flex items-center justify-between">
                <p class="text-xs text-slate-500">Menampilkan {products.from}-{products.to} dari {products.total} produk</p>
                <div class="flex gap-1">
                    {#each products.links as link (link.label)}
                        <button
                            onclick={() => router.get(link.url)}
                            class="rounded-lg px-3 py-1.5 text-xs font-medium transition {link.active ? 'bg-primary-600 text-white' : 'text-slate-600 hover:bg-slate-100'}"
                            disabled={!link.url}
                        >
                            {@html link.label}
                        </button>
                    {/each}
                </div>
            </div>
        {/if}
    </div>
</AppLayout>
