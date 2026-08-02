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
        { label: 'Order Aktif', value: '2', sub: 'sedang berjalan', icon: 'cart', tone: 'accent' },
        { label: 'Dalam Pengiriman', value: '1', sub: '1 order di perjalanan', icon: 'truck', tone: 'primary' },
        { label: 'Total Transaksi', value: '8', sub: 'sepanjang riwayat', icon: 'history', tone: 'slate' },
        { label: 'Total Belanja', value: 'Rp 86,5 jt', sub: 'akumulasi semua order', icon: 'chart', tone: 'primary' },
    ];

    const orders = [
        { id: 'TRX-0237', date: '02 Agu 2026', product: 'Semen 50 kg · 150 zak', total: 'Rp 6,3 jt', status: 'Pending' },
        { id: 'TRX-0234', date: '28 Jul 2026', product: 'Semen 50 kg · 220 zak', total: 'Rp 9,2 jt', status: 'Dikirim' },
        { id: 'TRX-0229', date: '21 Jul 2026', product: 'Semen 40 kg · 300 zak', total: 'Rp 11,1 jt', status: 'Selesai' },
        { id: 'TRX-0221', date: '15 Jul 2026', product: 'Semen 50 kg · 180 zak', total: 'Rp 7,6 jt', status: 'Selesai' },
        { id: 'TRX-0216', date: '09 Jul 2026', product: 'Semen 40 kg · 250 zak', total: 'Rp 9,3 jt', status: 'Selesai' },
    ];
</script>

<AppLayout>
    <div class="mx-auto max-w-7xl">
        <header class="mb-6 flex flex-col justify-between gap-1 sm:flex-row sm:items-end">
            <div>
                <h2 class="text-xl font-bold text-slate-800">Halo, {user?.name}</h2>
                <p class="mt-0.5 text-sm text-slate-500">Pantau order & status pengiriman Anda.</p>
            </div>
            <p class="text-sm capitalize text-slate-400">{today}</p>
        </header>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            {#each stats as s (s.label)}
                <DashboardStat label={s.label} value={s.value} sub={s.sub} icon={s.icon} tone={s.tone} />
            {/each}
        </div>

        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
            <DashboardCard title="Pesan Sekarang" sub="Order baru langsung diproses admin">
                <div class="rounded-xl border-2 border-dashed border-slate-200 bg-slate-50 p-5 text-center">
                    <p class="text-sm text-slate-500">Halaman order online akan segera hadir.</p>
                </div>
            </DashboardCard>

            <DashboardCard title="Riwayat Order" sub="5 transaksi terakhir" class="lg:col-span-2">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-xs uppercase tracking-wide text-slate-400">
                                <th class="pb-2 font-semibold">No.</th>
                                <th class="pb-2 font-semibold">Produk</th>
                                <th class="pb-2 text-right font-semibold">Total</th>
                                <th class="pb-2 text-right font-semibold">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            {#each orders as t (t.id)}
                                <tr>
                                    <td class="py-2.5 pr-2">
                                        <p class="font-mono text-xs text-slate-500">{t.id}</p>
                                        <p class="text-xs text-slate-400">{t.date}</p>
                                    </td>
                                    <td class="py-2.5 pr-2 font-medium text-slate-700">{t.product}</td>
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
