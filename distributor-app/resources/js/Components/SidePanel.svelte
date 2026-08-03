<script>
    let { open = false, title = '', subtitle = '', size = 'md', onclose, children } = $props();

    const sizes = {
        sm: 'max-w-sm',
        md: 'max-w-[540px]',
        lg: 'max-w-[540px]',
        xl: 'max-w-xl',
    };

    function handleBackdrop(e) {
        if (e.target === e.currentTarget) onclose?.();
    }

    function handleKey(e) {
        if (e.key === 'Escape') onclose?.();
    }
</script>

<svelte:window onkeydown={handleKey} />

{#if open}
    <!-- Backdrop -->
    <!-- svelte-ignore a11y_click_events_have_key_events a11y_no_static_element_interactions -->
    <div
        class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-[2px] transition-opacity"
        onclick={handleBackdrop}
        aria-hidden="true"
    ></div>

    <!-- Panel -->
    <aside class="fixed inset-x-0 bottom-0 z-50 flex max-h-[90vh] w-full flex-col rounded-t-3xl bg-white shadow-2xl transition-all duration-300 lg:inset-y-0 lg:right-0 lg:left-auto lg:h-full lg:max-h-none lg:w-full lg:rounded-none lg:{sizes[size]}">
        <!-- Mobile drag handle bar -->
        <div class="flex justify-center pt-3 pb-1 lg:hidden">
            <div class="h-1.5 w-12 rounded-full bg-slate-200"></div>
        </div>

        <!-- Header -->
        <header class="flex shrink-0 items-start justify-between gap-3 border-b border-slate-100 px-4 sm:px-6 py-4 lg:py-5">
            <div>
                <h2 class="text-lg font-bold text-slate-800">{title}</h2>
                {#if subtitle}
                    <p class="mt-0.5 text-sm text-slate-500">{subtitle}</p>
                {/if}
            </div>
            <button
                onclick={onclose}
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
                aria-label="Tutup"
            >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="h-5 w-5">
                    <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                </svg>
            </button>
        </header>

        <!-- Body -->
        <div class="flex-1 overflow-y-auto px-4 sm:px-6 py-4 sm:py-5">
            {@render children?.()}
        </div>
    </aside>
{/if}
