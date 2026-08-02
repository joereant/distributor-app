<script>
    import { router, usePage } from '@inertiajs/svelte';
    import AppLayout from '../../Layouts/AppLayout.svelte';
    import StatusBadge from '../../Components/StatusBadge.svelte';
    import Icon from '../../Components/Icon.svelte';

    const page = usePage();

    const transactions = $derived(page.props.transactions ?? []);
    const flash = $derived(page.props.flash?.message);

    let selected = $state(null);

    function formatRp(value) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value ?? 0);
    }

    function formatDate(date) {
        if (!date) return '—';
        return new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
    }

    function openDetail(t) {
        selected = t;
    }

    function closeDetail() {
        selected = null;
    }

    function approve(id) {
        router.post(`/admin/orders/${id}/approve`, {}, { onSuccess: closeDetail });
    }
</script>

<AppLayout>
    <div>
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
                            <tr class="transition hover:bg-slate-50/60">
                                <td class="px-5 py-3">
                                    <p class="font-mono text-xs text-slate-500">#{t.id}</p>
                                    <p class="text-xs text-slate-400">{formatDate(t.transaction_date)}</p>
                                </td>
                                <td class="px-5 py-3">
                                    <p class="font-medium text-slate-700">{t.customer?.company_name ?? '—'}</p>
                                    <p class="text-xs text-slate-400">{t.customer?.area?.name ?? '—'} · {t.items?.length ?? 0} item</p>
                                </td>
                                <td class="px-5 py-3 text-right font-semibold text-slate-800">{formatRp(t.total)}</td>
                                <td class="px-5 py-3 text-right font-medium {t.margin_estimate >= 0 ? 'text-emerald-600' : 'text-red-600'}">
                                    {formatRp(t.margin_estimate ?? 0)}
                                </td>
                                <td class="px-5 py-3 text-right"><StatusBadge status={t.status} /></td>
                                <td class="px-5 py-3 text-right">
                                    <button onclick={() => openDetail(t)} class="rounded-lg bg-primary-600 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-primary-700">Review</button>
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

    {#if selected}
        <div class="fixed inset-0 z-50">
            <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-[2px]" onclick={closeDetail}></div>

            <aside class="absolute inset-y-0 right-0 flex w-full max-w-md flex-col bg-white shadow-2xl">
                <header class="flex items-start justify-between gap-3 border-b border-slate-100 px-5 py-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="font-mono text-base font-bold text-slate-800">Order #{selected.id}</h3>
                            <StatusBadge status={selected.status} />
                        </div>
                        <p class="mt-0.5 text-xs text-slate-400">{formatDate(selected.transaction_date)}</p>
                    </div>
                    <button onclick={closeDetail} class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-600" aria-label="Tutup">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="h-5 w-5"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    </button>
                </header>

                <div class="flex-1 overflow-y-auto px-5 py-4">
                    <section class="mb-5">
                        <h4 class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-400">Customer</h4>
                        <div class="rounded-xl bg-slate-50 p-3.5 ring-1 ring-slate-100">
                            <p class="font-semibold text-slate-800">{selected.customer?.company_name ?? '—'}</p>
                            <dl class="mt-2 space-y-1 text-xs text-slate-500">
                                <div class="flex justify-between gap-2">
                                    <dt>Kontak</dt>
                                    <dd class="font-medium text-slate-600">{selected.customer?.contact_person ?? '—'}</dd>
                                </div>
                                <div class="flex justify-between gap-2">
                                    <dt>Area</dt>
                                    <dd class="font-medium text-slate-600">{selected.customer?.area?.name ?? '—'}</dd>
                                </div>
                                <div class="flex justify-between gap-2">
                                    <dt>Telepon</dt>
                                    <dd class="font-medium text-slate-600">{selected.customer?.phone ?? '—'}</dd>
                                </div>
                            </dl>
                        </div>
                    </section>

                    <section class="mb-5">
                        <h4 class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-400">Detail Produk</h4>
                        <ul class="divide-y divide-slate-100 rounded-xl ring-1 ring-slate-200">
                            {#each selected.items ?? [] as item (item.id)}
                                <li class="flex items-center justify-between gap-3 px-3.5 py-3">
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-medium text-slate-700">{item.product?.name ?? 'Produk'}</p>
                                        <p class="mt-0.5 text-xs text-slate-400">{item.quantity} × {formatRp(item.unit_price)} / {item.product?.unit ?? 'unit'}</p>
                                    </div>
                                    <span class="shrink-0 text-sm font-semibold text-slate-800">{formatRp(item.subtotal)}</span>
                                </li>
                            {/each}
                        </ul>
                    </section>

                    <section class="mb-5">
                        <h4 class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-400">Ringkasan</h4>
                        <dl class="space-y-1.5 rounded-xl bg-slate-50 p-3.5 text-sm ring-1 ring-slate-100">
                            <div class="flex justify-between text-slate-600">
                                <dt>Subtotal</dt>
                                <dd class="font-medium text-slate-800">{formatRp(selected.subtotal)}</dd>
                            </div>
                            <div class="flex justify-between text-slate-600">
                                <dt>Ongkir</dt>
                                <dd class="font-medium text-slate-800">{formatRp(selected.ongkir ?? 0)}</dd>
                            </div>
                            <div class="flex justify-between border-t border-slate-200 pt-2 text-base font-bold text-slate-900">
                                <dt>Total</dt>
                                <dd>{formatRp(selected.total)}</dd>
                            </div>
                        </dl>
                    </section>

                    <section>
                        <h4 class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-400">Proyeksi Margin</h4>
                        <div class="rounded-xl bg-slate-50 p-3.5 ring-1 ring-slate-100">
                            <dl class="space-y-1.5 text-sm text-slate-600">
                                <div class="flex justify-between">
                                    <dt>Harga Beli</dt>
                                    <dd class="font-medium text-slate-800">{formatRp(selected.harga_beli ?? 0)}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt>Subtotal Jual</dt>
                                    <dd class="font-medium text-slate-800">{formatRp(selected.subtotal)}</dd>
                                </div>
                                <div class="flex justify-between border-t border-slate-200 pt-2">
                                    <dt class="font-semibold text-slate-700">Proyeksi Margin</dt>
                                    <dd class="font-bold {selected.margin_estimate >= 0 ? 'text-emerald-600' : 'text-red-600'}">{formatRp(selected.margin_estimate ?? 0)}</dd>
                                </div>
                            </dl>
                            <p class="mt-2 text-xs text-slate-400">
                                {#if selected.margin_status === 'positif'}
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 font-medium text-emerald-700 ring-1 ring-emerald-100">Positif</span>
                                {:else if selected.margin_status === 'negatif'}
                                    <span class="inline-flex items-center gap-1 rounded-full bg-red-50 px-2 py-0.5 font-medium text-red-700 ring-1 ring-red-100">Negatif</span>
                                {:else}
                                    <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-600 ring-1 ring-slate-200">Netral</span>
                                {/if}
                                <span class="ml-1">· {selected.subtotal > 0 ? ((selected.margin_estimate ?? 0) / selected.subtotal * 100).toFixed(1) : 0}% dari subtotal</span>
                            </p>
                        </div>
                    </section>
                </div>

                <footer class="flex gap-3 border-t border-slate-100 px-5 py-4">
                    {#if selected.status === 'pending'}
                        <button onclick={() => approve(selected.id)} class="flex-1 rounded-xl bg-primary-600 py-2.5 text-sm font-semibold text-white transition hover:bg-primary-700">
                            Approve Order
                        </button>
                    {:else}
                        <p class="flex-1 text-center text-sm font-medium text-slate-400">Order ini sudah diproses.</p>
                    {/if}
                </footer>
            </aside>
        </div>
    {/if}
</AppLayout>
