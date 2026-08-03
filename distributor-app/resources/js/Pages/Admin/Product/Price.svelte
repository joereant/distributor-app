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
    let matrixFilter = $state('');

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

    // Calculations
    const allProductsCount = $derived(() => {
        let count = 0;
        for (const prods of Object.values(groupedProducts)) {
            count += prods.length;
        }
        return count;
    });

    const totalCellsCount = $derived(allProductsCount() * areas.length);
    const filledCellsCount = $derived(() => {
        let filled = 0;
        for (const areaMap of Object.values(priceForm)) {
            for (const val of Object.values(areaMap)) {
                if (val !== undefined && val !== null && val !== '') filled++;
            }
        }
        return filled;
    });

    const fillPercentage = $derived(() => {
        if (totalCellsCount === 0) return 0;
        return ((filledCellsCount() / totalCellsCount) * 100).toFixed(0);
    });

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
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900">Matriks Daftar Harga</h1>
                    <span class="inline-flex items-center rounded-full bg-teal-50 px-2.5 py-0.5 text-xs font-semibold text-teal-700 ring-1 ring-teal-200">{selectedPlant?.name ?? 'Pabrik'}</span>
                </div>
                <p class="mt-1 text-sm text-slate-500">Atur matriks harga jual produk per wilayah distribusi untuk tiap pabrik pengirim.</p>
            </div>
            {#if Object.keys(groupedProducts).length > 0}
                <button
                    onclick={savePrices}
                    class="inline-flex items-center gap-2 rounded-xl bg-teal-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:bg-teal-700 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 active:scale-[0.98]"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    Simpan Perubahan Harga
                </button>
            {/if}
        </header>

        <!-- 2. Executive Summary Metric Strip -->
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 sm:gap-4">
            <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-slate-500">Pabrik Dipilih</span>
                    <span class="rounded-lg bg-teal-50 p-2 text-teal-600">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><path d="M2 20a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8l-7 5V8l-7 5V4a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"/></svg>
                    </span>
                </div>
                <p class="mt-2 text-xl font-bold tracking-tight text-slate-900 truncate">{selectedPlant?.name ?? '—'}</p>
                <span class="text-[11px] font-medium text-slate-400">{selectedPlant?.location ?? 'Pabrik Utama'}</span>
            </div>

            <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-slate-500">Cakupan Wilayah</span>
                    <span class="rounded-lg bg-indigo-50 p-2 text-indigo-600">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
                    </span>
                </div>
                <p class="mt-2 text-2xl font-bold tracking-tight text-slate-900">{areas.length}</p>
                <span class="text-[11px] font-medium text-slate-400">Area pasar terdaftar</span>
            </div>

            <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-slate-500">Katalog Produk</span>
                    <span class="rounded-lg bg-amber-50 p-2 text-amber-600">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/></svg>
                    </span>
                </div>
                <p class="mt-2 text-2xl font-bold tracking-tight text-slate-900">{allProductsCount()}</p>
                <span class="text-[11px] font-medium text-slate-400">Item aktif diset harga</span>
            </div>

            <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-slate-500">Kelengkapan Harga</span>
                    <span class="rounded-lg bg-emerald-50 p-2 text-emerald-600">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    </span>
                </div>
                <p class="mt-2 text-2xl font-bold tracking-tight text-emerald-600">{fillPercentage()}%</p>
                <span class="text-[11px] font-medium text-slate-400">{filledCellsCount()} dari {totalCellsCount} sel terisi</span>
            </div>
        </div>

        <!-- 3. Toolbar & Plant Selection Segmented Pills -->
        <div class="flex flex-col gap-3 rounded-2xl border border-slate-200/80 bg-white p-4 shadow-xs sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-2 overflow-x-auto pb-1 sm:pb-0">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 shrink-0 mr-1">Pilih Pabrik:</span>
                {#each plants as p (p.id)}
                    <button
                        onclick={() => setPlant(p.id)}
                        class="inline-flex items-center gap-2 rounded-xl px-3.5 py-2 text-xs font-semibold transition-all shrink-0 {selectedPlant?.id === p.id ? 'bg-teal-600 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200/70'}"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-3.5 w-3.5 opacity-80"><path d="M2 20a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8l-7 5V8l-7 5V4a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"/></svg>
                        {p.name}
                        {#if p.location}
                            <span class="opacity-75 text-[10px]">({p.location})</span>
                        {/if}
                    </button>
                {/each}
            </div>

            <!-- Fast Matrix Search Filter -->
            <div class="relative min-w-[220px]">
                <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-3.5 w-3.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                </span>
                <input
                    bind:value={matrixFilter}
                    type="search"
                    placeholder="Filter produk di tabel..."
                    class="w-full rounded-xl border border-slate-200 bg-slate-50/50 py-1.5 pl-8 pr-3 text-xs text-slate-800 transition focus:border-teal-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-100"
                />
            </div>
        </div>

        <!-- 4. Matrix Data Table Grid Pro -->
        {#if Object.keys(groupedProducts).length === 0}
            <div class="flex flex-col items-center justify-center rounded-2xl border border-slate-200/80 bg-white py-16 text-center shadow-xs">
                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-teal-50 text-teal-600 ring-8 ring-teal-50/50">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-8 w-8"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/></svg>
                </div>
                <h3 class="mt-4 text-base font-bold text-slate-900">Belum ada produk aktif</h3>
                <p class="mt-1 max-w-sm text-sm text-slate-500">Silakan aktifkan atau buat produk baru di katalog terlebih dahulu.</p>
            </div>
        {:else}
            <div class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-xs">
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50/80 text-slate-500">
                                <th class="sticky left-0 z-20 min-w-[240px] bg-slate-50 px-5 py-3.5 font-bold uppercase tracking-wider text-slate-700 shadow-sm">
                                    Item Produk
                                </th>
                                {#each areas as a (a.id)}
                                    <th class="min-w-[130px] px-3 py-3.5 text-center font-semibold">
                                        <div class="font-bold text-slate-900">{a.name}</div>
                                        <div class="mt-0.5 inline-flex items-center rounded-md bg-slate-200/70 px-1.5 py-0.5 text-[10px] font-mono text-slate-600">{a.code}</div>
                                    </th>
                                {/each}
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            {#each Object.entries(groupedProducts) as [catName, prods] (catName)}
                                {@const filteredProds = prods.filter(p => p.name.toLowerCase().includes(matrixFilter.toLowerCase()))}
                                {#if filteredProds.length > 0}
                                    <!-- Category Header Section -->
                                    <tr class="bg-slate-100/70">
                                        <td colspan={areas.length + 1} class="sticky left-0 z-10 px-5 py-2.5 text-xs font-bold uppercase tracking-wider text-teal-800">
                                            <div class="flex items-center gap-2">
                                                <span class="h-2 w-2 rounded-full bg-teal-600"></span>
                                                {catName}
                                                <span class="rounded-full bg-teal-200/60 px-2 py-0.2 text-[10px] font-semibold text-teal-900">{filteredProds.length} produk</span>
                                            </div>
                                        </td>
                                    </tr>
                                    {#each filteredProds as p (p.id)}
                                        <tr class="group transition-colors hover:bg-teal-50/20">
                                            <!-- Sticky Product Info -->
                                            <td class="sticky left-0 z-10 min-w-[240px] bg-white px-5 py-3 shadow-sm group-hover:bg-slate-50">
                                                <p class="font-bold text-slate-900 group-hover:text-teal-700">{p.name}</p>
                                                <p class="mt-0.5 text-[11px] text-slate-400">
                                                    <span class="font-semibold text-slate-600">{packagingLabel[p.packaging_type] ?? p.packaging_type}</span> · per {p.unit}
                                                </p>
                                            </td>

                                            <!-- Matrix Inputs per Area -->
                                            {#each areas as a (a.id)}
                                                <td class="px-2.5 py-2 text-center">
                                                    <div class="relative">
                                                        <span class="pointer-events-none absolute left-2.5 top-1/2 -translate-y-1/2 text-[10px] font-bold text-slate-400">Rp</span>
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
                                                            class="w-full rounded-xl border border-slate-200/90 bg-white py-1.5 pl-7 pr-2 text-center font-mono text-xs font-semibold text-slate-800 transition focus:border-teal-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-100"
                                                            placeholder="0"
                                                        />
                                                    </div>
                                                </td>
                                            {/each}
                                        </tr>
                                    {/each}
                                {/if}
                            {/each}
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-slate-100 bg-slate-50/50 px-6 py-3 text-xs text-slate-500">
                    💡 <span class="font-semibold">Petunjuk:</span> Ubah nominal pada tiap sel area sesuai harga yang ditetapkan. Klik tombol <span class="font-bold text-teal-700">"Simpan Perubahan Harga"</span> di kanan atas untuk memperbarui database.
                </div>
            </div>
        {/if}
    </div>
</AppLayout>

