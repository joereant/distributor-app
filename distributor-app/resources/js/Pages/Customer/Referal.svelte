<script>
    import { router, usePage } from '@inertiajs/svelte';
    import AppLayout from '../../Layouts/AppLayout.svelte';

    const page = usePage();
    const flash = $derived(page.props.message ?? page.props.success ?? null);
    const customer = $derived(page.props.customer);
    const referralCode = $derived(page.props.referral_code);
    const referrals = $derived(page.props.referrals ?? []);
    const stats = $derived(page.props.stats ?? {});

    function formatRp(value) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value ?? 0);
    }

    function copyCode() {
        if (referralCode) {
            navigator.clipboard.writeText(referralCode).then(() => {
                copied = true;
                setTimeout(() => { copied = false; }, 2000);
            });
        }
    }

    let copied = $state(false);

    function regenerate() {
        if (confirm('Buat kode baru? Kode lama tidak bisa dipakai lagi.')) {
            router.post('/customer/referal/regenerate');
        }
    }

    const statusStyle = {
        pending: 'bg-amber-50 text-amber-700 ring-amber-100',
        approved: 'bg-emerald-50 text-emerald-700 ring-emerald-100',
        rejected: 'bg-red-50 text-red-700 ring-red-100',
    };
</script>

<AppLayout>
    <div>
        {#if flash}
            <div class="mb-5 rounded-xl bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 ring-1 ring-emerald-100">{flash}</div>
        {/if}

        <!-- Header -->
        <header class="mb-5">
            <h2 class="text-xl font-bold text-slate-800">Program Referal</h2>
            <p class="mt-0.5 text-sm text-slate-500">Ajak teman mendaftar & dapatkan benefit.</p>
        </header>

        {#if !customer}
            <div class="rounded-2xl bg-white py-16 text-center shadow-sm ring-1 ring-slate-200">
                <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-slate-100">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-6 w-6 text-slate-400"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="m8.6 13.5 6.8 4"/><path d="m15.4 6.5-6.8 4"/></svg>
                </div>
                <p class="text-sm text-slate-500">Akun Anda belum memiliki profil customer. Hubungi admin.</p>
            </div>
        {:else}
            <!-- Referral Code Card -->
            <div class="mb-6 overflow-hidden rounded-2xl bg-gradient-to-br from-primary-600 to-primary-800 text-white shadow-lg">
                <div class="px-6 py-6">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="mb-1 text-xs font-medium uppercase tracking-wide text-white/60">Kode Referal Anda</p>
                            <div class="flex items-center gap-3">
                                <p class="text-3xl font-bold tracking-widest">{referralCode ?? '—'}</p>
                            </div>
                            <p class="mt-2 text-sm text-white/70">Bagikan kode ini ke teman yang ingin mendaftar sebagai customer.</p>
                        </div>
                        <div class="flex flex-col gap-2">
                            <button onclick={copyCode} class="flex items-center gap-2 rounded-xl bg-white/15 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/25">
                                {#if copied}
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><polyline points="20 6 9 17 4 12"/></svg>
                                    Tersalin!
                                {:else}
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                    Salin
                                {/if}
                            </button>
                            {#if referralCode}
                                <button onclick={regenerate} class="flex items-center gap-2 rounded-xl bg-white/10 px-4 py-2 text-xs font-medium text-white/70 transition hover:bg-white/20">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-3.5 w-3.5"><path d="M1 4v6h6"/><path d="M23 20v-6h-6"/><path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 0 1 3.51 15"/></svg>
                                    Buat baru
                                </button>
                            {/if}
                        </div>
                    </div>

                    <!-- Share hint -->
                    <div class="mt-4 flex items-center gap-2 rounded-xl bg-white/10 px-4 py-3 text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4 shrink-0 text-white/60"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                        <span class="text-white/80">Tautan pendaftaran dengan kode referal: <span class="font-mono font-semibold text-white">{window.location.origin}/register?ref={referralCode}</span></span>
                    </div>
                </div>
            </div>

            <!-- Stats -->
            <div class="mb-6 grid grid-cols-3 gap-4">
                <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Total Referal</p>
                    <p class="mt-1 text-2xl font-bold text-slate-800">{stats.total_referrals ?? 0}</p>
                </div>
                <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Transaksi Berhasil</p>
                    <p class="mt-1 text-2xl font-bold text-slate-800">{stats.total_transactions ?? 0}</p>
                </div>
                <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Proyeksi Margin</p>
                    <p class="mt-1 text-2xl font-bold text-emerald-600">{formatRp(stats.total_margin)}</p>
                </div>
            </div>

            <!-- Referral List -->
            <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
                <div class="border-b border-slate-100 bg-slate-50 px-5 py-3">
                    <h3 class="text-sm font-semibold text-slate-700">Riwayat Referal</h3>
                </div>
                {#if referrals.length === 0}
                    <p class="px-5 py-12 text-center text-sm text-slate-400">Belum ada yang mendaftar pakai kode Anda. Ajak teman sekarang!</p>
                {:else}
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-100 text-left text-xs uppercase tracking-wide text-slate-400">
                                <th class="px-5 py-3 font-semibold">Teman</th>
                                <th class="px-5 py-3 font-semibold">Tanggal</th>
                                <th class="px-5 py-3 font-semibold">Transaksi</th>
                                <th class="px-5 py-3 text-right font-semibold">Margin</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            {#each referrals as r (r.id)}
                                <tr class="transition hover:bg-slate-50/60">
                                    <td class="px-5 py-3">
                                        <p class="font-medium text-slate-700">{r.customer?.company_name ?? r.customer?.user?.name ?? '—'}</p>
                                        <p class="text-xs text-slate-400">{r.customer?.user?.email ?? ''}</p>
                                    </td>
                                    <td class="px-5 py-3 text-slate-500">
                                        {new Date(r.created_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })}
                                    </td>
                                    <td class="px-5 py-3">
                                        {#if r.transaction}
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-medium capitalize ring-1 {statusStyle[r.transaction.status] ?? statusStyle.pending}">
                                                {r.transaction.status}
                                            </span>
                                        {:else}
                                            <span class="text-slate-400">—</span>
                                        {/if}
                                    </td>
                                    <td class="px-5 py-3 text-right font-semibold text-emerald-600">
                                        {r.transaction?.margin_estimate ? formatRp(r.transaction.margin_estimate) : '—'}
                                    </td>
                                </tr>
                            {/each}
                        </tbody>
                    </table>
                {/if}
            </div>
        {/if}
    </div>
</AppLayout>
