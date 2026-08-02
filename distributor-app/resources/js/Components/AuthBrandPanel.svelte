<script>
    import Logo from './Logo.svelte';
</script>

<aside class="relative overflow-hidden bg-gradient-to-br from-primary-700 via-primary-800 to-primary-900 text-white">
    <div class="pointer-events-none absolute -top-24 -right-24 h-72 w-72 rounded-full bg-accent-500/20 blur-3xl"></div>
    <div class="pointer-events-none absolute -bottom-32 -left-20 h-80 w-80 rounded-full bg-teal-300/10 blur-3xl"></div>
    <div class="pointer-events-none absolute top-1/3 left-1/2 h-40 w-40 -translate-x-1/2 rounded-full bg-white/5 blur-2xl"></div>

    <div class="relative flex min-h-[260px] flex-col justify-between gap-8 px-6 py-10 lg:min-h-screen lg:px-12 lg:py-16">
        <Logo size="sm" />

        <div class="flex items-center gap-3">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl border-2 border-dashed border-white/40 bg-white/5 text-[10px] font-medium text-white/60">Logo</div>
            <div>
                <p class="text-[11px] text-white/50">Distributor</p>
                <p class="text-base font-semibold">Nama Distributor</p>
            </div>
        </div>

        <div class="flex h-28 items-center justify-center rounded-2xl border-2 border-dashed border-white/40 bg-white/5">
            <span class="text-xs text-white/50">Banner / Iklan Distributor</span>
        </div>

        <div>
            <div class="mb-3 flex items-center gap-2">
                <p class="text-xs font-semibold text-white/70">Performa Distributor</p>
                <span class="rounded-full bg-white/10 px-2 py-0.5 text-[10px] text-white/50">demo</span>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div class="rounded-xl bg-white/10 p-3">
                    <p class="text-[11px] text-white/60">Order Selesai</p>
                    <p class="text-lg font-bold leading-tight">1.240</p>
                </div>
                <div class="rounded-xl bg-white/10 p-3">
                    <p class="text-[11px] text-white/60">Service Time</p>
                    <p class="text-lg font-bold leading-tight">2,4 jam</p>
                </div>
                <div class="rounded-xl bg-white/10 p-3">
                    <p class="text-[11px] text-white/60">Customer Aktif</p>
                    <p class="text-lg font-bold leading-tight">348</p>
                </div>
                <div class="rounded-xl bg-white/10 p-3">
                    <p class="text-[11px] text-white/60">Area</p>
                    <p class="text-lg font-bold leading-tight">6</p>
                </div>
            </div>
        </div>

        <p class="text-xs text-white/45">© 2026 JR Logix</p>
    </div>
</aside>
