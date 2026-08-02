<script>
    import { usePage } from '@inertiajs/svelte';
    import AppLayout from '../../Layouts/AppLayout.svelte';
    import DashboardStat from '../../Components/DashboardStat.svelte';
    import DashboardCard from '../../Components/DashboardCard.svelte';
    import StatusBadge from '../../Components/StatusBadge.svelte';

    const page = usePage();
    const user = $derived(page.props.auth?.user);
    const kpis = $derived(page.props.kpis ?? {});
    const orders = $derived(page.props.orders ?? []);
    const customer = $derived(page.props.customer);

    const today = new Date().toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });

    function formatRp(value) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value ?? 0);
    }

    function formatDate(date) {
        if (!date) return '—';
        return new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
    }

    const stats = $derived([
        { label: 'Order Menunggu Proses', value: String(kpis.active_orders ?? 0), sub: 'belum disetujui', icon: 'cart', tone: 'accent' },
        { label: 'Total Transaksi', value: String(kpis.total_orders ?? 0), sub: 'sepanjang riwayat', icon: 'history', tone: 'primary' },
        { label: 'Total Belanja', value: formatRp(kpis.total_spent), sub: 'akumulasi semua order', icon: 'chart', tone: 'primary' },
        { label: 'Order Terakhir', value: formatDate(kpis.last_order), sub: '', icon: 'box', tone: 'slate' },
    ]);
</script>

<AppLayout>
    <div>
        <header class="mb-6 flex flex-col justify-between gap-1 sm:flex-row sm:items-end">
            <div>
                <h2 class="text-xl font-bold text-slate-800">Halo, {user?.name}</h2>
                <p class="mt-0.5 text-sm text-slate-500">{customer?.company_name ?? 'Pantau order & status pengiriman Anda.'}</p>
            </div>
            <p class="text-sm capitalize text-slate-400">{today}</p>
        </header>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            {#each stats as s (s.label)}
                <DashboardStat label={s.label} value={s.value} sub={s.sub} icon={s.icon} tone={s.tone} />
            {/each}
        </div>

        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
            <DashboardCard title="Pesan Sekarang" sub="Pilih produk & tujuan kirim, ongkir dihitung otomatis">
                <a href="/customer/order" class="flex flex-col items-center gap-3 rounded-xl border-2 border-dashed border-primary-200 bg-primary-50/50 p-8 text-center transition hover:border-primary-400 hover:bg-primary-50">
                    <span class="flex h-12 w-12 items-center justify-center rounded-full bg-primary-600 text-xl font-bold text-white">+</span>
                    <span class="text-sm font-semibold text-primary-700">Buat Pesanan Baru</span>
                    <span class="text-xs text-slate-500">Buka katalog produk & harga sesuai area Anda</span>
                </a>
            </DashboardCard>

            <DashboardCard title="Riwayat Order" sub="12 transaksi terakhir" class="lg:col-span-2">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-xs uppercase tracking-wide text-slate-400">
                                <th class="pb-2 font-semibold">No.</th>
                                <th class="pb-2 font-semibold">Tanggal</th>
                                <th class="pb-2 text-right font-semibold">Total</th>
                                <th class="pb-2 text-right font-semibold">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            {#each orders as t (t.id)}
                                <tr>
                                    <td class="py-2.5 pr-2 font-mono text-xs text-slate-500">#{t.id}</td>
                                    <td class="py-2.5 pr-2 text-slate-600">{formatDate(t.transaction_date)}</td>
                                    <td class="py-2.5 text-right font-semibold text-slate-800">{formatRp(t.total)}</td>
                                    <td class="py-2.5 text-right"><StatusBadge status={t.status} /></td>
                                </tr>
                            {/each}
                        </tbody>
                    </table>
                </div>
                {#if orders.length === 0}
                    <p class="py-10 text-center text-sm text-slate-400">Belum ada order. Mulai pesan sekarang!</p>
                {/if}
            </DashboardCard>
        </div>
    </div>
</AppLayout>
