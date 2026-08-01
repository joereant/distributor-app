<script>
    import { useForm, usePage } from '@inertiajs/svelte';
    import AuthBrandPanel from '../../Components/AuthBrandPanel.svelte';

    let { areas = [] } = $props();

    const page = usePage();
    const form = useForm({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        sales_area_id: '',
    });

    const errors = $derived(page.props.errors ?? {});
</script>

<div class="min-h-screen lg:grid lg:grid-cols-2">
    <AuthBrandPanel />

    <!-- Form -->
    <main class="flex items-center justify-center px-6 py-10 lg:py-16">
        <div class="w-full max-w-sm">
            <div class="mb-8 text-center lg:text-left">
                <h2 class="text-2xl font-bold tracking-tight text-slate-900">Buat akun customer baru</h2>
                <p class="mt-1 text-sm text-slate-500">Daftar untuk mulai berbelanja & memantau pesanan</p>
            </div>

            <form onsubmit={(e) => { e.preventDefault(); form.post('/register'); }} class="space-y-4">
                <div>
                    <label for="name" class="mb-1 block text-sm font-medium text-slate-700">Nama / nama usaha</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </span>
                        <input id="name" type="text" bind:value={form.name} placeholder="Nama pribadi, toko, PT, kontraktor, dll." autocomplete="name"
                            class="w-full rounded-lg border border-slate-300 bg-white py-2.5 pl-10 pr-3 text-sm text-slate-800 placeholder-slate-400 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20" required />
                    </div>
                    {#if errors.name}
                        <p class="mt-1 text-xs text-red-600">{errors.name}</p>
                    {/if}
                </div>
                <div>
                    <label for="email" class="mb-1 block text-sm font-medium text-slate-700">Email</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </span>
                        <input id="email" type="email" bind:value={form.email} autocomplete="email"
                            class="w-full rounded-lg border border-slate-300 bg-white py-2.5 pl-10 pr-3 text-sm text-slate-800 placeholder-slate-400 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20" required />
                    </div>
                    {#if errors.email}
                        <p class="mt-1 text-xs text-red-600">{errors.email}</p>
                    {/if}
                </div>
                <div>
                    <label for="sales_area_id" class="mb-1 block text-sm font-medium text-slate-700">Area</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.66 16.26a8 8 0 10-11.32 0M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5v.01"/></svg>
                        </span>
                        <select id="sales_area_id" bind:value={form.sales_area_id}
                            class="w-full appearance-none rounded-lg border border-slate-300 bg-white py-2.5 pl-10 pr-8 text-sm text-slate-800 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20" required>
                            <option value="" disabled>Pilih area</option>
                            {#each areas as area (area.id)}
                                <option value={area.id}>{area.name}</option>
                            {/each}
                        </select>
                        <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-400">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </span>
                    </div>
                    {#if errors.sales_area_id}
                        <p class="mt-1 text-xs text-red-600">{errors.sales_area_id}</p>
                    {/if}
                </div>
                <div>
                    <label for="password" class="mb-1 block text-sm font-medium text-slate-700">Password</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </span>
                        <input id="password" type="password" bind:value={form.password} autocomplete="new-password"
                            class="w-full rounded-lg border border-slate-300 bg-white py-2.5 pl-10 pr-3 text-sm text-slate-800 placeholder-slate-400 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20" required />
                    </div>
                    {#if errors.password}
                        <p class="mt-1 text-xs text-red-600">{errors.password}</p>
                    {/if}
                </div>
                <div>
                    <label for="password_confirmation" class="mb-1 block text-sm font-medium text-slate-700">Konfirmasi password</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </span>
                        <input id="password_confirmation" type="password" bind:value={form.password_confirmation} autocomplete="new-password"
                            class="w-full rounded-lg border border-slate-300 bg-white py-2.5 pl-10 pr-3 text-sm text-slate-800 placeholder-slate-400 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20" required />
                    </div>
                </div>
                <button type="submit" disabled={form.processing}
                    class="w-full rounded-lg bg-primary-600 py-2.5 text-sm font-semibold text-white transition hover:bg-primary-700 disabled:opacity-50">
                    {form.processing ? 'Mendaftar...' : 'Daftar'}
                </button>
            </form>

            <p class="mt-5 text-center text-sm text-slate-600">
                Sudah punya akun? <a href="/login" class="font-medium text-primary-600 hover:text-primary-700">Login</a>
            </p>

            <p class="mt-6 text-center text-xs leading-relaxed text-slate-400">
                Akun menunggu persetujuan admin sebelum bisa login.
            </p>
        </div>
    </main>
</div>
