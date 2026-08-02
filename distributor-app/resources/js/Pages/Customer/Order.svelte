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

    const rateByArea = $derived(Object.fromEntries(shippingRates.map((r) => [String(r.area_id), Number(r.rate)])));

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
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value);
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
    <div class="mx-auto max-w-7xl">
        <header class="mb-6">
            <h2 class="text-xl font-bold text-slate-800">Buat Pesanan</h2>
            <p class="mt-0.5 text-sm text-slate-500">Pilih produk & tentukan tujuan kirim — ongkir dihitung otomatis.</p>
        </header>

        {#if flash}
            <div class="mb-6 rounded-xl bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 ring-1 ring-emerald-100">{flash}</div>
        {/if}

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="space-y-4 lg:col-span-2">
                {#each products as p (p.id)}
                    <div class="flex items-center justify-between gap-4 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                        <div class="min-w-0">
                            <p class="font-semibold text-slate-800">{p.name}</p>
                            <p class="mt-0.5 text-sm text-slate-500">{formatRp(unitPrice(p))} / {p.unit}</p>
                        </div>
                        <div class="flex shrink-0 items-center gap-3">
                            <button onclick={() => changeQty(p.id, -1)} class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-slate-600 transition hover:bg-slate-200">−</button>
                            <span class="w-8 text-center text-sm font-bold text-slate-800">{quantities[p.id] ?? 0}</span>
                            <button onclick={() => changeQty(p.id, 1)} class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary-600 text-white transition hover:bg-primary-700">+</button>
                        </div>
                    </div>
                {/each}
            </div>

            <DashboardCard title="Ringkasan Pesanan">
                <div class="space-y-4">
                    <div>
                        <label for="order-area" class="mb-1 block text-xs font-medium text-slate-500">Tujuan Kirim (Area)</label>
                        <select id="order-area" bind:value={areaId} class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 focus:border-primary-500 focus:outline-none">
                            <option value="">Pilih area…</option>
                            {#each areas as a (a.id)}
                                <option value={a.id}>{a.name}</option>
                            {/each}
                        </select>
                    </div>

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
                            <dd class="font-medium text-slate-800">{rate > 0 ? formatRp(ongkir) + ' · ' + rate.toLocaleString('id-ID') + '/zak × ' + totalQty : formatRp(0)}</dd>
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
</AppLayout>
