<script>
    import { router, usePage } from '@inertiajs/svelte';
    import AppLayout from '../../../Layouts/AppLayout.svelte';

    const page = usePage();
    const flash = $derived(page.props.flash?.message);
    const errors = $derived(page.props.errors ?? {});
    const customers = $derived(page.props.customers ?? []);
    const customersData = $derived(customers.data ?? customers);
    const areas = $derived(page.props.areas ?? []);
    const filters = $derived(page.props.filters ?? {});

    let search = $state(filters.search ?? '');
    let selectedArea = $state(filters.area_id ?? '');
    let selectedStatus = $state(filters.status ?? '');

    let showModal = $state(false);
    let editMode = $state(false);
    let form = $state({
        id: null,
        company_name: '',
        contact_person: '',
        phone: '',
        email: '',
        address: '',
        area_id: '',
        customer_type: '',
        user_id: '',
    });

    function openCreate() {
        form = { id: null, company_name: '', contact_person: '', phone: '', email: '', address: '', area_id: '', customer_type: '', user_id: '' };
        editMode = false;
        showModal = true;
    }

    function openEdit(c) {
        form = {
            id: c.id,
            company_name: c.company_name ?? '',
            contact_person: c.contact_person ?? '',
            phone: c.phone ?? '',
            email: c.email ?? '',
            address: c.address ?? '',
            area_id: c.area_id ?? '',
            customer_type: c.customer_type ?? '',
            user_id: c.user_id ?? '',
        };
        editMode = true;
        showModal = true;
    }

    function closeModal() {
        showModal = false;
    }

    function submit() {
        if (editMode) {
            router.put(`/admin/customers/${form.id}`, form, { onSuccess: closeModal });
        } else {
            router.post('/admin/customers', form, { onSuccess: closeModal });
        }
    }

    function confirmDelete(c) {
        if (confirm(`Hapus customer "${c.company_name}"?`)) {
            router.delete(`/admin/customers/${c.id}`);
        }
    }

    function applyFilter() {
        router.get('/admin/customers', {
            search: search || undefined,
            area_id: selectedArea || undefined,
            status: selectedStatus || undefined,
        });
    }

    function resetFilter() {
        search = '';
        selectedArea = '';
        selectedStatus = '';
        router.get('/admin/customers');
    }

    const customerTypeLabel = {
        ritel: 'Ritel',
        grosir: 'Grosir',
        proyek: 'Proyek',
        distributor: 'Distributor',
    };

    const statusStyle = {
        pending: 'bg-amber-50 text-amber-700 ring-amber-100',
        active: 'bg-emerald-50 text-emerald-700 ring-emerald-100',
        rejected: 'bg-red-50 text-red-700 ring-red-100',
    };

    const meta = $derived(customers.meta ?? null);
</script>

<AppLayout>
    <div>
        {#if flash}
            <div class="mb-5 rounded-xl bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 ring-1 ring-emerald-100">{flash}</div>
        {/if}

        <!-- Header -->
        <header class="mb-5 flex flex-col justify-between gap-1 sm:flex-row sm:items-end">
            <div>
                <h2 class="text-xl font-bold text-slate-800">Customer</h2>
                <p class="mt-0.5 text-sm text-slate-500">Kelola data customer & perusahaan.</p>
            </div>
            <button onclick={openCreate} class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-700">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="h-4 w-4"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                Tambah Customer
            </button>
        </header>

        <!-- Filters -->
        <div class="mb-5 flex flex-wrap items-center gap-3">
            <div class="relative min-w-[200px] flex-1">
                <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                </span>
                <input bind:value={search} type="search" placeholder="Cari nama, email, telepon..." onkeydown={(e) => e.key === 'Enter' && applyFilter()} class="w-full rounded-xl border border-slate-200 bg-white py-2 pl-9 pr-3 text-sm text-slate-800 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100" />
            </div>
            <select bind:value={selectedArea} onchange={applyFilter} class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100">
                <option value="">Semua Area</option>
                {#each areas as a (a.id)}<option value={a.id}>{a.name}</option>{/each}
            </select>
            <select bind:value={selectedStatus} onchange={applyFilter} class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100">
                <option value="">Semua Status</option>
                <option value="active">Active</option>
                <option value="pending">Pending</option>
                <option value="rejected">Rejected</option>
            </select>
            <button onclick={applyFilter} class="rounded-xl bg-slate-100 px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-200">Filter</button>
            {#if filters.search || filters.area_id || filters.status}
                <button onclick={resetFilter} class="text-sm font-medium text-primary-600 transition hover:text-primary-700">Reset</button>
            {/if}
        </div>

        <!-- Table -->
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-left text-xs uppercase tracking-wide text-slate-400">
                            <th class="px-5 py-3.5 font-semibold">Perusahaan</th>
                            <th class="px-5 py-3.5 font-semibold">Kontak</th>
                            <th class="px-5 py-3.5 font-semibold">Area</th>
                            <th class="px-5 py-3.5 font-semibold">Tipe</th>
                            <th class="px-5 py-3.5 font-semibold">Status</th>
                            <th class="px-5 py-3.5 text-right font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        {#each customersData as c (c.id)}
                            <tr class="transition hover:bg-slate-50/60">
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><path d="M6 22V2h12v20"/><path d="M6 12H4v10h16V12h-2"/><path d="M10 6h4"/><path d="M10 10h4"/><path d="M10 14h4"/><path d="M10 18h4"/></svg>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-slate-800">{c.company_name}</p>
                                            <p class="text-xs text-slate-400">{c.user?.email ?? c.email ?? '—'}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5">
                                    <p class="font-medium text-slate-700">{c.contact_person ?? '—'}</p>
                                    <p class="text-xs text-slate-400">{c.phone ?? '—'}</p>
                                </td>
                                <td class="px-5 py-3.5">
                                    {#if c.area}
                                        <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600">{c.area.name}</span>
                                    {:else}
                                        <span class="text-slate-400">—</span>
                                    {/if}
                                </td>
                                <td class="px-5 py-3.5">
                                    {#if c.customer_type}
                                        <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600 capitalize">{customerTypeLabel[c.customer_type] ?? c.customer_type}</span>
                                    {:else}
                                        <span class="text-slate-400">—</span>
                                    {/if}
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-medium capitalize ring-1 {statusStyle[c.user?.status ?? 'pending']}">
                                        {c.user?.status ?? 'pending'}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <div class="flex justify-end gap-2">
                                        <button onclick={() => openEdit(c)} class="rounded-lg px-3 py-1.5 text-xs font-semibold text-primary-600 transition hover:bg-primary-50">Edit</button>
                                        <button onclick={() => confirmDelete(c)} class="rounded-lg px-3 py-1.5 text-xs font-semibold text-red-500 transition hover:bg-red-50 hover:text-red-600">Hapus</button>
                                    </div>
                                </td>
                            </tr>
                        {:else}
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center text-sm text-slate-400">Belum ada customer. <button onclick={openCreate} class="font-medium text-primary-600 hover:text-primary-700">Tambah customer pertama</button></td>
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
                <h3 class="mb-4 text-lg font-bold text-slate-800">{editMode ? 'Edit Customer' : 'Tambah Customer'}</h3>

                <form onsubmit={(e) => { e.preventDefault(); submit(); }} class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700">Nama Perusahaan <span class="text-red-500">*</span></label>
                            <input bind:value={form.company_name} type="text" required placeholder="PT Maju Jaya" class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100" />
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700">Tipe</label>
                            <select bind:value={form.customer_type} class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100">
                                <option value="">— Pilih Tipe —</option>
                                <option value="ritel">Ritel</option>
                                <option value="grosir">Grosir</option>
                                <option value="proyek">Proyek</option>
                                <option value="distributor">Distributor</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700">Contact Person</label>
                            <input bind:value={form.contact_person} type="text" placeholder="Nama kontak" class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100" />
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700">Telepon</label>
                            <input bind:value={form.phone} type="text" placeholder="0812xxxx" class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100" />
                        </div>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700">Email</label>
                        <input bind:value={form.email} type="email" placeholder="email@perusahaan.com" class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100" />
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700">Alamat</label>
                        <textarea bind:value={form.address} rows="2" placeholder="Alamat lengkap..." class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100"></textarea>
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
