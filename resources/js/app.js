

import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

window.Chart = Chart;

Alpine.data('paretoChart', (initialData) => ({
    labels: initialData.labels,
    counts: initialData.counts,
    amounts: initialData.amounts,
    chart: null,
    isLoading: true,
    error: null,

    init() {
        this.$watch('labels', () => this.updateChart());

        // Wait for next tick to ensure DOM is ready
        this.$nextTick(() => {
            this.drawChart();
            this.isLoading = false;
        });
    },

    drawChart() {
        if (!this.$refs.canvas) return;

        try {
            const ctx = this.$refs.canvas.getContext('2d');

            // Destroy existing chart instance if it exists
            const existingChart = Chart.getChart(this.$refs.canvas);
            if (existingChart) existingChart.destroy();
            if (this.chart) this.chart.destroy();

            this.chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: Alpine.raw(this.labels),
                    datasets: [
                        {
                            label: 'Valor Total (R$)',
                            data: Alpine.raw(this.amounts),
                            backgroundColor: 'rgba(79, 70, 229, 0.6)',
                            borderColor: 'rgba(79, 70, 229, 1)',
                            borderWidth: 1,
                            yAxisID: 'y',
                            order: 2
                        },
                        {
                            label: 'Quantidade',
                            data: Alpine.raw(this.counts),
                            type: 'line',
                            borderColor: 'rgba(239, 68, 68, 1)',
                            backgroundColor: 'rgba(239, 68, 68, 0.2)',
                            borderWidth: 2,
                            tension: 0.3,
                            yAxisID: 'y1',
                            order: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    scales: {
                        y: {
                            type: 'linear', display: true, position: 'left',
                            title: { display: true, text: 'Valor (R$)' },
                            ticks: { callback: (val) => 'R$ ' + val.toLocaleString('pt-BR') }
                        },
                        y1: {
                            type: 'linear', display: true, position: 'right',
                            title: { display: true, text: 'Qtd' },
                            grid: { drawOnChartArea: false }
                        }
                    }
                }
            });
        } catch (e) {
            console.error('Error drawing chart:', e);
            this.error = 'Erro ao renderizar o gráfico.';
        }
    },

    updateChart() {
        if (this.chart) {
            this.chart.data.labels = Alpine.raw(this.labels);
            this.chart.data.datasets[0].data = Alpine.raw(this.amounts);
            this.chart.data.datasets[1].data = Alpine.raw(this.counts);
            this.chart.update();
        } else {
            this.drawChart();
        }
    }
}));

if (!window.Alpine) {
    window.Alpine = Alpine;
    Alpine.start();
}

console.log('App.js loaded, Chart.js version:', Chart.version);
