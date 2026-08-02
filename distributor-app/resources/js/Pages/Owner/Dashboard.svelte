<script>
    import { usePage } from '@inertiajs/svelte';
    import AppLayout from '../../Layouts/AppLayout.svelte';
    import DashboardStat from '../../Components/DashboardStat.svelte';
    import DashboardCard from '../../Components/DashboardCard.svelte';
    import StatusBadge from '../../Components/StatusBadge.svelte';
    import Chart from '../../Components/Chart.svelte';

    const page = usePage();
    const user = $derived(page.props.auth?.user);
    const kpis = $derived(page.props.kpis ?? {});
    const trend = $derived(page.props.trend ?? { labels: [], total: [], margin: [] });
    const topProducts = $derived(page.props.top_products ?? []);
    const areaSales = $derived(page.props.area_sales ?? []);
    const recent = $derived(page.props.recent ?? []);

    const today = new Date().toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });

    function formatRp(value) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value ?? 0);
    }

    function formatShort(value) {
        const n = Number(value ?? 0);
        if (n >= 1_000_000_000) return 'Rp ' + (n / 1_000_000_000).toLocaleString('id-ID', { maximumFractionDigits: 1 }) + ' M';
        if (n >= 1_000_000) return 'Rp ' + (n / 1_000_000).toLocaleString('id-ID', { maximumFractionDigits: 1 }) + ' jt';
        if (n >= 1_000) return 'Rp ' + (n / 1_000).toLocaleString('id-ID', { maximumFractionDigits: 0 }) + ' rb';
        return formatRp(n);
    }

    const delta = $derived(kpis.delta ?? 0);
    const deltaTone = $derived(delta >= 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700');

    const stats = $derived([
        { label: 'Omzet Bulan Ini', value: formatRp(kpis.omzet_month), sub: '', icon: 'chart', tone: 'primary' },
        { label: 'Transaksi Bulan Ini', value: String(kpis.orders_month ?? 0), sub: `${kpis.orders_approved ?? 0} selesai`, icon: 'cart', tone: 'accent' },
        { label: 'Customer Aktif', value: String(kpis.customers_active ?? 0), sub: 'pernah bertransaksi', icon: 'users', tone: 'slate' },
        { label: 'Proyeksi Margin', value: formatRp(kpis.margin_month), sub: `rata-rata ${kpis.margin_pct ?? 0}%`, icon: 'box', tone: 'primary' },
    ]);
</script>

<AppLayout>
    <div>
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

        <div class="mt-2 flex justify-end">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-white px-3 py-1 text-xs font-medium text-slate-600 ring-1 ring-slate-200">
                vs bulan lalu
                <span class="font-semibold {deltaTone === 'bg-rose-50 text-rose-700' ? 'text-rose-600' : 'text-emerald-600'}">{delta >= 0 ? '▲' : '▼'} {Math.abs(delta)}%</span>
            </span>
        </div>

        <div class="mt-4 grid grid-cols-1 gap-6 lg:grid-cols-3">
            <DashboardCard title="Tren Penjualan & Margin" sub="6 bulan terakhir" class="lg:col-span-2">
                <Chart
                    type="area"
                    series={[{ name: 'Penjualan', data: trend.total }, { name: 'Proyeksi Margin', data: trend.margin }]}
                    categories={trend.labels}
                    height={280}
                    formatter={formatShort}
                />
            </DashboardCard>

            <DashboardCard title="Penjualan per Area" sub="Akumulasi keseluruhan">
                {#if areaSales.length}
                    <Chart type="donut" series={areaSales.map((a) => a.total)} labels={areaSales.map((a) => a.name)} height={280} formatter={formatShort} />
                {:else}
                    <p class="py-12 text-center text-sm text-slate-400">Belum ada data penjualan.</p>
                {/if}
            </DashboardCard>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
            <DashboardCard title="Produk Terlaris" sub="Berdasarkan jumlah zak terjual">
                {#if topProducts.length}
                    <Chart type="bar" horizontal series={[{ name: 'Terjual', data: topProducts.map((p) => p.qty) }]} categories={topProducts.map((p) => p.name)} height={280} formatter={(v) => `${v} zak`} />
                {:else}
                    <p class="py-12 text-center text-sm text-slate-400">Belum ada data produk.</p>
                {/if}
            </DashboardCard>

            <DashboardCard title="Transaksi Terbaru" sub="8 transaksi terakhir" class="lg:col-span-2">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-xs uppercase tracking-wide text-slate-400">
                                <th class="pb-2 font-semibold">No.</th>
                                <th class="pb-2 font-semibold">Customer</th>
                                <th class="pb-2 text-right font-semibold">Total</th>
                                <th class="pb-2 text-right font-semibold">Margin</th>
                                <th class="pb-2 text-right font-semibold">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            {#each recent as t (t.id)}
                                <tr>
                                    <td class="py-2.5 pr-2">
                                        <p class="font-mono text-xs text-slate-500">#{t.id}</p>
                                        <p class="text-xs text-slate-400">{new Date(t.transaction_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' })}</p>
                                    </td>
                                    <td class="py-2.5 pr-2">
                                        <p class="font-medium text-slate-700">{t.customer?.company_name ?? '—'}</p>
                                        <p class="text-xs text-slate-400">{t.customer?.area ?? '—'} · {t.items_count} item</p>
                                    </td>
                                    <td class="py-2.5 text-right font-semibold text-slate-800">{formatRp(t.total)}</td>
                                    <td class="py-2.5 text-right font-medium {t.margin_estimate >= 0 ? 'text-emerald-600' : 'text-rose-600'}">{formatRp(t.margin_estimate)}</td>
                                    <td class="py-2.5 text-right"><StatusBadge status={t.status} /></td>
                                </tr>
                            {/each}
                        </tbody>
                    </table>
                </div>
                {#if recent.length === 0}
                    <p class="py-10 text-center text-sm text-slate-400">Belum ada transaksi.</p>
                {/if}
            </DashboardCard>
        </div>
    </div>
</AppLayout>
