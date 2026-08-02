<script>
    import { router, usePage } from '@inertiajs/svelte';
    import AppLayout from '../../Layouts/AppLayout.svelte';
    import StatusBadge from '../../Components/StatusBadge.svelte';

    const page = usePage();

    const transactions = $derived(page.props.transactions ?? []);
    const flash = $derived(page.props.flash?.message);

    function formatRp(value) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value);
    }

    function formatDate(date) {
        if (!date) return '—';
        return new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
    }

    function approve(id) {
        router.post(`/admin/orders/${id}/approve`);
    }
</script>

<AppLayout>
    <div class="mx-auto max-w-7xl">
        <header class="mb-6">
            <h2 class="text-xl font-bold text-slate-800">Daftar Order</h2>
            <p class="mt-0.5 text-sm text-slate-500">Review & approve order masuk — pantau proyeksi margin.</p>
        </header>

        {#if flash}
            <div class="mb-6 rounded-xl bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 ring-1 ring-emerald-100">{flash}</div>
        {/if}

        <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-left text-xs uppercase tracking-wide text-slate-400">
                            <th class="px-5 py-3 font-semibold">Order</th>
                            <th class="px-5 py-3 font-semibold">Customer</th>
                            <th class="px-5 py-3 text-right font-semibold">Total</th>
                            <th class="px-5 py-3 text-right font-semibold">Margin</th>
                            <th class="px-5 py-3 text-right font-semibold">Status</th>
                            <th class="px-5 py-3 text-right font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        {#each transactions as t (t.id)}
                            <tr>
                                <td class="px-5 py-3">
                                    <p class="font-mono text-xs text-slate-500">#{t.id}</p>
                                    <p class="text-xs text-slate-400">{formatDate(t.transaction_date)}</p>
                                </td>
                                <td class="px-5 py-3">
                                    <p class="font-medium text-slate-700">{t.customer?.company_name ?? '—'}</p>
                                    <p class="text-xs text-slate-400">{t.items?.length ?? 0} item</p>
                                </td>
                                <td class="px-5 py-3 text-right font-semibold text-slate-800">{formatRp(t.total)}</td>
                                <td class="px-5 py-3 text-right {t.margin_estimate >= 0 ? 'text-emerald-600' : 'text-red-600'}">
                                    {formatRp(t.margin_estimate ?? 0)}
                                </td>
                                <td class="px-5 py-3 text-right"><StatusBadge status={t.status === 'approved' ? 'Proses' : t.status} /></td>
                                <td class="px-5 py-3 text-right">
                                    {#if t.status === 'pending'}
                                        <button onclick={() => approve(t.id)} class="rounded-lg bg-primary-600 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-primary-700">Approve</button>
                                    {:else}
                                        <span class="text-xs text-slate-400">—</span>
                                    {/if}
                                </td>
                            </tr>
                        {/each}
                    </tbody>
                </table>
            </div>
            {#if transactions.length === 0}
                <p class="px-5 py-10 text-center text-sm text-slate-400">Belum ada order masuk.</p>
            {/if}
        </div>
    </div>
</AppLayout>
