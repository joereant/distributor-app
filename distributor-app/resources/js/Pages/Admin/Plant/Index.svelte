<script>
    import { router, usePage } from '@inertiajs/svelte';
    import AppLayout from '../../../Layouts/AppLayout.svelte';

    const page = usePage();
    const flash = $derived(page.props.flash?.message);
    const errors = $derived(page.props.errors ?? {});
    const plants = $derived(page.props.plants ?? []);
    const plantsData = $derived(plants.data ?? plants);
    const areas = $derived(page.props.areas ?? []);

    let showModal = $state(false);
    let editMode = $state(false);
    let form = $state({ id: null, name: '', location: '', area_id: '' });
    let deleteTarget = $state(null);

    function openCreate() {
        form = { id: null, name: '', location: '', area_id: '' };
        editMode = false;
        showModal = true;
    }

    function openEdit(p) {
        form = { id: p.id, name: p.name, location: p.location ?? '', area_id: p.area_id ?? '' };
        editMode = true;
        showModal = true;
    }

    function closeModal() {
        showModal = false;
    }

    function submit() {
        if (editMode) {
            router.put(`/admin/plants/${form.id}`, form, { onSuccess: closeModal });
        } else {
            router.post('/admin/plants', form, { onSuccess: closeModal });
        }
    }

    function confirmDelete(p) {
        if (confirm(`Hapus pabrik "${p.name}"?`)) {
            router.delete(`/admin/plants/${p.id}`);
        }
    }

    const meta = $derived(plants.meta ?? null);
</script>

<AppLayout>
    <div>
        {#if flash}
            <div class="mb-5 rounded-xl bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 ring-1 ring-emerald-100">{flash}</div>
        {/if}

        <!-- Header -->
        <header class="mb-5 flex flex-col justify-between gap-1 sm:flex-row sm:items-end">
            <div>
                <h2 class="text-xl font-bold text-slate-800">Pabrik / Plant</h2>
                <p class="mt-0.5 text-sm text-slate-500">Kelola pabrik & gudang asal pengiriman.</p>
            </div>
            <button onclick={openCreate} class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-700">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="h-4 w-4"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                Tambah Pabrik
            </button>
        </header>

        <!-- Table -->
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-left text-xs uppercase tracking-wide text-slate-400">
                            <th class="px-5 py-3.5 font-semibold">Nama Pabrik</th>
                            <th class="px-5 py-3.5 font-semibold">Lokasi</th>
                            <th class="px-5 py-3.5 font-semibold">Area</th>
                            <th class="px-5 py-3.5 text-right font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        {#each plantsData as p (p.id)}
                            <tr class="transition hover:bg-slate-50/60">
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg bg-amber-50 text-amber-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><path d="M2 20h20"/><path d="M5 20V8l7-5 7 5v12"/><path d="M9 20v-6h6v6"/></svg>
                                        </div>
                                        <span class="font-semibold text-slate-800">{p.name}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-slate-600">{p.location ?? '—'}</td>
                                <td class="px-5 py-3.5">
                                    {#if p.area}
                                        <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600">{p.area.name}</span>
                                    {:else}
                                        <span class="text-slate-400">—</span>
                                    {/if}
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <div class="flex justify-end gap-2">
                                        <button onclick={() => openEdit(p)} class="rounded-lg px-3 py-1.5 text-xs font-semibold text-primary-600 transition hover:bg-primary-50">Edit</button>
                                        <button onclick={() => confirmDelete(p)} class="rounded-lg px-3 py-1.5 text-xs font-semibold text-red-500 transition hover:bg-red-50 hover:text-red-600">Hapus</button>
                                    </div>
                                </td>
                            </tr>
                        {:else}
                            <tr>
                                <td colspan="4" class="px-5 py-12 text-center text-sm text-slate-400">Belum ada pabrik. <button onclick={openCreate} class="font-medium text-primary-600 hover:text-primary-700">Tambah pabrik pertama</button></td>
                            </tr>
                        {/each}
                    </tbody>
                </table>
            </div>
        </div>

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
                <h3 class="mb-4 text-lg font-bold text-slate-800">{editMode ? 'Edit Pabrik' : 'Tambah Pabrik'}</h3>

                <form onsubmit={(e) => { e.preventDefault(); submit(); }} class="space-y-4">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700">Nama Pabrik <span class="text-red-500">*</span></label>
                        <input bind:value={form.name} type="text" required placeholder="Contoh: Plant Tonasa" class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100" />
                        {#if errors.name}<p class="mt-1 text-xs text-red-500">{errors.name}</p>{/if}
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700">Lokasi</label>
                        <input bind:value={form.location} type="text" placeholder="Contoh: Makassar, Sulawesi Selatan" class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100" />
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700">Area</label>
                        <select bind:value={form.area_id} class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100">
                            <option value="">— Pilih Area —</option>
                            {#each areas as a (a.id)}<option value={a.id}>{a.name}</option>{/each}
                        </select>
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" onclick={closeModal} class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50">Batal</button>
                        <button type="submit" class="rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-primary-700">{editMode ? 'Simpan' : 'Tambah'}</button>
                    </div>
                </form>
            </div>
        </div>
    {/if}
</AppLayout>
