<div class="p-6 bg-white border-b border-gray-200">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-medium text-gray-900">Análise Mensal (Pareto)</h3>
        <select wire:model.live="year"
            class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            @foreach($availableYears as $y)
                <option value="{{ $y }}">{{ $y }}</option>
            @endforeach
        </select>
    </div>

    <div class="relative h-96 w-full" wire:ignore>
        <canvas id="paretoChart"></canvas>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('livewire:initialized', () => {
            const ctx = document.getElementById('paretoChart').getContext('2d');
            let chart;

            const initChart = (labels, counts, amounts) => {
                if (chart) {
                    chart.destroy();
                }

                chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Valor Total (R$)',
                                data: amounts,
                                backgroundColor: 'rgba(79, 70, 229, 0.6)', // Indigo-600
                                borderColor: 'rgba(79, 70, 229, 1)',
                                borderWidth: 1,
                                yAxisID: 'y',
                                order: 2
                            },
                            {
                                label: 'Quantidade de Solicitações',
                                data: counts,
                                type: 'line',
                                borderColor: 'rgba(239, 68, 68, 1)', // Red-500
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
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        scales: {
                            y: {
                                type: 'linear',
                                display: true,
                                position: 'left',
                                title: {
                                    display: true,
                                    text: 'Valor (R$)'
                                },
                                ticks: {
                                    callback: function (value) {
                                        return 'R$ ' + value.toLocaleString('pt-BR');
                                    }
                                }
                            },
                            y1: {
                                type: 'linear',
                                display: true,
                                position: 'right',
                                title: {
                                    display: true,
                                    text: 'Quantidade'
                                },
                                grid: {
                                    drawOnChartArea: false,
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.dataset.yAxisID === 'y') {
                                            label += new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(context.raw);
                                        } else {
                                            label += context.raw;
                                        }
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
            };

            // Initial load
            initChart(@json($chartLabels), @json($chartCounts), @json($chartAmounts));

            // Listen for updates
            Livewire.on('chart-updated', (data) => {
                // We need to pass data from PHP to JS. 
                // Since we are using wire:model.live, the component re-renders.
                // But we used wire:ignore on the canvas div, so the canvas stays.
                // We need to re-init the chart with new data.
                // Actually, if we use wire:ignore, the script inside might not re-run with new blade variables.
                // Better approach: Use a watcher in Alpine or a Livewire event.
            });

            // Since the script tag is inside the component but outside wire:ignore (wait, no, it's inside the file),
            // when Livewire updates, it might re-execute scripts if they are newly injected, but usually it doesn't.
            // A better way is to use Alpine.js to bridge the data.
        });
    </script>

    {{-- Better approach with Alpine to handle reactivity --}}
    <div x-data="{ 
        labels: @entangle('chartLabels'), 
        counts: @entangle('chartCounts'), 
        amounts: @entangle('chartAmounts'),
        chart: null,
        init() {
            this.$watch('labels', () => this.updateChart());
            // Wait for Chart.js to load if using CDN in head, but here we put it in body.
            // We might need to wait a bit or check if Chart is defined.
            if (typeof Chart === 'undefined') {
                const script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
                script.onload = () => this.drawChart();
                document.head.appendChild(script);
            } else {
                this.drawChart();
            }
        },
        drawChart() {
            const ctx = document.getElementById('paretoChartAlpine').getContext('2d');
            if (this.chart) this.chart.destroy();
            
            this.chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: this.labels,
                    datasets: [
                        {
                            label: 'Valor Total (R$)',
                            data: this.amounts,
                            backgroundColor: 'rgba(79, 70, 229, 0.6)',
                            borderColor: 'rgba(79, 70, 229, 1)',
                            borderWidth: 1,
                            yAxisID: 'y',
                            order: 2
                        },
                        {
                            label: 'Quantidade',
                            data: this.counts,
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
        },
        updateChart() {
            if (this.chart) {
                this.chart.data.labels = this.labels;
                this.chart.data.datasets[0].data = this.amounts;
                this.chart.data.datasets[1].data = this.counts;
                this.chart.update();
            } else {
                this.drawChart();
            }
        }
    }" class="relative h-96 w-full mt-4" wire:ignore>
        <canvas id="paretoChartAlpine"></canvas>
    </div>
</div>