<script>
    import { router } from '@inertiajs/svelte';

    let {
        product = null,  // null = create mode, object = edit mode
        categories = [],
        onclose,
    } = $props();

    const isEdit = $derived(!!product?.id);

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

    $effect(() => {
        form.name = product?.name ?? '';
        form.code = product?.code ?? '';
        form.description = product?.description ?? '';
        form.unit = product?.unit ?? 'zak';
        form.packaging_type = product?.packaging_type ?? 'zak';
        form.price = product?.price ?? '';
        form.harga_beli = product?.harga_beli ?? '';
        form.category_id = product?.category_id ?? '';
        form.min_stock = product?.min_stock ?? 0;
        form.is_active = product?.is_active ?? true;
    });

    const packagingOptions = [
        { value: 'zak', label: 'Zak (Pack)' },
        { value: 'ton_bag', label: 'Ton Bag' },
        { value: 'bulk', label: 'Curah (Tonase)' },
    ];

    function submit() {
        if (isEdit) {
            router.put(`/admin/products/${product.id}`, form, { onSuccess: onclose });
        } else {
            router.post('/admin/products', form, { onSuccess: onclose });
        }
    }

    function deleteProduct() {
        if (confirm(`Hapus produk "${product.name}"?`)) {
            router.delete(`/admin/products/${product.id}`, { onSuccess: onclose });
        }
    }
</script>

<form onsubmit={(e) => { e.preventDefault(); submit(); }} class="space-y-5">
    <!-- Basic Info -->
    <div>
        <h3 class="mb-3 text-xs font-semibold uppercase tracking-wide text-slate-400">Informasi Produk</h3>
        <div class="space-y-3">
            <div>
                <label for="pf-name" class="mb-1 block text-sm font-medium text-slate-700">Nama Produk *</label>
                <input id="pf-name" type="text" bind:value={form.name} required class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100" placeholder="Contoh: Semen Portland Composite 50 kg" />
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="pf-code" class="mb-1 block text-sm font-medium text-slate-700">Kode / SKU</label>
                    <input id="pf-code" type="text" bind:value={form.code} class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100" placeholder="SPC50" />
                </div>
                <div>
                    <label for="pf-cat" class="mb-1 block text-sm font-medium text-slate-700">Kategori</label>
                    <select id="pf-cat" bind:value={form.category_id} class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100">
                        <option value="">Tanpa kategori</option>
                        {#each categories as c (c.id)}
                            <option value={c.id}>{c.name}</option>
                        {/each}
                    </select>
                </div>
            </div>
            <div>
                <label for="pf-desc" class="mb-1 block text-sm font-medium text-slate-700">Deskripsi</label>
                <textarea id="pf-desc" bind:value={form.description} rows="2" class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100" placeholder="Opsional"></textarea>
            </div>
        </div>
    </div>

    <!-- Packaging -->
    <div>
        <h3 class="mb-3 text-xs font-semibold uppercase tracking-wide text-slate-400">Kemasan & Unit</h3>
        <div class="space-y-3">
            <div>
                <p class="mb-1.5 text-sm font-medium text-slate-700">Tipe Kemasan *</p>
                <div class="flex flex-wrap gap-2">
                    {#each packagingOptions as opt (opt.value)}
                        <label class="flex cursor-pointer items-center gap-1.5 rounded-lg border px-3.5 py-2 text-sm transition has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50 has-[:checked]:ring-1 has-[:checked]:ring-primary-200 {form.packaging_type === opt.value ? 'border-primary-500 bg-primary-50 ring-1 ring-primary-200' : 'border-slate-200 hover:border-slate-300'}">
                            <input type="radio" bind:group={form.packaging_type} value={opt.value} class="sr-only" />
                            {opt.label}
                        </label>
                    {/each}
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="pf-unit" class="mb-1 block text-sm font-medium text-slate-700">Unit *</label>
                    <input id="pf-unit" type="text" bind:value={form.unit} required class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100" placeholder="zak, ton, m3" />
                </div>
                <div>
                    <label for="pf-stock" class="mb-1 block text-sm font-medium text-slate-700">Min. Stok</label>
                    <input id="pf-stock" type="number" bind:value={form.min_stock} min="0" class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100" />
                </div>
            </div>
        </div>
    </div>

    <!-- Pricing -->
    <div>
        <h3 class="mb-3 text-xs font-semibold uppercase tracking-wide text-slate-400">Harga</h3>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label for="pf-price" class="mb-1 block text-sm font-medium text-slate-700">Harga Jual (Rp) *</label>
                <input id="pf-price" type="number" bind:value={form.price} required min="0" step="0.01" class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100" placeholder="72000" />
            </div>
            <div>
                <label for="pf-buy" class="mb-1 block text-sm font-medium text-slate-700">Harga Beli (Rp) *</label>
                <input id="pf-buy" type="number" bind:value={form.harga_beli} required min="0" step="0.01" class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100" placeholder="58000" />
            </div>
        </div>
    </div>

    <!-- Active (edit mode only) -->
    {#if isEdit}
        <div class="rounded-xl bg-slate-50 p-3.5 ring-1 ring-slate-100">
            <label class="flex cursor-pointer items-center gap-3">
                <input type="checkbox" bind:checked={form.is_active} class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500" />
                <div>
                    <p class="text-sm font-medium text-slate-700">Produk Aktif</p>
                    <p class="text-xs text-slate-500">Nonaktifkan untuk menyembunyikan dari katalog.</p>
                </div>
            </label>
        </div>
    {/if}

    <!-- Actions -->
    <div class="flex items-center justify-between gap-3 pt-2">
        {#if isEdit}
            <button type="button" onclick={deleteProduct} class="rounded-xl border border-red-200 bg-white px-4 py-2.5 text-sm font-semibold text-red-600 transition hover:bg-red-50">Hapus</button>
        {:else}
            <div></div>
        {/if}
        <div class="flex gap-2">
            <button type="button" onclick={onclose} class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">Batal</button>
            <button type="submit" class="rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-700">
                {isEdit ? 'Simpan' : 'Tambah'}
            </button>
        </div>
    </div>
</form>
