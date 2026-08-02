<script>
    import { usePage } from '@inertiajs/svelte';
    import AppLayout from '../../Layouts/AppLayout.svelte';
    import DashboardStat from '../../Components/DashboardStat.svelte';
    import DashboardCard from '../../Components/DashboardCard.svelte';
    import StatusBadge from '../../Components/StatusBadge.svelte';

    const page = usePage();
    const user = $derived(page.props.auth?.user);

    const today = new Date().toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });

    const stats = [
        { label: 'Order Perlu Approve', value: '12', sub: 'menunggu review', icon: 'cart', tone: 'accent' },
        { label: 'Order Bulan Ini', value: '342', sub: '286 selesai · 56 proses', icon: 'history', tone: 'primary' },
        { label: 'Customer Aktif', value: '248', sub: '18 pelanggan baru', icon: 'users', tone: 'slate' },
        { label: 'Proyeksi Margin', value: 'Rp 21,4 jt', sub: 'rata-rata 11,6%', icon: 'box', tone: 'primary' },
    ];

    const pending = [
        { id: 'TRX-0242', customer: 'UD Berkah Jaya', area: 'Jakarta Barat', total: 'Rp 9,1 jt', margin: 'Rp 1,2 jt', status: 'Pending' },
        { id: 'TRX-0241', customer: 'Kontraktor Jaya Makmur', area: 'Bekasi', total: 'Rp 6,3 jt', margin: 'Rp 740 rb', status: 'Pending' },
        { id: 'TRX-0240', customer: 'Toko Bangunan Rejeki', area: 'Depok', total: 'Rp 11,8 jt', margin: 'Rp 1,4 jt', status: 'Pending' },
        { id: 'TRX-0239', customer: 'CV Mitra Karya', area: 'Depok', total: 'Rp 15,2 jt', margin: 'Rp 1,9 jt', status: 'Proses' },
    ];

    const recent = [
        { id: 'TRX-0238', customer: 'PT Karya Abadi', area: 'Jakarta Timur', total: 'Rp 22,7 jt', status: 'Selesai' },
        { id: 'TRX-0237', customer: 'UD Sinar Jaya', area: 'Jakarta Barat', total: 'Rp 12,4 jt', status: 'Selesai' },
        { id: 'TRX-0236', customer: 'Toko Bangunan Amanah', area: 'Bogor', total: 'Rp 8,9 jt', status: 'Dikirim' },
        { id: 'TRX-0235', customer: 'Kontraktor Citra', area: 'Jakarta Timur', total: 'Rp 17,5 jt', status: 'Selesai' },
    ];
</script>

<AppLayout>
    <div class="mx-auto max-w-7xl">
        <header class="mb-6 flex flex-col justify-between gap-1 sm:flex-row sm:items-end">
            <div>
                <h2 class="text-xl font-bold text-slate-800">Selamat datang, {user?.name}</h2>
                <p class="mt-0.5 text-sm text-slate-500">Kelola data, harga, ongkir, dan order masuk.</p>
            </div>
            <p class="text-sm capitalize text-slate-400">{today}</p>
        </header>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            {#each stats as s (s.label)}
                <DashboardStat label={s.label} value={s.value} sub={s.sub} icon={s.icon} tone={s.tone} />
            {/each}
        </div>

        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
            <DashboardCard title="Order Menunggu Approve" sub="Review & proyeksi margin" class="lg:col-span-2">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-xs uppercase tracking-wide text-slate-400">
                                <th class="pb-2 font-semibold">No.</th>
                                <th class="pb-2 font-semibold">Customer</th>
                                <th class="pb-2 text-right font-semibold">Total</th>
                                <th class="pb-2 text-right font-semibold">Margin</th>
                                <th class="pb-2 text-right font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            {#each pending as t (t.id)}
                                <tr>
                                    <td class="py-2.5 pr-2 font-mono text-xs text-slate-500">{t.id}</td>
                                    <td class="py-2.5 pr-2">
                                        <p class="font-medium text-slate-700">{t.customer}</p>
                                        <p class="text-xs text-slate-400">{t.area}</p>
                                    </td>
                                    <td class="py-2.5 text-right font-semibold text-slate-800">{t.total}</td>
                                    <td class="py-2.5 text-right text-emerald-600">{t.margin}</td>
                                    <td class="py-2.5 text-right">
                                        {#if t.status === 'Pending'}
                                            <button type="button" class="rounded-lg bg-primary-600 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-primary-700">Approve</button>
                                        {:else}
                                            <StatusBadge status={t.status} />
                                        {/if}
                                    </td>
                                </tr>
                            {/each}
                        </tbody>
                    </table>
                </div>
            </DashboardCard>

            <DashboardCard title="Transaksi Terbaru" sub="4 transaksi terakhir">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-xs uppercase tracking-wide text-slate-400">
                                <th class="pb-2 font-semibold">No.</th>
                                <th class="pb-2 font-semibold">Customer</th>
                                <th class="pb-2 text-right font-semibold">Total</th>
                                <th class="pb-2 text-right font-semibold">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            {#each recent as t (t.id)}
                                <tr>
                                    <td class="py-2.5 pr-2 font-mono text-xs text-slate-500">{t.id}</td>
                                    <td class="py-2.5 pr-2">
                                        <p class="font-medium text-slate-700">{t.customer}</p>
                                        <p class="text-xs text-slate-400">{t.area}</p>
                                    </td>
                                    <td class="py-2.5 text-right font-semibold text-slate-800">{t.total}</td>
                                    <td class="py-2.5 text-right"><StatusBadge status={t.status} /></td>
                                </tr>
                            {/each}
                        </tbody>
                    </table>
                </div>
            </DashboardCard>
        </div>
    </div>
</AppLayout>
