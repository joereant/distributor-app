<script>
    import { router, usePage } from '@inertiajs/svelte';
    import AppLayout from '../../../Layouts/AppLayout.svelte';
    import SidePanel from '../../../Components/SidePanel.svelte';

    const page = usePage();
    const flash = $derived(page.props.flash?.message);
    const pageErrors = $derived(page.props.errors ?? {});
    const users = $derived(page.props.users ?? []);
    const usersData = $derived(users.data ?? users);
    const areas = $derived(page.props.areas ?? []);
    const stats = $derived(page.props.stats ?? { total: 0, active: 0, pending: 0, sales: 0 });
    const filters = $derived(page.props.filters ?? {});

    let search = $state(filters.search ?? '');
    let selectedRole = $state(filters.role ?? '');
    let selectedStatus = $state(filters.status ?? '');

    // Multiselect state
    let selectedIds = $state([]);
    let bulkRole = $state('');

    const allSelected = $derived(
        usersData.length > 0 && usersData.every((u) => selectedIds.includes(u.id))
    );

    function toggleSelectAll() {
        if (allSelected) {
            selectedIds = [];
        } else {
            selectedIds = usersData.map((u) => u.id);
        }
    }

    function toggleSelect(id, e) {
        e.stopPropagation();
        if (selectedIds.includes(id)) {
            selectedIds = selectedIds.filter((item) => item !== id);
        } else {
            selectedIds = [...selectedIds, id];
        }
    }

    function executeBulkAction(action, roleValue = '') {
        if (selectedIds.length === 0) return;
        router.post('/admin/users-manage/bulk-action', {
            ids: selectedIds,
            action: action,
            role: roleValue || undefined,
        }, {
            onSuccess: () => {
                selectedIds = [];
                bulkRole = '';
            }
        });
    }

    // Auto-dismiss flash message
    let flashDismissed = $state(false);
    $effect(() => {
        if (flash) {
            flashDismissed = false;
            const timer = setTimeout(() => {
                flashDismissed = true;
            }, 3500);
            return () => clearTimeout(timer);
        }
    });

    // SidePanel State
    let sidePanelOpen = $state(false);
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
        sidePanelOpen = true;
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
            sales_area_id: u.sales_area_id ?? (u.area?.id ?? ''),
        };
        editMode = true;
        sidePanelOpen = true;
    }

    function closePanel() {
        sidePanelOpen = false;
    }

    function submit() {
        if (editMode) {
            const data = { ...form };
            if (!data.password) delete data.password;
            router.put('/admin/users-manage/' + form.id, data, { onSuccess: closePanel });
        } else {
            router.post('/admin/users-manage', form, { onSuccess: closePanel });
        }
    }

    function updateStatus(newStatus) {
        form.status = newStatus;
        submit();
    }

    function applyFilter() {
        router.get('/admin/users-manage', {
            search: search || undefined,
            role: selectedRole || undefined,
            status: selectedStatus || undefined,
        }, { preserveState: true });
    }

    function resetFilter() {
        search = '';
        selectedRole = '';
        selectedStatus = '';
        selectedIds = [];
        router.get('/admin/users-manage');
    }

    const roleLabel = {
        owner: 'Owner',
        admin: 'Admin',
        sales: 'Sales',
        customer: 'Customer',
    };

    const roleStyle = {
        owner: 'bg-purple-50 text-purple-700 ring-purple-200/80',
        admin: 'bg-blue-50 text-blue-700 ring-blue-200/80',
        sales: 'bg-teal-50 text-teal-700 ring-teal-200/80',
        customer: 'bg-slate-100 text-slate-700 ring-slate-200',
    };

    const statusStyle = {
        pending: 'bg-amber-50 text-amber-700 ring-amber-200/80',
        active: 'bg-emerald-50 text-emerald-700 ring-emerald-200/80',
        rejected: 'bg-red-50 text-red-700 ring-red-200/80',
    };

    const statusDot = {
        pending: 'bg-amber-500 animate-pulse',
        active: 'bg-emerald-500',
        rejected: 'bg-red-500',
    };

    const meta = $derived(users.meta ?? null);
</script>

<AppLayout>
    <div class="space-y-6">
        <!-- Flash Alert with Auto-Dismiss -->
        {#if flash && !flashDismissed}
            <div class="flex items-center justify-between rounded-xl bg-emerald-50 border border-emerald-200/80 px-4 py-3 text-sm font-medium text-emerald-800 shadow-sm transition-all duration-300">
                <div class="flex items-center gap-2.5">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5 text-emerald-600"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    <span>{flash}</span>
                </div>
                <button onclick={() => (flashDismissed = true)} class="rounded-lg p-1 text-emerald-600 transition hover:bg-emerald-100 hover:text-emerald-900" aria-label="Tutup">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>
            </div>
        {/if}

        {#if pageErrors.general}
            <div class="flex items-center justify-between rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm font-medium text-red-800 shadow-sm">
                <div class="flex items-center gap-2.5">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5 text-red-600"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <span>{pageErrors.general}</span>
                </div>
            </div>
        {/if}

        <!-- Header Title -->
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Manajemen Pengguna</h1>
                <p class="mt-1 text-sm text-slate-500">Klik baris pengguna untuk melihat & mengedit detail. Pilih centang untuk aksi masal.</p>
            </div>
            <button
                onclick={openCreate}
                class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:bg-primary-700 hover:shadow focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
            >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
                <span>Tambah Pengguna</span>
            </button>
        </div>

        <!-- PILAR 1: Executive Summary Strip (KPI Stat Cards) -->
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
            <!-- Card 1: Total Pengguna -->
            <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm transition-all hover:shadow-md">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total User</span>
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                </div>
                <div class="mt-3 flex items-baseline justify-between">
                    <span class="text-2xl font-bold text-slate-900">{stats.total}</span>
                    <span class="text-xs font-medium text-slate-400">terdaftar</span>
                </div>
            </div>

            <!-- Card 2: User Aktif -->
            <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm transition-all hover:shadow-md">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">User Aktif</span>
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg>
                    </div>
                </div>
                <div class="mt-3 flex items-baseline justify-between">
                    <span class="text-2xl font-bold text-emerald-600">{stats.active}</span>
                    <span class="text-xs font-medium text-emerald-600/80">aktif & diverifikasi</span>
                </div>
            </div>

            <!-- Card 3: Menunggu Verifikasi -->
            <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm transition-all hover:shadow-md">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Pending Approval</span>
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                </div>
                <div class="mt-3 flex items-baseline justify-between">
                    <span class="text-2xl font-bold text-amber-600">{stats.pending}</span>
                    <span class="text-xs font-medium text-amber-600/80">perlu tindakan</span>
                </div>
            </div>

            <!-- Card 4: Tim Sales -->
            <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm transition-all hover:shadow-md">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Tim Sales</span>
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-teal-50 text-teal-600">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                    </div>
                </div>
                <div class="mt-3 flex items-baseline justify-between">
                    <span class="text-2xl font-bold text-teal-600">{stats.sales}</span>
                    <span class="text-xs font-medium text-teal-600/80">personel lapangan</span>
                </div>
            </div>
        </div>

        <!-- PILAR 2: Control Center Toolbar -->
        <div class="flex flex-col gap-3 rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-1 flex-col gap-3 sm:flex-row sm:items-center">
                <!-- Live Search input -->
                <div class="relative flex-1 min-w-[240px]">
                    <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    </span>
                    <input
                        bind:value={search}
                        type="search"
                        placeholder="Cari berdasarkan nama atau email..."
                        onkeydown={(e) => e.key === 'Enter' && applyFilter()}
                        class="w-full rounded-xl border border-slate-200 bg-slate-50/50 py-2 pl-10 pr-9 text-sm text-slate-800 transition placeholder:text-slate-400 focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-100"
                    />
                    {#if search}
                        <button onclick={() => { search = ''; applyFilter(); }} class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600" aria-label="Bersihkan pencarian">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                        </button>
                    {/if}
                </div>

                <!-- Dropdown Role Filter -->
                <select
                    bind:value={selectedRole}
                    onchange={applyFilter}
                    class="rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2 text-sm font-medium text-slate-700 transition focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-100"
                >
                    <option value="">Semua Role</option>
                    <option value="owner">Owner</option>
                    <option value="admin">Admin</option>
                    <option value="sales">Sales</option>
                    <option value="customer">Customer</option>
                </select>

                <!-- Dropdown Status Filter -->
                <select
                    bind:value={selectedStatus}
                    onchange={applyFilter}
                    class="rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2 text-sm font-medium text-slate-700 transition focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-100"
                >
                    <option value="">Semua Status</option>
                    <option value="active">Aktif</option>
                    <option value="pending">Pending</option>
                    <option value="rejected">Nonaktif / Ditolak</option>
                </select>

                <!-- Action Filter Buttons -->
                <button
                    onclick={applyFilter}
                    class="rounded-xl bg-slate-800 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-900"
                >
                    Terapkan
                </button>

                {#if filters.search || filters.role || filters.status}
                    <button
                        onclick={resetFilter}
                        class="rounded-xl border border-slate-200 px-3.5 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-50 hover:text-slate-900"
                    >
                        Reset Filter
                    </button>
                {/if}
            </div>
        </div>

        <!-- FLOATING BULK ACTION BAR -->
        {#if selectedIds.length > 0}
            <div class="sticky top-4 z-20 flex flex-wrap items-center justify-between gap-3 rounded-2xl bg-slate-900 p-3.5 text-white shadow-xl ring-1 ring-slate-800 transition-all duration-300">
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center rounded-full bg-primary-500/20 px-3 py-1 text-xs font-bold text-primary-300 ring-1 ring-primary-500/40">
                        {selectedIds.length} Diterapkan
                    </span>
                    <span class="text-xs font-medium text-slate-300">Aksi Masal:</span>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <button
                        onclick={() => executeBulkAction('activate')}
                        class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 px-3.5 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:bg-emerald-500"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        <span>Aktifkan Masal</span>
                    </button>

                    <button
                        onclick={() => executeBulkAction('deactivate')}
                        class="inline-flex items-center gap-1.5 rounded-xl bg-slate-800 px-3.5 py-1.5 text-xs font-semibold text-slate-300 border border-slate-700 transition hover:bg-slate-700 hover:text-white"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                        <span>Nonaktifkan Masal</span>
                    </button>

                    <!-- Change Role Bulk Dropdown -->
                    <div class="flex items-center gap-1 bg-slate-800 rounded-xl px-2 py-0.5 border border-slate-700">
                        <select
                            bind:value={bulkRole}
                            class="bg-transparent py-1 text-xs font-medium text-slate-200 focus:outline-none"
                        >
                            <option value="" class="bg-slate-900 text-slate-300">— Ubah Role ke —</option>
                            <option value="customer" class="bg-slate-900 text-slate-200">Customer</option>
                            <option value="sales" class="bg-slate-900 text-slate-200">Sales</option>
                            <option value="admin" class="bg-slate-900 text-slate-200">Admin</option>
                            <option value="owner" class="bg-slate-900 text-slate-200">Owner</option>
                        </select>
                        {#if bulkRole}
                            <button
                                onclick={() => executeBulkAction('change_role', bulkRole)}
                                class="rounded-lg bg-primary-600 px-2 py-1 text-xs font-bold text-white transition hover:bg-primary-500"
                            >
                                Set
                            </button>
                        {/if}
                    </div>

                    <button
                        onclick={() => (selectedIds = [])}
                        class="ml-2 text-xs font-medium text-slate-400 hover:text-white"
                    >
                        Batal
                    </button>
                </div>
            </div>
        {/if}

        <!-- PILAR 3: Data Hierarchy & Micro-details (User Table) -->
        <div class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/60 text-xs font-semibold uppercase tracking-wider text-slate-500">
                            <!-- Multiselect Checkbox Header -->
                            <th class="w-12 px-4 py-4 text-center">
                                <input
                                    type="checkbox"
                                    checked={allSelected}
                                    onchange={toggleSelectAll}
                                    class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500"
                                    aria-label="Pilih Semua Pengguna"
                                />
                            </th>
                            <th class="px-5 py-4">Pengguna</th>
                            <th class="px-5 py-4">Role</th>
                            <th class="px-5 py-4">Area Tugas Sales</th>
                            <th class="px-5 py-4">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        {#each usersData as u (u.id)}
                            <tr
                                onclick={() => openEdit(u)}
                                class="group cursor-pointer transition-colors duration-150 hover:bg-slate-50/90 {selectedIds.includes(u.id) ? 'bg-primary-50/40' : ''}"
                            >
                                <!-- Multiselect Checkbox Row -->
                                <td class="w-12 px-4 py-3.5 text-center" onclick={(e) => e.stopPropagation()}>
                                    <input
                                        type="checkbox"
                                        checked={selectedIds.includes(u.id)}
                                        onchange={(e) => toggleSelect(u.id, e)}
                                        class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500"
                                        aria-label={`Pilih ${u.name}`}
                                    />
                                </td>

                                <!-- Col 1: Pengguna Info -->
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3.5">
                                        <div class="relative shrink-0">
                                            {#if u.avatar}
                                                <img src={u.avatar} alt={u.name} class="h-10 w-10 rounded-full object-cover ring-2 ring-slate-100" />
                                            {:else}
                                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 font-bold text-slate-700 ring-2 ring-slate-200/60">
                                                    {((u.name ?? '?')[0] + ((u.name ?? '?').split(' ')[1]?.[0] ?? '')).toUpperCase()}
                                                </div>
                                            {/if}
                                            <!-- Glowing Status Dot Indicator -->
                                            <span
                                                class="absolute bottom-0 right-0 h-3 w-3 rounded-full ring-2 ring-white {statusDot[u.status] ?? statusDot.pending}"
                                                title={`Status: ${u.status}`}
                                            ></span>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-slate-900 group-hover:text-primary-600 transition-colors">{u.name}</p>
                                            <p class="text-xs text-slate-500">{u.email}</p>
                                            {#if u.phone}
                                                <p class="mt-0.5 text-[11px] text-slate-400">📞 {u.phone}</p>
                                            {/if}
                                        </div>
                                    </div>
                                </td>

                                <!-- Col 2: Role Badge -->
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold capitalize ring-1 ring-inset {roleStyle[u.role] ?? roleStyle.customer}">
                                        {roleLabel[u.role] ?? u.role}
                                    </span>
                                </td>

                                <!-- Col 3: Area Sales -->
                                <td class="px-5 py-3.5">
                                    {#if u.area}
                                        <div class="inline-flex items-center gap-1.5 rounded-lg bg-teal-50/80 px-2.5 py-1 text-xs font-medium text-teal-700 ring-1 ring-teal-200/60">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-3.5 w-3.5"><path d="M20 10c0 6-8 12-8 12s-8-6-8-10a8 8 0 0 1 16 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                            <span>{u.area.name}</span>
                                        </div>
                                    {:else if u.role === 'sales'}
                                        <span class="text-xs font-medium italic text-amber-600">Belum diassign</span>
                                    {:else}
                                        <span class="text-slate-400 text-xs">—</span>
                                    {/if}
                                </td>

                                <!-- Col 4: Status -->
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium capitalize ring-1 ring-inset {statusStyle[u.status] ?? statusStyle.pending}">
                                        <span class="h-1.5 w-1.5 rounded-full {statusDot[u.status] ?? 'bg-slate-400'}"></span>
                                        {u.status === 'rejected' ? 'Nonaktif' : u.status}
                                    </span>
                                </td>
                            </tr>
                        {:else}
                            <!-- PILAR 5: Illustrative Empty State -->
                            <tr>
                                <td colspan="5" class="px-5 py-12 text-center">
                                    <div class="mx-auto flex max-w-sm flex-col items-center justify-center text-center">
                                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-8 w-8"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6"/><path d="m9 9 6 6"/></svg>
                                        </div>
                                        <h3 class="mt-4 text-base font-semibold text-slate-900">Pengguna Tidak Ditemukan</h3>
                                        <p class="mt-1 text-sm text-slate-500">Tidak ada pengguna yang cocok dengan kata kunci pencarian atau filter yang dipilih.</p>
                                        <div class="mt-5 flex gap-3">
                                            {#if filters.search || filters.role || filters.status}
                                                <button onclick={resetFilter} class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Reset Filter</button>
                                            {/if}
                                            <button onclick={openCreate} class="rounded-xl bg-primary-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-primary-700">Tambah User Baru</button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        {/each}
                    </tbody>
                </table>
            </div>

            <!-- Footer Pagination -->
            {#if meta && meta.last_page > 1}
                <div class="flex flex-col items-center justify-between gap-3 border-t border-slate-100 bg-slate-50/50 px-5 py-4 sm:flex-row">
                    <p class="text-xs text-slate-500">Menampilkan <span class="font-semibold text-slate-800">{meta.from}</span> sampai <span class="font-semibold text-slate-800">{meta.to}</span> dari <span class="font-semibold text-slate-800">{meta.total}</span> pengguna</p>
                    <div class="flex items-center gap-1">
                        {#each meta.links as link (link.label)}
                            <button
                                onclick={() => link.url && router.get(link.url)}
                                class="rounded-lg px-3 py-1.5 text-xs font-medium transition-all {link.active ? 'bg-primary-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-200/60'}"
                                disabled={!link.url}
                            >
                                {@html link.label}
                            </button>
                        {/each}
                    </div>
                </div>
            {/if}
        </div>
    </div>

    <!-- PILAR 4: Contextual SidePanel (540px) -->
    <SidePanel
        open={sidePanelOpen}
        title={editMode ? 'Detail & Edit Pengguna' : 'Tambah Pengguna Baru'}
        subtitle={editMode ? 'Lihat profil lengkap, kelola role, atau ubah status keaktifan' : 'Buat akun pengguna baru dalam sistem'}
        size="md"
        onclose={closePanel}
    >
        <form onsubmit={(e) => { e.preventDefault(); submit(); }} class="space-y-4">
            <!-- Quick Status Toggle Action Bar di SidePanel -->
            {#if editMode}
                <div class="flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50/80 p-3">
                    <div class="flex items-center gap-2">
                        <span class="h-2.5 w-2.5 rounded-full {statusDot[form.status] ?? 'bg-slate-400'}"></span>
                        <span class="text-xs font-bold text-slate-800 capitalize">Status: {form.status === 'rejected' ? 'Nonaktif' : form.status}</span>
                    </div>
                    <div>
                        {#if form.status === 'active'}
                            <button
                                type="button"
                                onclick={() => updateStatus('rejected')}
                                class="rounded-lg bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-600 border border-red-200 transition hover:bg-red-100"
                            >
                                Nonaktifkan Akun
                            </button>
                        {:else}
                            <button
                                type="button"
                                onclick={() => updateStatus('active')}
                                class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-emerald-700 shadow-sm"
                            >
                                Setujui & Aktifkan
                            </button>
                        {/if}
                    </div>
                </div>
            {/if}

            <!-- Nama -->
            <div>
                <label for="form_name" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-700">Nama Lengkap <span class="text-red-500">*</span></label>
                <input
                    id="form_name"
                    bind:value={form.name}
                    type="text"
                    required
                    placeholder="Contoh: Budi Santoso"
                    class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-sm text-slate-900 transition focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-100"
                />
                {#if pageErrors.name}<p class="mt-1 text-xs text-red-500">{pageErrors.name}</p>{/if}
            </div>

            <!-- Email -->
            <div>
                <label for="form_email" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-700">Alamat Email <span class="text-red-500">*</span></label>
                <input
                    id="form_email"
                    bind:value={form.email}
                    type="email"
                    required
                    placeholder="email@perusahaan.com"
                    class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-sm text-slate-900 transition focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-100"
                />
                {#if pageErrors.email}<p class="mt-1 text-xs text-red-500">{pageErrors.email}</p>{/if}
            </div>

            <!-- Password -->
            <div>
                <label for="form_password" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-700">
                    {editMode ? 'Password Baru (Opsional)' : 'Password *'}
                </label>
                <input
                    id="form_password"
                    bind:value={form.password}
                    type="password"
                    minlength="6"
                    placeholder={editMode ? 'Biarkan kosong jika tidak diubah' : 'Minimal 6 karakter'}
                    class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-sm text-slate-900 transition focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-100"
                />
                {#if pageErrors.password}<p class="mt-1 text-xs text-red-500">{pageErrors.password}</p>{/if}
            </div>

            <!-- Grid Role & Status -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="form_role" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-700">Peran (Role) <span class="text-red-500">*</span></label>
                    <select
                        id="form_role"
                        bind:value={form.role}
                        class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-sm font-medium text-slate-900 transition focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-100"
                    >
                        <option value="customer">Customer</option>
                        <option value="sales">Sales</option>
                        <option value="admin">Admin</option>
                        <option value="owner">Owner</option>
                    </select>
                </div>
                <div>
                    <label for="form_status" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-700">Status Akun</label>
                    <select
                        id="form_status"
                        bind:value={form.status}
                        class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-sm font-medium text-slate-900 transition focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-100"
                    >
                        <option value="active">Active (Aktif)</option>
                        <option value="pending">Pending</option>
                        <option value="rejected">Rejected (Nonaktif)</option>
                    </select>
                </div>
            </div>

            <!-- Conditional Area Tugas jika role == 'sales' -->
            {#if form.role === 'sales'}
                <div class="rounded-xl border border-teal-200 bg-teal-50/40 p-3.5 space-y-2 transition-all">
                    <label for="form_sales_area" class="block text-xs font-semibold uppercase tracking-wider text-teal-800">Penugasan Area Sales</label>
                    <select
                        id="form_sales_area"
                        bind:value={form.sales_area_id}
                        class="w-full rounded-xl border border-teal-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 shadow-sm transition focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-100"
                    >
                        <option value="">— Pilih Area Tugas —</option>
                        {#each areas as a (a.id)}
                            <option value={a.id}>{a.name}</option>
                        {/each}
                    </select>
                    <p class="text-[11px] text-teal-600">User sales hanya dapat mengakses data pelanggan & transaksi di area ini.</p>
                </div>
            {/if}

            <!-- Telepon -->
            <div>
                <label for="form_phone" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-700">Nomor Telepon / WhatsApp</label>
                <input
                    id="form_phone"
                    bind:value={form.phone}
                    type="text"
                    placeholder="08xxxxxxxxxx"
                    class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-sm text-slate-900 transition focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-100"
                />
            </div>

            <!-- Buttons -->
            <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                <button
                    type="button"
                    onclick={closePanel}
                    class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
                >
                    Batal
                </button>
                <button
                    type="submit"
                    class="rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                >
                    {editMode ? 'Simpan Perubahan' : 'Tambah User'}
                </button>
            </div>
        </form>
    </SidePanel>
</AppLayout>
