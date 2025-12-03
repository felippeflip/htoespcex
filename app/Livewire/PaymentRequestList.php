<?php

namespace App\Livewire;

use App\Models\PaymentRequest;
use App\Services\PagTesouroService;
use Livewire\Component;
use Livewire\WithPagination;

class PaymentRequestList extends Component
{
    use WithPagination;

    public function checkStatus($id, PagTesouroService $pagTesouroService)
    {
        $paymentRequest = PaymentRequest::find($id);

        if ($paymentRequest && $paymentRequest->pagtesouro_id) {
            $response = $pagTesouroService->checkPaymentStatus($paymentRequest->pagtesouro_id);

            if ($response && isset($response['situacaoCodigo'])) {
                // Map PagTesouro status to our status
                // Example mapping, needs adjustment based on real API docs
                $statusMap = [
                    'CONCLUIDO' => 'PAID',
                    'PENDENTE' => 'PENDING',
                    'CANCELADO' => 'CANCELLED',
                ];

                $newStatus = $statusMap[$response['situacaoCodigo']] ?? 'PENDING';
                
                $paymentRequest->update(['status' => $newStatus]);
                
                session()->flash('message', 'Status atualizado com sucesso!');
            } else {
                session()->flash('error', 'Não foi possível obter o status.');
            }
        }
    }

    public function render()
    {
        return view('livewire.payment-request-list', [
            'paymentRequests' => PaymentRequest::where('user_id', auth()->id())
                ->orderBy('created_at', 'desc')
                ->paginate(10),
        ]);
    }
}
