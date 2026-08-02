<script>
    let { title = '', sub = '', action = '', class: cls = '', children } = $props();
</script>

<section class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200 {cls}">
    <header class="mb-4 flex items-start justify-between gap-3">
        <div>
            <h3 class="text-sm font-semibold text-slate-800">{title}</h3>
            {#if sub}
                <p class="mt-0.5 text-xs text-slate-500">{sub}</p>
            {/if}
        </div>
        {#if action}
            <span class="shrink-0 text-xs font-medium text-primary-700">{action}</span>
        {/if}
    </header>
    {@render children?.()}
</section>
