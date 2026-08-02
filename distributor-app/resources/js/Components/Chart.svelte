<script>
    import ApexCharts from 'apexcharts';

    let {
        type = 'area',
        series = [],
        categories = [],
        labels = [],
        height = 260,
        horizontal = false,
        colors = [],
        formatter = (v) => v,
    } = $props();

    const palette = ['#0d9488', '#f59e0b', '#3b82f6', '#8b5cf6', '#f43f5e', '#10b981'];
    const chartColors = $derived(colors.length ? colors : palette);

    let el;
    let chart;

    function buildOptions() {
        return {
            chart: {
                type,
                height,
                toolbar: { show: false },
                fontFamily: "'Inter', sans-serif",
                animations: { enabled: true, speed: 650, animateGradually: { enabled: true } },
            },
            colors: chartColors,
            series,
            labels,
            dataLabels: { enabled: false },
            grid: { borderColor: '#e2e8f0', strokeDashArray: 4, padding: { left: 8, right: 8 } },
            stroke: { curve: 'smooth', width: 2.5 },
            fill: {
                type: 'gradient',
                gradient: { shadeIntensity: 1, opacityFrom: 0.3, opacityTo: 0.04, stops: [0, 90, 100] },
            },
            xaxis: {
                categories,
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: { style: { colors: '#94a3b8', fontSize: '11px', fontWeight: 500 } },
            },
            yaxis: {
                labels: { style: { colors: '#94a3b8', fontSize: '11px' }, formatter },
            },
            legend: {
                position: 'top',
                horizontalAlign: 'right',
                fontSize: '12px',
                markers: { size: 5, strokeWidth: 0 },
                labels: { colors: '#64748b' },
            },
            tooltip: {
                theme: 'light',
                y: { formatter },
            },
            plotOptions: {
                bar: {
                    borderRadius: 6,
                    columnWidth: '45%',
                    horizontal,
                    barHeight: '55%',
                    dataLabels: { position: 'top' },
                },
                pie: {
                    donut: {
                        size: '72%',
                        labels: {
                            show: true,
                            name: { fontSize: '12px', colors: '#475569' },
                            value: { fontSize: '14px', fontWeight: 700, colors: '#0f172a' },
                            total: {
                                show: true,
                                label: 'Total',
                                fontSize: '11px',
                                fontWeight: 600,
                                color: '#94a3b8',
                                formatter: () => ' ',
                            },
                        },
                    },
                },
            },
        };
    }

    $effect(() => {
        if (!el) return;
        if (!chart) {
            chart = new ApexCharts(el, buildOptions());
            chart.render();
            return;
        }
        chart.updateOptions(buildOptions(), false, true);
        chart.updateSeries(series, true);
    });
</script>

<div bind:this={el}></div>
