<script>
    import { router, usePage } from '@inertiajs/svelte';
    import AppLayout from '../../../Layouts/AppLayout.svelte';

    const page = usePage();
    const transactions = $derived(page.props.transactions ?? []);
    const transactionsData = $derived(transactions.data ?? transactions);
    const stats = $derived(page.props.stats ?? {});
    const areas = $derived(page.props.areas ?? []);
    const filters = $derived(page.props.filters ?? {});

    let startDate = $state(filters.start_date ?? '');
    let endDate = $state(filters.end_date ?? '');
    let areaId = $state(filters.area_id ?? '');
    let status = $state(filters.status ?? '');
    let sort = $state(filters.sort ?? 'date_desc');

    function applyFilter() {
        router.get('/admin/reports', {
            start_date: startDate || undefined,
            end_date: endDate || undefined,
            area_id: areaId || undefined,
            status: status || undefined,
            sort: sort !== 'date_desc' ? sort : undefined,
        });
    }

    function resetFilter() {
        startDate = '';
        endDate = '';
        areaId = '';
        status = '';
        sort = 'date_desc';
        router.get('/admin/reports');
    }

    function exportCsv() {
        const params = new URLSearchParams();
        if (startDate) params.set('start_date', startDate);
        if (endDate) params.set('end_date', endDate);
        if (areaId) params.set('area_id', areaId);
        if (status) params.set('status', status);
        window.location.href = '/admin/reports/export?' + params.toString();
    }

    function formatRp(value) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value ?? 0);
    }

    function formatDate(dateStr) {
        if (!dateStr) return '—';
        return new Date(dateStr).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
    }

    const statusStyle = {
        pending: 'bg-amber-50 text-amber-700 ring-amber-100',
        approved: 'bg-emerald-50 text-emerald-700 ring-emerald-100',
        rejected: 'bg-red-50 text-red-700 ring-red-100',
    };

    const meta = $derived(transactions.meta ?? null);
</script>

<AppLayout>
    <div>
        <!-- Header -->
        <header class="mb-5 flex flex-col justify-between gap-1 sm:flex-row sm:items-end">
            <div>
                <h2 class="text-xl font-bold text-slate-800">Laporan Transaksi</h2>
                <p class="mt-0.5 text-sm text-slate-500">Filter & laporan penjualan.</p>
            </div>
            <button onclick={exportCsv} class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="h-4 w-4"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Export CSV
            </button>
        </header>

        <!-- Stats Summary -->
        <div class="mb-5 grid grid-cols-2 gap-4 lg:grid-cols-5">
            <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Total Order</p>
                <p class="mt-1 text-2xl font-bold text-slate-800">{stats.total_orders ?? 0}</p>
            </div>
            <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Total Penjualan</p>
                <p class="mt-1 text-xl font-bold text-slate-800">{formatRp(stats.total_sales)}</p>
            </div>
            <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Total Ongkir</p>
                <p class="mt-1 text-xl font-bold text-slate-800">{formatRp(stats.total_ongkir)}</p>
            </div>
            <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Total Proyeksi Margin</p>
                <p class="mt-1 text-xl font-bold text-emerald-600">{formatRp(stats.total_margin)}</p>
            </div>
            <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Rata-rata Margin</p>
                <p class="mt-1 text-xl font-bold text-blue-600">{formatRp(stats.avg_margin)}</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="mb-5 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
            <div class="flex flex-wrap items-end gap-3">
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-500">Dari Tanggal</label>
                    <input bind:value={startDate} type="date" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100" />
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-500">Sampai Tanggal</label>
                    <input bind:value={endDate} type="date" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100" />
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-500">Area</label>
                    <select bind:value={areaId} class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100">
                        <option value="">Semua Area</option>
                        {#each areas as a (a.id)}<option value={a.id}>{a.name}</option>{/each}
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-500">Status</label>
                    <select bind:value={status} class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100">
                        <option value="">Semua</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-500">Urutkan</label>
                    <select bind:value={sort} class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100">
                        <option value="date_desc">Terbaru</option>
                        <option value="date_asc">Terlama</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button onclick={applyFilter} class="rounded-xl bg-primary-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-primary-700">Filter</button>
                    <button onclick={resetFilter} class="rounded-xl bg-slate-100 px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-200">Reset</button>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-left text-xs uppercase tracking-wide text-slate-400">
                            <th class="px-5 py-3.5 font-semibold">ID</th>
                            <th class="px-5 py-3.5 font-semibold">Tanggal</th>
                            <th class="px-5 py-3.5 font-semibold">Customer</th>
                            <th class="px-5 py-3.5 font-semibold">Area</th>
                            <th class="px-5 py-3.5 text-right font-semibold">Subtotal</th>
                            <th class="px-5 py-3.5 text-right font-semibold">Ongkir</th>
                            <th class="px-5 py-3.5 text-right font-semibold">Total</th>
                            <th class="px-5 py-3.5 text-right font-semibold">Margin</th>
                            <th class="px-5 py-3.5 font-semibold">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        {#each transactionsData as t (t.id)}
                            <tr class="transition hover:bg-slate-50/60">
                                <td class="px-5 py-3.5 font-mono text-xs text-slate-400">#{String(t.id).padStart(4, '0')}</td>
                                <td class="px-5 py-3.5 text-slate-600">{formatDate(t.transaction_date)}</td>
                                <td class="px-5 py-3.5">
                                    <p class="font-medium text-slate-800">{t.customer?.company_name ?? t.customer?.user?.name ?? '—'}</p>
                                    {#if t.plant}<p class="text-xs text-slate-400">{t.plant.name}</p>{/if}
                                </td>
                                <td class="px-5 py-3.5">
                                    {#if t.customer?.area}
                                        <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600">{t.customer.area.name}</span>
                                    {:else}
                                        <span class="text-slate-400">—</span>
                                    {/if}
                                </td>
                                <td class="px-5 py-3.5 text-right font-medium text-slate-700">{formatRp(t.subtotal)}</td>
                                <td class="px-5 py-3.5 text-right text-slate-600">{formatRp(t.ongkir)}</td>
                                <td class="px-5 py-3.5 text-right font-bold text-slate-800">{formatRp(t.total)}</td>
                                <td class="px-5 py-3.5 text-right font-semibold {(t.margin_estimate ?? 0) >= 0 ? 'text-emerald-600' : 'text-red-600'}">
                                    {formatRp(t.margin_estimate)}
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-medium capitalize ring-1 {statusStyle[t.status] ?? statusStyle.pending}">
                                        {t.status}
                                    </span>
                                </td>
                            </tr>
                        {:else}
                            <tr>
                                <td colspan="9" class="px-5 py-12 text-center text-sm text-slate-400">Tidak ada transaksi ditemukan.</td>
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
</AppLayout>
