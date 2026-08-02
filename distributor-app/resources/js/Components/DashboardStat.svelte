<script>
    import Icon from './Icon.svelte';

    let { label = '', value = '0', sub = '', icon = 'box', tone = 'primary' } = $props();

    const tones = {
        primary: 'bg-primary-50 text-primary-700',
        accent: 'bg-accent-50 text-accent-700',
        slate: 'bg-slate-100 text-slate-600',
    };
</script>

<div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
    <div class="flex items-center justify-between">
        <p class="text-xs font-medium text-slate-500">{label}</p>
        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg {tones[tone] ?? tones.primary}">
            <Icon name={icon} class="h-4 w-4" />
        </span>
    </div>
    <p class="mt-2 text-xl font-bold tracking-tight text-slate-800">{value}</p>
    {#if sub}
        <p class="mt-1 text-xs text-slate-500">{sub}</p>
    {/if}
</div>
