<script>
    import { router, usePage } from '@inertiajs/svelte';
    import AppLayout from '../../../Layouts/AppLayout.svelte';

    const page = usePage();
    const flash = $derived(page.props.flash?.message);
    const pageErrors = $derived(page.props.errors ?? {});
    const users = $derived(page.props.users ?? []);
    const usersData = $derived(users.data ?? users);
    const areas = $derived(page.props.areas ?? []);
    const filters = $derived(page.props.filters ?? {});

    let search = $state(filters.search ?? '');
    let selectedRole = $state(filters.role ?? '');
    let selectedStatus = $state(filters.status ?? '');

    let showModal = $state(false);
    let editMode = $state(false);
    let form = $state({
        id: null,
        name: '',
        email: '',
        password: '',
        phone: '',
        role: 'customer',
        status: 'active',
        sales_area_id: '',
    });

    function openCreate() {
        form = { id: null, name: '', email: '', password: '', phone: '', role: 'customer', status: 'active', sales_area_id: '' };
        editMode = false;
        showModal = true;
    }

    function openEdit(u) {
        form = {
            id: u.id,
            name: u.name,
            email: u.email,
            password: '',
            phone: u.phone ?? '',
            role: u.role,
            status: u.status,
            sales_area_id: u.sales_area_id ?? '',
        };
        editMode = true;
        showModal = true;
    }

    function closeModal() {
        showModal = false;
    }

    function submit() {
        if (editMode) {
            const data = { ...form };
            if (!data.password) delete data.password;
            router.put('/admin/users-manage/' + form.id, data, { onSuccess: closeModal });
        } else {
            router.post('/admin/users-manage', form, { onSuccess: closeModal });
        }
    }

    function confirmDelete(u) {
        if (confirm('Hapus user "' + u.name + '"?')) {
            router.delete('/admin/users-manage/' + u.id);
        }
    }

    function applyFilter() {
        router.get('/admin/users-manage', {
            search: search || undefined,
            role: selectedRole || undefined,
            status: selectedStatus || undefined,
        });
    }

    function resetFilter() {
        search = '';
        selectedRole = '';
        selectedStatus = '';
        router.get('/admin/users-manage');
    }

    const roleLabel = {
        owner: 'Owner',
        admin: 'Admin',
        sales: 'Sales',
        customer: 'Customer',
    };

    const roleStyle = {
        owner: 'bg-purple-50 text-purple-700 ring-purple-100',
        admin: 'bg-blue-50 text-blue-700 ring-blue-100',
        sales: 'bg-teal-50 text-teal-700 ring-teal-100',
        customer: 'bg-slate-100 text-slate-600 ring-slate-200',
    };

    const statusStyle = {
        pending: 'bg-amber-50 text-amber-700 ring-amber-100',
        active: 'bg-emerald-50 text-emerald-700 ring-emerald-100',
        rejected: 'bg-red-50 text-red-700 ring-red-100',
    };

    const meta = $derived(users.meta ?? null);
</script>

<AppLayout>
    <div>
        {#if flash}
            <div class="mb-5 rounded-xl bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 ring-1 ring-emerald-100">{flash}</div>
        {/if}

        {#if pageErrors.general}
            <div class="mb-5 rounded-xl bg-red-50 px-4 py-3 text-sm font-medium text-red-700 ring-1 ring-red-100">{pageErrors.general}</div>
        {/if}

        <!-- Header -->
        <header class="mb-5 flex flex-col justify-between gap-1 sm:flex-row sm:items-end">
            <div>
                <h2 class="text-xl font-bold text-slate-800">User Management</h2>
                <p class="mt-0.5 text-sm text-slate-500">Kelola akun user — role, status, dan data.</p>
            </div>
            <button onclick={openCreate} class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-700">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="h-4 w-4"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                Tambah User
            </button>
        </header>

        <!-- Filters -->
        <div class="mb-5 flex flex-wrap items-center gap-3">
            <div class="relative min-w-[200px] flex-1">
                <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                </span>
                <input bind:value={search} type="search" placeholder="Cari nama atau email..." onkeydown={(e) => e.key === 'Enter' && applyFilter()} class="w-full rounded-xl border border-slate-200 bg-white py-2 pl-9 pr-3 text-sm text-slate-800 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100" />
            </div>
            <select bind:value={selectedRole} onchange={applyFilter} class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100">
                <option value="">Semua Role</option>
                <option value="owner">Owner</option>
                <option value="admin">Admin</option>
                <option value="sales">Sales</option>
                <option value="customer">Customer</option>
            </select>
            <select bind:value={selectedStatus} onchange={applyFilter} class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100">
                <option value="">Semua Status</option>
                <option value="active">Active</option>
                <option value="pending">Pending</option>
                <option value="rejected">Rejected</option>
            </select>
            <button onclick={applyFilter} class="rounded-xl bg-slate-100 px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-200">Filter</button>
            {#if filters.search || filters.role || filters.status}
                <button onclick={resetFilter} class="text-sm font-medium text-primary-600 transition hover:text-primary-700">Reset</button>
            {/if}
        </div>

        <!-- Table -->
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-left text-xs uppercase tracking-wide text-slate-400">
                            <th class="px-5 py-3.5 font-semibold">Nama</th>
                            <th class="px-5 py-3.5 font-semibold">Email</th>
                            <th class="px-5 py-3.5 font-semibold">Role</th>
                            <th class="px-5 py-3.5 font-semibold">Area</th>
                            <th class="px-5 py-3.5 font-semibold">Status</th>
                            <th class="px-5 py-3.5 text-right font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        {#each usersData as u (u.id)}
                            <tr class="transition hover:bg-slate-50/60">
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        {#if u.avatar}
                                            <img src={u.avatar} alt={u.name} class="h-9 w-9 rounded-full object-cover ring-2 ring-slate-200" />
                                        {:else}
                                            <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-primary-100 text-sm font-bold text-primary-700 ring-2 ring-white">
                                                {((u.name ?? '?')[0] + (u.name ?? '?').split(' ')[1]?.[0] ?? '').toUpperCase()}
                                            </div>
                                        {/if}
                                        <div>
                                            <p class="font-semibold text-slate-800">{u.name}</p>
                                            {#if u.phone}<p class="text-xs text-slate-400">{u.phone}</p>{/if}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-slate-600">{u.email}</td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[11px] font-medium capitalize ring-1 {roleStyle[u.role] ?? roleStyle.customer}">
                                        {roleLabel[u.role] ?? u.role}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    {#if u.area}
                                        <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600">{u.area.name}</span>
                                    {:else}
                                        <span class="text-slate-400">—</span>
                                    {/if}
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-medium capitalize ring-1 {statusStyle[u.status] ?? statusStyle.pending}">
                                        {u.status}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <div class="flex justify-end gap-2">
                                        <button onclick={() => openEdit(u)} class="rounded-lg px-3 py-1.5 text-xs font-semibold text-primary-600 transition hover:bg-primary-50">Edit</button>
                                        <button onclick={() => confirmDelete(u)} class="rounded-lg px-3 py-1.5 text-xs font-semibold text-red-500 transition hover:bg-red-50 hover:text-red-600">Hapus</button>
                                    </div>
                                </td>
                            </tr>
                        {:else}
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center text-sm text-slate-400">Belum ada user. <button onclick={openCreate} class="font-medium text-primary-600 hover:text-primary-700">Tambah user pertama</button></td>
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
                <h3 class="mb-4 text-lg font-bold text-slate-800">{editMode ? 'Edit User' : 'Tambah User'}</h3>

                <form onsubmit={(e) => { e.preventDefault(); submit(); }} class="space-y-4">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700">Nama <span class="text-red-500">*</span></label>
                        <input bind:value={form.name} type="text" required placeholder="Nama lengkap" class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100" />
                        {#if pageErrors.name}<p class="mt-1 text-xs text-red-500">{pageErrors.name}</p>{/if}
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700">Email <span class="text-red-500">*</span></label>
                        <input bind:value={form.email} type="email" required placeholder="email@example.com" class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100" />
                        {#if pageErrors.email}<p class="mt-1 text-xs text-red-500">{pageErrors.email}</p>{/if}
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700">{editMode ? 'Password Baru' : 'Password'} {editMode ? '' : '*'}</label>
                        <input
                            bind:value={form.password}
                            type="password"
                            minlength="6"
                            placeholder={editMode ? 'Kosongkan jika tidak diubah' : 'Min. 6 karakter'}
                            class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100"
                        />
                        {#if pageErrors.password}<p class="mt-1 text-xs text-red-500">{pageErrors.password}</p>{/if}
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700">Role <span class="text-red-500">*</span></label>
                            <select bind:value={form.role} class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100">
                                <option value="customer">Customer</option>
                                <option value="sales">Sales</option>
                                <option value="admin">Admin</option>
                                <option value="owner">Owner</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700">Status</label>
                            <select bind:value={form.status} class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100">
                                <option value="active">Active</option>
                                <option value="pending">Pending</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                    </div>

                    {#if form.role === 'sales'}
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700">Area Tugas</label>
                            <select bind:value={form.sales_area_id} class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100">
                                <option value="">— Semua Area —</option>
                                {#each areas as a (a.id)}<option value={a.id}>{a.name}</option>{/each}
                            </select>
                        </div>
                    {/if}

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700">Telepon</label>
                        <input bind:value={form.phone} type="text" placeholder="08xxxxxxxxxx" class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100" />
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
