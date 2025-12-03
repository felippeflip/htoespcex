<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payment_requests', function (Blueprint $table) {
            $table->string('codigo_servico')->nullable();
            $table->string('competencia', 6)->nullable();
            $table->string('cnpj_cpf', 14)->nullable();
            $table->string('nome_contribuinte')->nullable();
            $table->decimal('valor_principal', 15, 2)->default(0);
            $table->decimal('valor_descontos', 15, 2)->default(0);
            $table->decimal('valor_outras_deducoes', 15, 2)->default(0);
            $table->decimal('valor_multa', 15, 2)->default(0);
            $table->decimal('valor_juros', 15, 2)->default(0);
            $table->decimal('valor_outros_acrescimos', 15, 2)->default(0);
            $table->text('proxima_url')->nullable();
            $table->integer('modo_navegacao')->default(2);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_requests', function (Blueprint $table) {
            //
        });
    }
};
