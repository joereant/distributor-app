<script>
    let { status = 'Pending' } = $props();

    const styles = {
        Pending: 'bg-accent-50 text-accent-700 ring-accent-100',
        Proses: 'bg-blue-50 text-blue-700 ring-blue-100',
        Dikirim: 'bg-primary-50 text-primary-700 ring-primary-100',
        Selesai: 'bg-emerald-50 text-emerald-700 ring-emerald-100',
    };
</script>

<span class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-medium ring-1 {styles[status] ?? styles.Pending}">
    {status}
</span>
