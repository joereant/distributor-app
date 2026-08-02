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
        { label: 'Omzet Bulan Ini', value: 'Rp 184,6 jt', sub: '+12,4% dari bulan lalu', icon: 'chart', tone: 'primary' },
        { label: 'Order Bulan Ini', value: '342', sub: '286 selesai · 56 proses', icon: 'cart', tone: 'accent' },
        { label: 'Customer Aktif', value: '248', sub: '18 pelanggan baru', icon: 'users', tone: 'slate' },
        { label: 'Proyeksi Margin', value: 'Rp 21,4 jt', sub: 'rata-rata 11,6%', icon: 'box', tone: 'primary' },
    ];

    const areas = [
        { name: 'Jakarta Barat', amount: 'Rp 58,2 jt', pct: 84 },
        { name: 'Jakarta Timur', amount: 'Rp 41,7 jt', pct: 62 },
        { name: 'Bogor', amount: 'Rp 33,1 jt', pct: 48 },
        { name: 'Depok', amount: 'Rp 29,4 jt', pct: 40 },
        { name: 'Bekasi', amount: 'Rp 22,2 jt', pct: 32 },
    ];

    const recent = [
        { id: 'TRX-0241', customer: 'UD Sinar Jaya', area: 'Jakarta Barat', total: 'Rp 12,4 jt', status: 'Selesai' },
        { id: 'TRX-0240', customer: 'Toko Bangunan Amanah', area: 'Bogor', total: 'Rp 8,9 jt', status: 'Dikirim' },
        { id: 'TRX-0239', customer: 'CV Mitra Karya', area: 'Depok', total: 'Rp 15,2 jt', status: 'Proses' },
        { id: 'TRX-0238', customer: 'PT Karya Abadi', area: 'Jakarta Timur', total: 'Rp 22,7 jt', status: 'Selesai' },
        { id: 'TRX-0237', customer: 'Kontraktor Jaya Makmur', area: 'Bekasi', total: 'Rp 6,3 jt', status: 'Pending' },
    ];
</script>

<AppLayout>
    <div class="mx-auto max-w-7xl">
        <header class="mb-6 flex flex-col justify-between gap-1 sm:flex-row sm:items-end">
            <div>
                <h2 class="text-xl font-bold text-slate-800">Selamat datang, {user?.name}</h2>
                <p class="mt-0.5 text-sm text-slate-500">Ringkasan penjualan seluruh distributor.</p>
            </div>
            <p class="text-sm capitalize text-slate-400">{today}</p>
        </header>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            {#each stats as s (s.label)}
                <DashboardStat label={s.label} value={s.value} sub={s.sub} icon={s.icon} tone={s.tone} />
            {/each}
        </div>

        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
            <DashboardCard title="Penjualan per Area" sub="Bulan ini" action="Lihat semua">
                <ul class="space-y-4">
                    {#each areas as a (a.name)}
                        <li>
                            <div class="mb-1.5 flex items-center justify-between text-sm">
                                <span class="font-medium text-slate-700">{a.name}</span>
                                <span class="font-semibold text-slate-800">{a.amount}</span>
                            </div>
                            <div class="h-2 overflow-hidden rounded-full bg-slate-100">
                                <div class="h-full rounded-full bg-primary-600" style="width: {a.pct}%"></div>
                            </div>
                        </li>
                    {/each}
                </ul>
            </DashboardCard>

            <DashboardCard title="Transaksi Terbaru" sub="5 transaksi terakhir" action="Lihat semua" >
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
