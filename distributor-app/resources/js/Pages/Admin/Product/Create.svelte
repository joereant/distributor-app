<script>
    import { Link, router, usePage } from '@inertiajs/svelte';
    import AppLayout from '../../../Layouts/AppLayout.svelte';

    const page = usePage();
    const categories = $derived(page.props.categories ?? []);
    const errors = $derived(page.props.errors ?? {});

    let form = $state({
        name: '',
        code: '',
        description: '',
        unit: 'zak',
        packaging_type: 'zak',
        price: '',
        harga_beli: '',
        category_id: '',
        min_stock: 0,
        is_active: true,
    });

    const packagingOptions = [
        { value: 'zak', label: 'Zak (Pack)' },
        { value: 'ton_bag', label: 'Ton Bag' },
        { value: 'bulk', label: 'Curah (Tonase)' },
    ];

    function submit() {
        router.post('/admin/products', form);
    }
</script>

<AppLayout>
    <div class="mx-auto max-w-2xl">
        <header class="mb-6">
            <Link href="/admin/products" class="mb-2 inline-flex items-center gap-1.5 text-sm text-slate-500 transition hover:text-slate-700">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><path d="m15 18-6-6 6-6"/></svg>
                Kembali ke Produk
            </Link>
            <h2 class="text-xl font-bold text-slate-800">Tambah Produk</h2>
            <p class="mt-0.5 text-sm text-slate-500">Tambah produk baru ke katalog.</p>
        </header>

        <form onsubmit={(e) => { e.preventDefault(); submit(); }} class="space-y-6">
            <!-- Basic Info -->
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <h3 class="mb-4 text-sm font-semibold text-slate-700">Informasi Produk</h3>
                <div class="space-y-4">
                    <div>
                        <label for="name" class="mb-1 block text-sm font-medium text-slate-700">Nama Produk *</label>
                        <input id="name" type="text" bind:value={form.name} required class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100 {errors.name ? 'border-red-400' : ''}" placeholder="Contoh: Semen Portland Composite 50 kg" />
                        {#if errors.name}<p class="mt-1 text-xs text-red-500">{errors.name}</p>{/if}
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="code" class="mb-1 block text-sm font-medium text-slate-700">Kode / SKU</label>
                            <input id="code" type="text" bind:value={form.code} class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100 {errors.code ? 'border-red-400' : ''}" placeholder="Contoh: SPC50" />
                            {#if errors.code}<p class="mt-1 text-xs text-red-500">{errors.code}</p>{/if}
                        </div>
                        <div>
                            <label for="category" class="mb-1 block text-sm font-medium text-slate-700">Kategori</label>
                            <select id="category" bind:value={form.category_id} class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100">
                                <option value="">Tanpa kategori</option>
                                {#each categories as c (c.id)}
                                    <option value={c.id}>{c.name}</option>
                                {/each}
                            </select>
                        </div>
                    </div>
                    <div>
                        <label for="description" class="mb-1 block text-sm font-medium text-slate-700">Deskripsi</label>
                        <textarea id="description" bind:value={form.description} rows="2" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100" placeholder="Deskripsi produk (opsional)"></textarea>
                    </div>
                </div>
            </div>

            <!-- Packaging -->
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <h3 class="mb-4 text-sm font-semibold text-slate-700">Kemasan & Unit</h3>
                <div class="space-y-4">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Tipe Kemasan *</label>
                        <div class="flex flex-wrap gap-3">
                            {#each packagingOptions as opt (opt.value)}
                                <label class="flex items-center gap-2 rounded-xl border px-4 py-2.5 text-sm transition cursor-pointer has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50 has-[:checked]:ring-1 has-[:checked]:ring-primary-200 {form.packaging_type === opt.value ? 'border-primary-500 bg-primary-50 ring-1 ring-primary-200' : 'border-slate-200 hover:border-slate-300'}">
                                    <input type="radio" bind:group={form.packaging_type} value={opt.value} class="sr-only" />
                                    {opt.label}
                                </label>
                            {/each}
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="unit" class="mb-1 block text-sm font-medium text-slate-700">Unit Pengukuran *</label>
                            <input id="unit" type="text" bind:value={form.unit} required class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100" placeholder="Contoh: zak, ton, kg" />
                        </div>
                        <div>
                            <label for="min_stock" class="mb-1 block text-sm font-medium text-slate-700">Min. Stok</label>
                            <input id="min_stock" type="number" bind:value={form.min_stock} min="0" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pricing -->
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <h3 class="mb-4 text-sm font-semibold text-slate-700">Harga</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="price" class="mb-1 block text-sm font-medium text-slate-700">Harga Dasar / Jual (Rp) *</label>
                        <input id="price" type="number" bind:value={form.price} required min="0" step="0.01" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100 {errors.price ? 'border-red-400' : ''}" placeholder="62000" />
                        {#if errors.price}<p class="mt-1 text-xs text-red-500">{errors.price}</p>{/if}
                    </div>
                    <div>
                        <label for="harga_beli" class="mb-1 block text-sm font-medium text-slate-700">Harga Beli dari Pabrik (Rp) *</label>
                        <input id="harga_beli" type="number" bind:value={form.harga_beli} required min="0" step="0.01" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100 {errors.harga_beli ? 'border-red-400' : ''}" placeholder="51000" />
                        {#if errors.harga_beli}<p class="mt-1 text-xs text-red-500">{errors.harga_beli}</p>{/if}
                    </div>
                </div>
            </div>

            <!-- Active -->
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <label class="flex cursor-pointer items-center gap-3">
                    <input type="checkbox" bind:checked={form.is_active} class="h-5 w-5 rounded border-slate-300 text-primary-600 focus:ring-primary-500" />
                    <div>
                        <p class="text-sm font-medium text-slate-700">Produk Aktif</p>
                        <p class="text-xs text-slate-500">Produk tidak aktif tidak akan muncul di katalog order.</p>
                    </div>
                </label>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <Link href="/admin/products" class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">Batal</Link>
                <button type="submit" class="rounded-xl bg-primary-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-700">Simpan Produk</button>
            </div>
        </form>
    </div>
</AppLayout>
