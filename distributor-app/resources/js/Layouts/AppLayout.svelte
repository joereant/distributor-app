<script>
    import { router, usePage } from '@inertiajs/svelte';

    let { children } = $props();

    const page = usePage();
    const user = $derived(page.props.auth?.user);

    function logout() {
        router.post('/logout');
    }
</script>

<div class="min-h-screen bg-gray-100">
    <header class="bg-white shadow-sm px-4 py-3 flex items-center justify-between">
        <div>
            <h1 class="font-bold text-gray-800">DistributorApp</h1>
            <p class="text-xs text-gray-500">Panel: {user?.role}</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-sm text-gray-700">{user?.name}</span>
            <button onclick={logout} class="text-sm text-red-600 hover:text-red-700">Logout</button>
        </div>
    </header>
    <main class="p-4">
        {@render children?.()}
    </main>
</div>
