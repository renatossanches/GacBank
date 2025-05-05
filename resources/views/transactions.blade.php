<x-app-layout>
    <!-- Barra de Navegação -->
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Minhas Transações') }}
        </h2>
    </x-slot>

    <div class="max-w-screen-2xl mx-auto mt-10 p-4">
        @if($transactions->isEmpty())
            <p class="text-center text-gray-700">Você ainda não realizou transações.</p>
        @else
            <div class="overflow-x-auto">
            <!-- Mensagem de Erro do Servidor -->
            @if (session('error'))
                <div 
                    x-data="{ show: true }" 
                    x-init="setTimeout(() => show = false, 3000)" 
                    x-show="show" 
                    class="mt-4 p-2 bg-red-100 text-red-700 rounded"
                >
                    {{ session('error') }}
                </div>
            @endif

            <!-- Mensagem de Sucesso -->
            @if (session('success'))
                <div 
                    x-data="{ show: true }" 
                    x-init="setTimeout(() => show = false, 1300)" 
                    x-show="show" 
                    class="mt-4 p-2 bg-green-100 text-green-700 rounded"
                >
                    {{ session('success') }}
                </div>
            @endif


                <table class="w-full table-fixed bg-white border border-gray-200 rounded">
                    <thead>
                        <tr class="bg-gray-100 text-gray-700">
                            <th class="px-4 py-2">Ação</th>
                            <th class="px-4 py-2 ">Remetente CPF</th>
                            <th class="px-4 py-2">Remetente Nome</th>
                            <th class="px-4 py-2">Destinatário CPF</th>
                            <th class="px-4 py-2">Destinatário Nome</th>
                            <th class="px-4 py-2">Valor</th>
                            <th class="px-4 py-2">Tipo</th>
                            <th class="px-4 py-2">Status</th>
                            <th class="px-4 py-2">Descrição</th>
                            <th class="px-4 py-2">Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $transaction)
                            <tr class="border-t">
                                <td class="px-4 py-2">
                                    @if($transaction['status'] !== 'reversed')
                                    <form method="POST" action="{{ route('banking.reverse', $transaction['id']) }}">
                                        @csrf
                                        <button type="submit" class="text-blue-500 hover:underline" title="Reverter">
                                            ♻️
                                        </button>
                                    </form>

                                    @else
                                        <span class="text-gray-400">Revertida</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2" break-words>{{ $transaction['sender_cpf'] }}</td>
                                <td class="px-4 py-2">{{ $transaction['sender_name'] }}</td>
                                <td class="px-4 py-2">{{ $transaction['receiver_cpf'] }}</td>
                                <td class="px-4 py-2">{{ $transaction['receiver_name'] }}</td>
                                <td class="px-4 py-2">R$ {{ number_format((float) $transaction['amount'], 2, ',', '.') }}</td>
                                <td class="px-4 py-2">{{ ucfirst($transaction['type']) }}</td>
                                <td class="px-4 py-2">{{ ucfirst($transaction['status']) }}</td>
                                <td class="px-4 py-2">{{ $transaction['description'] }}</td>
                                <td class="px-4 py-2">{{ \Carbon\Carbon::parse($transaction['created_at'])->format('d/m/Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-app-layout>
