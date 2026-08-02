<script>
    let { tone = 'light', showText = true, size = 'md' } = $props();

    const icon = $derived(tone === 'light' ? '/images/Icon.svg' : '/images/Icon Dark.svg');
    const long = $derived(tone === 'light' ? '/images/Logo Long Sentra - white.svg' : '/images/Logo Long Sentra.svg');
    const iconBox = $derived(size === 'sm' ? 'h-8 w-8' : 'h-9 w-9');
    const longBox = $derived(size === 'sm' ? 'h-6 w-auto' : 'h-7 w-auto');
</script>

{#if showText}
    <img src={long} alt="SentraX" class="{longBox} shrink-0" />
{:else}
    <img src={icon} alt="SentraX" class="{iconBox} shrink-0" />
{/if}
