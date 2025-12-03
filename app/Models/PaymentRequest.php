<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reference_code',
        'amount',
        'description',
        'status',
        'pagtesouro_id',
        'due_date',
        'codigo_servico',
        'competencia',
        'cnpj_cpf',
        'nome_contribuinte',
        'valor_principal',
        'valor_descontos',
        'valor_outras_deducoes',
        'valor_multa',
        'valor_juros',
        'valor_outros_acrescimos',
        'proxima_url',
        'modo_navegacao',
        'tipo_pagamento_escolhido',
        'nome_psp',
        'transacao_psp',
    ];

    protected $casts = [
        'due_date' => 'date',
        'amount' => 'decimal:2',
        'valor_principal' => 'decimal:2',
        'valor_descontos' => 'decimal:2',
        'valor_outras_deducoes' => 'decimal:2',
        'valor_multa' => 'decimal:2',
        'valor_juros' => 'decimal:2',
        'valor_outros_acrescimos' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
