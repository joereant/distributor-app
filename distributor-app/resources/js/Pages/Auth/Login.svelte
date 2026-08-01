<script>
    import { useForm, usePage } from '@inertiajs/svelte';
    import AuthBrandPanel from '../../Components/AuthBrandPanel.svelte';

    const page = usePage();
    const form = useForm({
        email: '',
        password: '',
        remember: false,
    });

    const errors = $derived(page.props.errors ?? {});
    const flash = $derived(page.props.flash ?? {});
</script>

<div class="min-h-screen lg:grid lg:grid-cols-2">
    <AuthBrandPanel />

    <!-- Form -->
    <main class="flex items-center justify-center px-6 py-10 lg:py-0">
        <div class="w-full max-w-sm">
            <div class="mb-8 text-center lg:text-left">
                <h2 class="text-2xl font-bold tracking-tight text-slate-900">Selamat datang kembali</h2>
                <p class="mt-1 text-sm text-slate-500">Login untuk masuk ke panel Anda</p>
            </div>

            {#if errors.email}
                <p class="mb-3 text-sm text-red-600">{errors.email}</p>
            {/if}
            {#if flash?.message}
                <p class="mb-3 text-sm text-green-600">{flash.message}</p>
            {/if}
            {#if flash?.error}
                <p class="mb-3 text-sm text-red-600">{flash.error}</p>
            {/if}

            <a href="/auth/google"
                class="flex w-full items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                <svg class="h-4 w-4" viewBox="0 0 24 24" aria-hidden="true">
                    <path fill="#4285F4" d="M23.49 12.27c0-.79-.07-1.54-.19-2.27H12v4.51h6.47c-.29 1.48-1.14 2.73-2.4 3.58v3h3.86c2.26-2.09 3.56-5.17 3.56-8.82z"/>
                    <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.86-3c-1.08.72-2.45 1.16-4.07 1.16-3.13 0-5.78-2.11-6.73-4.96H1.29v3.09C3.26 21.3 7.31 24 12 24z"/>
                    <path fill="#FBBC05" d="M5.27 14.29c-.25-.72-.38-1.49-.38-2.29s.14-1.57.38-2.29V6.62H1.29C.47 8.24 0 10.06 0 12s.47 3.76 1.29 5.38l3.98-3.09z"/>
                    <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.31 0 3.26 2.7 1.29 6.62l3.98 3.09C6.22 6.86 8.87 4.75 12 4.75z"/>
                </svg>
                Login dengan Google
            </a>

            <div class="my-5 flex items-center gap-3">
                <span class="flex-1 border-t border-slate-200"></span>
                <span class="text-xs text-slate-400">atau</span>
                <span class="flex-1 border-t border-slate-200"></span>
            </div>

            <form onsubmit={(e) => { e.preventDefault(); form.post('/login'); }} class="space-y-4">
                <div>
                    <label for="email" class="mb-1 block text-sm font-medium text-slate-700">Email</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </span>
                        <input id="email" type="email" bind:value={form.email} autocomplete="email"
                            class="w-full rounded-lg border border-slate-300 bg-white py-2.5 pl-10 pr-3 text-sm text-slate-800 placeholder-slate-400 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20" required />
                    </div>
                </div>
                <div>
                    <label for="password" class="mb-1 block text-sm font-medium text-slate-700">Password</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </span>
                        <input id="password" type="password" bind:value={form.password} autocomplete="current-password"
                            class="w-full rounded-lg border border-slate-300 bg-white py-2.5 pl-10 pr-3 text-sm text-slate-800 placeholder-slate-400 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20" required />
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 text-sm text-slate-700">
                        <input type="checkbox" bind:checked={form.remember}
                            class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500" />
                        Ingat saya
                    </label>
                </div>
                <button type="submit" disabled={form.processing}
                    class="w-full rounded-lg bg-primary-600 py-2.5 text-sm font-semibold text-white transition hover:bg-primary-700 disabled:opacity-50">
                    {form.processing ? 'Masuk...' : 'Login'}
                </button>
            </form>

            <p class="mt-5 text-center text-sm text-slate-600">
                Belum punya akun? <a href="/register" class="font-medium text-primary-600 hover:text-primary-700">Daftar</a>
            </p>

            <p class="mt-6 text-center text-xs leading-relaxed text-slate-400">
                Demo:<br />
                owner@demo.com / admin@demo.com / sales@demo.com / customer@demo.com<br />
                Password: <b>password</b>
            </p>
        </div>
    </main>
</div>
