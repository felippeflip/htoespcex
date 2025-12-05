<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Solicitações de Pagamento') }}
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                    <h2 class="text-xl font-semibold text-gray-800">Solicitações de Pagamento</h2>

                    <div class="flex gap-4">
                        <div>
                            <select wire:model.live="month" id="month"
                                class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                <option value="">Todos os Meses</option>
                                @foreach(range(1, 12) as $m)
                                    <option value="{{ $m }}">
                                        {{ Carbon\Carbon::create()->month($m)->locale('pt_BR')->monthName }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <select wire:model.live="year" id="year"
                                class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                <option value="">Todos os Anos</option>
                                @foreach(range(Carbon\Carbon::now()->year - 5, Carbon\Carbon::now()->year + 1) as $y)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                        <a href="{{ route('payment-requests.create') }}"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 whitespace-nowrap">Nova
                            Solicitação</a>
                    </div>
                </div>

                @if (session()->has('message'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                        role="alert">
                        <span class="block sm:inline">{{ session('message') }}</span>
                    </div>
                @endif

                @if (session()->has('paymentUrl'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                        role="alert">
                        <span class="font-bold">Solicitação criada com sucesso!</span>
                        <span class="block sm:inline">Clique <a href="{{ session('paymentUrl') }}" target="_blank"
                                rel="noopener noreferrer" class="underline font-bold">aqui para abrir o pagamento</a> em uma
                            nova aba.</span>
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Referência</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Contribuinte</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Usuário</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Descrição</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Valor</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Vencimento</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($paymentRequests as $request)
                                                    <tr>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                            {{ $request->reference_code }}
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                            {{ $request->nome_contribuinte }}<br>
                                                            <span class="text-xs text-gray-400">{{ $request->cnpj_cpf }}</span>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                            {{ $request->user->name ?? 'N/A' }}
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                            {{ Str::limit($request->description, 30) }}
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                            R$ {{ number_format($request->amount, 2, ',', '.') }}
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                            {{ $request->due_date ? $request->due_date->format('d/m/Y') : '-' }}
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <span
                                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                                                                                                                                                                                    {{ $request->status === 'Concluído' ? 'bg-green-100 text-green-800' :
                                ($request->status === 'Pendente' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                                {{ $request->status }}
                                                            </span>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                            <div class="flex flex-col space-y-2">
                                                                <button wire:click="checkStatus({{ $request->id }})"
                                                                    class="text-indigo-600 hover:text-indigo-900 text-left">
                                                                    Atualizar Status
                                                                </button>

                                                                @if (session()->has('paymentUrl') && session('paymentId') == $request->id)
                                                                    <a href="{{ session('paymentUrl') }}" target="_blank" rel="noopener noreferrer"
                                                                        class="inline-flex items-center justify-center px-3 py-1 border border-transparent text-xs font-medium rounded text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                                        Abrir Pagamento
                                                                    </a>
                                                                @endif
                                                            </div>
                                                        </td>
                                                    </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $paymentRequests->links() }}
                </div>
            </div>
        </div>
    </div>
</div>