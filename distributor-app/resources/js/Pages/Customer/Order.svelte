<script>
    import { router, usePage } from '@inertiajs/svelte';
    import AppLayout from '../../Layouts/AppLayout.svelte';
    import DashboardCard from '../../Components/DashboardCard.svelte';

    const page = usePage();

    const products = $derived(page.props.products ?? []);
    const areas = $derived(page.props.areas ?? []);
    const shippingRates = $derived(page.props.shipping_rates ?? []);
    const pricesByArea = $derived(page.props.product_prices ?? {});
    const flash = $derived(page.props.flash?.message);

    let areaId = $state('');
    let quantities = $state({});
    let query = $state('');

    const rateByArea = $derived(Object.fromEntries(shippingRates.map((r) => [String(r.area_id), Number(r.rate)])));

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

    // For filtered display, flatten grouped
    const filteredGrouped = $derived(
        query.trim()
            ? { 'Hasil Pencarian': products.filter((p) => {
                const q = query.trim().toLowerCase();
                return p.name.toLowerCase().includes(q) || (p.code ?? '').toLowerCase().includes(q);
              })}
            : groupedProducts
    );

    const hasResults = $derived(Object.values(filteredGrouped).some(g => g.length > 0));

    const selected = $derived(
        products
            .map((p) => ({ ...p, qty: Number(quantities[p.id] ?? 0) }))
            .filter((p) => p.qty > 0)
    );

    const totalQty = $derived(selected.reduce((sum, p) => sum + p.qty, 0));
    const rate = $derived(rateByArea[String(areaId)] ?? 0);
    const ongkir = $derived(rate * totalQty);
    const subtotal = $derived(selected.reduce((sum, p) => sum + unitPrice(p) * p.qty, 0));
    const total = $derived(subtotal + ongkir);

    function unitPrice(product) {
        return Number(pricesByArea[String(areaId)]?.[product.id] ?? product.price);
    }

    function formatRp(value) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value ?? 0);
    }

    function changeQty(id, delta) {
        const next = (Number(quantities[id] ?? 0) || 0) + delta;
        quantities[id] = Math.max(0, next);
        quantities = { ...quantities };
    }

    function submit() {
        if (!areaId || selected.length === 0) return;
        router.post('/customer/order', {
            area_id: areaId,
            items: selected.map((p) => ({ product_id: p.id, quantity: p.qty })),
        });
    }
</script>

<AppLayout>
    <div>
        <header class="mb-6">
            <h2 class="text-xl font-bold text-slate-800">Buat Pesanan</h2>
            <p class="mt-0.5 text-sm text-slate-500">Pilih produk & tentukan tujuan kirim — ongkir dihitung otomatis.</p>
        </header>

        {#if flash}
            <div class="mb-6 rounded-xl bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 ring-1 ring-emerald-100">{flash}</div>
        {/if}

        <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-3">
            <div class="space-y-4 lg:col-span-2">
                <div class="flex flex-col gap-3 sm:flex-row">
                    <label class="flex-1">
                        <span class="mb-1 block text-xs font-medium text-slate-500">Tujuan Kirim (Area)</span>
                        <select id="order-area" bind:value={areaId} class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100">
                            <option value="">Pilih area…</option>
                            {#each areas as a (a.id)}
                                <option value={a.id}>{a.name}</option>
                            {/each}
                        </select>
                    </label>
                    <label class="flex-1">
                        <span class="mb-1 block text-xs font-medium text-slate-500">Cari Produk</span>
                        <input bind:value={query} type="search" placeholder="Cari nama / kode produk…" class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 shadow-sm transition placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100" />
                    </label>
                </div>

                {#if !hasResults}
                    <p class="rounded-xl bg-white py-12 text-center text-sm text-slate-400 ring-1 ring-slate-200">Produk tidak ditemukan.</p>
                {:else}
                    {#each Object.entries(filteredGrouped) as [category, prods] (category)}
                        {#if prods.length > 0}
                            <div class="space-y-3">
                                <h3 class="text-xs font-semibold uppercase tracking-wide text-slate-400">{category}</h3>
                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                    {#each prods as p (p.id)}
                                        <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200 transition hover:shadow-md">
                                            <div class="flex items-start justify-between gap-2">
                                                <div class="min-w-0">
                                                    <p class="truncate font-semibold text-slate-800">{p.name}</p>
                                                    <p class="mt-0.5 text-xs text-slate-400">{p.code ?? ''}</p>
                                                </div>
                                                <span class="shrink-0 rounded-full px-2 py-0.5 text-[11px] font-semibold ring-1 {packagingStyle[p.packaging_type] ?? packagingStyle.zak}">
                                                    {packagingLabel[p.packaging_type] ?? p.packaging_type}
                                                </span>
                                            </div>
                                            <div class="mt-3 flex items-center justify-between">
                                                <p class="text-xs text-slate-500">
                                                    <span class="font-medium text-slate-700">{areaId ? formatRp(unitPrice(p)) : formatRp(p.price)}</span>
                                                    <span class="text-slate-400"> / {p.unit}</span>
                                                </p>
                                                <div class="flex items-center gap-2">
                                                    <button onclick={() => changeQty(p.id, -1)} class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-slate-600 transition hover:bg-slate-200 disabled:opacity-40" disabled={!quantities[p.id]}>−</button>
                                                    <span class="w-7 text-center text-sm font-bold text-slate-800">{quantities[p.id] ?? 0}</span>
                                                    <button onclick={() => changeQty(p.id, 1)} class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary-600 text-white transition hover:bg-primary-700">+</button>
                                                </div>
                                            </div>
                                        </div>
                                    {/each}
                                </div>
                            </div>
                        {/if}
                    {/each}
                {/if}
            </div>

            <div class="lg:sticky lg:top-8">
                <DashboardCard title="Ringkasan Pesanan">
                    <div class="space-y-4">
                        {#if !areaId}
                            <p class="rounded-lg bg-accent-50 px-3 py-2 text-xs font-medium text-accent-700 ring-1 ring-accent-100">Pilih area tujuan kirim dulu — harga & ongkir menyesuaikan.</p>
                        {/if}

                        {#if selected.length > 0}
                            <ul class="space-y-2 border-t border-slate-100 pt-3">
                                {#each selected as p (p.id)}
                                    <li class="flex items-center justify-between gap-2 text-sm">
                                        <span class="min-w-0 truncate text-slate-600">{p.qty}× {p.name}</span>
                                        <span class="shrink-0 font-medium text-slate-800">{formatRp(unitPrice(p) * p.qty)}</span>
                                    </li>
                                {/each}
                            </ul>
                        {/if}

                        <dl class="space-y-1.5 border-t border-slate-100 pt-3 text-sm">
                            <div class="flex justify-between text-slate-600">
                                <dt>Subtotal</dt>
                                <dd class="font-medium text-slate-800">{formatRp(subtotal)}</dd>
                            </div>
                            <div class="flex justify-between text-slate-600">
                                <dt>Ongkir</dt>
                                <dd class="font-medium text-slate-800">{rate > 0 ? `${formatRp(ongkir)} · ${rate.toLocaleString('id-ID')}/zak × ${totalQty}` : formatRp(0)}</dd>
                            </div>
                            <div class="flex justify-between border-t border-slate-100 pt-2 text-base font-bold text-slate-900">
                                <dt>Total</dt>
                                <dd>{formatRp(total)}</dd>
                            </div>
                        </dl>

                        <button onclick={submit} disabled={!areaId || selected.length === 0} class="w-full rounded-xl bg-primary-600 py-2.5 text-sm font-semibold text-white transition hover:bg-primary-700 disabled:cursor-not-allowed disabled:opacity-40">
                            Buat Pesanan
                        </button>
                    </div>
                </DashboardCard>
            </div>
        </div>
    </div>
</AppLayout>
