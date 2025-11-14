<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Histórico de Transações') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Saldo Atual -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-2xl font-bold mb-2">Saldo Atual</h3>
                    <p class="text-3xl text-green-600">R$ {{ number_format(Auth::user()->balance, 2, ',', '.') }}</p>
                </div>
            </div>

            <!-- Ações Rápidas -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <a href="{{ route('deposit.form') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-4 px-6 rounded text-center">
                    Fazer Depósito
                </a>
                <a href="{{ route('transfer.form') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-4 px-6 rounded text-center">
                    Fazer Transferência
                </a>
            </div>

            <!-- Mensagens de Sucesso/Erro -->
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Tabela de Transações -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-xl font-bold mb-4">Histórico</h3>
                    
                    @if($transactions->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descrição</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($transactions as $transaction)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $transaction->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                @if($transaction->type == 'deposit')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                        Depósito
                                                    </span>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                                        Transferência
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900">
                                                {{ $transaction->description }}
                                                @if($transaction->type == 'transfer')
                                                    <br>
                                                    @if($transaction->sender_id == Auth::id())
                                                        <span class="text-xs text-gray-500">Para: {{ $transaction->receiver->name }}</span>
                                                    @else
                                                        <span class="text-xs text-gray-500">De: {{ $transaction->sender->name }}</span>
                                                    @endif
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                @if($transaction->sender_id == Auth::id())
                                                    <span class="text-red-600">- R$ {{ number_format($transaction->amount, 2, ',', '.') }}</span>
                                                @else
                                                    <span class="text-green-600">+ R$ {{ number_format($transaction->amount, 2, ',', '.') }}</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                @if($transaction->status == 'completed')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        Concluída
                                                    </span>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                        Revertida
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                @if($transaction->status == 'completed')
                                                    <form action="{{ route('transactions.reverse', $transaction->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja reverter esta transação?');">
                                                        @csrf
                                                        <button type="submit" class="text-red-600 hover:text-red-900">Reverter</button>
                                                    </form>
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $transactions->links() }}
                        </div>
                    @else
                        <p class="text-gray-500">Nenhuma transação encontrada.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
