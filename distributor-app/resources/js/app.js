import './bootstrap';
import { createInertiaApp } from '@inertiajs/svelte';
import { mount } from 'svelte';

createInertiaApp({
    title: title => title ? `${title} - SentraX` : 'SentraX',
    resolve: name => {
        const pages = import.meta.glob('./Pages/**/*.svelte');
        return pages[`./Pages/${name}.svelte`]();
    },
    setup({ el, App, props }) {
        mount(App, { target: el, props });
    },
});