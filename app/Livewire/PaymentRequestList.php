<?php

namespace App\Livewire;

use App\Models\PaymentRequest;
use App\Services\PagTesouroService;
use Livewire\Component;
use Livewire\WithPagination;

class PaymentRequestList extends Component
{
    use WithPagination;

    public $month;
    public $year;

    public function mount()
    {
        $this->month = now()->month;
        $this->year = now()->year;
    }

    public function checkStatus($id, PagTesouroService $pagTesouroService)
    {
        $paymentRequest = PaymentRequest::find($id);

        if ($paymentRequest && $paymentRequest->pagtesouro_id) {
            $response = $pagTesouroService->checkPaymentStatus($paymentRequest->pagtesouro_id);

            if ($response && isset($response['situacao']['codigo'])) {
                // Map PagTesouro status to our status
                $statusMap = [
                    'CRIADO' => 'Pendente',
                    'INICIADO' => 'Pendente',
                    'SUBMETIDO' => 'Pendente',
                    'CONCLUIDO' => 'Concluído',
                    'REJEITADO' => 'Rejeitado',
                    'CANCELADO' => 'Cancelado',
                ];

                $newStatus = $statusMap[$response['situacao']['codigo']] ?? 'Pendente';

                $paymentRequest->update([
                    'status' => $newStatus,
                    'tipo_pagamento_escolhido' => $response['tipoPagamentoEscolhido'] ?? null,
                    'nome_psp' => $response['nomePSP'] ?? null,
                    'transacao_psp' => $response['transacaoPSP'] ?? null,
                ]);

                session()->flash('message', 'Status atualizado com sucesso!');
            } else {
                session()->flash('error', 'Não foi possível obter o status.');
            }
        }
    }

    public function render()
    {
        $query = PaymentRequest::query();

        if ($this->month) {
            $query->whereMonth('created_at', $this->month);
        }

        if ($this->year) {
            $query->whereYear('created_at', $this->year);
        }

        return view('livewire.payment-request-list', [
            'paymentRequests' => $query->orderBy('created_at', 'desc')->paginate(10),
        ])->layout('layouts.app');
    }
}
