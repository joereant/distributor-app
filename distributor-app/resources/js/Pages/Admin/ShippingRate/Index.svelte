<script>
    import { router, usePage } from '@inertiajs/svelte';
    import AppLayout from '../../../Layouts/AppLayout.svelte';

    const page = usePage();
    const flash = $derived(page.props.flash?.message);
    const errors = $derived(page.props.errors ?? {});
    const rates = $derived(page.props.rates ?? []);
    const ratesData = $derived(rates.data ?? rates);
    const plants = $derived(page.props.plants ?? []);
    const areas = $derived(page.props.areas ?? []);

    let showModal = $state(false);
    let form = $state({ plant_id: '', area_id: '', rate: '' });
    let editTarget = $state(null);
    let editForm = $state({ rate: '' });

    function openCreate() {
        form = { plant_id: '', area_id: '', rate: '' };
        editTarget = null;
        showModal = true;
    }

    function openEdit(r) {
        editTarget = r;
        editForm = { rate: r.rate };
        showModal = true;
    }

    function closeModal() {
        showModal = false;
    }

    function submitCreate() {
        router.post('/admin/shipping-rates', form, { onSuccess: closeModal });
    }

    function submitEdit() {
        router.put(`/admin/shipping-rates/${editTarget.id}`, editForm, { onSuccess: closeModal });
    }

    function confirmDelete(r) {
        if (confirm(`Hapus tarif ongkir ${r.plant?.name} → ${r.area?.name}?`)) {
            router.delete(`/admin/shipping-rates/${r.id}`);
        }
    }

    function formatRp(value) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value ?? 0);
    }

    // Group rates by plant
    const groupedRates = $derived(
        ratesData.reduce((acc, r) => {
            const key = r.plant_id;
            if (!acc[key]) acc[key] = { plant: r.plant, rates: [] };
            acc[key].rates.push(r);
            return acc;
        }, {})
    );

    const meta = $derived(rates.meta ?? null);
</script>

<AppLayout>
    <div>
        {#if flash}
            <div class="mb-5 rounded-xl bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 ring-1 ring-emerald-100">{flash}</div>
        {/if}

        {#if errors.area_id}
            <div class="mb-5 rounded-xl bg-red-50 px-4 py-3 text-sm font-medium text-red-700 ring-1 ring-red-100">{errors.area_id}</div>
        {/if}

        <!-- Header -->
        <header class="mb-5 flex flex-col justify-between gap-1 sm:flex-row sm:items-end">
            <div>
                <h2 class="text-xl font-bold text-slate-800">Tarif Ongkir</h2>
                <p class="mt-0.5 text-sm text-slate-500">Atur tarif pengiriman per pabrik dan area tujuan.</p>
            </div>
            <button onclick={openCreate} class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-700">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="h-4 w-4"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                Tambah Tarif
            </button>
        </header>

        <!-- Grouped by Plant -->
        {#if ratesData.length > 0}
            <div class="space-y-5">
                {#each Object.values(groupedRates) as group (group.plant.id)}
                    <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
                        <!-- Plant header -->
                        <div class="flex items-center gap-3 border-b border-slate-100 bg-slate-50 px-5 py-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-100 text-amber-600">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><path d="M2 20h20"/><path d="M5 20V8l7-5 7 5v12"/><path d="M9 20v-6h6v6"/></svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-800">{group.plant.name}</p>
                                {#if group.plant.location}<p class="text-xs text-slate-500">{group.plant.location}</p>{/if}
                            </div>
                        </div>

                        <!-- Rates table -->
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-slate-100 text-left text-xs uppercase tracking-wide text-slate-400">
                                    <th class="px-5 py-3 font-semibold">Area Tujuan</th>
                                    <th class="px-5 py-3 text-right font-semibold">Tarif / Ton</th>
                                    <th class="px-5 py-3 text-right font-semibold">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                {#each group.rates as r (r.id)}
                                    <tr class="transition hover:bg-slate-50/60">
                                        <td class="px-5 py-3">
                                            <div class="flex items-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4 text-slate-400"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                                <span class="font-medium text-slate-700">{r.area?.name ?? '—'}</span>
                                                {#if r.area?.code}<span class="text-xs text-slate-400">({r.area.code})</span>{/if}
                                            </div>
                                        </td>
                                        <td class="px-5 py-3 text-right font-semibold text-emerald-600">{formatRp(r.rate)}</td>
                                        <td class="px-5 py-3 text-right">
                                            <div class="flex justify-end gap-2">
                                                <button onclick={() => openEdit(r)} class="rounded-lg px-3 py-1.5 text-xs font-semibold text-primary-600 transition hover:bg-primary-50">Edit</button>
                                                <button onclick={() => confirmDelete(r)} class="rounded-lg px-3 py-1.5 text-xs font-semibold text-red-500 transition hover:bg-red-50 hover:text-red-600">Hapus</button>
                                            </div>
                                        </td>
                                    </tr>
                                {/each}
                            </tbody>
                        </table>
                    </div>
                {/each}
            </div>
        {:else}
            <div class="overflow-hidden rounded-2xl bg-white py-16 text-center shadow-sm ring-1 ring-slate-200">
                <p class="text-sm text-slate-400">Belum ada tarif ongkir. <button onclick={openCreate} class="font-medium text-primary-600 hover:text-primary-700">Tambah tarif pertama</button></p>
            </div>
        {/if}

        <!-- Pagination -->
        {#if meta && meta.last_page > 1}
            <div class="mt-4 flex items-center justify-between">
                <p class="text-xs text-slate-500">Menampilkan {meta.from}-{meta.to} dari {meta.total}</p>
                <div class="flex gap-1">
                    {#each meta.links as link (link.label)}
                        <button onclick={() => link.url && router.get(link.url)} class="rounded-lg px-3 py-1.5 text-xs font-medium transition {link.active ? 'bg-primary-600 text-white' : 'text-slate-600 hover:bg-slate-100'}" disabled={!link.url}>{@html link.label}</button>
                    {/each}
                </div>
            </div>
        {/if}
    </div>

    <!-- Modal -->
    {#if showModal}
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-2.5 sm:p-4">
            <div class="w-full max-w-[540px] rounded-2xl bg-white p-4 sm:p-6 shadow-xl">
                {#if editTarget}
                    <h3 class="mb-4 text-lg font-bold text-slate-800">Edit Tarif Ongkir</h3>
                    <p class="mb-4 text-sm text-slate-500">{editTarget.plant?.name} → {editTarget.area?.name}</p>

                    <form onsubmit={(e) => { e.preventDefault(); submitEdit(); }} class="space-y-4">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700">Tarif / Ton <span class="text-red-500">*</span></label>
                            <input bind:value={editForm.rate} type="number" min="0" step="1000" required placeholder="Contoh: 150000" class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100" />
                            {#if errors.rate}<p class="mt-1 text-xs text-red-500">{errors.rate}</p>{/if}
                        </div>
                        <div class="flex justify-end gap-3 pt-2">
                            <button type="button" onclick={closeModal} class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50">Batal</button>
                            <button type="submit" class="rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-primary-700">Simpan</button>
                        </div>
                    </form>
                {:else}
                    <h3 class="mb-4 text-lg font-bold text-slate-800">Tambah Tarif Ongkir</h3>

                    <form onsubmit={(e) => { e.preventDefault(); submitCreate(); }} class="space-y-4">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700">Pabrik / Plant <span class="text-red-500">*</span></label>
                            <select bind:value={form.plant_id} required class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100">
                                <option value="">— Pilih Pabrik —</option>
                                {#each plants as p (p.id)}<option value={p.id}>{p.name}{p.location ? ` (${p.location})` : ''}</option>{/each}
                            </select>
                            {#if errors.plant_id}<p class="mt-1 text-xs text-red-500">{errors.plant_id}</p>{/if}
                        </div>

                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700">Area Tujuan <span class="text-red-500">*</span></label>
                            <select bind:value={form.area_id} required class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100">
                                <option value="">— Pilih Area —</option>
                                {#each areas as a (a.id)}<option value={a.id}>{a.name}{a.region ? ` (${a.region})` : ''}</option>{/each}
                            </select>
                            {#if errors.area_id}<p class="mt-1 text-xs text-red-500">{errors.area_id}</p>{/if}
                        </div>

                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700">Tarif / Ton <span class="text-red-500">*</span></label>
                            <input bind:value={form.rate} type="number" min="0" step="1000" required placeholder="Contoh: 150000" class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100" />
                            {#if errors.rate}<p class="mt-1 text-xs text-red-500">{errors.rate}</p>{/if}
                        </div>

                        <div class="flex justify-end gap-3 pt-2">
                            <button type="button" onclick={closeModal} class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50">Batal</button>
                            <button type="submit" class="rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-primary-700">Tambah</button>
                        </div>
                    </form>
                {/if}
            </div>
        </div>
    {/if}
</AppLayout>
