<x-app-layout>
    <!-- Barra de Navegação -->
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Sistema Bancário') }}
        </h2>
    </x-slot>

    <div class="max-w-md mx-auto mt-10">
        <h1 class="text-xl font-bold mb-4 text-white w-full p-2 rounde">Transferir</h1>

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

<!-- Formulário de Transferência -->
<form method="POST" action="{{ route('transfer') }}">
    @csrf
    <x-text-input id="cpf" placeholder="CPF do destinatário" class="w-full p-2 border mb-4" type="text" name="receiver_cpf" :value="old('receiver_cpf')" required autocomplete="receiver_cpf" x-data x-on:input="cpfFormatter"/>
    
    <x-text-input type="text" name="description" class="w-full p-2 border mb-4" placeholder="Descrição" required/>

    <x-text-input type="number" name="amount" step="0.01" class="w-full p-2 border mb-4" placeholder="Valor do depósito" required/>
    
    <button type="submit" class="bg-green-500 text-white w-full p-2 rounded">Transferir</button>
</form>

</x-app-layout>
