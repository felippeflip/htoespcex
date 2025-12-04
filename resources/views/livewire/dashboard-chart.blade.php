<div class="p-6 bg-white border-b border-gray-200" x-data="{
        labels: @entangle('chartLabels'),
        counts: @entangle('chartCounts'),
        amounts: @entangle('chartAmounts'),
        chart: null,
        isLoading: true,
        error: null,
        init() {
            this.$watch('labels', () => this.updateChart());
            
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
                const existingChart = window.Chart.getChart(this.$refs.canvas);
                if (existingChart) existingChart.destroy();
                if (this.chart) this.chart.destroy();

                this.chart = new window.Chart(ctx, {
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
    }">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-medium text-gray-900">Análise Mensal (Pareto)</h3>
        <select wire:model.live="year"
            class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            @foreach($availableYears as $y)
                <option value="{{ $y }}">{{ $y }}</option>
            @endforeach
        </select>
    </div>

    {{-- Debug Info --}}
    <div x-show="!window.Chart" class="text-xs text-red-500 mb-2" style="display: none;">
        Chart.js global not found.
    </div>

    <div class="relative h-96 w-full" wire:ignore>
        <div x-show="isLoading" class="absolute inset-0 flex items-center justify-center bg-gray-50 bg-opacity-75 z-10">
            <span class="text-gray-500">Carregando gráfico...</span>
        </div>
        <div x-show="error" class="absolute inset-0 flex items-center justify-center bg-red-50 bg-opacity-75 z-10"
            style="display: none;">
            <span class="text-red-500" x-text="error"></span>
        </div>
        <canvas x-ref="canvas" style="width: 100%; height: 100%;"></canvas>
    </div>
</div>