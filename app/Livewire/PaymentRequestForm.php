<?php

namespace App\Livewire;

use App\Models\PaymentRequest;
use App\Services\PagTesouroService;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Livewire\Component;

class PaymentRequestForm extends Component
{
    // Campos do formulário
    public $cnpj_cpf;
    public $nome_contribuinte;
    public $valor_principal;
    public $valor_descontos = 0;
    public $valor_outras_deducoes = 0;
    public $valor_multa = 0;
    public $valor_juros = 0;
    public $valor_outros_acrescimos = 0;
    public $due_date;
    public $description;

    // Campos automáticos/configuração
    public $codigo_servico;

    protected $rules = [
        'cnpj_cpf' => 'required|string|max:14', // Idealmente adicionar validação de CPF/CNPJ
        'nome_contribuinte' => 'required|string|min:2|max:45',
        'valor_principal' => 'required|numeric|min:0.01',
        'valor_descontos' => 'nullable|numeric|min:0',
        'valor_outras_deducoes' => 'nullable|numeric|min:0',
        'valor_multa' => 'nullable|numeric|min:0',
        'valor_juros' => 'nullable|numeric|min:0',
        'valor_outros_acrescimos' => 'nullable|numeric|min:0',
        'due_date' => 'nullable|date|after_or_equal:today',
        'description' => 'nullable|string|max:255',
    ];

    public function mount()
    {
        // Carregar código do serviço da configuração
        $config = \Illuminate\Support\Facades\DB::table('configurations')
            ->where('key', 'pagtesouro_codigo_servico')
            ->first();

        $this->codigo_servico = $config ? $config->value : null;

        if (!$this->codigo_servico) {
            session()->flash('error', 'Código de Serviço não configurado no sistema.');
        }
    }

    public function save(PagTesouroService $pagTesouroService)
    {
        $this->validate();

        if (!$this->codigo_servico) {
            session()->flash('error', 'Código de Serviço não configurado.');
            return;
        }

        // Gerar Referência (Sequencial 6 dígitos)
        $lastId = PaymentRequest::max('id') ?? 0;
        $referenceCode = str_pad($lastId + 1, 6, '0', STR_PAD_LEFT);

        // Gerar Competência (MMAAAA)
        $competencia = now()->format('mY');

        // Calcular valor total (apenas para registro, o PagTesouro calcula baseado nos componentes)
        $amount = $this->valor_principal - $this->valor_descontos - $this->valor_outras_deducoes + $this->valor_multa + $this->valor_juros + $this->valor_outros_acrescimos;

        // Dados para o PagTesouro
        $data = [
            'codigoServico' => $this->codigo_servico,
            'referencia' => $referenceCode,
            'competencia' => $competencia,
            'vencimento' => $this->due_date ? Carbon::parse($this->due_date)->format('dmY') : null,
            'cnpjCpf' => $this->cnpj_cpf,
            'nomeContribuinte' => $this->nome_contribuinte,
            'valorPrincipal' => number_format($this->valor_principal, 2, '.', ''),
            'valorDescontos' => number_format($this->valor_descontos, 2, '.', ''),
            'valorOutrasDeducoes' => number_format($this->valor_outras_deducoes, 2, '.', ''),
            'valorMulta' => number_format($this->valor_multa, 2, '.', ''),
            'valorJuros' => number_format($this->valor_juros, 2, '.', ''),
            'valorOutrosAcrescimos' => number_format($this->valor_outros_acrescimos, 2, '.', ''),
            'modoNavegacao' => 2, // Nova aba
        ];

        // Remover campos nulos ou vazios que não são obrigatórios
        $data = array_filter($data, function ($value) {
            return !is_null($value) && $value !== '';
        });

        // Call PagTesouro API
        $response = $pagTesouroService->createPaymentRequest($data);

        if ($response && isset($response['idPagamento'])) {
            $paymentRequest = PaymentRequest::create([
                'user_id' => auth()->id(),
                'reference_code' => $referenceCode,
                'codigo_servico' => $this->codigo_servico,
                'competencia' => $competencia,
                'cnpj_cpf' => $this->cnpj_cpf,
                'nome_contribuinte' => $this->nome_contribuinte,
                'amount' => $amount,
                'valor_principal' => $this->valor_principal,
                'valor_descontos' => $this->valor_descontos,
                'valor_outras_deducoes' => $this->valor_outras_deducoes,
                'valor_multa' => $this->valor_multa,
                'valor_juros' => $this->valor_juros,
                'valor_outros_acrescimos' => $this->valor_outros_acrescimos,
                'description' => $this->description,
                'due_date' => $this->due_date,
                'status' => 'Pendente', // Ou mapear do response['situacao']['codigo']
                'pagtesouro_id' => $response['idPagamento'],
                'proxima_url' => $response['proximaUrl'] ?? null,
                'modo_navegacao' => 2,
            ]);

            if (isset($response['proximaUrl'])) {
                // $this->dispatch('payment-created', url: $response['proximaUrl']); // Removido
                session()->flash('paymentUrl', $response['proximaUrl']);
                session()->flash('paymentId', $paymentRequest->id); // Para identificar na lista
                session()->flash('message', 'Solicitação criada com sucesso!');
                return redirect()->route('payment-requests.index');
            }

            session()->flash('message', 'Solicitação criada, mas URL de pagamento não retornada.');
            return redirect()->route('payment-requests.index');
        } else {
            session()->flash('error', 'Erro ao comunicar com PagTesouro.');
            return;
        }
    }

    public function render()
    {
        return view('livewire.payment-request-form')->layout('layouts.app');
    }
}
