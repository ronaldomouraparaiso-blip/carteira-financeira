<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __("Fazer Transferência") }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Saldo Atual -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-2">Saldo Atual</h3>
                    <p class="text-2xl text-green-600">R$ {{ number_format(Auth::user()->balance, 2, ',', '.') }}</p>
                </div>
            </div>

            <!-- Mensagens de Erro -->
            @if (session("error"))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session("error") }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Formulário de Transferência -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('transfer') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="receiver_id" class="block text-gray-700 text-sm font-bold mb-2">
                                Destinatário
                            </label>
                            <select name="receiver_id" id="receiver_id" 
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    required>
                                <option value="">Selecione um usuário</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('receiver_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="amount" class="block text-gray-700 text-sm font-bold mb-2">
                                Valor da Transferência (R$)
                            </label>
                            <input type="number" name="amount" id="amount" step="0.01" min="0.01" 
                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                   placeholder="0.00" value="{{ old('amount') }}" required>
                        </div>

                        <div class="mb-6">
                            <label for="description" class="block text-gray-700 text-sm font-bold mb-2">
                                Descrição (Opcional)
                            </label>
                            <textarea name="description" id="description" rows="3"
                                      class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                      placeholder="Motivo da transferência">{{ old('description') }}</textarea>
                        </div>

                        <div class="flex items-center justify-between">
                            <button type="submit" 
                                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Confirmar Transferência
                            </button>
                            <a href="{{ route('transactions.index') }}" 
                               class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                                Voltar
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Informações -->
            <div class="bg-red-50 border-l-4 border-red-400 p-4 mt-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700">
                            <strong>Atenção:</strong> Você deve ter saldo suficiente para realizar a transferência. O sistema validará seu saldo antes de processar a transação.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
