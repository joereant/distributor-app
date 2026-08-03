<script>
    import { router, usePage } from '@inertiajs/svelte';
    import AppLayout from '../../../Layouts/AppLayout.svelte';

    const page = usePage();
    const flash = $derived(page.props.flash?.message);
    const errors = $derived(page.props.errors ?? {});
    const areas = $derived(page.props.areas ?? []);
    const areasData = $derived(areas.data ?? areas);

    let showModal = $state(false);
    let editMode = $state(false);
    let form = $state({ id: null, name: '', code: '', region: '' });
    let deleteTarget = $state(null);

    function openCreate() {
        form = { id: null, name: '', code: '', region: '' };
        editMode = false;
        showModal = true;
    }

    function openEdit(a) {
        form = { id: a.id, name: a.name, code: a.code ?? '', region: a.region ?? '' };
        editMode = true;
        showModal = true;
    }

    function closeModal() {
        showModal = false;
    }

    function submit() {
        if (editMode) {
            router.put(`/admin/areas/${form.id}`, form, { onSuccess: closeModal });
        } else {
            router.post('/admin/areas', form, { onSuccess: closeModal });
        }
    }

    function confirmDelete(a) {
        if (confirm(`Hapus area "${a.name}"?`)) {
            router.delete(`/admin/areas/${a.id}`);
        }
    }

    const meta = $derived(areas.meta ?? null);
</script>

<AppLayout>
    <div>
        {#if flash}
            <div class="mb-5 rounded-xl bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 ring-1 ring-emerald-100">{flash}</div>
        {/if}

        <!-- Header -->
        <header class="mb-5 flex flex-col justify-between gap-1 sm:flex-row sm:items-end">
            <div>
                <h2 class="text-xl font-bold text-slate-800">Area</h2>
                <p class="mt-0.5 text-sm text-slate-500">Kelola area distribusi.</p>
            </div>
            <button onclick={openCreate} class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-700">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="h-4 w-4"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                Tambah Area
            </button>
        </header>

        <!-- Table -->
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-left text-xs uppercase tracking-wide text-slate-400">
                            <th class="px-5 py-3.5 font-semibold">Nama Area</th>
                            <th class="px-5 py-3.5 font-semibold">Kode</th>
                            <th class="px-5 py-3.5 font-semibold">Wilayah</th>
                            <th class="px-5 py-3.5 text-right font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        {#each areasData as a (a.id)}
                            <tr class="transition hover:bg-slate-50/60">
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg bg-primary-50 text-primary-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                        </div>
                                        <span class="font-semibold text-slate-800">{a.name}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="rounded bg-slate-100 px-2 py-0.5 text-xs font-mono font-medium text-slate-600">{a.code ?? '—'}</span>
                                </td>
                                <td class="px-5 py-3.5 text-slate-600">{a.region ?? '—'}</td>
                                <td class="px-5 py-3.5 text-right">
                                    <div class="flex justify-end gap-2">
                                        <button onclick={() => openEdit(a)} class="rounded-lg px-3 py-1.5 text-xs font-semibold text-primary-600 transition hover:bg-primary-50">Edit</button>
                                        {#if (a.product_prices_count ?? 0) === 0}
                                            <button onclick={() => confirmDelete(a)} class="rounded-lg px-3 py-1.5 text-xs font-semibold text-red-500 transition hover:bg-red-50 hover:text-red-600">Hapus</button>
                                        {/if}
                                    </div>
                                </td>
                            </tr>
                        {:else}
                            <tr>
                                <td colspan="4" class="px-5 py-12 text-center text-sm text-slate-400">Belum ada area. <button onclick={openCreate} class="font-medium text-primary-600 hover:text-primary-700">Tambah area pertama</button></td>
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
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
                <h3 class="mb-4 text-lg font-bold text-slate-800">{editMode ? 'Edit Area' : 'Tambah Area'}</h3>

                <form onsubmit={(e) => { e.preventDefault(); submit(); }} class="space-y-4">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700">Nama Area <span class="text-red-500">*</span></label>
                        <input bind:value={form.name} type="text" required placeholder="Contoh: Jakarta Selatan" class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100" />
                        {#if errors.name}<p class="mt-1 text-xs text-red-500">{errors.name}</p>{/if}
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700">Kode</label>
                        <input bind:value={form.code} type="text" placeholder="Contoh: JKS" maxlength="20" class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100" />
                        {#if errors.code}<p class="mt-1 text-xs text-red-500">{errors.code}</p>{/if}
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700">Wilayah</label>
                        <input bind:value={form.region} type="text" placeholder="Contoh: DKI Jakarta" class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100" />
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
