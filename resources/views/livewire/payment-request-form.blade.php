<div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">
    <form wire:submit.prevent="save" class="space-y-6 bg-white p-6 rounded-lg shadow-md">
        
        @if (session()->has('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Dados do Contribuinte -->
            <div class="col-span-2">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Dados do Contribuinte</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="cnpj_cpf" class="block text-sm font-medium text-gray-700">CPF/CNPJ</label>
                        <input type="text" wire:model="cnpj_cpf" id="cnpj_cpf" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" placeholder="Apenas números">
                        @error('cnpj_cpf') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="nome_contribuinte" class="block text-sm font-medium text-gray-700">Nome do Contribuinte</label>
                        <input type="text" wire:model="nome_contribuinte" id="nome_contribuinte" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        @error('nome_contribuinte') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Valores -->
            <div class="col-span-2">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Valores</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="valor_principal" class="block text-sm font-medium text-gray-700">Valor Principal (R$)</label>
                        <input type="number" step="0.01" wire:model="valor_principal" id="valor_principal" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        @error('valor_principal') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="valor_descontos" class="block text-sm font-medium text-gray-700">Descontos (R$)</label>
                        <input type="number" step="0.01" wire:model="valor_descontos" id="valor_descontos" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        @error('valor_descontos') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="valor_outras_deducoes" class="block text-sm font-medium text-gray-700">Outras Deduções (R$)</label>
                        <input type="number" step="0.01" wire:model="valor_outras_deducoes" id="valor_outras_deducoes" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        @error('valor_outras_deducoes') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="valor_multa" class="block text-sm font-medium text-gray-700">Multa (R$)</label>
                        <input type="number" step="0.01" wire:model="valor_multa" id="valor_multa" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        @error('valor_multa') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="valor_juros" class="block text-sm font-medium text-gray-700">Juros (R$)</label>
                        <input type="number" step="0.01" wire:model="valor_juros" id="valor_juros" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        @error('valor_juros') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="valor_outros_acrescimos" class="block text-sm font-medium text-gray-700">Outros Acréscimos (R$)</label>
                        <input type="number" step="0.01" wire:model="valor_outros_acrescimos" id="valor_outros_acrescimos" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        @error('valor_outros_acrescimos') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Outras Informações -->
            <div class="col-span-2">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Outras Informações</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="due_date" class="block text-sm font-medium text-gray-700">Data de Vencimento</label>
                        <input type="date" wire:model="due_date" id="due_date" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        @error('due_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Descrição (Opcional)</label>
                        <textarea wire:model="description" id="description" rows="3" class="mt-1 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"></textarea>
                        @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-5">
            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Gerar Solicitação e Pagar
            </button>
        </div>
    </form>
</div>
