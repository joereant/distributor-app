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
        { label: 'Omzet Area', value: 'Rp 52,3 jt', sub: '+8,1% dari bulan lalu', icon: 'chart', tone: 'primary' },
        { label: 'Order Area', value: '98', sub: '84 selesai · 14 proses', icon: 'cart', tone: 'accent' },
        { label: 'Customer Referal', value: '24', sub: '3 pelanggan baru', icon: 'referral', tone: 'slate' },
        { label: 'Proyeksi Margin', value: 'Rp 6,1 jt', sub: 'rata-rata 11,6%', icon: 'box', tone: 'primary' },
    ];

    const referals = [
        { name: 'UD Sinar Jaya', area: 'Jakarta Barat', orders: 12, total: 'Rp 38,2 jt', last: '2 hari lalu' },
        { name: 'Toko Bangunan Rejeki', area: 'Jakarta Barat', orders: 8, total: 'Rp 24,7 jt', last: '5 hari lalu' },
        { name: 'Kontraktor Jaya Makmur', area: 'Jakarta Barat', orders: 5, total: 'Rp 18,1 jt', last: '1 minggu lalu' },
        { name: 'UD Berkah Jaya', area: 'Jakarta Barat', orders: 4, total: 'Rp 11,6 jt', last: '2 minggu lalu' },
    ];

    const recent = [
        { id: 'TRX-0241', customer: 'UD Sinar Jaya', area: 'Jakarta Barat', total: 'Rp 12,4 jt', status: 'Selesai' },
        { id: 'TRX-0237', customer: 'Kontraktor Jaya Makmur', area: 'Jakarta Barat', total: 'Rp 6,3 jt', status: 'Pending' },
        { id: 'TRX-0233', customer: 'Toko Bangunan Rejeki', area: 'Jakarta Barat', total: 'Rp 8,1 jt', status: 'Dikirim' },
        { id: 'TRX-0230', customer: 'UD Berkah Jaya', area: 'Jakarta Barat', total: 'Rp 5,4 jt', status: 'Selesai' },
    ];
</script>

<AppLayout>
    <div class="mx-auto max-w-7xl">
        <header class="mb-6 flex flex-col justify-between gap-1 sm:flex-row sm:items-end">
            <div>
                <h2 class="text-xl font-bold text-slate-800">Selamat datang, {user?.name}</h2>
                <p class="mt-0.5 text-sm text-slate-500">Monitoring area {user?.sales_area?.name ?? 'Anda'} & customer referal.</p>
            </div>
            <p class="text-sm capitalize text-slate-400">{today}</p>
        </header>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            {#each stats as s (s.label)}
                <DashboardStat label={s.label} value={s.value} sub={s.sub} icon={s.icon} tone={s.tone} />
            {/each}
        </div>

        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
            <DashboardCard title="Customer Referal" sub="Customer dari non-sales" action="Lihat semua">
                <ul class="divide-y divide-slate-100">
                    {#each referals as r (r.name)}
                        <li class="flex items-center gap-3 py-2.5">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary-50 text-xs font-bold text-primary-700">
                                {r.name.split(/\s+/).slice(0, 2).map((w) => w[0]).join('')}
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-slate-700">{r.name}</p>
                                <p class="text-xs text-slate-400">{r.orders} order · terakhir {r.last}</p>
                            </div>
                            <span class="text-sm font-semibold text-slate-800">{r.total}</span>
                        </li>
                    {/each}
                </ul>
            </DashboardCard>

            <DashboardCard title="Transaksi Area" sub="Transaksi terakhir di area Anda" class="lg:col-span-2">
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
