<script>
    let { name = 'home', class: cls = 'h-5 w-5' } = $props();

    const icons = {
        home: '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><path d="M9 22V12h6v10"/>',
        users: '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
        box: '<path d="M3 8l9-5 9 5v8l-9 5-9-5z"/><path d="M3 8l9 5 9-5"/><path d="M12 13v9"/>',
        map: '<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0z"/><circle cx="12" cy="10" r="3"/>',
        cart: '<path d="M2 3h2l2.6 13a2 2 0 0 0 2 1.6h9.8a2 2 0 0 0 2-1.6l1.6-8.6H5"/><circle cx="9" cy="21" r="1"/><circle cx="19" cy="21" r="1"/>',
        chart: '<path d="M3 3v18h18"/><path d="M7 16v-4"/><path d="M12 16V8"/><path d="M17 16v-6"/>',
        monitor: '<rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8"/><path d="M12 17v4"/>',
        history: '<path d="M12 3a9 9 0 1 1-9 9"/><path d="M12 7v5l3 2"/><path d="M3 3v4h4"/>',
        truck: '<path d="M14 18V5H3v13h2"/><path d="M15 18h5v-6l-4-4h-1"/><path d="M15 5h2l4 4v9h-1"/><circle cx="7" cy="18" r="2"/><circle cx="17" cy="18" r="2"/>',
        referral: '<circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="m8.6 13.5 6.8 4"/><path d="m15.4 6.5-6.8 4"/>',
        logout: '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/>',
        chevron: '<path d="m6 9 6 6 6-6"/>',
    };
</script>

<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class={cls} aria-hidden="true">
    {@html icons[name] ?? icons.home}
</svg>
