<script>
    import { router, usePage } from '@inertiajs/svelte';
    import AppLayout from '../../../Layouts/AppLayout.svelte';

    const page = usePage();
    const flash = $derived(page.props.flash?.message);

    const plants = $derived(page.props.plants ?? []);
    const selectedPlant = $derived(page.props.selected_plant);
    const areas = $derived(page.props.areas ?? []);
    const groupedProducts = $derived(page.props.grouped_products ?? {});
    const priceMap = $derived(page.props.prices ?? {});

    const packagingLabel = { zak: 'Zak', ton_bag: 'Ton Bag', bulk: 'Curah' };

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

    $effect(() => {
        if (Object.keys(groupedProducts).length > 0) {
            initPriceForm();
        }
    });

    function setPlant(id) {
        router.get('/admin/products/prices', { plant: id });
    }

    function savePrices() {
        router.post('/admin/products/prices', {
            plant_id: selectedPlant?.id,
            prices: priceForm,
        }, {
            onSuccess: () => initPriceForm(),
        });
    }
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
                <h2 class="text-xl font-bold text-slate-800">Daftar Harga</h2>
                <p class="mt-0.5 text-sm text-slate-500">Kelola daftar harga per produk per area transaksi.</p>
            </div>
            {#if Object.keys(groupedProducts).length > 0}
                <button onclick={savePrices} class="rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-700">
                    Simpan Harga
                </button>
            {/if}
        </header>

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
                                                    if (!priceForm[p.id]) priceForm[p.id] = {};
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
            <p class="mt-2 text-xs text-slate-400">Klik sel untuk edit harga per produk per area. Klik "Simpan Harga" di kanan atas untuk menyimpan perubahan.</p>
        {/if}
    </div>
</AppLayout>
