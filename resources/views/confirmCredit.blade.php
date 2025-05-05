<x-app-layout>
    <div class="max-w-md mx-auto mt-10">
        <h1 class="text-xl font-bold mb-4 text-white">Confirmar uso do Crédito</h1>
        
        <p class="text-white">Você não tem saldo suficiente. Deseja usar o limite do seu cartão de crédito para completar a transferência de R${{ number_format($amount, 2, ',', '.') }} + 10% de acréscimo
        ?</p>

        <form method="POST" action="{{ route('transfer.confirmCredit') }}">
            @csrf
            <input type="hidden" name="receiver_cpf" value="{{ $receiver_cpf }}">
            <input type="hidden" name="amount" value="{{ $amount }}">
            <input type="hidden" name="description" value="{{ $description }}">
            <button type="submit" class="bg-green-500 text-white w-full p-2 rounded">
                Sim, usar cartão de crédito
            </button>
        </form>

        <a href="{{ route('transfer.form') }}" class="text-blue-500">Não, voltar para a transferência</a>
    </div>
</x-app-layout>
