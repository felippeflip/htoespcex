<?php

namespace App\Livewire;

use App\Models\PaymentRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class DashboardChart extends Component
{
    public $year;
    public $availableYears = [];

    public $chartLabels = [];
    public $chartCounts = [];
    public $chartAmounts = [];

    public function mount()
    {
        $this->year = Carbon::now()->year;

        // Get available years from database
        $years = PaymentRequest::select(DB::raw('YEAR(created_at) as year'))
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        $this->availableYears = array_unique(array_merge([Carbon::now()->year], $years));

        $this->loadChartData();
    }

    public function updatedYear()
    {
        $this->loadChartData();
    }

    public function loadChartData()
    {
        // Fetch data for the selected year
        $data = PaymentRequest::whereYear('created_at', $this->year)
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(amount) as total_amount')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Prepare data for Chart.js
        $chartData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthData = $data->firstWhere('month', $i);
            $chartData[] = [
                'month' => Carbon::create()->month($i)->translatedFormat('F'),
                'count' => $monthData ? $monthData->count : 0,
                'total_amount' => $monthData ? (float) $monthData->total_amount : 0,
            ];
        }

        // Pareto Sort (Desc by Amount)
        $paretoData = collect($chartData)->sortByDesc('total_amount')->values();

        $this->chartLabels = $paretoData->pluck('month')->toArray();
        $this->chartCounts = $paretoData->pluck('count')->toArray();
        $this->chartAmounts = $paretoData->pluck('total_amount')->toArray();
    }

    public function render()
    {
        return view('livewire.dashboard-chart');
    }
}
