<script>
    import { router, usePage } from '@inertiajs/svelte';
    import AppLayout from '../../Layouts/AppLayout.svelte';

    const page = usePage();

    const users = $derived(page.props.users ?? []);
    const flash = $derived(page.props.flash?.message);

    const roleLabel = {
        owner: 'Owner',
        admin: 'Admin',
        sales: 'Sales',
        customer: 'Customer',
    };

    const statusStyle = {
        pending: 'bg-accent-50 text-accent-700 ring-accent-100',
        active: 'bg-emerald-50 text-emerald-700 ring-emerald-100',
        rejected: 'bg-red-50 text-red-700 ring-red-100',
    };

    function approve(id) {
        router.post(`/admin/users/${id}/approve`);
    }

    function reject(id) {
        router.post(`/admin/users/${id}/reject`);
    }
</script>

<AppLayout>
    <div>
        <header class="mb-6">
            <h2 class="text-xl font-bold text-slate-800">Kelola User</h2>
            <p class="mt-0.5 text-sm text-slate-500">Setujui akun customer yang mendaftar.</p>
        </header>

        {#if flash}
            <div class="mb-6 rounded-xl bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 ring-1 ring-emerald-100">{flash}</div>
        {/if}

        <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-left text-xs uppercase tracking-wide text-slate-400">
                            <th class="px-5 py-3 font-semibold">Nama</th>
                            <th class="px-5 py-3 font-semibold">Email</th>
                            <th class="px-5 py-3 font-semibold">Role</th>
                            <th class="px-5 py-3 font-semibold">Area</th>
                            <th class="px-5 py-3 font-semibold">Status</th>
                            <th class="px-5 py-3 text-right font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        {#each users as u (u.id)}
                            <tr>
                                <td class="px-5 py-3 font-medium text-slate-700">{u.name}</td>
                                <td class="px-5 py-3 text-slate-500">{u.email}</td>
                                <td class="px-5 py-3 text-slate-500">{roleLabel[u.role] ?? u.role}</td>
                                <td class="px-5 py-3 text-slate-500">{u.area ?? '—'}</td>
                                <td class="px-5 py-3">
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-medium capitalize ring-1 {statusStyle[u.status] ?? statusStyle.pending}">
                                        {u.status}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-right">
                                    {#if u.status === 'pending'}
                                        <div class="flex justify-end gap-2">
                                            <button onclick={() => approve(u.id)} class="rounded-lg bg-primary-600 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-primary-700">Setujui</button>
                                            <button onclick={() => reject(u.id)} class="rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600 transition hover:bg-red-50 hover:text-red-600">Tolak</button>
                                        </div>
                                    {:else}
                                        <span class="text-xs text-slate-400">—</span>
                                    {/if}
                                </td>
                            </tr>
                        {/each}
                    </tbody>
                </table>
            </div>
            {#if users.length === 0}
                <p class="px-5 py-10 text-center text-sm text-slate-400">Belum ada user.</p>
            {/if}
        </div>
    </div>
</AppLayout>
