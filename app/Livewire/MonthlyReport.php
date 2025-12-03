<?php

namespace App\Livewire;

use App\Models\PaymentRequest;
use Carbon\Carbon;
use Livewire\Component;

class MonthlyReport extends Component
{
    public $month;
    public $year;

    public function mount()
    {
        $this->month = Carbon::now()->month;
        $this->year = Carbon::now()->year;
    }

    public function render()
    {
        $startDate = Carbon::createFromDate($this->year, $this->month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($this->year, $this->month, 1)->endOfMonth();

        $payments = PaymentRequest::whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $totalAmount = $payments->sum('amount');
        $totalPaid = $payments->where('status', 'PAID')->sum('amount');
        $totalPending = $payments->where('status', 'PENDING')->sum('amount');

        return view('livewire.monthly-report', [
            'payments' => $payments,
            'totalAmount' => $totalAmount,
            'totalPaid' => $totalPaid,
            'totalPending' => $totalPending,
        ])->layout('layouts.app');
    }
}
